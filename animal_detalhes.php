<?php
// 1. INICIAR SESSÃO E CONEXÃO
session_start();
require_once 'config/conexao.php';

// Inclui os scripts de funções (JS)
// (Vamos carregar os scripts no final do <body>)

// 2. SEGURANÇA: PEGAR O ID DO ANIMAL
$animal_id = $_GET['id'] ?? 0;
if (!is_numeric($animal_id) || $animal_id <= 0) {
    // Redireciona para o index se o ID for inválido
    header("Location: index.php");
    exit();
}

// 3. BUSCAR DADOS DO ANIMAL E VACINAS
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

// 4. [LÓGICA ATUALIZADA]
// Verifica se o USUÁRIO LOGADO tem uma solicitação PENDENTE para este animal
$solicitacao_pendente = false;
if (isset($_SESSION['solicitante_id'])) {
    $id_solicitante_logado = $_SESSION['solicitante_id'];

    $sql_check = "SELECT id FROM SolicitacaoAdoção 
                  WHERE id_animal = ? AND id_solicitante = ? AND (status = 'Pendente' OR status = 'Aprovada')"; // Verifica pendente ou aprovada
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->execute([$animal_id, $id_solicitante_logado]);
    
    if ($stmt_check->fetch()) {
        $solicitacao_pendente = true;
    }
}

// 5. LÓGICA DO BOTÃO "QUERO ADOTAR"
$link_adotar = '#'; 
$texto_adotar = 'Quero Adotar';
$disabled = '';
$classe_botao = 'btn-adotar';
$mostrar_modal_btn = false; // Flag para mostrar o botão do modal

// Caso 1: Usuário logado JÁ TEM uma solicitação pendente
if ($solicitacao_pendente) {
    $texto_adotar = 'Solicitação Pendente';
    $disabled = 'disabled';
    $classe_botao = 'btn-adotar-disabled';
}
// Caso 2: Animal não está disponível (Adotado ou Em Processo por OUTRO)
elseif ($animal['status'] != 'Disponível') {
    $texto_adotar = ($animal['status'] == 'Adotado' ? 'Adotado' : 'Em processo');
    $disabled = 'disabled';
    $classe_botao = 'btn-adotar-disabled';
} 
// Caso 3: Usuário (solicitante) não está logado
elseif (!isset($_SESSION['solicitante_id'])) {
    $link_adotar = 'login.php?necessario=1&redirect=animal_detalhes.php?id=' . $animal['id'];
} 
// Caso 4: Logado como Admin
elseif (isset($_SESSION['admin_id'])) {
     $texto_adotar = 'Logado como Admin';
     $disabled = 'disabled';
     $classe_botao = 'btn-adotar-disabled';
}
// Caso 5: Animal disponível, usuário logado, sem solicitação pendente
elseif (isset($_SESSION['solicitante_id'])) {
    $mostrar_modal_btn = true; // SINAL VERDE! Mostra o botão que abre o modal
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
    
    </head>
<body>

    <?php
    // Recomendo fortemente usar "partials" para o header e footer
    // Ex: require 'partials/header.php';
    // Por enquanto, cole o HTML do seu header aqui.
    ?>
    <header class="navbar">
        </header>

    <main class="container">
        
        <a href="index.php#galeria" class="voltar-link"><i class="fas fa-arrow-left"></i> Voltar Aos Animais</a>
        
        <?php if(isset($_GET['sucesso']) && $_GET['sucesso'] == 'solicitacao_enviada'): ?>
            <div class="aviso-sucesso" style="background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; margin-bottom: 1rem;">
                <strong>Solicitação enviada com sucesso!</strong> A administração entrará em contato em breve.
            </div>
        <?php endif; ?>

        <div class="animal-detalhe-container">
            
            <div class="detalhe-imagem">
                <?php if (!empty($animal['imagem_url'])): ?>
                    <img src="uploads/<?php echo htmlspecialchars($animal['imagem_url']); ?>" alt="Foto de <?php echo htmlspecialchars($animal['nome']); ?>">
                <?php else: ?>
                    <img src="assets/images/placeholder-dog.jpg" alt="Animal sem foto"> 
                <?php endif; ?>
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

                <?php if ($solicitacao_pendente): ?>
                    <span class="aviso-pendente">
                        Você já tem uma solicitação para este animal.
                    </span>
                <?php endif; ?>

                <?php
                // Se for "Sinal Verde" (Caso 5)
                if ($mostrar_modal_btn):
                ?>
                    <button id="btn-abrir-modal-adocao" class="<?php echo $classe_botao; ?>">
                        <?php echo $texto_adotar; ?>
                    </button>
                <?php else: ?>
                    <a href="<?php echo $link_adotar; ?>" class="<?php echo $classe_botao; ?>" <?php echo $disabled; ?>>
                        <?php echo $texto_adotar; ?>
                    </a>
                <?php endif; ?>
                </div> </div> <div class="modal-overlay" id="modal-adocao">
            <div class="modal-box">
                <button class="modal-close" id="btn-fechar-modal-adocao">&times;</button>
                
                <h2>Confirmar Solicitação</h2>
                <p>Você está prestes a solicitar a adoção de <strong><?php echo htmlspecialchars($animal['nome']); ?></strong>.</p>
                <p>Seu perfil será enviado para análise. Você pode adicionar uma mensagem e sugerir uma data para visita.</p>

                <form action="backend/processa_solicitacao.php" method="POST">
                    <input type="hidden" name="id_animal" value="<?php echo $animal['id']; ?>">
                    
                    <div class="form-group">
                        <label for="data_visita">Sugira uma data e hora para visita:</label>
                        <input type="datetime-local" id="data_visita" name="data_visita_sugerida">
                    </div>

                    <div class="form-group">
                        <label for="mensagem">Mensagem (Opcional):</label>
                        <textarea id="mensagem" name="mensagem" rows="3" placeholder="Ex: Tenho uma casa com quintal e adoraria conhecê-lo(a)..."></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn-cancelar" id="btn-cancelar-modal-adocao">Cancelar</button>
                        <button type="submit" class="btn-submit">Enviar Solicitação</button>
                    </div>
                </form>
            </div>
        </div>
        </main>

    <footer>
        </footer>

    <script src="assets/js/funcoes.js"></script> <script src="assets/js/main.js"></script>

</body>
</html>
