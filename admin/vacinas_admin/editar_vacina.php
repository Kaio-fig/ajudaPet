<?php
session_start();
if (!isset($_SESSION['admin_id']) || $_SESSION['nivel_acesso'] != 0) {
    header("Location: ../../login.php?erro=acesso_negado");
    exit();
}

require_once '../../config/conexao.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID inválido.");
}

$id_vacina = $_GET['id'];

// Busca os dados da vacina para preencher o form
try {
    $sql = "SELECT * FROM vacinas WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_vacina]);
    $vacina = $stmt->fetch();

    if (!$vacina) {
        die("Vacina não encontrada.");
    }
} catch (PDOException $e) {
    die("Erro: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Vacina - Ajudapet</title>
    <link rel="stylesheet" href="../../assets/css/estilo.css">
</head>

<body>
    <header class="navbar">
        <div class="container">
            <a href="../index.php" class="logo">Ajudapet (Admin)</a>
        </div>
    </header>

    <main class="container" style="padding-top: 2rem; max-width: 800px;">
        <h2>Editar Vacina</h2>
        <p>Alterando registro de vacinação.</p>

        <div style="background-color: #fff; padding: 20px; border-radius: 8px; border: 1px solid #ddd; margin-top: 1rem;">

            <form action="../../backend/vacinas_backend/processa_edicao_vacinas.php" method="POST" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">

                <input type="hidden" name="id_vacina" value="<?php echo $vacina['id']; ?>">
                <input type="hidden" name="id_animal" value="<?php echo $vacina['id_animal']; ?>">

                <div class="form-group">
                    <label for="tipo_vacina">Vacina:</label>
                    <input type="text" id="tipo_vacina" name="tipo_vacina" required value="<?php echo htmlspecialchars($vacina['tipo_vacina']); ?>">
                </div>

                <div class="form-group">
                    <label for="data_aplicacao">Data da Aplicação:</label>
                    <input type="date" id="data_aplicacao" name="data_aplicacao" required value="<?php echo htmlspecialchars($vacina['data_aplicacao']); ?>">
                </div>

                <div class="form-group">
                    <label for="proxima_dose">Próxima Dose:</label>
                    <input type="date" id="proxima_dose" name="proxima_dose" value="<?php echo htmlspecialchars($vacina['proxima_dose']); ?>">
                </div>

                <div class="form-group">
                    <label for="veterinario">Veterinário:</label>
                    <input type="text" id="veterinario" name="veterinario" value="<?php echo htmlspecialchars($vacina['veterinario']); ?>">
                </div>

                <div class="form-group" style="grid-column: 1 / -1;">
                    <label for="obs_vacina">Observações:</label>
                    <input type="text" id="obs_vacina" name="observacoes" value="<?php echo htmlspecialchars($vacina['observacoes']); ?>">
                </div>

                <div style="grid-column: 1 / -1; display: flex; gap: 10px;">
                    <a href="editar_animais.php?id=<?php echo $vacina['id_animal']; ?>" class="btn-editar" style="background-color: #eee; color: #555; text-decoration: none; padding: 10px 20px; border-radius: 5px;">
                        Cancelar
                    </a>

                    <button type="submit" class="btn-submit" style="width: auto;">
                        Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>

</html>