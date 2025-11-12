<?php
// backend/processa_bloqueio_solicitante.php
session_start();
require_once '../config/conexao.php';

// 1. SEGURANÇA: Somente Admin Nível 0
if (!isset($_SESSION['admin_id']) || $_SESSION['nivel_acesso'] != 0) {
    header("Location: ../admin/index.php?erro=acesso_negado");
    exit();
}

// 2. Validação dos dados da URL
if (!isset($_GET['id']) || !isset($_GET['acao'])) {
    header("Location: ../admin/relatorio.php?erro=dados_invalidos");
    exit();
}

$id_solicitante = $_GET['id'];
$acao = $_GET['acao']; // 'bloquear' ou 'desbloquear'

// 3. Define o novo status (0 para bloquear, 1 para desbloquear)
$novo_status = ($acao == 'bloquear') ? 0 : 1;

try {
    // 4. Executa o UPDATE
    $sql = "UPDATE Solicitante SET ativo = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$novo_status, $id_solicitante]);

    // 5. Redireciona de volta para o relatório
    header("Location: ../admin/relatorio.php?sucesso=status_alterado");
    exit();

} catch (PDOException $e) {
    header("Location: ../admin/relatorio.php?erro=" . urlencode($e->getMessage()));
    exit();
}
?>