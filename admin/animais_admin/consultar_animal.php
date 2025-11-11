<?php
// 1. INICIA A SESSÃO
session_start();

// 2. O Bouncer (Segurança)
if ( !isset($_SESSION['admin_id']) || $_SESSION['nivel_acesso'] != 0 ) {
    header("Location: ../../login.php?erro=acesso_negado");
    exit();
}

// 3. Inclui a conexão
require_once '../../config/conexao.php';

// 4. Preparar e Executar a Query SQL (Read)
try {
    // Vamos selecionar os principais, mais o ID (essencial para Editar/Excluir)
    $sql = "SELECT id, nome, status, imagem_url FROM animal ORDER BY data_cadastro DESC";
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
    <link rel="stylesheet" href="../../assets/css/estilo.css"> 
</head>
<body>

    <header class="navbar">
        <div class="container">
            <a href="../index.php" class="logo">Ajudapet (Admin)</a>
            <nav>
                <ul>
                    <li><a href="../index.php">Início</a></li>
                    <li><a href="cadastrar_animal.php">Cadastrar Animal</a></li>
                    <li><a href="consultar_animal.php">Consultar Animais</a></li>
                    <li><a href="../avaliar_solicitacoes.php">Ver Solicitações</a></li>
                </ul>
            </nav>
            <a href="../../backend/logout.php" class="btn-login">Sair</a>
        </div>
    </header>

    <main class="container" style="padding-top: 2rem;">
        <h2>Gerenciar Animais Cadastrados</h2>
        
        <table class="tabela-admin">
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
                                     style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">
                            </td>
                            <td><?php echo htmlspecialchars($animal['nome']); ?></td>
                            <td><?php echo htmlspecialchars($animal['status']); ?></td>
                            <td class="acoes">
                                <a href="editar_animal.php?id=<?php echo $animal['id']; ?>" class="btn-editar">
                                    Editar
                                </a>
                                <a href="../../backend/animais_backend/excluir_animal.php?id=<?php echo $animal['id']; ?>" class="btn-excluir">
                                    Excluir
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">Nenhum animal cadastrado no momento.</td>
                    </tr>
                <?php endif; ?>

            </tbody>
        </table>

    </main>
</body>
</html>