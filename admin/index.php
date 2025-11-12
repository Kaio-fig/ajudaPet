<?php
// 1. INICIA A SESSÃO E VERIFICA O ACESSO
session_start();
if ( !isset($_SESSION['admin_id']) || $_SESSION['nivel_acesso'] != 0 ) {
    header("Location: ../login.php?erro=acesso_negado");
    exit();
}
$nome_admin = $_SESSION['admin_nome'];

// 2. INCLUI A CONEXÃO (Para fazer as consultas)
require_once '../config/conexao.php';

// 3. BUSCAR DADOS PARA OS CARDS
try {
    // --- Contagem de Animais ---
    // (Usamos SUM(CASE...) para fazer tudo em uma consulta só)
    $sql_animais = "SELECT
                        COUNT(*) AS total,
                        SUM(CASE WHEN status = 'Disponível' THEN 1 ELSE 0 END) AS disponiveis,
                        SUM(CASE WHEN status = 'Adotado' THEN 1 ELSE 0 END) AS adotados
                    FROM Animal";
    $stmt_animais = $pdo->query($sql_animais);
    $counts_animais = $stmt_animais->fetch(PDO::FETCH_ASSOC);

    // --- Contagem de Solicitações ---
    $sql_solicitacoes = "SELECT
                            SUM(CASE WHEN status = 'Pendente' THEN 1 ELSE 0 END) AS pendentes,
                            SUM(CASE WHEN status = 'Aprovada' THEN 1 ELSE 0 END) AS aprovadas,
                            SUM(CASE WHEN status = 'Rejeitada' THEN 1 ELSE 0 END) AS rejeitadas
                        FROM SolicitacaoAdoção";
    $stmt_solicitacoes = $pdo->query($sql_solicitacoes);
    $counts_solicitacoes = $stmt_solicitacoes->fetch(PDO::FETCH_ASSOC);

    // (Garante que os números não sejam nulos se o banco estiver vazio)
    $total_animais = $counts_animais['total'] ?? 0;
    $total_disponiveis = $counts_animais['disponiveis'] ?? 0;
    $total_adotados = $counts_animais['adotados'] ?? 0;

    $total_pendentes = $counts_solicitacoes['pendentes'] ?? 0;
    $total_aprovadas = $counts_solicitacoes['aprovadas'] ?? 0;
    // O mockup_4 tem "Finalizados", mas o DB tem "Aprovada" e "Rejeitada" (mockup_5)
    // Vamos mostrar os 3 status do DB, o que é mais útil.
    $total_rejeitadas = $counts_solicitacoes['rejeitadas'] ?? 0;

} catch (PDOException $e) {
    die("Erro ao buscar dados do dashboard: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Ajudapet</title>
    <link rel="stylesheet" href="../assets/css/estilo.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

    <header class="navbar">
        <div class="container">
            <a href="index.php" class="logo" style="color: var(--cor-principal);">Ajudapet (Admin)</a>
            <nav>
                </nav>
            <a href="../backend/logout.php" class="btn-login" style="background-color: var(--cor-principal);">Sair</a>
        </div>
    </header>

    <main class="container admin-dashboard">

        <div class="admin-header">
            <h1>Painel Administrativo</h1>
            <p>Gerencie animais, solicitações de adoção e acompanhe estatísticas</p>
        </div>

        <nav class="admin-tabs">
            <a href="index.php" class="tab-link active">
                <i class="fas fa-chart-bar"></i> DashBoard
            </a>
            <a href="avaliar_solicitacoes.php" class="tab-link">
                <i class="fas fa-tasks"></i> Solicitações 
                <?php if ($total_pendentes > 0): ?>
                    <span class="notification-badge"><?php echo $total_pendentes; ?></span>
                <?php endif; ?>
            </a>
            <a href="./animais_admin/consultar_animal.php" class="tab-link">
                <i class="fas fa-paw"></i> Gerenciar Animais
            </a>
            <a href="relatorio.php" class="tab-link">
                <i class="fas fa-file-alt"></i> Gerenciar Solicitantes
            </a>
        </nav>

        <div id="dashboard" class="tab-content active">
            
            <div class="stat-grid">
                <div class="stat-card" style="background: #e6f7ff; border-color: #91d5ff;">
                    <i class="fas fa-paw icon" style="color: #096dd9;"></i>
                    <div class="stat-info">
                        <h2><?php echo $total_animais; ?></h2>
                        <p>Total de Animais</p>
                    </div>
                </div>
                <div class="stat-card" style="background: #f6ffed; border-color: #b7eb8f;">
                    <i class="fas fa-shield-dog icon" style="color: #389e0d;"></i> <div class="stat-info">
                        <h2><?php echo $total_disponiveis; ?></h2>
                        <p>Disponíveis</p>
                    </div>
                </div>
                <div class="stat-card" style="background: #fff0f6; border-color: #ffadd2;">
                    <i class="fas fa-home icon" style="color: #c41d7f;"></i>
                    <div class="stat-info">
                        <h2><?php echo $total_adotados; ?></h2>
                        <p>Adotados</p>
                    </div>
                </div>
            </div>

            <h3>Status de Solicitações</h3>
            <div class="stat-grid solicitation-status">
                <div class="stat-card" style="background: #fffbe6; border-color: #ffe58f;">
                    <i class="fas fa-clock icon" style="color: #d4b106;"></i>
                    <div class="stat-info">
                        <h2><?php echo $total_pendentes; ?></h2>
                        <p>Pendente</p>
                    </div>
                </div>
                <div class="stat-card" style="background: #e6f7ff; border-color: #91d5ff;">
                    <i class="fas fa-check-circle icon" style="color: #096dd9;"></i>
                    <div class="stat-info">
                        <h2><?php echo $total_aprovadas; ?></h2>
                        <p>Aprovado</p>
                    </div>
                </div>
                <div class="stat-card" style="background: #fff1f0; border-color: #ffa39e;">
                    <i class="fas fa-times-circle icon" style="color: #cf1322;"></i>
                    <div class="stat-info">
                        <h2><?php echo $total_rejeitadas; ?></h2>
                        <p>Rejeitado</p>
                    </div>
                </div>
            </div>

            <h3>Ações Rápidas</h3>
            <div class="quick-actions">
                <a href="./animais_admin/cadastrar_animal.php" class="action-button">
                    <i class="fas fa-plus-circle"></i> Adicionar Animal
                </a>
                <a href="ver_doacoes.php" class="action-button">
                    <i class="fas fa-file-alt"></i> Ver Doações
                </a>
            </div>

        </div> </main>
</body>
</html>