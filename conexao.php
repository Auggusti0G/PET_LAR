<?php

    // =================================================================
    // conexao.php
    // Abre a conexão com o banco de dados MySQL usando PDO.
    // Todos os outros arquivos fazem include deste arquivo e usam
    // a variável $pdo para executar os comandos SQL.
    // =================================================================

    // Fuso horário de Brasília (usado em date(), nos PDFs e nas regras de data)
    date_default_timezone_set('America/Sao_Paulo');

    // Estabelecer a conexão com o banco de dados;
    $host = '127.0.0.1';      // endereço do servidor MySQL
    $db   = 'petlar';     // nome do banco criado pelo banco.sql
    $user = 'root';           // usuário do MySQL
    $pass = 'ceub123456';     // senha do MySQL (troque pela sua)

    // DSN = "endereço" do banco, com charset utf8mb4 para aceitar acentos
    $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass);

    // Faz o fetch() devolver só arrays associativos ($linha['nome'])
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

?>
