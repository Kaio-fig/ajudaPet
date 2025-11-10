<?php
// Inicia a sessão para poder mostrar mensagens de erro/sucesso
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Ajudapet</title>
    <link rel="stylesheet" href="assets/css/estilo.css">
</head>
<body>
    <header class="navbar">
        </header>

    <main class="container">
        <h2>Login</h2>
        <p>Acesse sua conta para continuar.</p>

        <?php
        // Mostra mensagem de sucesso (se veio do cadastro)
        if (isset($_GET['sucesso'])) {
            echo "<p style='color:green;'>Cadastro realizado com sucesso! Faça o login.</p>";
        }
        // Mostra mensagem de erro (se o login falhar)
        if (isset($_GET['erro'])) {
            echo "<p style='color:red;'>E-mail ou senha inválidos. Tente novamente.</p>";
        }
        // Mostra mensagem se foi forçado a logar
        if (isset($_GET['necessario'])) {
            echo "<p style='color:blue;'>Você precisa estar logado para fazer isso.</p>";
        }
        ?>

        <form action="backend/processa_login.php" method="POST">
            <div class="form-group">
                <label for="email">E-mail:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="senha">Senha:</label>
                <input type="password" id="senha" name="senha" required>
            </div>
            <button type="submit" class="btn-detalhes">Entrar</button>
        </form>
        
        <p>Não tem conta? <a href="cadastro_solicitante.php">Cadastre-se aqui</a></p>

    </main>
</body>
</html>