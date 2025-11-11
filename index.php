<?php
// 1. INICIA A SESSÃO
session_start();

// 2. Incluir o arquivo de conexão
require_once 'config/conexao.php';

// 3. Preparar e Executar a Query SQL
try {
    $sql = "SELECT id, nome, sexo, porte, imagem_url, personalidade, data_nascimento FROM Animal WHERE status = 'Disponível' ORDER BY data_cadastro DESC";
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
    <meta charset="UTF-B">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajudapet - Adote seu novo melhor amigo</title>
    <link rel="stylesheet" href="assets/css/estilo.css">
</head>

<body>

    <header class="navbar">
        <div class="container">
            <a href="index.php" class="logo">Pet & Tudo Mais</a>
            <nav>
                <ul>
                    <li><a href="index.php">Início</a></li>
                    <li><a href="#galeria">Animais</a></li>
                    <li><a href="#como-funciona">Instruções</a></li>
                </ul>
            </nav>

            <?php
            if (isset($_SESSION['admin_id']) || isset($_SESSION['solicitante_id'])):
            ?>
                <a href="backend/logout.php" class="btn-login">Sair</a>

            <?php else: ?>
                <a href="login.php" class="btn-login">Login/Cadastro</a>

            <?php endif; ?>

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
    <script src="assets/js/index.js" defer></script>

</body>

</html>