<?php

    // =================================================================
    // adocao/inserir.php - SALVAR (CREATE ou UPDATE)
    // Além de salvar a adoção, atualiza o STATUS do animal:
    //   animal adotado  -> 'Adotado'
    //   animal trocado  -> o antigo volta para 'Disponível'
    // =================================================================

    $raiz = '../';
    include '../verifica_login.php';
    include '../conexao.php';

    // Recuperando as informações do formulário
    $id            = $_GET['id'];
    $animal_id     = $_GET['animal_id'];
    $adotante_id   = $_GET['adotante_id'];
    $voluntario_id = $_GET['voluntario_id'];
    $data_adocao   = $_GET['data_adocao'];
    $observacao    = trim($_GET['observacao']);

    // Busca o animal escolhido para conferir as regras de negócio
    $sqlAnimal = "SELECT * FROM animal WHERE id =:id";
    $stmtAnimal = $pdo->prepare($sqlAnimal);
    $stmtAnimal->bindParam(":id", $animal_id);
    $stmtAnimal->execute();
    $animal = $stmtAnimal->fetch();

    // RN: a data da adoção não pode ser no futuro nem antes da entrada do animal
    if($data_adocao > date('Y-m-d') || $data_adocao < $animal['data_entrada']) {
        header("Location: index.php?msg=" . urlencode("Data inválida: a adoção deve ser entre a entrada do animal e hoje.") . "&tipo=danger");
        exit;
    }

    if($id) {
        // Descobre qual era o animal antes da edição
        $sqlAntigo = "SELECT animal_id FROM adocao WHERE id =:id";
        $stmtAntigo = $pdo->prepare($sqlAntigo);
        $stmtAntigo->bindParam(":id", $id);
        $stmtAntigo->execute();
        $animalAntigo = $stmtAntigo->fetch()['animal_id'];

        // Execução SQL de atualização (UPDATE)
        $sql = "UPDATE adocao SET animal_id =:animal_id, adotante_id =:adotante_id,
                       voluntario_id =:voluntario_id, data_adocao =:data_adocao, observacao =:observacao
                WHERE id =:id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":animal_id", $animal_id);
        $stmt->bindParam(":adotante_id", $adotante_id);
        $stmt->bindParam(":voluntario_id", $voluntario_id);
        $stmt->bindParam(":data_adocao", $data_adocao);
        $stmt->bindParam(":observacao", $observacao);
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        // Se trocou o animal, o antigo volta a ficar disponível
        if($animalAntigo != $animal_id) {
            $sqlStatus = "UPDATE animal SET status = 'Disponível' WHERE id =:id";
            $stmtStatus = $pdo->prepare($sqlStatus);
            $stmtStatus->bindParam(":id", $animalAntigo);
            $stmtStatus->execute();
        }
        $mensagem = "Adoção atualizada com sucesso!";
    } else {
        // RN: só animal "Disponível" pode ser adotado
        if($animal['status'] != 'Disponível') {
            header("Location: index.php?msg=" . urlencode("Este animal não está disponível para adoção.") . "&tipo=danger");
            exit;
        }

        // Execução SQL de inserção (INSERT)
        $sql = "INSERT INTO adocao (animal_id, adotante_id, voluntario_id, data_adocao, observacao)
                VALUES (:animal_id, :adotante_id, :voluntario_id, :data_adocao, :observacao)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":animal_id", $animal_id);
        $stmt->bindParam(":adotante_id", $adotante_id);
        $stmt->bindParam(":voluntario_id", $voluntario_id);
        $stmt->bindParam(":data_adocao", $data_adocao);
        $stmt->bindParam(":observacao", $observacao);
        $stmt->execute();
        $mensagem = "Adoção registrada! O animal agora está como Adotado.";
    }

    // RN: o animal da adoção passa a ficar com status "Adotado"
    $sqlStatus = "UPDATE animal SET status = 'Adotado' WHERE id =:id";
    $stmtStatus = $pdo->prepare($sqlStatus);
    $stmtStatus->bindParam(":id", $animal_id);
    $stmtStatus->execute();

    header("Location: index.php?msg=" . urlencode($mensagem));
    exit;

?>
