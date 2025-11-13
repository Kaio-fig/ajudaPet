<?php
// 1. INICIA A SESSÃO E SEGURANÇA
session_start();
if (!isset($_SESSION['admin_id']) || $_SESSION['nivel_acesso'] != 0) {
    header("Location: ../../login.php?erro=acesso_negado");
    exit();
}

// 2. PEGAR O ID DA URL E BUSCAR O ANIMAL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID do animal inválido.");
}
$animal_id = $_GET['id'];

// 3. Inclui a conexão
require_once '../../config/conexao.php';

// 4. Buscar os dados do animal
try {
    $sql = "SELECT nome FROM Animal WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$animal_id]);
    $animal = $stmt->fetch();

    if (!$animal) {
        header("Location: consultar_animal.php?erro=nao_encontrado");
        exit();
    }
} catch (PDOException $e) {
    die("Erro ao buscar dados: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmar Exclusão - Ajudapet</title> 
    
    <link rel="stylesheet" href="../../assets/css/global.css">
    <link rel="stylesheet" href="../../assets/css/admin_global.css">
    <link rel="stylesheet" href="../../assets/css/admin_animais.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

    <header class="navbar">
        <div class="container">
            <a href="../index.php" class="logo" style="color: var(--cor-principal);">Ajudapet (Admin)</a>
            <a href="../../backend/logout.php" class="btn-login" style="background-color: var(--cor-principal);">Sair</a>
        </div>
    </header>

    <main class="container admin-dashboard">

        <div class="admin-header">
            <h1>Painel Administrativo</h1>
        </div>
        <nav class="admin-tabs">
            <a href="../index.php" class="tab-link">
                <i class="fas fa-chart-bar"></i> DashBoard
            </a>
            <a href="../avaliar_solicitacoes.php" class="tab-link">
                <i class="fas fa-tasks"></i> Solicitações 
            </a>
            <a href="consultar_animal.php" class="tab-link active">
                <i class="fas fa-paw"></i> Gerenciar Animais
            </a>
            <a href="../relatorio.php" class="tab-link">
                <i class="fas fa-file-alt"></i> Solicitantes
            </a>
            <a href="../ver_doacoes.php" class="tab-link">
                <i class="fas fa-box-open"></i> Doações Físicas
            </a>
        </nav>

        <div id="gerenciar-animais-delete" class="tab-content active" style="display:block;">
            
            <div class="delete-confirmation-box">
                <h2><i class="fas fa-exclamation-triangle"></i> Confirmar Exclusão</h2>
                
                <p>
                    Você tem certeza que deseja excluir permanentemente o registro de:
                </p>
                
                <h3><?php echo htmlspecialchars($animal['nome']); ?> (ID: <?php echo $animal_id; ?>)</h3>
                
                <p>
                    <strong>Esta ação não pode ser desfeita.</strong> Todos os dados associados (vacinas, solicitações) e a foto do animal serão apagados do servidor.
                </p>

                <form action="../../backend/animais_backend/processa_exclusao_animal.php" method="POST" class="delete-actions">
                    
                    <input type="hidden" name="id" value="<?php echo $animal_id; ?>">
                    
                    <a href="consultar_animal.php" class="btn-cancelar">
                        Cancelar
                    </a>
                    
                    <button type="submit" class="btn-submit btn-danger">
                        Sim, excluir permanentemente
                    </button>
                </form>
            </div>
            
        </div>
    </main>
</body>
</html>