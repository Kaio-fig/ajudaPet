<?php
session_start();
require_once '../../config/conexao.php';

// 1. SEGURANÇA
if ($_SERVER["REQUEST_METHOD"] != "POST" || !isset($_SESSION['admin_id'])) {
    header("Location: ../../login.php?erro=acesso_negado");
    exit();
}

// 2. COLETA DADOS
$id_vacina = $_POST['id_vacina'];
$id_animal = $_POST['id_animal']; // Importante para o redirecionamento

$tipo_vacina = $_POST['tipo_vacina'];
$data_aplicacao = $_POST['data_aplicacao'];
$proxima_dose = !empty($_POST['proxima_dose']) ? $_POST['proxima_dose'] : null;
$veterinario = !empty($_POST['veterinario']) ? $_POST['veterinario'] : null;
$observacoes = !empty($_POST['observacoes']) ? $_POST['observacoes'] : null;

try {
    // 3. UPDATE
    $sql = "UPDATE vacinas SET 
                tipo_vacina = ?, 
                data_aplicacao = ?, 
                proxima_dose = ?, 
                veterinario = ?, 
                observacoes = ?
            WHERE id = ?";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$tipo_vacina, $data_aplicacao, $proxima_dose, $veterinario, $observacoes, $id_vacina]);

    // 4. SUCESSO: Volta para a edição do ANIMAL
    header("Location: ../../admin/animais_admin/editar_animais.php?id=" . $id_animal . "&sucesso_vacina_edit=1");
    exit();
} catch (PDOException $e) {
    echo "Erro ao atualizar vacina: " . $e->getMessage();
    exit();
}
