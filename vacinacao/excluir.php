<?php

    // =================================================================
    // vacinacao/excluir.php - EXCLUIR (DELETE)
    // =================================================================

    $raiz = '../';
    include '../verifica_login.php';
    include '../conexao.php';

    $id = $_GET['id'];

    $sql = "DELETE FROM vacinacao WHERE id =:id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":id", $id);

    $stmt->execute();

    header("Location: index.php?msg=" . urlencode("Registro de vacina excluído com sucesso!"));
    exit;

?>
