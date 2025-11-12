<?php
// 1. INICIAR SESSÃO E CONEXÃO
session_start();
require_once 'config/conexao.php';

// 2. SEGURANÇA: PEGAR O ID DO ANIMAL
$animal_id = $_GET['id'] ?? 0;
if (!is_numeric($animal_id) || $animal_id <= 0) {
    header("Location: index.php");
    exit();
}

// 3. BUSCAR DADOS DO ANIMAL E VACINAS
try {
    $sql = "SELECT * FROM Animal WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$animal_id]);
    $animal = $stmt->fetch();
    if (!$animal) {
        header("Location: index.php?erro=animal_nao_encontrado");
        exit();
    }
    $sql_vacinas = "SELECT * FROM Vacinas WHERE id_animal = ?";
    $stmt_vacinas = $pdo->prepare($sql_vacinas);
    $stmt_vacinas->execute([$animal_id]);
    $vacinas = $stmt_vacinas->fetchAll();
} catch (PDOException $e) {
    die("Erro ao buscar dados do animal: " . $e->getMessage());
}

// 4. [LÓGICA CORRIGIDA]
// Verifica o status da solicitação DO USUÁRIO LOGADO para este animal
$status_solicitacao_usuario = false; // false = sem solicitação
if (isset($_SESSION['solicitante_id'])) {
    $id_solicitante_logado = $_SESSION['solicitante_id'];

    // Pega o status da solicitação ativa (Pendente ou Aprovada)
    $sql_check = "SELECT status FROM SolicitacaoAdoção 
                  WHERE id_animal = ? AND id_solicitante = ? 
                  AND (status = 'Pendente' OR status = 'Aprovada')
                  ORDER BY data_solicitacao DESC LIMIT 1";
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->execute([$animal_id, $id_solicitante_logado]);

    $resultado = $stmt_check->fetch();
    if ($resultado) {
        $status_solicitacao_usuario = $resultado['status']; // 'Pendente' ou 'Aprovada'
    }
}

// 5. [LÓGICA DO BOTÃO CORRIGIDA]
$link_adotar = '#';
$texto_adotar = 'Quero Adotar';
$disabled = '';
$classe_botao = 'btn-adotar';
$mostrar_modal_btn = false;
$mostrar_aviso = false; // Flag para o span amarelo/verde
$aviso_texto = '';
$aviso_classe = 'aviso-pendente'; // Amarelo por padrão

// Caso 1: Usuário logado tem uma solicitação PENDENTE
if ($status_solicitacao_usuario == 'Pendente') {
    $texto_adotar = 'Solicitação Pendente';
    $disabled = 'disabled';
    $classe_botao = 'btn-adotar-disabled';
    $mostrar_aviso = true;
    $aviso_texto = 'Você já tem uma solicitação pendente para este animal.';
}
// Caso 2: Usuário logado teve a solicitação APROVADA (O bug da sua imagem)
elseif ($status_solicitacao_usuario == 'Aprovada') {
    $texto_adotar = 'Adoção Aprovada!';
    $disabled = 'disabled';
    $classe_botao = 'btn-adotar-aprovada'; // Classe CSS Verde
    $mostrar_aviso = true;
    $aviso_texto = 'Parabéns! Sua solicitação foi aprovada. Verifique "Meus Pedidos" para detalhes.';
    $aviso_classe = 'aviso-sucesso'; // Classe CSS Verde
}
// Caso 3: Animal não está disponível (Adotado por OUTRO ou Em Processo)
// (Nota: 'Em processo' não é mais usado, mas deixamos por segurança)
elseif ($animal['status'] == 'Adotado' || $animal['status'] == 'Em processo') {
    $texto_adotar = ($animal['status'] == 'Adotado' ? 'Adotado' : 'Em processo');
    $disabled = 'disabled';
    $classe_botao = 'btn-adotar-disabled';
}
// Caso 4: Usuário (solicitante) não está logado
$status_solicitacao_usuario = false;
if (isset($_SESSION['solicitante_id'])) {
    $id_solicitante_logado = $_SESSION['solicitante_id'];

    // A QUERY CORRIGIDA: Só nos importamos com solicitações PENDENTES.
    // Se foi Aprovada (e o animal voltou), a aprovação é irrelevante.
    $sql_check = "SELECT status FROM SolicitacaoAdoção 
                  WHERE id_animal = ? AND id_solicitante = ? 
                  AND status = 'Pendente'
                  ORDER BY data_solicitacao DESC LIMIT 1";
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->execute([$animal_id, $id_solicitante_logado]);

    $resultado = $stmt_check->fetch();
    if ($resultado) {
        $status_solicitacao_usuario = $resultado['status']; // 'Pendente'
    }
}

// 5. [LÓGICA DO BOTÃO REFINADA]
$link_adotar = '#'; 
$texto_adotar = 'Quero Adotar';
$disabled = '';
$classe_botao = 'btn-adotar';
$mostrar_modal_btn = false; 
$mostrar_aviso = false;
$aviso_texto = '';
$aviso_classe = 'aviso-pendente';

// --- A VERIFICAÇÃO PRINCIPAL É O STATUS DO ANIMAL ---

// CASO A: O animal está DISPONÍVEL (Pode ser adotado)
if ($animal['status'] == 'Disponível') {
    
    // A.1: Usuário logado tem uma solicitação PENDENTE
    if ($status_solicitacao_usuario == 'Pendente') {
        $texto_adotar = 'Solicitação Pendente';
        $disabled = 'disabled';
        $classe_botao = 'btn-adotar-disabled';
        $mostrar_aviso = true;
        $aviso_texto = 'Você já tem uma solicitação pendente para este animal.';
    }
    // A.2: Usuário é um solicitante (e não tem solic. pendente)
    elseif (isset($_SESSION['solicitante_id'])) {
        $mostrar_modal_btn = true; // SINAL VERDE!
    }
    // A.3: Usuário é admin
    elseif (isset($_SESSION['admin_id'])) {
        $texto_adotar = 'Logado como Admin';
        $disabled = 'disabled';
        $classe_botao = 'btn-adotar-disabled';
    }
    // A.4: Usuário é visitante
    else {
        $link_adotar = 'login.php?necessario=1&redirect=animal_detalhes.php?id=' . $animal['id'];
    }

// CASO B: O animal está ADOTADO (Não pode ser adotado)
} elseif ($animal['status'] == 'Adotado') {
    
    $texto_adotar = 'Adotado';
    $disabled = 'disabled';
    $classe_botao = 'btn-adotar-disabled';

    // B.1: Verifica se FOI ESTE usuário que adotou
    // (Verificamos o 'id_adotante' na tabela 'Animal')
    if (isset($_SESSION['solicitante_id']) && $animal['id_adotante'] == $_SESSION['solicitante_id']) {
        $texto_adotar = 'Adoção Aprovada!';
        $classe_botao = 'btn-adotar-aprovada';
        $mostrar_aviso = true;
        $aviso_texto = 'Parabéns! Você adotou este animal.';
        $aviso_classe = 'aviso-sucesso';
    }

// CASO C: O animal está EM PROCESSO (Não deve acontecer, mas por segurança)
} elseif ($animal['status'] == 'Em processo') {
    $texto_adotar = 'Em processo';
    $disabled = 'disabled';
    $classe_botao = 'btn-adotar-disabled';
}
// Caso 6: Logado como Admin
elseif (isset($_SESSION['admin_id'])) {
    $texto_adotar = 'Logado como Admin';
    $disabled = 'disabled';
    $classe_botao = 'btn-adotar-disabled';
}
// Caso 7: (Sinal Verde) Animal disponível, usuário logado, sem solicitação
elseif (isset($_SESSION['solicitante_id']) && $animal['status'] == 'Disponível') {
    $mostrar_modal_btn = true;
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


    <main class="container">

        <a href="index.php#galeria" class="voltar-link"><i class="fas fa-arrow-left"></i> Voltar Aos Animais</a>

        <?php if (isset($_GET['sucesso']) && $_GET['sucesso'] == 'solicitacao_enviada'): ?>
            <div class="aviso-sucesso">
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
                        <div>
                            <span>Idade</span>
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
                <p><?php echo nl2br(htmlspecialchars($animal['descricao_historia'] ?? '...')); ?></p>
                <br>
                <p><b>Personalidade: </b><?php echo nl2br(htmlspecialchars($animal['personalidade'] ?? '...')); ?></p>
                <div class="cuidados-box">
                    <strong><i class="fas fa-check-circle"></i> Cuidados Veterinários</strong>
                    <ul>
                        <li><?php echo $animal['castrado'] ? 'Castrado(a)' : 'Castração pendente'; ?></li>
                        <li><?php echo count($vacinas) > 0 ? 'Vacinado(a) em dia' : 'Vacinas pendentes'; ?></li>
                        <li><?php echo !empty($animal['microchip']) ? 'Microchipado(a)' : 'Sem microchip'; ?></li>
                    </ul>
                </div>

                <?php if ($mostrar_aviso): ?>
                    <span class="<?php echo $aviso_classe; ?>">
                        <?php echo $aviso_texto; ?>
                    </span>
                <?php endif; ?>

                <?php
                if ($mostrar_modal_btn):
                ?>
                    <button id="btn-abrir-modal-adocao" class="<?php echo $classe_botao; ?>">
                        <?php echo $texto_adotar; ?>
                    </button>
                <?php else:
                ?>
                    <a href="<?php echo $link_adotar; ?>" class="<?php echo $classe_botao; ?>" <?php echo $disabled; ?>>
                        <?php echo $texto_adotar; ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="modal-overlay" id="modal-adocao">
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
                        <textarea id="mensagem" name="mensagem" rows="3" placeholder="Ex: Tenho uma casa com quintal..."></textarea>
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

    <script src="assets/js/funcoes.js"></script>
    <script src="assets/js/main.js"></script>
</body>

</html>