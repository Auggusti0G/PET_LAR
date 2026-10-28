<?php

    // =================================================================
    // vacinacao/inserir.php - SALVAR (CREATE ou UPDATE)
    // =================================================================

    $raiz = '../';
    include '../verifica_login.php';
    include '../conexao.php';

    // Recuperando as informações do formulário
    $id             = $_GET['id'];
    $animal_id      = $_GET['animal_id'];
    $vacina         = trim($_GET['vacina']);
    $data_aplicacao = $_GET['data_aplicacao'];
    $veterinario    = trim($_GET['veterinario']);

    // Próxima dose é opcional: se veio vazia, grava NULL no banco
    $proxima_dose = $_GET['proxima_dose'] != '' ? $_GET['proxima_dose'] : null;

    // RN: a vacina não pode ter sido aplicada no futuro
    if($data_aplicacao > date('Y-m-d')) {
        header("Location: index.php?msg=" . urlencode("A data de aplicação não pode ser no futuro.") . "&tipo=danger");
        exit;
    }

    // RN: a próxima dose precisa ser depois da aplicação
    if($proxima_dose && $proxima_dose <= $data_aplicacao) {
        header("Location: index.php?msg=" . urlencode("A próxima dose deve ser depois da data de aplicação.") . "&tipo=danger");
        exit;
    }

    if($id) {
        // Execução SQL de atualização (UPDATE)
        $sql = "UPDATE vacinacao SET animal_id =:animal_id, vacina =:vacina, data_aplicacao =:data_aplicacao,
                       proxima_dose =:proxima_dose, veterinario =:veterinario
                WHERE id =:id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":id", $id);
        $mensagem = "Vacina atualizada com sucesso!";
    } else {
        // Execução SQL de inserção (INSERT)
        $sql = "INSERT INTO vacinacao (animal_id, vacina, data_aplicacao, proxima_dose, veterinario)
                VALUES (:animal_id, :vacina, :data_aplicacao, :proxima_dose, :veterinario)";
        $stmt = $pdo->prepare($sql);
        $mensagem = "Vacina registrada com sucesso!";
    }

    $stmt->bindParam(":animal_id", $animal_id);
    $stmt->bindParam(":vacina", $vacina);
    $stmt->bindParam(":data_aplicacao", $data_aplicacao);
    $stmt->bindParam(":proxima_dose", $proxima_dose);
    $stmt->bindParam(":veterinario", $veterinario);
    $stmt->execute();

    header("Location: index.php?msg=" . urlencode($mensagem));
    exit;

?>
