<?php
// 1. Inicia a sessão para poder destruí-la
session_start();

// 2. Limpa todas as variáveis da sessão
session_unset();

// 3. Destrói a sessão
session_destroy();

// 4. Redireciona para a página de login com uma msg de sucesso
header("Location: ../index.php?logout=sucesso");
exit();
?>