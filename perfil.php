<?php
// 1. INICIAR SESSÃO E CONEXÃO
session_start();
require_once 'config/conexao.php';

// 2. SEGURANÇA (BOUNCER)
// Se não for um solicitante logado, expulsa para o login
if (!isset($_SESSION['solicitante_id'])) {
    header("Location: login.php?erro=acesso_negado");
    exit();
}

// 3. BUSCAR DADOS (READ)
// Pega o ID do solicitante logado
$id_solicitante = $_SESSION['solicitante_id'];

try {
    // Busca TODOS os dados do solicitante no banco
    $sql = "SELECT * FROM Solicitante WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_solicitante]);
    $solicitante = $stmt->fetch();

    // Se não encontrar o usuário (ex: foi deletado), desloga
    if (!$solicitante) {
        header("Location: backend/logout.php");
        exit();
    }
} catch (PDOException $e) {
    die("Erro ao buscar dados do perfil: " . $e->getMessage());
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
    <title>Meus Dados - Ajudapet</title>

    <link rel="stylesheet" href="assets/css/global.css">
    <link rel="stylesheet" href="assets/css/perfil.css">
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
                    <li><a href="#doacoes">Doações</a></li>
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

    <main class="container perfil-page-container">

        <div class="profile-header">
            <div class="profile-avatar">
                <img src="assets/images/icon-profile.png" alt="Icone Perfil" class="profile-icon">
            </div>
            <div class="profile-info">
                <h2><?php echo htmlspecialchars($solicitante['nome']); ?></h2>
                <p><?php echo htmlspecialchars($solicitante['email']); ?></p>
            </div>
        </div>

        <div class="profile-warning">
            <p><strong>Complete seu perfil para poder solicitar adoções.</strong> Precisamos de algumas informações para garantir o bem-estar dos animais.</p>
        </div>

        <div class="tab-nav">
            <button class="tab-link active" data-tab="tab-pessoais">
                <span class="icon">👤</span> Dados Pessoais
            </button>
            <button class="tab-link" data-tab="tab-moradia">
                <span class="icon">🏠</span> Moradia
            </button>
            <button class="tab-link" data-tab="tab-experiencia">
                <span class="icon">❤️</span> Experiência
            </button>
        </div>

        <form action="backend/processa_atualiza_perfil.php" method="POST">

            <div id="tab-pessoais" class="tab-content active">
                <h3>Informações Pessoais</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="nome">Nome Completo:</label>
                        <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($solicitante['nome']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="telefone">Telefone:</label>
                        <input type="tel" id="telefone" name="telefone" value="<?php echo htmlspecialchars($solicitante['telefone']); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="endereco_completo">Endereço (Rua, N°, Complemento):</label>
                    <input type="text" id="endereco_completo" name="endereco_completo" value="<?php echo htmlspecialchars($solicitante['endereco_completo']); ?>">
                </div>
                <div class="form-grid tres-colunas">
                    <div class="form-group">
                        <label for="cidade">Cidade:</label>
                        <input type="text" id="cidade" name="cidade" value="<?php echo htmlspecialchars($solicitante['cidade']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="estado">Estado (UF):</label>
                        <input type="text" id="estado" name="estado" maxlength="2" value="<?php echo htmlspecialchars($solicitante['estado']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="cep">CEP:</label>
                        <input type="text" id="cep" name="cep" value="<?php echo htmlspecialchars($solicitante['cep']); ?>">
                    </div>
                </div>
            </div>

            <div id="tab-moradia" class="tab-content">
                <h3>Informações sobre Moradia</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="tipo_moradia">Tipo de Moradia:</label>
                        <input type="text" id="tipo_moradia" name="tipo_moradia" value="<?php echo htmlspecialchars($solicitante['tipo_moradia']); ?>" placeholder="Casa, Apto, Chácara...">
                    </div>
                    <div class="form-group">
                        <label for="possui_quintal">Possui Quintal/Jardim?</label>
                        <select id="possui_quintal" name="possui_quintal">
                            <option value="Não" <?php if ($solicitante['possui_quintal'] == 'Não') echo 'selected'; ?>>Não</option>
                            <option value="Sim" <?php if ($solicitante['possui_quintal'] == 'Sim') echo 'selected'; ?>>Sim</option>
                        </select>
                    </div>
                </div>
            </div>

            <div id="tab-experiencia" class="tab-content">
                <h3>Experiência com Animais</h3>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="experiencia_animais">Experiência com Animais:</label>
                        <input type="text" id="experiencia_animais" name="experiencia_animais"
                            value="<?php echo htmlspecialchars($solicitante['experiencia_animais']); ?>"
                            placeholder="Nenhuma, Pouca, Muita...">
                    </div>

                    <div class="form-group">
                        <label for="disponibilidade_tempo">Disponibilidade de Tempo:</label>
                        <input type="text" id="disponibilidade_tempo" name="disponibilidade_tempo"
                            value="<?php echo htmlspecialchars($solicitante['disponibilidade_tempo']); ?>"
                            placeholder="Manhã, Tarde, Integral...">
                    </div>
                </div>

                <div class="form-group">
                    <label for="motivacao_adotar">Motivação para Adotar:</label>
                    <textarea id="motivacao_adotar" name="motivacao_adotar" rows="6"
                        placeholder="Conte-nos por que você quer adotar um animal e como pretende cuidar dele..."><?php echo htmlspecialchars($solicitante['motivacao_adotar']); ?></textarea>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit">Salvar Perfil</button>
            </div>
        </form>

    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabLinks = document.querySelectorAll('.tab-link');
            const tabContents = document.querySelectorAll('.tab-content');

            tabLinks.forEach(link => {
                link.addEventListener('click', () => {
                    const tabId = link.dataset.tab;
                    tabLinks.forEach(l => l.classList.remove('active'));
                    tabContents.forEach(c => c.classList.remove('active'));
                    link.classList.add('active');
                    document.getElementById(tabId).classList.add('active');
                });
            });
        });
    </script>
</body>

</html>