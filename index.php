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
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 Ajudapet. Todos os direitos reservados.</p>
        </div>
    </footer>
    <script src="assets/js/funcoes.js" defer></script>
    <script src="assets/js/main.js" defer></script>
    <script>const listaImagensCarrossel = <?php echo json_encode($imagens_carrossel); ?>;</script>

</body>

</html>