<?php

    // =================================================================
    // voluntario/excluir.php - EXCLUIR (DELETE)
    // =================================================================

    $raiz = '../';
    include '../verifica_login.php';
    include '../conexao.php';

    $id = $_GET['id'];

    // RN: voluntário que acompanhou adoção não pode ser excluído
    $sqlCheca = "SELECT COUNT(*) AS total FROM adocao WHERE voluntario_id =:id";
    $stmtCheca = $pdo->prepare($sqlCheca);
    $stmtCheca->bindParam(":id", $id);
    $stmtCheca->execute();

    if($stmtCheca->fetch()['total'] > 0) {
        header("Location: index.php?msg=" . urlencode("Não é possível excluir: este voluntário acompanhou adoções.") . "&tipo=danger");
        exit;
    }

    $sql = "DELETE FROM voluntario WHERE id =:id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":id", $id);

    $stmt->execute();

    header("Location: index.php?msg=" . urlencode("Voluntário excluído com sucesso!"));
    exit;

?>
