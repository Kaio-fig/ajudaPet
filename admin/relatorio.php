<?php
// 1. INICIA A SESSÃO E VERIFICA O ACESSO
session_start();
if (!isset($_SESSION['admin_id']) || $_SESSION['nivel_acesso'] != 0) {
    header("Location: ../login.php?erro=acesso_negado");
    exit();
}

// 2. INCLUI A CONEXÃO
require_once '../config/conexao.php';

// 3. BUSCAR DADOS PARA O RELATÓRIO
try {
    // Query para listar solicitantes e contar suas adoções
    // (A "reincidência" que você mencionou)
    $sql_solicitantes = "SELECT 
                            sol.id, sol.nome, sol.email, sol.telefone, sol.data_cadastro, sol.ativo,
                            COUNT(a.id) AS total_adocoes
                        FROM Solicitante AS sol
                        LEFT JOIN Animal AS a ON sol.id = a.id_adotante AND a.status = 'Adotado'
                        GROUP BY sol.id
                        ORDER BY total_adocoes DESC, sol.nome ASC";
    
    $stmt_solicitantes = $pdo->query($sql_solicitantes);
    $solicitantes = $stmt_solicitantes->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erro ao buscar dados do relatório: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatórios - Ajudapet</title>
    <link rel="stylesheet" href="../assets/css/estilo.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

    <header class="navbar">
        <div class="container">
            <a href="index.php" class="logo" style="color: var(--cor-principal);">Ajudapet (Admin)</a>
            <a href="../backend/logout.php" class="btn-login" style="background-color: var(--cor-principal);">Sair</a>
        </div>
    </header>

    <main class="container admin-dashboard">

        <div class="admin-header">
            <h1>Painel Administrativo</h1>
            <p>Gerencie animais, solicitações de adoção e acompanhe estatísticas</p>
        </div>

        <nav class="admin-tabs">
            <a href="index.php" class="tab-link">
                <i class="fas fa-chart-bar"></i> DashBoard
            </a>
            <a href="avaliar_solicitacoes.php" class="tab-link">
                <i class="fas fa-tasks"></i> Solicitações 
            </a>
            <a href="./animais_admin/consultar_animal.php" class="tab-link">
                <i class="fas fa-paw"></i> Gerenciar Animais
            </a>
            <a href="relatorio.php" class="tab-link active">
                <i class="fas fa-file-alt"></i> Gerenciar solicitantes
            </a>
        </nav>

        <div id="relatorios" class="tab-content active" style="display:block;">
            
            <h3>Relatório de Solicitantes</h3>
            <p>Lista de todos os usuários cadastrados, total de adoções e status da conta.</p>

            <div class="admin-table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Solicitante</th>
                            <th>Contato</th>
                            <th>Membro Desde</th>
                            <th>Adoções Feitas</th>
                            <th>Status</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($solicitantes as $sol): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($sol['nome']); ?></strong><br>
                                    <small><?php echo htmlspecialchars($sol['email']); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($sol['telefone']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($sol['data_cadastro'])); ?></td>
                                <td><?php echo $sol['total_adocoes']; ?></td>
                                <td>
                                    <?php if ($sol['ativo'] == 1): ?>
                                        <span class="status-aprovada">Ativo</span>
                                    <?php else: ?>
                                        <span class="status-rejeitada">Bloqueado</span>
                                    <?php endif; ?>
                                </td>
                                <td class="action-buttons">
                                    <?php if ($sol['ativo'] == 1): ?>
                                        <a href="../backend/processa_bloqueio_solicitante.php?id=<?php echo $sol['id']; ?>&acao=bloquear" 
                                           class="action-btn reject" 
                                           onclick="return confirm('Tem certeza que deseja BLOQUEAR este usuário? Ele não poderá mais fazer login.')">
                                            <i class="fas fa-ban"></i> Bloquear
                                        </a>
                                    <?php else: ?>
                                        <a href="../backend/processa_bloqueio_solicitante.php?id=<?php echo $sol['id']; ?>&acao=desbloquear" 
                                           class="action-btn approve"
                                           onclick="return confirm('Tem certeza que deseja DESBLOQUEAR este usuário?')">
                                            <i class="fas fa-check"></i> Desbloquear
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            </div> </main>
</body>
</html>