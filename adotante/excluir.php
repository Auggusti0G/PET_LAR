<?php

    // =================================================================
    // adotante/excluir.php - EXCLUIR (DELETE)
    // =================================================================

    $raiz = '../';
    include '../verifica_login.php';
    include '../conexao.php';

    $id = $_GET['id'];

    // RN: quem já adotou não pode ser excluído (histórico da adoção)
    $sqlCheca = "SELECT COUNT(*) AS total FROM adocao WHERE adotante_id =:id";
    $stmtCheca = $pdo->prepare($sqlCheca);
    $stmtCheca->bindParam(":id", $id);
    $stmtCheca->execute();

    if($stmtCheca->fetch()['total'] > 0) {
        header("Location: index.php?msg=" . urlencode("Não é possível excluir: este adotante possui adoção registrada.") . "&tipo=danger");
        exit;
    }

    $sql = "DELETE FROM adotante WHERE id =:id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":id", $id);

    $stmt->execute();

    header("Location: index.php?msg=" . urlencode("Adotante excluído com sucesso!"));
    exit;

?>
