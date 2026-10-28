<?php

    // =================================================================
    // especie/inserir.php - SALVAR (CREATE ou UPDATE)
    // Se o id veio preenchido, atualiza. Se veio vazio, insere.
    // =================================================================

    $raiz = '../';
    include '../verifica_login.php';
    include '../conexao.php';

    // Recuperando as informações do formulário
    $id        = $_GET['id'];
    $nome      = trim($_GET['nome']);
    $descricao = trim($_GET['descricao']);

    // RN: não pode existir duas espécies com o mesmo nome
    $sqlCheca = "SELECT COUNT(*) AS total FROM especie WHERE nome =:nome AND id <> :id";
    $stmtCheca = $pdo->prepare($sqlCheca);
    $idChecagem = $id ? $id : 0;
    $stmtCheca->bindParam(":nome", $nome);
    $stmtCheca->bindParam(":id", $idChecagem);
    $stmtCheca->execute();
    if($stmtCheca->fetch()['total'] > 0) {
        header("Location: index.php?msg=" . urlencode("Já existe uma espécie com esse nome.") . "&tipo=danger");
        exit;
    }

    if($id) {
        // Execução SQL de atualização (UPDATE)
        $sql = "UPDATE especie SET nome =:nome, descricao =:descricao WHERE id =:id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":nome", $nome);
        $stmt->bindParam(":descricao", $descricao);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $mensagem = "Espécie atualizada com sucesso!";
    } else {
        // Execução SQL de inserção (INSERT)
        $sql = "INSERT INTO especie (nome, descricao) VALUES (:nome, :descricao)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":nome", $nome);
        $stmt->bindParam(":descricao", $descricao);
        $stmt->execute();
        $mensagem = "Espécie cadastrada com sucesso!";
    }

    // Volta para a listagem com a mensagem de sucesso
    header("Location: index.php?msg=" . urlencode($mensagem));
    exit;

?>
