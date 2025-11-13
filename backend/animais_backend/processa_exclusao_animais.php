<?php
// backend/animais_backend/processa_exclusao_animais.php (CORRIGIDO)
session_start();
require_once '../../config/conexao.php';

// 1. SEGURANÇA: Somente Admin Nível 0 e deve ser POST
if (!isset($_SESSION['admin_id']) || $_SESSION['nivel_acesso'] != 0) {
    header("Location: ../../admin/index.php?erro=acesso_negado");
    exit();
}
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: ../../admin/animais_admin/consultar_animal.php");
    exit();
}

// 2. Validação do ID (vem do <input type="hidden">)
if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    header("Location: ../../admin/animais_admin/consultar_animal.php?erro=id_invalido");
    exit();
}
$id_animal = $_POST['id'];

// [INÍCIO DA TRANSAÇÃO]
// Usamos uma transação para garantir que tudo seja excluído.
// Se a etapa 5 falhar, a etapa 4 é desfeita.
$pdo->beginTransaction();

try {
    // 3. Pegar o nome da imagem para apagar o arquivo
    $stmt_img = $pdo->prepare("SELECT imagem_url FROM Animal WHERE id = ?");
    $stmt_img->execute([$id_animal]);
    $animal = $stmt_img->fetch();
    $imagem_para_apagar = $animal['imagem_url'] ?? null;

    // 4. DELETAR OS "FILHOS" PRIMEIRO
    
    // Deleta Vacinas (Filho 1)
    $stmt_vac = $pdo->prepare("DELETE FROM Vacinas WHERE id_animal = ?");
    $stmt_vac->execute([$id_animal]);
    
    // Deleta Solicitações (Filho 2 - A CAUSA DO SEU ERRO)
    $stmt_sol = $pdo->prepare("DELETE FROM SolicitacaoAdoção WHERE id_animal = ?");
    $stmt_sol->execute([$id_animal]);

    // 5. DELETAR O "PAI" (O próprio animal)
    $stmt_animal = $pdo->prepare("DELETE FROM Animal WHERE id = ?");
    $stmt_animal->execute([$id_animal]);

    // 6. Se tudo deu certo no banco, apaga o arquivo da imagem
    if ($imagem_para_apagar && file_exists("../../uploads/" . $imagem_para_apagar)) {
        unlink("../../uploads/" . $imagem_para_apagar);
    }

    // 7. Confirma a transação (Tudo deu certo)
    $pdo->commit();
    
    // Redireciona com sucesso
    header("Location: ../../admin/animais_admin/consultar_animal.php?sucesso=exclusao");
    exit();

} catch (Exception $e) {
    // 8. Se algo deu errado, desfaz tudo
    $pdo->rollBack();
    header("Location: ../../admin/animais_admin/consultar_animal.php?erro=" . urlencode($e->getMessage()));
    exit();
}
?>