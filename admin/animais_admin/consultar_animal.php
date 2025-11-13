<?php
// 1. INICIA A SESSÃO
session_start();

// 2. O Bouncer (Segurança)
if (!isset($_SESSION['admin_id']) || $_SESSION['nivel_acesso'] != 0) {
    header("Location: ../../login.php?erro=acesso_negado");
    exit();
}

// 3. Inclui a conexão
require_once '../../config/conexao.php';

// 4. Preparar e Executar a Query SQL (Read)
try {
    $sql = "SELECT id, nome, status, imagem_url FROM Animal ORDER BY data_cadastro DESC";
    $stmt = $pdo->query($sql);
    $animais = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Erro ao buscar animais: " . $e->getMessage();
    $animais = [];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Animais - Ajudapet</title>

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

        <div id="gerenciar-animais" class="tab-content active" style="display:block;">

            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h3>Animais Cadastrados</h3>
                <a href="cadastrar_animal.php" class="btn-add-new"><i class="fas fa-plus"></i> Cadastrar Novo</a>
            </div>

            <?php if (isset($_GET['sucesso']) && $_GET['sucesso'] == 'exclusao'): ?>
                <div class="aviso-sucesso">Animal excluído com sucesso!</div>
            <?php endif; ?>
            <?php if (isset($_GET['erro']) && $_GET['erro'] == 'nao_encontrado'): ?>
                <div class="aviso-erro">Erro: O animal não foi encontrado.</div>
            <?php endif; ?>

            <div class="admin-table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Foto</th>
                            <th>Nome</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($animais) > 0): ?>
                            <?php foreach ($animais as $animal): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($animal['id']); ?></td>
                                    <td>
                                        <img src="../../uploads/<?php echo htmlspecialchars($animal['imagem_url']); ?>"
                                            alt="Foto do <?php echo htmlspecialchars($animal['nome']); ?>"
                                            class="animal-list-photo">
                                    </td>
                                    <td><?php echo htmlspecialchars($animal['nome']); ?></td>
                                    <td>
                                        <span class="status-<?php echo strtolower(str_replace(' ', '-', $animal['status'])); ?>">
                                            <?php echo htmlspecialchars($animal['status']); ?>
                                        </span>
                                    </td>
                                    <td class="action-buttons">
                                        <a href="editar_animais.php?id=<?php echo $animal['id']; ?>" class="action-btn view" title="Editar">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        <a href="deletar_animal.php?id=<?php echo $animal['id']; ?>"
                                            class="action-btn reject" title="Excluir">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center;">Nenhum animal cadastrado.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>

</html>