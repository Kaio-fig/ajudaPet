<?php
session_start();
require_once '../../config/conexao.php';

// 1. SEGURANÇA
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../login.php?erro=acesso_negado");
    exit();
}

// 2. VALIDAÇÃO
if (!isset($_GET['id_vacina']) || !isset($_GET['id_animal'])) {
    die("IDs inválidos.");
}

$id_vacina = $_GET['id_vacina'];
$id_animal = $_GET['id_animal']; // Precisamos disso só para voltar para a página certa

try {
    // 3. DELETA
    $sql = "DELETE FROM vacinas WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_vacina]);

    // 4. SUCESSO: Volta para a edição do animal
    header("Location: ../../admin/animais_admin/editar_animais.php?id=" . $id_animal . "&sucesso_exclusao_vacina=1");
    exit();

} catch (PDOException $e) {
    echo "Erro ao excluir vacina: " . $e->getMessage();
    exit();
}
?>