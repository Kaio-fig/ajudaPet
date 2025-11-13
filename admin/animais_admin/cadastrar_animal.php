<?php
// 1. INICIA A SESSÃO
session_start();

// 2. O Bouncer (Segurança)
if (!isset($_SESSION['admin_id']) || $_SESSION['nivel_acesso'] != 0) {
    header("Location: ../../login.php?erro=acesso_negado");
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

    <link rel="stylesheet" href="../../assets/css/global.css">
    <link rel="stylesheet" href="../../assets/css/admin_global.css">
    <link rel="stylesheet" href="../../assets/css/admin_animais.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>

    <header class="navbar">
        <div class="container">
            <a href="../index.php" class="logo" style="color: var(--cor-principal);">Ajudapet (Admin)</a>
            <a href="../../backend/logout.php" class="btn-login" style="background-color: var(--cor-principal);">Sair</a>
        </div>
    </header>

    <main class="container admin-dashboard">

        <div class="admin-header">
            <h1>Painel Administrativo</h1>
        </div>
        <nav class="admin-tabs">
            <a href="../index.php" class="tab-link">
                <i class="fas fa-chart-bar"></i> DashBoard
            </a>
            <a href="../avaliar_solicitacoes.php" class="tab-link">
                <i class="fas fa-tasks"></i> Solicitações
            </a>
            <a href="consultar_animal.php" class="tab-link active">
                <i class="fas fa-paw"></i> Gerenciar Animais
            </a>
            <a href="../relatorio.php" class="tab-link">
                <i class="fas fa-file-alt"></i> Solicitantes
            </a>
            <a href="../ver_doacoes.php" class="tab-link">
                <i class="fas fa-box-open"></i> Doações Físicas
            </a>
        </nav>

        <div id="gerenciar-animais-cadastro" class="tab-content active" style="display:block;">

            <h3>Cadastro de Novo Animal</h3>
            <p>Preencha os dados abaixo para adicionar um novo pet para adoção.</p>

            <?php
            if (isset($_GET['sucesso'])) {
                echo "<div class='aviso-sucesso'>Animal cadastrado com sucesso!</div>";
            }
            if (isset($_GET['erro'])) {
                $msg = $_GET['erro'];
                if ($msg == 'upload') {
                    echo "<div class='aviso-erro' style='background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;'>Ocorreu um erro ao salvar a imagem.</div>";
                } else {
                    echo "<div class='aviso-erro' style='background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;'>Ocorreu um erro ao cadastrar. Tente novamente.</div>";
                }
            }
            ?>

            <div class="form-card" style="margin-top: 1rem;">
                <form action="../../backend/animais_backend/processa_cadastro_animais.php" method="POST" enctype="multipart/form-data">

                    <input type="hidden" name="id_admin_cadastro" value="<?php echo $id_admin; ?>">

                    <div class="form-grid-2-cols">
                        <div class="form-group">
                            <label for="nome">Nome do Animal:</label>
                            <input type="text" id="nome" name="nome" required>
                        </div>
                        <div class="form-group">
                            <label for="raca">Raça (ex: SRD, Poodle):</label>
                            <input type="text" id="raca" name="raca" value="SRD" required>
                        </div>
                    </div>

                    <div class="form-grid-2-cols">
                        <div class="form-group">
                            <label for="sexo">Sexo:</label>
                            <select id="sexo" name="sexo" required>
                                <option value="Macho">Macho</option>
                                <option value="Fêmea">Fêmea</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="porte">Porte:</label>
                            <select id="porte" name="porte" required>
                                <option value="Pequeno">Pequeno</option>
                                <option value="Médio" selected>Médio</option>
                                <option value="Grande">Grande</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-grid-2-cols">
                        <div class="form-group">
                            <label for="data_nascimento">Data de Nascimento (Aproximada):</label>
                            <input type="date" id="data_nascimento" name="data_nascimento">
                        </div>
                        <div class="form-group">
                            <label for="peso">Peso (kg) (ex: 12.5):</label>
                            <input type="number" step="0.1" id="peso" name="peso">
                        </div>
                    </div>

                    <div class="form-grid-2-cols">
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
            </div>
        </div>
    </main>
</body>

</html>