<?php
// 1. INICIA A SESSÃO
session_start();

// 2. Inclui a conexão
require_once '../config/conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $email_digitado = $_POST['email'];
    $senha_digitada = $_POST['senha'];

    // LÓGICA DE LOGIN UNIFICADO
    try {
        // TENTATIVA 1: É UM ADMINISTRADOR?
        $sql_admin = "SELECT * FROM Administrador WHERE email = ?";
        $stmt_admin = $pdo->prepare($sql_admin);
        $stmt_admin->execute([$email_digitado]);
        $admin = $stmt_admin->fetch();

        if ($admin && password_verify($senha_digitada, $admin['senha'])) {

            if ($admin['ativo'] == 1 && $admin['nivel_acesso'] == 0) {
                
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_nome'] = $admin['nome'];
                $_SESSION['nivel_acesso'] = $admin['nivel_acesso'];

                header("Location: ../admin/index.php"); 
                exit();
            } else {
                header("Location: ../login.php?erro=1");
                exit();
            }
        }

        // TENTATIVA 2: É UM SOLICITANTE?
        $sql_solicitante = "SELECT * FROM Solicitante WHERE email = ?";
        $stmt_solicitante = $pdo->prepare($sql_solicitante);
        $stmt_solicitante->execute([$email_digitado]);
        $solicitante = $stmt_solicitante->fetch();

        if ($solicitante && password_verify($senha_digitada, $solicitante['senha'])) {
            if ($solicitante['ativo'] == 1) {
                $_SESSION['solicitante_id'] = $solicitante['id'];
                $_SESSION['solicitante_nome'] = $solicitante['nome'];
                header("Location: ../index.php");
                exit();
            }
        }

        // FALHA GERAL: Se chegou até aqui, email ou senha estão errados
        header("Location: ../login.php?erro=1");
        exit();

    } catch (PDOException $e) {
        // Erro de banco de dados
        die("Erro no login: " . $e->getMessage());
    }

} else {
    // Se não for POST, redireciona
    header("Location: ../index.php");
    exit();
}
?>