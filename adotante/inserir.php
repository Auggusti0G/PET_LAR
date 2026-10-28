<?php

    // =================================================================
    // adotante/inserir.php - SALVAR (CREATE ou UPDATE)
    // =================================================================

    $raiz = '../';
    include '../verifica_login.php';
    include '../conexao.php';

    // Recuperando as informações do formulário
    $id              = $_GET['id'];
    $nome            = trim($_GET['nome']);
    $cpf             = trim($_GET['cpf']);
    $data_nascimento = $_GET['data_nascimento'];
    $telefone        = trim($_GET['telefone']);
    $email           = trim($_GET['email']);
    $cidade          = trim($_GET['cidade']);

    // RN: o adotante precisa ter 18 anos ou mais
    // date('Y-m-d', strtotime('-18 years')) = data de hoje menos 18 anos
    if($data_nascimento > date('Y-m-d', strtotime('-18 years'))) {
        header("Location: index.php?msg=" . urlencode("O adotante precisa ter pelo menos 18 anos.") . "&tipo=danger");
        exit;
    }

    // RN: não pode haver dois adotantes com o mesmo CPF
    $sqlCheca = "SELECT COUNT(*) AS total FROM adotante WHERE cpf =:cpf AND id <> :id";
    $stmtCheca = $pdo->prepare($sqlCheca);
    $idChecagem = $id ? $id : 0;
    $stmtCheca->bindParam(":cpf", $cpf);
    $stmtCheca->bindParam(":id", $idChecagem);
    $stmtCheca->execute();
    if($stmtCheca->fetch()['total'] > 0) {
        header("Location: index.php?msg=" . urlencode("Já existe um adotante com esse CPF.") . "&tipo=danger");
        exit;
    }

    if($id) {
        // Execução SQL de atualização (UPDATE)
        $sql = "UPDATE adotante SET nome =:nome, cpf =:cpf, data_nascimento =:data_nascimento,
                       telefone =:telefone, email =:email, cidade =:cidade
                WHERE id =:id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":id", $id);
        $mensagem = "Adotante atualizado com sucesso!";
    } else {
        // Execução SQL de inserção (INSERT)
        $sql = "INSERT INTO adotante (nome, cpf, data_nascimento, telefone, email, cidade)
                VALUES (:nome, :cpf, :data_nascimento, :telefone, :email, :cidade)";
        $stmt = $pdo->prepare($sql);
        $mensagem = "Adotante cadastrado com sucesso!";
    }

    $stmt->bindParam(":nome", $nome);
    $stmt->bindParam(":cpf", $cpf);
    $stmt->bindParam(":data_nascimento", $data_nascimento);
    $stmt->bindParam(":telefone", $telefone);
    $stmt->bindParam(":email", $email);
    $stmt->bindParam(":cidade", $cidade);
    $stmt->execute();

    header("Location: index.php?msg=" . urlencode($mensagem));
    exit;

?>
