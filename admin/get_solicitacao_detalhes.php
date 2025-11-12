<?php
// admin/get_solicitacao_detalhes.php
session_start();
require_once '../config/conexao.php';

// Segurança
if (!isset($_SESSION['admin_id']) || !isset($_GET['id'])) {
    http_response_code(403);
    echo json_encode(['erro' => 'Acesso negado']);
    exit();
}

$id_solicitacao = $_GET['id'];

try {
    // Query que busca tudo
    $sql = "SELECT 
                s.id, s.data_solicitacao, s.data_visita_sugerida, s.observacoes,
                sol.nome AS sol_nome, sol.email AS sol_email, sol.telefone AS sol_telefone,
                sol.endereco_completo, sol.cidade, sol.estado, sol.cep,
                sol.possui_quintal, sol.experiencia_animais, sol.disponibilidade_tempo,
                a.nome AS animal_nome
            FROM SolicitacaoAdoção AS s
            JOIN Solicitante AS sol ON s.id_solicitante = sol.id
            JOIN Animal AS a ON s.id_animal = a.id
            WHERE s.id = ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_solicitacao]);
    $dados = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($dados) {
        // Envia os dados de volta como JSON
        header('Content-Type: application/json');
        echo json_encode($dados);
    } else {
        http_response_code(404);
        echo json_encode(['erro' => 'Solicitação não encontrada']);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['erro' => $e->getMessage()]);
}
?>