<?php
// 1. INICIA A SESSÃO E INCLUI A CONEXÃO
session_start();
require_once '../../config/conexao.php'; 
// 2. SEGURANÇA: Verifica se é POST e se o Admin está logado
if ($_SERVER["REQUEST_METHOD"] != "POST" || !isset($_SESSION['admin_id'])) {
    header("Location: ../../login.php?erro=acesso_negado"); 
    exit();
}

// 3. COLETAR DADOS BÁSICOS DO FORMULÁRIO
$animal_id = $_POST['id']; // O ID do animal a ser ATUALIZADO
$nome = $_POST['nome'];
$sexo = $_POST['sexo'];
$raca = $_POST['raca'];
$porte = $_POST['porte'];
$data_nascimento = !empty($_POST['data_nascimento']) ? $_POST['data_nascimento'] : null;
$peso = !empty($_POST['peso']) ? $_POST['peso'] : null;
$cor_pelagem = $_POST['cor_pelagem'] ?? null;
$castrado = $_POST['castrado'];
$status = $_POST['status']; // O novo campo de status
$microchip = $_POST['microchip'] ?? null;
$personalidade = $_POST['personalidade'] ?? null;
$descricao_historia = $_POST['descricao_historia'] ?? null;
$observacoes = $_POST['observacoes'] ?? null;

// 4. LÓGICA DA IMAGEM (A parte mais importante)
$imagem_url_para_db = $_POST['imagem_antiga']; // Assume a imagem antiga por padrão
$upload_dir = '../../uploads/'; 

// Verifica se um NOVO arquivo foi enviado (sem erros)
if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == UPLOAD_ERR_OK && $_FILES['imagem']['size'] > 0) {
    
    // Gera um nome único para o novo arquivo
    $nome_arquivo_novo = uniqid('animal_') . '.' . strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
    $caminho_completo_novo = $upload_dir . $nome_arquivo_novo;

    // Tenta mover o novo arquivo
    if (move_uploaded_file($_FILES['imagem']['tmp_name'], $caminho_completo_novo)) {
        
        // SUCESSO!
        $imagem_url_para_db = $nome_arquivo_novo; // Define o novo nome para o banco

        // APAGA A IMAGEM ANTIGA para não ocupar espaço
        // (Verifica se a imagem antiga não está vazia)
        if (!empty($_POST['imagem_antiga']) && file_exists($upload_dir . $_POST['imagem_antiga'])) {
            unlink($upload_dir . $_POST['imagem_antiga']);
        }
    } else {
        // Falha ao mover o novo upload
        header("Location: ../../admin/animais_admin/editar_animal.php?id=" . $animal_id . "&erro=upload");
        exit();
    }
}
// Se nenhum arquivo novo foi enviado, $imagem_url_para_db simplesmente
// continua sendo o valor da 'imagem_antiga' que pegamos no início.

try {
    // 5. SQL UPDATE
    // a query é bem maior, atualizando todos os campos
    $sql = "UPDATE animal SET 
                nome = ?, sexo = ?, raca = ?, porte = ?, data_nascimento = ?, 
                peso = ?, cor_pelagem = ?, castrado = ?, status = ?, 
                microchip = ?, personalidade = ?, descricao_historia = ?, 
                observacoes = ?, imagem_url = ?
            WHERE id = ?"; // O 'id' é o último '?'
    
    $stmt = $pdo->prepare($sql);
    
    // 6. Array de execução (NA ORDEM EXATA DA SQL)
    $stmt->execute([
        $nome, $sexo, $raca, $porte, $data_nascimento,
        $peso, $cor_pelagem, $castrado, $status,
        $microchip, $personalidade, $descricao_historia,
        $observacoes, $imagem_url_para_db,
        $animal_id // O ID do animal (para o WHERE)
    ]);

    // 7. Redireciona de volta para a lista (ou para a edição) com sucesso
    // É melhor voltar para a lista, para o admin ver o resultado
    header("Location: ../../admin/animais_admin/consultar_animal.php?sucesso=edicao");
    exit();

} catch (PDOException $e) {
    // Em caso de erro, volta para a página de edição
    echo "Erro ao atualizar: " . $e->getMessage();
    // header("Location: ../../admin/animais_admin/editar_animal.php?id=" . $animal_id . "&erro=db");
    exit();
}
?>