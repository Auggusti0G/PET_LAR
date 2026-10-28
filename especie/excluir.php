<?php

    // =================================================================
    // especie/excluir.php - EXCLUIR (DELETE)
    // =================================================================

    $raiz = '../';
    include '../verifica_login.php';
    include '../conexao.php';

    $id = $_GET['id'];

    // RN: não pode excluir espécie que ainda tem animais cadastrados
    $sqlCheca = "SELECT COUNT(*) AS total FROM animal WHERE especie_id =:id";
    $stmtCheca = $pdo->prepare($sqlCheca);
    $stmtCheca->bindParam(":id", $id);
    $stmtCheca->execute();

    if($stmtCheca->fetch()['total'] > 0) {
        header("Location: index.php?msg=" . urlencode("Não é possível excluir: existem animais desta espécie.") . "&tipo=danger");
        exit;
    }

    $sql = "DELETE FROM especie WHERE id =:id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":id", $id);

    $stmt->execute();

    header("Location: index.php?msg=" . urlencode("Espécie excluída com sucesso!"));
    exit;

?>
