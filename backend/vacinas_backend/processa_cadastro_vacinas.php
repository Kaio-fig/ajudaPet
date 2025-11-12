<?php
session_start();
require_once '../../config/conexao.php';

// 1. SEGURANÇA
if ($_SERVER["REQUEST_METHOD"] != "POST" || !isset($_SESSION['admin_id'])) {
    header("Location: ../../login.php?erro=acesso_negado");
    exit();
}

// 2. COLETA OS DADOS
$id_animal = $_POST['id_animal'];
$tipo_vacina = $_POST['tipo_vacina'];
$data_aplicacao = $_POST['data_aplicacao'];

// Campos opcionais: se estiver vazio, salva NULL
$proxima_dose = !empty($_POST['proxima_dose']) ? $_POST['proxima_dose'] : null;
$veterinario = !empty($_POST['veterinario']) ? $_POST['veterinario'] : null;
$observacoes = !empty($_POST['observacoes']) ? $_POST['observacoes'] : null;

try {
    // 3. INSERE NO BANCO
    $sql = "INSERT INTO vacinas (id_animal, tipo_vacina, data_aplicacao, proxima_dose, veterinario, observacoes) 
            VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_animal, $tipo_vacina, $data_aplicacao, $proxima_dose, $veterinario, $observacoes]);

    // 4. SUCESSO: Volta para a página de edição do animal
    header("Location: ../../admin/animais_admin/editar_animais.php?id=" . $id_animal . "&sucesso_vacina=1");
    exit();

} catch (PDOException $e) {
    echo "Erro ao cadastrar vacina: " . $e->getMessage();
    exit();
}
?>