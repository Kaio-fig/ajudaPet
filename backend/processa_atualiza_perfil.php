<?php
// backend/processa_atualiza_perfil.php

session_start();
require_once '../config/conexao.php';

// 1. SEGURANÇA: Verifica se é um POST e se o usuário está logado
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['solicitante_id'])) {
    
    $id_solicitante = $_SESSION['solicitante_id'];

    // 2. Coletar TODOS os dados do formulário
    // (Aba 1: Pessoais)
    $nome = $_POST['nome'];
    $telefone = $_POST['telefone'];
    $endereco_completo = $_POST['endereco_completo'];
    $cidade = $_POST['cidade'];
    $estado = $_POST['estado'];
    $cep = $_POST['cep'];

    // (Aba 2: Moradia)
    $tipo_moradia = $_POST['tipo_moradia'];
    $possui_quintal = $_POST['possui_quintal'];
    $ja_teve_pets = $_POST['ja_teve_pets'];
    $descricao_pets_anteriores = $_POST['descricao_pets_anteriores'];

    // (Aba 3: Experiência)
    $experiencia_animais = $_POST['experiencia_animais'];
    $disponibilidade_tempo = $_POST['disponibilidade_tempo'];
    $motivacao_adotar = $_POST['motivacao_adotar'];

    // 3. Montar o SQL de UPDATE
    try {
        $sql = "UPDATE Solicitante SET 
                    nome = ?, 
                    telefone = ?, 
                    endereco_completo = ?, 
                    cidade = ?, 
                    estado = ?, 
                    cep = ?, 
                    tipo_moradia = ?, 
                    possui_quintal = ?, 
                    ja_teve_pets = ?, 
                    descricao_pets_anteriores = ?, 
                    experiencia_animais = ?, 
                    disponibilidade_tempo = ?, 
                    motivacao_adotar = ?
                WHERE id = ?"; // O WHERE é CRUCIAL!

        $stmt = $pdo->prepare($sql);
        
        $stmt->execute([
            $nome, $telefone, $endereco_completo, $cidade, $estado, $cep,
            $tipo_moradia, $possui_quintal, $ja_teve_pets, $descricao_pets_anteriores,
            $experiencia_animais, $disponibilidade_tempo, $motivacao_adotar,
            $id_solicitante // O último '?' é o ID
        ]);

        // 4. ATUALIZAR O NOME NA SESSÃO (para o header mudar)
        $_SESSION['solicitante_nome'] = $nome;

        // 5. Redirecionar de volta para o perfil com msg de sucesso
        header("Location: ../perfil.php?sucesso=1");
        exit();

    } catch (PDOException $e) {
        // Redirecionar com msg de erro
        header("Location: ../perfil.php?erro=" . urlencode($e->getMessage()));
        exit();
    }

} else {
    // Se não for POST ou não estiver logado
    header("Location: ../login.php");
    exit();
}
?>