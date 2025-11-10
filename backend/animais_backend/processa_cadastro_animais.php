<?php
// 1. INICIA A SESSÃO
session_start();

// 2. Inclui a conexão
require_once '../../config/conexao.php';

// 3. SEGURANÇA: Verifica se o método é POST e se o Admin está logado
if ($_SERVER["REQUEST_METHOD"] != "POST" || !isset($_SESSION['admin_id'])) {
    // Redireciona para o login se não for admin ou não for POST
    header("Location: ../../login.php?erro=acesso_negado");
    exit();
}

// 4. LÓGICA DE UPLOAD DA IMAGEM
$imagem_url_para_db = null; // Inicia como nulo

if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == UPLOAD_ERR_OK) {
    
    $upload_dir = '../../uploads/'; // A pasta que você criou
    $nome_arquivo = $_FILES['imagem']['name'];
    $extensao = strtolower(pathinfo($nome_arquivo, PATHINFO_EXTENSION));
    
    // Gera um nome único para evitar sobreposição
    $nome_unico = uniqid('animal_') . '.' . $extensao;
    
    $caminho_completo = $upload_dir . $nome_unico;

    // Move o arquivo da pasta temporária para a pasta /uploads
    if (move_uploaded_file($_FILES['imagem']['tmp_name'], $caminho_completo)) {
        // Sucesso! Salva apenas o NOME do arquivo no banco
        $imagem_url_para_db = $nome_unico;
    } else {
        // Falha no upload
        header("Location: ../../admin/animais_admin/cadastrar_animal.php?erro=upload");
        exit();
    }
} else {
    // Se a imagem for obrigatória e falhar (ex: nenhum arquivo enviado)
    header("Location: ../../admin/animais_admin/cadastrar_animal.php?erro=imagem_obrigatoria");
    exit();
}


// 5. COLETAR DADOS DO FORMULÁRIO (Combinando com o SQL)
// Usamos '?? null' para campos opcionais não quebrarem o SQL
$id_admin = $_POST['id_admin_cadastro'];
$nome = $_POST['nome'];
$sexo = $_POST['sexo'];
$raca = $_POST['raca'];
$porte = $_POST['porte'];
$data_nascimento = !empty($_POST['data_nascimento']) ? $_POST['data_nascimento'] : null;
$peso = !empty($_POST['peso']) ? $_POST['peso'] : null;
$cor_pelagem = $_POST['cor_pelagem'] ?? null;
$personalidade = $_POST['personalidade'] ?? null;
$castrado = $_POST['castrado'];
$microchip = $_POST['microchip'] ?? null;
$descricao_historia = $_POST['descricao_historia'] ?? null;
$observacoes = $_POST['observacoes'] ?? null;

// O 'status' é 'Disponível' por padrão no banco, não precisamos enviar.
// 'imagem_url' já está na variável $imagem_url_para_db.

try {
    // 6. SQL INSERT (Exatamente como o seu CREATE TABLE)
    $sql = "INSERT INTO animal (
                id_admin_cadastro, nome, sexo, raca, porte, 
                data_nascimento, peso, cor_pelagem, personalidade, 
                imagem_url, castrado, microchip, 
                descricao_historia, observacoes
                /* Status e data_cadastro são automáticos */
            ) VALUES (
                ?, ?, ?, ?, ?, 
                ?, ?, ?, ?, 
                ?, ?, ?, 
                ?, ?
            )";
    
    $stmt = $pdo->prepare($sql);
    
    // 7. Array de execução (na ordem exata do SQL acima)
    $stmt->execute([
        $id_admin, $nome, $sexo, $raca, $porte,
        $data_nascimento, $peso, $cor_pelagem, $personalidade,
        $imagem_url_para_db, $castrado, $microchip,
        $descricao_historia, $observacoes
    ]);

    // 8. Redireciona para o formulário com msg de sucesso
    header("Location: ../../admin/animais_admin/cadastrar_animal.php?sucesso=1");
    exit();

} catch (PDOException $e) {
    // Em caso de erro, volta para o form com msg de erro
    echo "Erro ao cadastrar: " . $e->getMessage();
    // header("Location: ../../admin/animais_admin/cadastrar_animal.php?erro=db");
    exit();
}
?>