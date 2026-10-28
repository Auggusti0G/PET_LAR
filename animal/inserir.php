<?php

    // =================================================================
    // animal/inserir.php - SALVAR (CREATE ou UPDATE)
    // =================================================================

    $raiz = '../';
    include '../verifica_login.php';
    include '../conexao.php';

    // Recuperando as informações do formulário
    $id           = $_GET['id'];
    $nome         = trim($_GET['nome']);
    $especie_id   = $_GET['especie_id'];
    $sexo         = $_GET['sexo'];
    $porte        = $_GET['porte'];
    $idade        = $_GET['idade'];
    $data_entrada = $_GET['data_entrada'];
    $status       = $_GET['status'];
    $observacao   = trim($_GET['observacao']);

    // RN: a data de entrada não pode estar no futuro
    if($data_entrada > date('Y-m-d')) {
        header("Location: index.php?msg=" . urlencode("A data de entrada não pode ser no futuro.") . "&tipo=danger");
        exit;
    }

    // RN: o status "Adotado" é controlado pelo módulo de Adoções.
    // Se o animal tem adoção registrada, continua "Adotado";
    // se não tem, ele não pode ser marcado como "Adotado" aqui.
    $sqlAdocao = "SELECT COUNT(*) AS total FROM adocao WHERE animal_id =:id";
    $stmtAdocao = $pdo->prepare($sqlAdocao);
    $stmtAdocao->bindParam(":id", $id);
    $stmtAdocao->execute();
    $temAdocao = $stmtAdocao->fetch()['total'] > 0;

    if($temAdocao) {
        $status = 'Adotado';
    } else if($status == 'Adotado') {
        $status = 'Disponível';
    }

    if($id) {
        // Execução SQL de atualização (UPDATE)
        $sql = "UPDATE animal SET nome =:nome, especie_id =:especie_id, sexo =:sexo, porte =:porte,
                       idade =:idade, data_entrada =:data_entrada, status =:status, observacao =:observacao
                WHERE id =:id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":id", $id);
        $mensagem = "Animal atualizado com sucesso!";
    } else {
        // Execução SQL de inserção (INSERT)
        $sql = "INSERT INTO animal (nome, especie_id, sexo, porte, idade, data_entrada, status, observacao)
                VALUES (:nome, :especie_id, :sexo, :porte, :idade, :data_entrada, :status, :observacao)";
        $stmt = $pdo->prepare($sql);
        $mensagem = "Animal cadastrado com sucesso!";
    }

    // Os parâmetros abaixo são iguais para o INSERT e o UPDATE
    $stmt->bindParam(":nome", $nome);
    $stmt->bindParam(":especie_id", $especie_id);
    $stmt->bindParam(":sexo", $sexo);
    $stmt->bindParam(":porte", $porte);
    $stmt->bindParam(":idade", $idade);
    $stmt->bindParam(":data_entrada", $data_entrada);
    $stmt->bindParam(":status", $status);
    $stmt->bindParam(":observacao", $observacao);
    $stmt->execute();

    header("Location: index.php?msg=" . urlencode($mensagem));
    exit;

?>
