<?php
// config/conexao.php

$config_db = require_once 'config_banco.php';

// 1. Configurações do Banco de Dados (Padrões do XAMPP)
$host = $config_db['DB_HOST']; 
$db   = $config_db['DB_NAME'];  
$user = $config_db['DB_USER'];        
$pass = $config_db['DB_PASS'];           
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