<?php
// admin/index.php

// 1. INICIA A SESSÃO
session_start();

// 2. O Bouncer
if ( !isset($_SESSION['admin_id']) || $_SESSION['nivel_acesso'] != 0 ) {
    
    // Se não for um admin Nível 0 logado, expulsa ele
    header("Location: ../login.php?erro=acesso_negado");
    exit();
}

// 3. Se o script chegou até aqui, a validação foi um SUCESSO!
$nome_admin = $_SESSION['admin_nome'];

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Ajudapet</title>
    
    <link rel="stylesheet" href="../assets/css/estilo.css">
</head>
<body>

    <header class="navbar">
        <div class="container">
            <a href="index.php" class="logo">Ajudapet (Admin)</a>
            <nav>
                <ul>
                    <li><a href="index.php">Início</a></li>
                    <li><a href="./animais_admin/cadastrar_animal.php">Cadastrar Animal</a></li>
                    <li><a href="./animais_admin/consultar_animal.php">Consultar Animal<a></li>
                    <li><a href="avaliar_solicitacoes.php">Ver Solicitações</a></li>
                </ul>
            </nav>
            <a href="../backend/logout.php" class="btn-login">Sair</a>
        </div>
    </header>

    <main class="container">
        <h1>Painel do Administrador</h1>
        
        <h2>Bem-vindo, <?php echo htmlspecialchars($nome_admin); ?>!</h2>

        <div style="background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; margin-top: 20px;">
            <strong>Validação bem-sucedida!</strong><br>
            Você está vendo esta página porque o sistema verificou sua sessão e confirmou que você é um <strong>Administrador Nível 0</strong>.
        </div>

        <div style="margin-top: 20px;">
            <p>Próximos passos:</p>
            <ul>
                <li><a href="./animais_admin/cadastrar_animal.php">Cadastrar um novo animal</a></li>
                <li><a href="./animais_admin/consultar_animal.php">Consultar animais cadastrados</a></li>
                <li><a href="avaliar_solicitacoes.php">Gerenciar solicitações de adoção</a></li>
            </ul>
        </div>
        
    </main>

</body>
</html>