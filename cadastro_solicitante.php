<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Ajudapet</title>
    <link rel="stylesheet" href="assets/css/estilo.css">
    
</head>
<body>

    <header class="navbar">
        <div class="container">
            <a href="index.php" class="logo">Pet & Tudo Mais</a>
            <nav>
                <ul>
                    <li><a href="index.php">Início</a></li>
                    <li><a href="login.php">Login</a></li>
                </ul>
            </nav>
            <a href="login.php" class="btn-login">Login/Cadastro</a>
        </div>
    </header>

    <main class="container" style="padding-top: 2rem;">
        <h2>Crie sua Conta de Adotante</h2>
        <p>Preencha o formulário para encontrar seu novo amigo.</p>

        <?php
        // Mostra mensagem de erro (se o backend redirecionar com um)
        if (isset($_GET['erro'])) {
            echo "<p style='color:red;'>Ocorreu um erro. Verifique se o e-mail ou CPF já estão em uso.</p>";
        }
        ?>

        <form action="backend/processa_cadastro_solicitante.php" method="POST">
            
            <div class="form-group">
                <label for="nome">Nome Completo:</label>
                <input type="text" id="nome" name="nome" required>
            </div>
            
            <div class="form-group">
                <label for="cpf">CPF (ex: 123.456.789-10):</label>
                <input type="text" id="cpf" name="cpf" required>
            </div>

            <div class="form-group">
                <label for="email">E-mail:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="senha">Senha:</label>
                <input type="password" id="senha" name="senha" minlength="6" required>
            </div>

            <div class="form-group">
                <label for="telefone">Telefone (WhatsApp):</label>
                <input type="tel" id="telefone" name="telefone" placeholder="(XX) XXXXX-XXXX">
            </div>

            <hr style="margin: 2rem 0;">
            <p>Por favor, preencha também os dados do seu endereço (conforme o diagrama UML):</p>
            
            <div class="form-group">
                <label for="endereco_completo">Endereço Completo (Rua, N°, Bairro):</label>
                <input type="text" id="endereco_completo" name="endereco_completo">
            </div>
            
            <div class="form-group">
                <label for="cidade">Cidade:</label>
                <input type="text" id="cidade" name="cidade">
            </div>

            <div class="form-group">
                <label for="estado">Estado (UF):</label>
                <input type="text" id="estado" name="estado" maxlength="2" placeholder="SP">
            </div>

            <div class="form-group">
                <label for="tipo_moradia">Tipo de Moradia (Casa com muro, Apartamento, etc.):</label>
                <input type="text" id="tipo_moradia" name="tipo_moradia">
            </div>

            <div class="form-group">
                <label for="ja_teve_pets">Já teve pets antes?</label>
                <select id="ja_teve_pets" name="ja_teve_pets">
                    <option value="1">Sim</option>
                    <option value="0">Não</option>
                </select>
            </div>

            <button type="submit" class="btn-submit">Finalizar Cadastro</button>
        </form>

    </main>
    
    <footer style="margin-top: 3rem;">
        <div class="container" style="text-align: center;">
            <p>&copy; 2025 Ajudapet.</p>
        </div>
    </footer>

</body>
</html>