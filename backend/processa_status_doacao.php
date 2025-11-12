<?php
// backend/processa_status_doacao.php
session_start();
require_once '../config/conexao.php';

// 1. SEGURANÇA: Somente Admin Nível 0
if (!isset($_SESSION['admin_id']) || $_SESSION['nivel_acesso'] != 0) {
    header("Location: ../admin/index.php?erro=acesso_negado");
    exit();
}

// 2. Validação dos dados da URL
if (!isset($_GET['id']) || !isset($_GET['status'])) {
    header("Location: ../admin/ver_doacoes.php?erro=dados_invalidos");
    exit();
}

$id_doacao = $_GET['id'];
$novo_status = $_GET['status']; // 'Contatado' ou 'Concluído'

// 3. Validação do Status
if ($novo_status != 'Contatado' && $novo_status != 'Concluído') {
     header("Location: ../admin/ver_doacoes.php?erro=status_invalido");
     exit();
}

try {
    // 4. Executa o UPDATE
    $sql = "UPDATE DoacoesFisicas SET status = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$novo_status, $id_doacao]);

    // 5. Redireciona de volta para a lista
    header("Location: ../admin/ver_doacoes.php?sucesso=status_alterado");
    exit();

} catch (PDOException $e) {
    header("Location: ../admin/ver_doacoes.php?erro=" . urlencode($e->getMessage()));
    exit();
}
?>