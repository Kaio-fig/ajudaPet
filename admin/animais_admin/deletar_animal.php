<?php
// 1. INICIA A SESSÃO E SEGURANÇA
session_start();
if ( !isset($_SESSION['admin_id']) || $_SESSION['nivel_acesso'] != 0 ) {
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

// 4. Buscar os dados do animal (só precisamos do nome)
try {
    $sql = "SELECT nome FROM animal WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$animal_id]);
    $animal = $stmt->fetch();

    // Verifica se o animal foi encontrado
    if (!$animal) {
        // Redireciona de volta para a lista com uma flag de erro
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
    <link rel="stylesheet" href="../../assets/css/estilo.css"> 
</head>
<body>

    <header class="navbar">
        <div class="container">
            <a href="../index.php" class="logo">Ajudapet (Admin)</a>
            <nav>
                <ul>
                    <li><a href="../index.php">Início</a></li>
                    <li><a href="consultar_animal.php">Consultar Animais</a></li>
                    <li><a href="../avaliar_solicitacoes.php">Ver Solicitações</a></li>
                </ul>
            </nav>
            <a href="../../backend/logout.php" class="btn-login">Sair</a>
        </div>
    </header>

    <main class="container" style="padding-top: 2rem;">
        
        <h2 style="color: #b91c1c;">Confirmar Exclusão</h2>
        
        <div style="background-color: #fee2e2; border: 1px solid #fecaca; padding: 20px; border-radius: 5px; margin-top: 1rem;">
            <p style="font-size: 1.1rem;">
                Você tem certeza que deseja excluir permanentemente o registro de:
            </p>
            
            <h3 style="font-size: 1.5rem; margin-top: 10px;"><?php echo htmlspecialchars($animal['nome']); ?> (ID: <?php echo $animal_id; ?>)</h3>
            
            <p style="margin-top: 15px; font-weight: bold;">
                Esta ação não pode ser desfeita. Todos os dados e a foto do animal serão apagados do servidor.
            </p>

            <form action="../../backend/animais_backend/processa_exclusao_animais.php" method="POST" style="margin-top: 20px;">
                
                <input type="hidden" name="id" value="<?php echo $animal_id; ?>">
                
                <a href="consultar_animal.php" class="btn-editar" style="background-color: #eee; color: #555;">
                    Cancelar
                </a>
                
                <button type="submit" class="btn-excluir" style="font-family: 'Montserrat', sans-serif; font-size: 0.9rem;">
                    Sim, excluir permanentemente
                </button>
            </form>
        </div>

    </main>
</body>
</html>