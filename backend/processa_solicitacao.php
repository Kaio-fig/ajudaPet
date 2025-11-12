<?php
// backend/processa_solicitacao.php (ATUALIZADO)

session_start();
require_once '../config/conexao.php';

// 1. SEGURANÇA: Só processa se for um POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 2. SEGURANÇA: Verifica se o solicitante está logado
    if (!isset($_SESSION['solicitante_id'])) {
        header("Location: ../login.php?erro=necessario");
        exit();
    }

    // 3. Pega os dados do formulário
    $id_solicitante = $_SESSION['solicitante_id'];
    $id_animal = $_POST['id_animal'];
    $mensagem = $_POST['mensagem'] ?? null;

    $data_visita = !empty($_POST['data_visita_sugerida']) ? $_POST['data_visita_sugerida'] : null;

    // 4. Validação básica
    if (empty($id_animal)) {
        die("Erro: Animal não especificado.");
    }

    // 5. INSERE A SOLICITAÇÃO NO BANCO (com a nova coluna)
    try {
        $sql = "INSERT INTO SolicitacaoAdoção 
                    (id_animal, id_solicitante, observacoes, data_visita_sugerida, status, data_solicitacao) 
                VALUES 
                    (?, ?, ?, ?, 'Pendente', NOW())";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_animal, $id_solicitante, $mensagem, $data_visita]);

        // 6. Muda o status do animal para 'Em processo'
        $sql_update_animal = "UPDATE Animal SET status = 'Em processo' WHERE id = ?";
        $stmt_update = $pdo->prepare($sql_update_animal);
        $stmt_update->execute([$id_animal]);

        // 7. Redireciona de volta com sucesso
        header("Location: ../animal_detalhes.php?id=$id_animal&sucesso=solicitacao_enviada");
        exit();
        
    } catch (PDOException $e) {
        // Em caso de erro
        header("Location: ../animal_detalhes.php?id=$id_animal&erro=" . urlencode($e->getMessage()));
        exit();
    }
} else {
    // Se não for POST, redireciona
    header("Location: ../index.php");
    exit();
}
