<?php

    // =================================================================
    // adocao/excluir.php - EXCLUIR (DELETE)
    // Ao apagar uma adoção, o animal volta a ficar 'Disponível'.
    // =================================================================

    $raiz = '../';
    include '../verifica_login.php';
    include '../conexao.php';

    $id = $_GET['id'];

    // Primeiro descobre qual animal estava nesta adoção
    $sqlAnimal = "SELECT animal_id FROM adocao WHERE id =:id";
    $stmtAnimal = $pdo->prepare($sqlAnimal);
    $stmtAnimal->bindParam(":id", $id);
    $stmtAnimal->execute();
    $adocao = $stmtAnimal->fetch();

    $sql = "DELETE FROM adocao WHERE id =:id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":id", $id);

    $stmt->execute();

    // RN: o animal volta a ficar disponível para adoção
    if($adocao) {
        $sqlStatus = "UPDATE animal SET status = 'Disponível' WHERE id =:id";
        $stmtStatus = $pdo->prepare($sqlStatus);
        $stmtStatus->bindParam(":id", $adocao['animal_id']);
        $stmtStatus->execute();
    }

    header("Location: index.php?msg=" . urlencode("Adoção excluída. O animal voltou a ficar disponível."));
    exit;

?>
