<?php
// config/conexao.php

// 1. Configurações do Banco de Dados (Padrões do XAMPP)
$host = '127.0.0.1'; 
$db   = 'ajudapet';  
$user = 'root';        
$pass = '';           
$charset = 'utf8mb4';  

// 2. Configuração do DSN (Data Source Name)
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// 3. Opções do PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,      
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,          
    PDO::ATTR_EMULATE_PREPARES   => false,                    
];

// 4. Criação da Instância do PDO
try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // apagar ao fim do prohjeto
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

?>