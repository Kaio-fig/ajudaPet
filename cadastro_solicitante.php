<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Ajudapet</title>

    <link rel="stylesheet" href="assets/css/global.css">

    <link rel="stylesheet" href="assets/css/cadastro.css">
</head>

<body>

    <div class="cadastro-page-container">

        <div class="cadastro-box">
            <h2>Crie sua Conta de Adotante</h2>
            <p>Preencha o formulário para encontrar seu novo amigo.</p>

            <?php
            if (isset($_GET['erro']) && $_GET['erro'] == 'duplicado') {
                echo "<div class='aviso-erro'>Ocorreu um erro. O e-mail ou CPF informado já está em uso.</div>";
            }
            ?>

            <form action="backend/processa_cadastro_solicitante.php" method="POST">

                <div class="form-grid">
                    <div class="form-group">
                        <label for="nome">Nome Completo:</label>
                        <input type="text" id="nome" name="nome" required>
                    </div>
                    <div class="form-group">
                        <label for="cpf">CPF (ex: 123.456.789-10):</label>
                        <input type="text" id="cpf" name="cpf" required>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="email">E-mail:</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="senha">Senha (mín. 6 caracteres):</label>
                        <input type="password" id="senha" name="senha" minlength="6" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="telefone">Telefone (WhatsApp):</label>
                    <input type="tel" id="telefone" name="telefone" placeholder="(XX) XXXXX-XXXX">
                </div>

                <hr>
                <p>Por favor, preencha também os dados do seu endereço:</p>

                <div class="form-group">
                    <label for="endereco_completo">Endereço Completo (Rua, N°, Bairro):</label>
                    <input type="text" id="endereco_completo" name="endereco_completo">
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="cidade">Cidade:</label>
                        <input type="text" id="cidade" name="cidade">
                    </div>
                    <div class="form-group">
                        <label for="estado">Estado (UF):</label>
                        <input type="text" id="estado" name="estado" maxlength="2" placeholder="SP">
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="tipo_moradia">Tipo de Moradia (Casa com muro, Apartamento, etc.):</label>
                        <input type="text" id="tipo_moradia" name="tipo_moradia">
                    </div>
                    <div class="form-group">
                        <label for="ja_teve_pets">Já teve pets antes?</label>
                        <select id="ja_teve_pets" name="ja_teve_pets">
                            <option value="1">Sim</option>
                            <option value="0" selected>Não</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn-submit" style="width:100%; margin-top: 1rem;">Finalizar Cadastro</button>
            </form>
        </div>

    </div>
    <script src="assets/js/funcoes.js"></script>
    <script src="assets/js/cadastro.js"></script>
</body>

</html>