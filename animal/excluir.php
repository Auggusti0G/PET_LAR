<?php

    // =================================================================
    // animal/excluir.php - EXCLUIR (DELETE)
    // As vacinas do animal são apagadas juntas (ON DELETE CASCADE).
    // =================================================================

    $raiz = '../';
    include '../verifica_login.php';
    include '../conexao.php';

    $id = $_GET['id'];

    // RN: animal com adoção registrada não pode ser excluído
    // (a adoção é um histórico importante para a ONG)
    $sqlCheca = "SELECT COUNT(*) AS total FROM adocao WHERE animal_id =:id";
    $stmtCheca = $pdo->prepare($sqlCheca);
    $stmtCheca->bindParam(":id", $id);
    $stmtCheca->execute();

    if($stmtCheca->fetch()['total'] > 0) {
        header("Location: index.php?msg=" . urlencode("Não é possível excluir: este animal possui adoção registrada.") . "&tipo=danger");
        exit;
    }

    $sql = "DELETE FROM animal WHERE id =:id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":id", $id);

    $stmt->execute();

    header("Location: index.php?msg=" . urlencode("Animal excluído com sucesso!"));
    exit;

?>
