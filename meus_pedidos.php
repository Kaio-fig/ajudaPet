<?php
// 1. INICIAR SESSÃO E CONEXÃO
session_start();
require_once 'config/conexao.php';

// 2. SEGURANÇA (BOUNCER)
if (!isset($_SESSION['solicitante_id'])) {
    header("Location: login.php?erro=acesso_negado");
    exit();
}
$id_solicitante_logado = $_SESSION['solicitante_id'];

try {
    // 3. BUSCAR OS PEDIDOS (Query Principal)
    $sql_pedidos = "SELECT 
                        s.id, s.status, s.data_solicitacao, s.data_visita_aprovada, 
                        s.observacoes, s.visto_pelo_solicitante,
                        a.id AS animal_id, a.nome AS animal_nome, a.imagem_url AS animal_imagem
                    FROM SolicitacaoAdoção AS s
                    JOIN Animal AS a ON s.id_animal = a.id
                    WHERE s.id_solicitante = ?
                    ORDER BY s.data_solicitacao DESC";
    $stmt_pedidos = $pdo->prepare($sql_pedidos);
    $stmt_pedidos->execute([$id_solicitante_logado]);
    $pedidos = $stmt_pedidos->fetchAll();

    // 4. [IMPORTANTE] LIMPAR AS NOTIFICAÇÕES
    // Marca todas as solicitações "não vistas" como "vistas"
    $sql_update_visto = "UPDATE SolicitacaoAdoção SET visto_pelo_solicitante = 1
                       WHERE id_solicitante = ? AND visto_pelo_solicitante = 0
                       AND (status = 'Aprovada' OR status = 'Rejeitada')";
    $stmt_update_visto = $pdo->prepare($sql_update_visto);
    $stmt_update_visto->execute([$id_solicitante_logado]);
} catch (PDOException $e) {
    die("Erro ao buscar pedidos: " . $e->getMessage());
}

$notificacoes_count = 0; // Inicia a contagem
if (isset($_SESSION['solicitante_id'])) {
    $id_solicitante_logado = $_SESSION['solicitante_id'];
    $sql_notif = "SELECT COUNT(*) FROM SolicitacaoAdoção 
                  WHERE id_solicitante = ? 
                  AND visto_pelo_solicitante = 0 
                  AND (status = 'Aprovada' OR status = 'Rejeitada')";
    $stmt_notif = $pdo->prepare($sql_notif);
    $stmt_notif->execute([$id_solicitante_logado]);
    $notificacoes_count = $stmt_notif->fetchColumn();
}

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Pedidos - Ajudapet</title>
    <link rel="stylesheet" href="assets/css/estilo.css">
</head>

<body>

    <header class="navbar">
        <div class="container">
            <a href="index.php" class="logo">Ajuda pet</a>
            <nav>
                <ul>
                    <li><a href="index.php">Início</a></li>
                    <li><a href="#galeria">Animais</a></li>
                    <li><a href="#como-funciona">Instruções</a></li>
                </ul>
            </nav>

            <div class="nav-buttons">
                <?php
                // VERIFICA SE É UM ADMIN LOGADO
                if (isset($_SESSION['admin_id'])):
                ?>
                    <a href="admin/index.php" class="btn-profile">Painel Admin</a>
                    <a href="backend/logout.php" class="btn-login">Sair</a>

                <?php
                // VERIFICA SE É UM SOLICITANTE LOGADO
                elseif (isset($_SESSION['solicitante_id'])):
                    // Pega o nome do solicitante (o "Marcos Oli" do exemplo)
                    $nome_solicitante = $_SESSION['solicitante_nome'];
                ?>
                    <div class="profile-dropdown">
                        <button class="btn-profile">
                            <img src="assets/images/icon-profile.png" alt="Icone Perfil" class="profile-icon">
                            <?php echo htmlspecialchars($nome_solicitante); ?>
                            &#9662; </button>
                        <div class="dropdown-content">
                            <a href="perfil.php">Meus Dados</a>
                            <a href="meus_pedidos.php" class="link-notificacao">
                                Meus Pedidos
                                <?php if ($notificacoes_count > 0): ?>
                                    <span class="notification-badge"><?php echo $notificacoes_count; ?></span>
                                <?php endif; ?>
                            </a>
                            <a href="backend/logout.php">Sair</a>
                        </div>
                    </div>

                <?php
                else:
                ?>
                    <a href="login.php" class="btn-login">Login/Cadastro</a>

                <?php endif; ?>
            </div>
        </div>
    </header>

    <main class="container" style="padding-top: 2rem;">
        <h1>Meus Pedidos de Adoção</h1>

        <div class="lista-pedidos">
            <?php if (empty($pedidos)): ?>
                <p>Você ainda não fez nenhum pedido de adoção.</p>
            <?php else: ?>
                <?php foreach ($pedidos as $pedido): ?>

                    <div class="pedido-card status-<?php echo strtolower($pedido['status']); ?>">

                        <div class="pedido-imagem">
                            <img src="uploads/<?php echo htmlspecialchars($pedido['animal_imagem']); ?>" alt="<?php echo htmlspecialchars($pedido['animal_nome']); ?>">
                        </div>

                        <div class="pedido-info">
                            <h3><?php echo htmlspecialchars($pedido['animal_nome']); ?></h3>
                            <span class="pedido-data">Solicitado em: <?php echo date('d/m/Y H:i', strtotime($pedido['data_solicitacao'])); ?></span>

                            <?php if ($pedido['status'] == 'Pendente'): ?>
                                <div class="pedido-status pendente">
                                    <strong>Status: Pendente</strong>
                                    <p>Sua solicitação está sendo avaliada pela administração.</p>
                                </div>

                            <?php elseif ($pedido['status'] == 'Aprovada'): ?>
                                <div class="pedido-status aprovada">
                                    <strong>Status: Aprovado! <i class="fas fa-check-circle"></i></strong>
                                    <p><strong>Visita agendada para:</strong> <?php echo date('d/m/Y H:i', strtotime($pedido['data_visita_aprovada'])); ?></p>
                                    <p><strong>Notas do Admin:</strong> <?php echo htmlspecialchars($pedido['observacoes'] ?? 'Nenhuma nota.'); ?></p>
                                </div>

                            <?php elseif ($pedido['status'] == 'Rejeitada'): ?>
                                <div class="pedido-status rejeitada">
                                    <strong>Status: Rejeitado <i class="fas fa-times-circle"></i></strong>
                                    <p><strong>Motivo:</strong> <?php echo htmlspecialchars($pedido['observacoes'] ?? 'Não informado.'); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="pedido-link">
                            <a href="animal_detalhes.php?id=<?php echo $pedido['animal_id']; ?>" class="btn-detalhes">Ver Animal</a>
                        </div>
                    </div>

                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </main>

    <footer>
    </footer>

    <script src="assets/js/funcoes.js"></script>
    <script src="assets/js/main.js"></script>

</body>

</html>