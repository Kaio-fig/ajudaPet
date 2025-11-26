<?php
// backend/processa_login.php
session_start();
require_once '../config/conexao.php';

// [NOVO] Define uma URL de fallback padrão
$redirect_fallback = '../index.php';
if (isset($_POST['redirect_url']) && !empty($_POST['redirect_url'])) {
    // Pega a URL de redirecionamento (o PHP lida com o urldecode)
    $redirect_fallback = $_POST['redirect_url'];
    // Medida de segurança: impede que ele redirecione para outro site ou login.php
    if (strpos($redirect_fallback, 'login.php') !== false || strpos($redirect_fallback, 'http') === 0) {
        $redirect_fallback = '../index.php'; // Reseta se for suspeito
    }
}

// 3. Verifica se os dados foram enviados via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $email_digitado = $_POST['email'];
    $senha_digitada = $_POST['senha'];

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

                // [MUDANÇA] Usa o fallback (que pode ser admin/index.php)
                // Se o admin estava em uma página pública, ele vai para o admin mesmo
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

                // [MUDANÇA] Redireciona para onde o usuário estava!
                header("Location: ../" . $redirect_fallback);
                exit();
            }
        }

        // FALHA GERAL:
        header("Location: ../login.php?erro=1");
        exit();

    } catch (PDOException $e) {
        die("Erro no login: " . $e->getMessage());
    }
} else {
    header("Location: ../index.php");
    exit();
}
?>