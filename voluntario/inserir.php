<?php

    // =================================================================
    // voluntario/inserir.php - SALVAR (CREATE ou UPDATE)
    // =================================================================

    $raiz = '../';
    include '../verifica_login.php';
    include '../conexao.php';

    // Recuperando as informações do formulário
    $id       = $_GET['id'];
    $nome     = trim($_GET['nome']);
    $telefone = trim($_GET['telefone']);
    $email    = trim($_GET['email']);
    $funcao   = $_GET['funcao'];

    if($id) {
        // Execução SQL de atualização (UPDATE)
        $sql = "UPDATE voluntario SET nome =:nome, telefone =:telefone, email =:email, funcao =:funcao
                WHERE id =:id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":nome", $nome);
        $stmt->bindParam(":telefone", $telefone);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":funcao", $funcao);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $mensagem = "Voluntário atualizado com sucesso!";
    } else {
        // Execução SQL de inserção (INSERT)
        $sql = "INSERT INTO voluntario (nome, telefone, email, funcao)
                VALUES (:nome, :telefone, :email, :funcao)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":nome", $nome);
        $stmt->bindParam(":telefone", $telefone);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":funcao", $funcao);
        $stmt->execute();
        $mensagem = "Voluntário cadastrado com sucesso!";
    }

    header("Location: index.php?msg=" . urlencode($mensagem));
    exit;

?>
