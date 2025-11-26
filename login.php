<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Ajudapet</title>
    <link rel="stylesheet" href="assets/css/global.css">
    <link rel="stylesheet" href="assets/css/login.css">
</head>

<body>

    <div class="login-page-container">

        <div class="login-box">
            <h2>Login</h2>
            <p>Acesse sua conta para continuar.</p>

            <?php
            // Mostra mensagem de sucesso (se veio do cadastro)
            if (isset($_GET['sucesso'])) {
                echo "<div class='aviso-sucesso'>Cadastro realizado com sucesso! Faça o login.</div>";
            }
            // Mostra mensagem de erro (se o login falhar)
            if (isset($_GET['erro']) && $_GET['erro'] == 1) {
                echo "<div class='aviso-erro'>E-mail ou senha inválidos. Tente novamente.</div>";
            }
            // Mostra mensagem se foi forçado a logar
            if (isset($_GET['necessario'])) {
                echo "<div class='aviso-pendente'>Você precisa estar logado para fazer isso.</div>";
            }
            ?>

            <form action="backend/processa_login.php" method="POST">
                <?php
                // Se uma URL de redirecionamento foi passada,
                // insere ela num campo escondido
                if (isset($_GET['redirect'])):
                ?>
                    <input type="hidden" name="redirect_url" value="<?php echo htmlspecialchars($_GET['redirect']); ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label for="email">E-mail:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="senha">Senha:</label>
                    <input type="password" id="senha" name="senha" required>
                </div>
                <button type="submit" class="btn-submit" style="width:100%;">Entrar</button>
            </form>

            <div class="register-link">
                <p>Não tem conta? <a href="cadastro_solicitante.php">Cadastre-se aqui</a></p>
            </div>
        </div>

    </div>
</body>

</html>