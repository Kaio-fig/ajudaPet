<?php

require_once '../config/conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Coletar TODOS os dados do formulário
    $nome = $_POST['nome'];
    $cpf = $_POST['cpf'];
    $email = $_POST['email'];
    $senha_digitada = $_POST['senha'];
    $telefone = $_POST['telefone'];
    $endereco_completo = $_POST['endereco_completo'] ?? null; 
    $cidade = $_POST['cidade'] ?? null;
    $estado = $_POST['estado'] ?? null;
    $tipo_moradia = $_POST['tipo_moradia'] ?? null;
    $ja_teve_pets = $_POST['ja_teve_pets'] ?? null;

    // 2. Criar o HASH da senha
    $hash_senha = password_hash($senha_digitada, PASSWORD_DEFAULT);

    try {
        // 3. SQL com TODAS as colunas
        $sql = "INSERT INTO Solicitante (
                    nome, cpf, email, senha, telefone, 
                    endereco_completo, cidade, estado, 
                    tipo_moradia, ja_teve_pets, ativo
                ) VALUES (
                    ?, ?, ?, ?, ?, 
                    ?, ?, ?, 
                    ?, ?, 1
                )";
        
        $stmt = $pdo->prepare($sql);
        
        // 4. Array de execução com TODAS as variáveis
        $stmt->execute([
            $nome, $cpf, $email, $hash_senha, $telefone,
            $endereco_completo, $cidade, $estado,
            $tipo_moradia, $ja_teve_pets
        ]);

        // 5. Redireciona para o login com msg de sucesso
        header("Location: ../login.php?sucesso=1");
        exit();

    } catch (PDOException $e) {
        // Trata erro de duplicidade (código 23000)
        if ($e->getCode() == 23000) {
            header("Location: ../cadastro_solicitante.php?erro=duplicado");
        } else {
            echo "Erro ao cadastrar: " . $e->getMessage();
            // header("Location: ../cadastro_solicitante.php?erro=1");
        }
        exit();
    }
} else {
    header("Location: ../index.php");
    exit();
}
?>