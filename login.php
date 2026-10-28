<?php

    // =================================================================
    // login.php - PROCESSA O LOGIN
    // Recebe usuário e senha do index.php, confere no banco e, se
    // estiver certo, cria a SESSÃO do usuário.
    // =================================================================

    session_start();          // liga o uso de sessão nesta página
    include 'conexao.php';

    // Recupera o que foi digitado (se não veio nada, fica vazio)
    $login = isset($_POST['usuario']) ? trim($_POST['usuario']) : '';

    // A senha é convertida em md5 porque no banco ela está guardada em md5
    $senha = md5(isset($_POST['senha']) ? $_POST['senha'] : '');

    // Busca um usuário com esse login E essa senha
    $sql =  " SELECT * FROM usuario
              WHERE login =:login AND
                    senha =:senha ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":login", $login);   // bindParam evita SQL Injection
    $stmt->bindParam(":senha", $senha);
    $stmt->execute();

    $usuario = $stmt->fetch();

    if($usuario) {
        // Segurança: gera um novo id de sessão (evita roubo de sessão)
        session_regenerate_id(true);

        // Guarda na sessão quem está logado e a hora do último acesso
        $_SESSION['usuario']      = $usuario['login'];
        $_SESSION['nome']         = $usuario['nome'];
        $_SESSION['ultimo_acesso'] = time();

        header("Location: painel.php");
        exit;
    } else {
        // Volta para o login com aviso de erro
        header("Location: index.php?erro=senha");
        exit;
    }

?>
