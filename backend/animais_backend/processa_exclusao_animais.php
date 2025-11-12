<?php
// 1. INICIA A SESSÃO E INCLUI A CONEXÃO
session_start();
require_once '../../config/conexao.php';

// 2. SEGURANÇA: Verifica se é POST e se o Admin está logado
// (Mudou de GET para POST)
if ($_SERVER["REQUEST_METHOD"] != "POST" || !isset($_SESSION['admin_id'])) {
    header("Location: ../../login.php?erro=acesso_negado");
    exit();
}

// 3. PEGAR O ID DO FORMULÁRIO
// (Mudou de $_GET para $_POST)
if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    die("Erro: ID do animal inválido.");
}
$animal_id = $_POST['id'];

try {
    // 4. Buscar o nome da imagem ANTES de deletar
    $sql_busca_img = "SELECT imagem_url FROM animal WHERE id = ?";
    $stmt_busca = $pdo->prepare($sql_busca_img);
    $stmt_busca->execute([$animal_id]);
    $animal = $stmt_busca->fetch();
    $imagem_para_apagar = $animal['imagem_url'] ?? null;

    // 5. DELETAR O REGISTRO DO BANCO
    $sql_delete = "DELETE FROM animal WHERE id = ?";
    $stmt_delete = $pdo->prepare($sql_delete);
    $stmt_delete->execute([$animal_id]);

    // 6. APAGAR O ARQUIVO FÍSICO (A FOTO)
    if (!empty($imagem_para_apagar)) {
        $caminho_arquivo = '../../uploads/' . $imagem_para_apagar;
        if (file_exists($caminho_arquivo)) {
            unlink($caminho_arquivo);
        }
    }

    // 7. Redireciona de volta para a lista com sucesso
    header("Location: ../../admin/animais_admin/consultar_animal.php?sucesso=exclusao");
    exit();

} catch (PDOException $e) {
    echo "Erro ao excluir: " . $e->getMessage();
    // header("Location: ../../admin/animais_admin/consultar_animal.php?erro=db");
    exit();
}
?>