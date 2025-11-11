<?php
// 1. INICIA A SESSÃO
session_start();

// 2. O Bouncer (Segurança)
// IDÊNTICO ao seu index_admin.php
if ( !isset($_SESSION['admin_id']) || $_SESSION['nivel_acesso'] != 0 ) {
    header("Location: ../login.php?erro=acesso_negado");
    exit();
}

// Pega o ID do admin logado para salvar no cadastro
$id_admin = $_SESSION['admin_id'];

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Animal - Ajudapet</title>
    <link rel="stylesheet" href="../../assets/css/estilo.css"> 
</head>
<body>

    <header class="navbar">
        <div class="container">
            <a href="index.php" class="logo">Ajudapet (Admin)</a>
            <nav>
                <ul>
                    <li><a href="../index.php">Início</a></li>
                    <li><a href="cadastrar_animal.php">Cadastrar Animal</a></li>
                    <li><a href="consultar_animal.php">Consultar Animal</a></li>
                    <li><a href="avaliar_solicitacoes.php">Ver Solicitações</a></li>
                </ul>
            </nav>
            <a href="../../backend/logout.php" class="btn-login">Sair</a>
        </div>
    </header>

    <main class="container" style="padding-top: 2rem;">
        <h2>Cadastro de Novo Animal</h2>
        <p>Preencha os dados abaixo para adicionar um novo pet para adoção.</p>

        <?php
        // Mensagens de sucesso ou erro (se o backend redirecionar)
        if (isset($_GET['sucesso'])) {
            echo "<p style='color:green;'>Animal cadastrado com sucesso!</p>";
        }
        if (isset($_GET['erro'])) {
            $msg = $_GET['erro'];
            if ($msg == 'upload') {
                echo "<p style='color:red;'>Ocorreu um erro ao salvar a imagem.</p>";
            } else {
                echo "<p style='color:red;'>Ocorreu um erro ao cadastrar. Tente novamente.</p>";
            }
        }
        ?>

        <form action="../../backend/animais_backend/processa_cadastro_animais.php" method="POST" enctype="multipart/form-data">
            
            <input type="hidden" name="id_admin_cadastro" value="<?php echo $id_admin; ?>">

            <div class="form-group">
                <label for="nome">Nome do Animal:</label>
                <input type="text" id="nome" name="nome" required>
            </div>
            
            <div class="form-group">
                <label for="sexo">Sexo:</label>
                <select id="sexo" name="sexo" required>
                    <option value="Macho">Macho</option>
                    <option value="Fêmea">Fêmea</option>
                </select>
            </div>

            <div class="form-group">
                <label for="raca">Raça (ex: SRD, Poodle):</label>
                <input type="text" id="raca" name="raca" value="SRD" required>
            </div>

            <div class="form-group">
                <label for="porte">Porte:</label>
                <select id="porte" name="porte" required>
                    <option value="Pequeno">Pequeno</option>
                    <option value="Médio">Médio</option>
                    <option value="Grande">Grande</option>
                </select>
            </div>

            <div class="form-group">
                <label for="data_nascimento">Data de Nascimento (Aproximada):</label>
                <input type="date" id="data_nascimento" name="data_nascimento">
            </div>

            <div class="form-group">
                <label for="peso">Peso (kg) (ex: 12.5):</label>
                <input type="number" step="0.1" id="peso" name="peso">
            </div>

            <div class="form-group">
                <label for="cor_pelagem">Cor da Pelagem:</label>
                <input type="text" id="cor_pelagem" name="cor_pelagem">
            </div>

            <div class="form-group">
                <label for="castrado">É castrado?</label>
                <select id="castrado" name="castrado" required>
                    <option value="1">Sim</option>
                    <option value="0" selected>Não</option>
                </select>
            </div>

            <div class="form-group">
                <label for="microchip">Microchip (se houver):</label>
                <input type="text" id="microchip" name="microchip">
            </div>

            <div class="form-group">
                <label for="personalidade">Personalidade (ex: Dócil, Agitado, Tímido):</label>
                <textarea id="personalidade" name="personalidade" rows="3"></textarea>
            </div>

            <div class="form-group">
                <label for="descricao_historia">História / Descrição:</label>
                <textarea id="descricao_historia" name="descricao_historia" rows="5"></textarea>
            </div>

            <div class="form-group">
                <label for="observacoes">Observações (Saúde, etc):</label>
                <textarea id="observacoes" name="observacoes" rows="3"></textarea>
            </div>

            <div class="form-group">
                <label for="imagem">Foto do Animal (JPG, PNG):</label>
                <input type="file" id="imagem" name="imagem" accept="image/png, image/jpeg" required>
            </div>
            
            <button type="submit" class="btn-submit">Cadastrar Animal</button>
        </form>

    </main>
</body>
</html>