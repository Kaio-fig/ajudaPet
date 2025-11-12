<?php
// 1. INICIA A SESSÃO
session_start();

// 2. O Bouncer (Segurança)
if ( !isset($_SESSION['admin_id']) || $_SESSION['nivel_acesso'] != 0 ) {
    header("Location: ../../login.php?erro=acesso_negado");
    exit();
}

// 4. PEGAR O ID DA URL E VERIFICAR
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Erro: ID do animal não fornecido ou inválido.");
}
$animal_id = $_GET['id'];

// 3. Inclui a conexão
require_once '../../config/conexao.php';

// 4. Preparar e Executar a Query SQL (Read)
try {
    // Agora a query é específica
    $sql = "SELECT * FROM animal WHERE id = ?";
    
    // Agora usamos PREPARE e EXECUTE (o "bind")
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$animal_id]); // Passa o ID com segurança
    
    // Usamos fetch() para pegar só UMA linha
    $animal = $stmt->fetch(); 

    // Verifica se o animal foi encontrado
    if (!$animal) {
        die("Animal com o ID $animal_id não encontrado.");
    }
} catch (PDOException $e) {
    echo "Erro ao buscar animal: " . $e->getMessage();
    $animal = null; // Garante que $animal não será usado se der erro
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Animal - Ajudapet</title>
    <link rel="stylesheet" href="../../assets/css/estilo.css"> 
</head>
<body>
    <header class="navbar">
        <div class="container">
            <a href="../index.php" class="logo">Ajudapet (Admin)</a>
            <nav>
                <ul>
                    <li><a href="../index.php">Início</a></li>
                    <li><a href="cadastrar_animal.php">Cadastrar Animal</a></li>
                    <li><a href="consultar_animal.php">Consultar Animais</a></li>
                    <li><a href="../avaliar_solicitacoes.php">Ver Solicitações</a></li>
                </ul>
            </nav>
            <a href="../../backend/logout.php" class="btn-login">Sair</a>
        </div>
    </header>

    <main class="container" style="padding-top: 2rem;">
        
        <h2>Editando: <?php echo htmlspecialchars($animal['nome']); ?></h2>
        <p>Altere os dados necessários e salve as modificações.</p>

        <form action="../../backend/animais_backend/processa_edicao_animais.php" method="POST" enctype="multipart/form-data">
            
            <input type="hidden" name="id" value="<?php echo $animal['id']; ?>">

            <div class="form-group">
                <label for="nome">Nome do Animal:</label>
                <input type="text" id="nome" name="nome" required value="<?php echo htmlspecialchars($animal['nome']); ?>">
            </div>
            
            <div class="form-group">
                <label for="sexo">Sexo:</label>
                <select id="sexo" name="sexo" required>
                    <option value="Macho" <?php if ($animal['sexo'] == 'Macho') echo 'selected'; ?>>Macho</option>
                    <option value="Fêmea" <?php if ($animal['sexo'] == 'Fêmea') echo 'selected'; ?>>Fêmea</option>
                </select>
            </div>

            <div class="form-group">
                <label for="raca">Raça (ex: SRD, Poodle):</label>
                <input type="text" id="raca" name="raca" required value="<?php echo htmlspecialchars($animal['raca']); ?>" >
            </div>

            <div class="form-group">
                <label for="porte">Porte:</label>
                <select id="porte" name="porte" required>
                    <option value="Pequeno" <?php if ($animal['porte'] == 'Pequeno') echo 'selected'; ?>>Pequeno</option>
                    <option value="Médio" <?php if ($animal['porte'] == 'Médio') echo 'selected'; ?>>Médio</option>
                    <option value="Grande" <?php if ($animal['porte'] == 'Grande') echo 'selected'; ?>>Grande</option>
                </select>
            </div>

            <div class="form-group">
                <label for="data_nascimento">Data de Nascimento (Aproximada):</label>
                <input type="date" id="data_nascimento" name="data_nascimento" value="<?php echo htmlspecialchars($animal['data_nascimento']); ?>">
            </div>

            <div class="form-group">
                <label for="peso">Peso (kg) (ex: 12.5):</label>
                <input type="number" step="0.1" id="peso" name="peso" value="<?php echo htmlspecialchars($animal['peso']); ?>">
            </div>

            <div class="form-group">
                <label for="cor_pelagem">Cor da Pelagem:</label>
                <input type="text" id="cor_pelagem" name="cor_pelagem" value="<?php echo htmlspecialchars($animal['cor_pelagem']); ?>">
            </div>

            <div class="form-group">
                <label for="castrado">É castrado?</label>
                <select id="castrado" name="castrado" required>
                    <option value="1" <?php if ($animal['castrado'] == '1') echo 'selected'; ?>>Sim</option>
                    <option value="0" <?php if ($animal['castrado'] == '0') echo 'selected'; ?>>Não</option>
                </select>
            </div>

            <div class="form-group">
                <label for="status">Status:</label>
                <select id="status" name="status" required>
                    <option value="Disponível" <?php if($animal['status'] == 'Disponível') echo 'selected'; ?>>Disponível</option>
                    <option value="Em processo" <?php if($animal['status'] == 'Em processo') echo 'selected'; ?>>Em processo</option>
                    <option value="Adotado" <?php if($animal['status'] == 'Adotado') echo 'selected'; ?>>Adotado</option>
                    <option value="Indisponível" <?php if($animal['status'] == 'Indisponível') echo 'selected'; ?>>Indisponível</option>
                </select>
            </div>

            <div class="form-group">
                <label for="microchip">Microchip (se houver):</label>
                <input type="text" id="microchip" name="microchip" value="<?php echo htmlspecialchars($animal['microchip']); ?>">
            </div>

            <div class="form-group">
                <label for="personalidade">Personalidade (ex: Dócil, Agitado, Tímido):</label>
                <textarea id="personalidade" name="personalidade" rows="3"><?php echo htmlspecialchars($animal['personalidade']); ?></textarea>
            </div>

            <div class="form-group">
                <label for="descricao_historia">História / Descrição:</label>
                <textarea id="descricao_historia" name="descricao_historia" rows="5"><?php echo htmlspecialchars($animal['descricao_historia']); ?></textarea>
            </div>

            <div class="form-group">
                <label for="observacoes">Observações (Saúde, etc):</label>
                <textarea id="observacoes" name="observacoes" rows="3"><?php echo htmlspecialchars($animal['observacoes']); ?></textarea>
            </div>

            <div class="form-group">
                <label>Foto Atual:</label>
                <img src="../../uploads/<?php echo htmlspecialchars($animal['imagem_url']); ?>" alt="Foto Atual" style="width: 100px; height: 100px; object-fit: cover; border-radius: 5px;">
                
                <label for="imagem" style="display:block; margin-top:10px;">Trocar Foto (Opcional):</label>
                <input type="file" id="imagem" name="imagem" accept="image/png, image/jpeg">
                
                <input type="hidden" name="imagem_antiga" value="<?php echo htmlspecialchars($animal['imagem_url']); ?>">
            </div>
            
            <button type="submit" class="btn-submit">Salvar Alterações</button>
        </form>

    </main>
</body>
</html>