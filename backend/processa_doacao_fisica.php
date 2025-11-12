<?php
// backend/processa_doacao_fisica.php
session_start();
require_once '../config/conexao.php';

// Só processa se for um POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Coleta e validação simples
    $nome = $_POST['nome_doador'] ?? '';
    $email = $_POST['email'] ?? '';
    $telefone = $_POST['telefone'] ?? null;
    $tipo_item = $_POST['tipo_item'] ?? '';
    $descricao = $_POST['descricao'] ?? '';

    if (empty($nome) || empty($email) || empty($tipo_item) || empty($descricao)) {
        header("Location: ../index.php?erro=doacao_campos_vazios#doacoes");
        exit();
    }

    // 2. Insere no banco de dados
    try {
        $sql = "INSERT INTO DoacoesFisicas (nome_doador, email, telefone, tipo_item, descricao, status) 
                VALUES (?, ?, ?, ?, ?, 'Pendente')";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nome, $email, $telefone, $tipo_item, $descricao]);

        // 3. Redireciona de volta com sucesso
        header("Location: ../index.php?sucesso=doacao_enviada#doacoes");
        exit();

    } catch (PDOException $e) {
        header("Location: ../index.php?erro=" . urlencode($e->getMessage()) . "#doacoes");
        exit();
    }

} else {
    // Se não for POST, redireciona
    header("Location: ../index.php");
    exit();
}
?>