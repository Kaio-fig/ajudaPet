<?php
// backend/processa_status_solicitacao.php (VERSÃO CORRIGIDA E COMPLETA)
session_start();
require_once '../config/conexao.php';

// 1. VERIFICAÇÃO (POST e Admin)
if ($_SERVER["REQUEST_METHOD"] != "POST" || !isset($_SESSION['admin_id'])) {
    header("Location: ../admin/index.php?erro=acesso_negado_backend");
    exit();
}

$id_solicitacao = $_POST['id_solicitacao']; 
$acao = $_POST['acao']; // 'aprovar' ou 'rejeitar'
$admin_id = $_SESSION['admin_id'];

try {
    // 2. Precisamos saber o id_animal e id_solicitante ANTES de qualquer ação
    $sql_get_ids = "SELECT id_animal, id_solicitante FROM SolicitacaoAdoção WHERE id = ?";
    $stmt_get = $pdo->prepare($sql_get_ids);
    $stmt_get->execute([$id_solicitacao]);
    $ids = $stmt_get->fetch();
    
    if (!$ids) {
        throw new Exception("Solicitação não encontrada.");
    }
    
    $id_animal = $ids['id_animal'];
    $id_solicitante = $ids['id_solicitante'];


    if ($acao == 'aprovar') {
        // --- LÓGICA DE APROVAR ---
        $data_visita = $_POST['data_visita_aprovada'];
        $notas_admin = $_POST['notas_admin'] ?? null;

        // Ação 1: Atualiza a solicitação para "Aprovada"
        $sql_sol = "UPDATE SolicitacaoAdoção SET
                        status = 'Aprovada',
                        visto_pelo_solicitante = 0,
                        data_visita_aprovada = ?,
                        observacoes = ?,
                        id_admin_avaliador = ?
                    WHERE id = ?";
        $stmt_sol = $pdo->prepare($sql_sol);
        $stmt_sol->execute([$data_visita, $notas_admin, $admin_id, $id_solicitacao]);

        // Ação 2: ATUALIZA O ANIMAL para "Adotado"
        $sql_animal = "UPDATE Animal SET
                            status = 'Adotado',
                            id_adotante = ?,
                            data_adocao = NOW()
                        WHERE id = ?";
        $stmt_animal = $pdo->prepare($sql_animal);
        $stmt_animal->execute([$id_solicitante, $id_animal]);
        
        // Ação 3: Rejeita outras solicitações pendentes para ESTE animal
        $sql_rejeita_outras = "UPDATE SolicitacaoAdoção SET
                                    status = 'Rejeitada',
                                    visto_pelo_solicitante = 0,
                                    observacoes = 'Adoção aprovada para outro solicitante.'
                                WHERE id_animal = ? AND status = 'Pendente'";
        $stmt_rejeita = $pdo->prepare($sql_rejeita_outras);
        $stmt_rejeita->execute([$id_animal]);


    } elseif ($acao == 'rejeitar') {
        // --- LÓGICA DE REJEITAR (A CORREÇÃO ESTÁ AQUI) ---
        $motivo_rejeicao = $_POST['motivo_rejeicao'] ?? 'Rejeitado pelo administrador.';

        // Ação 1: Atualiza a solicitação para "Rejeitada"
        $sql_sol = "UPDATE SolicitacaoAdoção SET
                        status = 'Rejeitada',
                        visto_pelo_solicitante = 0,
                        observacoes = ?,
                        id_admin_avaliador = ?
                    WHERE id = ?";
        $stmt_sol = $pdo->prepare($sql_sol);
        $stmt_sol->execute([$motivo_rejeicao, $admin_id, $id_solicitacao]);

        // Ação 2: ATUALIZA O ANIMAL de volta para "Disponível"
        $sql_animal = "UPDATE Animal SET status = 'Disponível' WHERE id = ?";
        $stmt_animal = $pdo->prepare($sql_animal);
        $stmt_animal->execute([$id_animal]);
    }
    
    // 3. Redireciona de volta para a lista
    header("Location: ../admin/avaliar_solicitacoes.php?sucesso=1");
    exit();

} catch (Exception $e) {
    header("Location: ../admin/avaliar_solicitacoes.php?erro=" . urlencode($e->getMessage()));
    exit();
}
?>