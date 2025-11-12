<?php
// 1. INICIA A SESSÃO
session_start();

// 2. Incluir o arquivo de conexão
require_once 'config/conexao.php';

// 3. Preparar e Executar a Query SQL (para a galeria)
try {
    $sql = "SELECT id, nome, sexo, porte, imagem_url, data_nascimento, personalidade FROM Animal WHERE status = 'Disponível' ORDER BY data_cadastro DESC";
    $stmt = $pdo->query($sql);
    $animais = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Erro ao buscar animais: " . $e->getMessage();
    $animais = [];
}

// 4.LÓGICA DO CARROSSEL
$imagens_carrossel = [];

// Adiciona as imagens dos animais (se tiverem)
if ($animais) {
    foreach ($animais as $animal) {
        if (!empty($animal['imagem_url'])) {
            $imagens_carrossel[] = 'uploads/' . $animal['imagem_url'];
        }
    }
}

// Adiciona a imagem de fallback (hero-bg.jpg) à rotação
$imagens_carrossel[] = 'assets/images/hero-bg.jpg';

// Embaralha a ordem
shuffle($imagens_carrossel);

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
    <meta charset="UTF-B">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajudapet - Adote seu novo melhor amigo</title>
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
    <main>
        <section class="hero">
            <div class="hero-content">
                <h1>Adote um Amigo</h1>
                <p>Encontre cães e gatos em busca de um lar. A adoção muda uma vida.</p>
            </div>
        </section>

        <section id="galeria" class="container">
            <h2>Encontre seu Novo Melhor Amigo</h2>
            <p>Temos dezenas de cãezinhos carinhosos e brincalhões esperando por você.</p>

            <div class="galeria-animais">

                <?php if (count($animais) > 0): ?>
                    <?php foreach ($animais as $animal): ?>

                        <div class="animal-card">
                            <img src="uploads/<?php echo htmlspecialchars($animal['imagem_url']); ?>" alt="Foto do <?php echo htmlspecialchars($animal['nome']); ?>">

                            <div class="card-info">
                                <h3><?php echo htmlspecialchars($animal['nome']); ?></h3>
                                <ul>
                                    <li><strong>Sexo:</strong> <?php echo htmlspecialchars($animal['sexo']); ?></li>
                                    <li><strong>Porte:</strong> <?php echo htmlspecialchars($animal['porte']); ?></li>
                                    <li class="info-idade-animal" data-nascimento="<?php echo htmlspecialchars($animal['data_nascimento']); ?>">
                                        <strong>Idade:</strong>
                                        <span class="idade-calculada">...</span>
                                    </li>
                                    <li><strong>Personalidade:</strong> <?php echo htmlspecialchars($animal['personalidade']) ?></li>
                                </ul>
                                <a href="animal_detalhes.php?id=<?php echo $animal['id']; ?>" class="btn-detalhes">
                                    Ver Detalhes
                                </a>
                            </div>
                        </div>

                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Nenhum animal disponível para adoção no momento. Volte em breve!</p>
                <?php endif; ?>

            </div>
        </section>

        <section id="como-funciona" class="container">
            <h2>Como Funciona a Adoção</h2>
            <p style="text-align: center; max-width: 600px; margin: 0 auto 2rem auto;">Nosso processo é pensado para garantir o bem-estar do animal e a segurança da sua nova família.</p>

            <div class="info-cards-container">

                <div class="info-card">
                    <span class="card-number">1</span>
                    <h3>Encontre seu Pet</h3>
                    <p>Navegue em nossa galeria, conheça os perfis e crie sua conta. Ao fazer login, você estará pronto para o próximo passo.</p>
                </div>

                <div class="info-card">
                    <span class="card-number">2</span>
                    <h3>Solicite a Visita</h3>
                    <p>Na página do animal, clique em "Quero Adotar". No formulário que aparecer, <b>você poderá sugerir a melhor data e hora</b> para você vir conhecê-lo.</p>
                </div>

                <div class="info-card">
                    <span class="card-number">3</span>
                    <h3>Aguarde a Confirmação</h3>
                    <p>Nossa equipe irá analisar seu perfil. Fique de olho na sua página <b>"Meus Pedidos"</b> para ver o status e a data confirmada da sua visita!</p>
                </div>

            </div>
        </section>
        <section id="doacoes" class="container" style="padding: 3rem 0;">

            <div style="text-align: center; margin-bottom: 2rem;">
                <h2>Ajude nossa Causa</h2>
                <p>Sua contribuição é vital para continuarmos nosso resgate e cuidado.</p>
            </div>

            <nav class="doacao-tabs">
                <button class="doacao-tab-link active" data-tab="tab-monetaria">
                    <i class="fas fa-hand-holding-usd"></i> Doação Monetária
                </button>
                <button class="doacao-tab-link" data-tab="tab-fisica">
                    <i class="fas fa-box-open"></i> Doação de Itens
                </button>
            </nav>

            <div id="tab-monetaria" class="doacao-content active">
                <h3>Faça uma contribuição financeira</h3>
                <p>Seu dinheiro será usado para cobrir custos veterinários, vacinas, cirurgias e ração.</p>

                <div class="pix-info">
                    <strong>Doe via PIX</strong>
                    <p>Use nossa chave (E-mail):</p>
                    <input type="text" value="financeiro@ajudapet.com" readonly onclick="this.select();">
                    <small>Ou aponte sua câmera para o QR Code ao lado.</small>
                </div>
                <div class="paypal-info">
                    <strong>Doe com PayPal ou Cartão</strong>
                    <p>Clique no botão abaixo para ser redirecionado a um ambiente de pagamento seguro.</p>
                    <a href="https://paypal.me/suaong" target="_blank" class="btn-submit" style="width: auto;">
                        Doar com PayPal
                    </a>
                </div>
            </div>

            <div id="tab-fisica" class="doacao-content">
                <h3>Oferta de Doação Física</h3>
                <p>Ração, remédios, coleiras e vacinas são sempre bem-vindos! Por favor, descreva sua doação e entraremos em contato para combinar a retirada.</p>

                <form action="backend/processa_doacao_fisica.php" method="POST">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="nome_doador">Seu Nome:</label>
                            <input type="text" id="nome_doador" name="nome_doador" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Seu E-mail:</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                    </div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="telefone">Telefone (Opcional):</label>
                            <input type="tel" id="telefone" name="telefone">
                        </div>
                        <div class="form-group">
                            <label for="tipo_item">Tipo de Item:</label>
                            <select id="tipo_item" name="tipo_item" required>
                                <option value="" disabled selected>-- Selecione --</option>
                                <option value="Ração">Ração</option>
                                <option value="Remédio">Remédio</option>
                                <option value="Vacina">Vacina</option>
                                <option value="Acessório">Acessório (Cama, Coleira, etc.)</option>
                                <option value="Outro">Outro</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="descricao">Descrição dos Itens:</label>
                        <textarea id="descricao" name="descricao" rows="4" placeholder="Ex: 3 pacotes de ração para filhotes..." required></textarea>
                    </div>
                    <button type="submit" class="btn-submit">Enviar Oferta de Doação</button>
                </form>
            </div>

        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 Ajudapet. Todos os direitos reservados.</p>
        </div>
    </footer>
    <script src="assets/js/funcoes.js" defer></script>
    <script src="assets/js/main.js" defer></script>
    <script>
        const listaImagensCarrossel = <?php echo json_encode($imagens_carrossel); ?>;
    </script>

</body>

</html>