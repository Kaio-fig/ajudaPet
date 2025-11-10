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
        </header>

    <main class="container">
        <h2>Crie sua Conta de Adotante</h2>
        <p>Preencha o formulário para encontrar seu novo amigo.</p>

        <form action="backend/processa_cadastro_solicitante.php" method="POST">
            <div class="form-group">
                <label for="nome">Nome Completo:</label>
                <input type="text" id="nome" name="nome" required>
            </div>
            
            <div class="form-group">
                <label for="cpf">CPF:</label>
                <input type="text" id="cpf" name="cpf" required>
            </div>

            <div class="form-group">
                <label for="email">E-mail:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="senha">Senha:</label>
                <input type="password" id="senha" name="senha" required>
            </div>

            <div class="form-group">
                <label for="telefone">Telefone (WhatsApp):</label>
                <input type="text" id="telefone" name="telefone">
            </div>

            <button type="submit" class="btn-detalhes">Cadastrar</button>
        </form>
    </main>
</body>
</html>