<?php
// 1. INICIAR SESSÃO E CONEXÃO
session_start();
require_once 'config/conexao.php';
// O 'helpers/funcoes.php' NÃO é mais necessário aqui.

// 2. SEGURANÇA: PEGAR O ID DO ANIMAL
$animal_id = $_GET['id'] ?? 0;
if (!is_numeric($animal_id) || $animal_id <= 0) {
    // Redireciona para o index se o ID for inválido
    header("Location: index.php");
    exit();
}

// 3. BUSCAR DADOS DO ANIMAL
try {
    // Query 1: Pega o animal principal
    $sql = "SELECT * FROM Animal WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$animal_id]);
    $animal = $stmt->fetch();

    // Se o animal não existe, redireciona
    if (!$animal) {
        header("Location: index.php?erro=animal_nao_encontrado");
        exit();
    }

    // Query 2: Pega as vacinas desse animal
    $sql_vacinas = "SELECT * FROM Vacinas WHERE id_animal = ?";
    $stmt_vacinas = $pdo->prepare($sql_vacinas);
    $stmt_vacinas->execute([$animal_id]);
    $vacinas = $stmt_vacinas->fetchAll();
} catch (PDOException $e) {
    die("Erro ao buscar dados do animal: " . $e->getMessage());
}

// 4. LÓGICA DO BOTÃO "QUERO ADOTAR"
$link_adotar = '#'; // Link padrão
$texto_adotar = 'Quero Adotar';
$disabled = '';
$classe_botao = 'btn-adotar'; // Classe padrão

// Se o animal não estiver disponível
if ($animal['status'] != 'Disponível') {
    $texto_adotar = ($animal['status'] == 'Adotado' ? 'Adotado' : 'Em processo');
    $disabled = 'disabled';
    $classe_botao = 'btn-adotar-disabled';
}
// Se estiver disponível, mas usuário (solicitante) não está logado
elseif (!isset($_SESSION['solicitante_id'])) {
    // Aponta para o login, e guarda o "redirect" para voltar aqui
    $link_adotar = 'login.php?necessario=1&redirect=animal_detalhes.php?id=' . $animal['id'];
}
// Se estiver disponível E logado como solicitante
elseif (isset($_SESSION['solicitante_id'])) {
    // Aponta para o script que processa a solicitação
    $link_adotar = 'backend/processa_solicitacao.php?id_animal=' . $animal['id'];
    // (Falta fazer esse script processa_solicitacao.php)
}
// Se for um admin logado, ele também não pode adotar
elseif (isset($_SESSION['admin_id'])) {
    $texto_adotar = 'Logado como Admin';
    $disabled = 'disabled';
    $classe_botao = 'btn-adotar-disabled';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes de <?php echo htmlspecialchars($animal['nome']); ?> - Ajudapet</title>
    <link rel="stylesheet" href="assets/css/estilo.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <script src="assets/js/utils.js" defer></script>
    <script src="assets/js/main.js" defer></script>
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
                elseif (isset($_SESSION['solicitante_id'])):
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

    <main class="container">

        <a href="index.php#galeria" class="voltar-link"><i class="fas fa-arrow-left"></i> Voltar Aos Animais</a>

        <div class="animal-detalhe-container">

            <div class="detalhe-imagem">
                <?php if (!empty($animal['imagem_url'])): ?>
                    <img src="uploads/<?php echo htmlspecialchars($animal['imagem_url']); ?>" alt="Foto de <?php echo htmlspecialchars($animal['nome']); ?>">
                <?php else: ?>
                    <img src="assets/images/placeholder-dog.jpg" alt="Animal sem foto"> <?php endif; ?>
            </div>

            <div class="detalhe-info">
                <h1><?php echo htmlspecialchars($animal['nome']); ?></h1>
                <p class="localizacao"><i class="fas fa-map-marker-alt"></i> São Paulo, SP</p>

                <div class="info-grid">
                    <div>
                        <i class="fas fa-paw icon-porte"></i>
                        <span>Porte</span>
                        <strong><?php echo htmlspecialchars($animal['porte']); ?></strong>
                    </div>
                    <div>
                        <i class="fas <?php echo ($animal['sexo'] == 'Macho' ? 'fa-mars' : 'fa-venus'); ?> icon-sexo"></i>
                        <span>Sexo</span>
                        <strong><?php echo htmlspecialchars($animal['sexo']); ?></strong>
                    </div>

                    <div class="info-idade-animal" data-nascimento="<?php echo htmlspecialchars($animal['data_nascimento']); ?>">
                        <i class="fas fa-calendar-alt icon-idade"></i>
                        <div> <span>Idade</span>
                            <strong class="idade-calculada">Calculando...</strong>
                        </div>
                    </div>

                    <div>
                        <i class="fas fa-heartbeat icon-status"></i>
                        <span>Status</span>
                        <strong class="status-<?php echo strtolower(str_replace(' ', '-', $animal['status'])); ?>">
                            <?php echo htmlspecialchars($animal['status']); ?>
                        </strong>
                    </div>
                </div>

                <h2>Sobre <?php echo htmlspecialchars($animal['nome']); ?></h2>
                <p><?php echo nl2br(htmlspecialchars($animal['descricao_historia'] ?? 'Nenhuma história cadastrada.')); ?></p>
                <p><?php echo nl2br(htmlspecialchars($animal['personalidade'] ?? 'Nenhuma personalidade cadastrada.')); ?></p>

                <div class="cuidados-box">
                    <strong><i class="fas fa-check-circle"></i> Cuidados Veterinários</strong>
                    <ul>
                        <li><?php echo $animal['castrado'] ? 'Castrado(a)' : 'Castração pendente'; ?></li>
                        <li><?php echo count($vacinas) > 0 ? 'Vacinado(a) em dia' : 'Vacinas pendentes'; ?></li>
                        <li><?php echo !empty($animal['microchip']) ? 'Microchipado(a)' : 'Sem microchip'; ?></li>
                    </ul>
                </div>

                <a href="<?php echo $link_adotar; ?>" class="<?php echo $classe_botao; ?>" <?php echo $disabled; ?>>
                    <?php echo $texto_adotar; ?>
                </a>

            </div>
        </div>
    </main>

    <footer>
    </footer>

</body>

</html>