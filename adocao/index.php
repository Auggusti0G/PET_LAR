<?php

    // =================================================================
    // adocao/index.php - TELA DE ADOÇÕES
    // Uma adoção liga 3 tabelas: animal + adotante + voluntário.
    // Por isso o formulário tem três <select> e a listagem usa JOIN.
    // =================================================================

    $raiz = '../';
    include '../verifica_login.php';
    include '../conexao.php';

    // --- Selecionar para edição (vem antes, pois a lista de animais depende dela) -----
    $id = isset($_GET['id']) ? $_GET['id'] : '';
    $sqlEdit = "SELECT * FROM adocao WHERE id =:id";
    $stmtEdit = $pdo->prepare($sqlEdit);
    $stmtEdit->bindParam(":id", $id);
    $stmtEdit->execute();
    $adocaoSelecionada = $stmtEdit->fetch();
    // ---- Fim da seleção para edição ------

    $atualAnimal     = isset($adocaoSelecionada['animal_id'])     ? $adocaoSelecionada['animal_id']     : '';
    $atualAdotante   = isset($adocaoSelecionada['adotante_id'])   ? $adocaoSelecionada['adotante_id']   : '';
    $atualVoluntario = isset($adocaoSelecionada['voluntario_id']) ? $adocaoSelecionada['voluntario_id'] : '';

    // --- Listas para os <select> ----
    // RN: só aparecem animais "Disponível" (+ o animal da adoção em edição)
    $sql = "SELECT animal.id, animal.nome, especie.nome AS especie
            FROM animal
            INNER JOIN especie ON especie.id = animal.especie_id
            WHERE animal.status = 'Disponível' OR animal.id =:atual
            ORDER BY animal.nome";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":atual", $atualAnimal);
    $stmt->execute();
    $animais = $stmt->fetchAll();

    $sql = "SELECT id, nome FROM adotante ORDER BY nome";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $adotantes = $stmt->fetchAll();

    $sql = "SELECT id, nome, funcao FROM voluntario ORDER BY nome";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $voluntarios = $stmt->fetchAll();

    // --- Listagem completa ----
    // INNER JOIN busca o nome de cada id nas outras tabelas
    $sql = "SELECT adocao.*,
                   animal.nome     AS animal,
                   especie.nome    AS especie,
                   adotante.nome   AS adotante,
                   voluntario.nome AS voluntario
            FROM adocao
            INNER JOIN animal     ON animal.id     = adocao.animal_id
            INNER JOIN especie    ON especie.id    = animal.especie_id
            INNER JOIN adotante   ON adotante.id   = adocao.adotante_id
            INNER JOIN voluntario ON voluntario.id = adocao.voluntario_id
            ORDER BY adocao.data_adocao DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $adocoes = $stmt->fetchAll();
    // ------ Fim Listagem -------

    $titulo = 'Adoções';
    $menu   = 'adocao';
    include '../cabecalho.php';
?>

    <h1 class="h3 titulo-pagina mb-4"><i class="bi bi-house-heart"></i> Adoções</h1>

    <!-- ===== FORMULÁRIO (CREATE / UPDATE) ===== -->
    <div class="card mb-4">
        <div class="card-header">
            <?php echo $adocaoSelecionada ? 'Editar adoção' : 'Registrar nova adoção'; ?>
        </div>
        <div class="card-body">
            <form action="inserir.php" class="row g-3">

                <input type="hidden" name="id"
                                     value="<?php echo isset($adocaoSelecionada['id']) ?
                                                             $adocaoSelecionada['id']  :
                                                             ''; ?>">

                <div class="col-md-4">
                    <label class="form-label">Animal</label>
                    <select name="animal_id" class="form-select" required>
                        <option value="">Selecione...</option>
                        <?php foreach ($animais as $animal) { ?>
                            <option value="<?php echo $animal['id'] ?>" <?php echo selecionado($animal['id'], $atualAnimal) ?>>
                                <?php echo htmlspecialchars($animal['nome']) ?> (<?php echo $animal['especie'] ?>)
                            </option>
                        <?php } ?>
                    </select>
                    <div class="form-text">Só aparecem animais disponíveis.</div>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Adotante</label>
                    <select name="adotante_id" class="form-select" required>
                        <option value="">Selecione...</option>
                        <?php foreach ($adotantes as $adotante) { ?>
                            <option value="<?php echo $adotante['id'] ?>" <?php echo selecionado($adotante['id'], $atualAdotante) ?>>
                                <?php echo htmlspecialchars($adotante['nome']) ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Voluntário responsável</label>
                    <select name="voluntario_id" class="form-select" required>
                        <option value="">Selecione...</option>
                        <?php foreach ($voluntarios as $voluntario) { ?>
                            <option value="<?php echo $voluntario['id'] ?>" <?php echo selecionado($voluntario['id'], $atualVoluntario) ?>>
                                <?php echo htmlspecialchars($voluntario['nome']) ?> - <?php echo $voluntario['funcao'] ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Data da adoção</label>
                    <input type="date" name="data_adocao" class="form-control" required max="<?php echo date('Y-m-d') ?>"
                           value="<?php echo isset($adocaoSelecionada['data_adocao']) ?
                                                   $adocaoSelecionada['data_adocao']  :
                                                   date('Y-m-d'); ?>">
                </div>

                <div class="col-md-9">
                    <label class="form-label">Observação</label>
                    <input type="text" name="observacao" class="form-control" maxlength="255"
                           value="<?php echo isset($adocaoSelecionada['observacao']) ?
                                                   htmlspecialchars($adocaoSelecionada['observacao']) :
                                                   ''; ?>">
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-pet"><i class="bi bi-check-lg"></i> Salvar</button>
                    <?php if($adocaoSelecionada) { ?>
                        <a href="index.php" class="btn btn-outline-secondary">Cancelar</a>
                    <?php } ?>
                </div>
            </form>
        </div>
    </div>

    <!-- ===== LISTAGEM (READ) ===== -->
    <div class="card">
        <div class="card-header">Adoções registradas (<?php echo count($adocoes) ?>)</div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Animal</th>
                        <th>Adotante</th>
                        <th>Voluntário</th>
                        <th>Observação</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($adocoes as $adocao) { ?>
                    <tr>
                        <td><?php echo dataBR($adocao['data_adocao']) ?></td>
                        <td class="fw-semibold"><?php echo htmlspecialchars($adocao['animal']) ?> <span class="text-secondary small">(<?php echo $adocao['especie'] ?>)</span></td>
                        <td><?php echo htmlspecialchars($adocao['adotante']) ?></td>
                        <td><?php echo htmlspecialchars($adocao['voluntario']) ?></td>
                        <td class="small"><?php echo htmlspecialchars($adocao['observacao']) ?></td>
                        <td class="text-end text-nowrap">
                            <a href="index.php?id=<?php echo $adocao['id'] ?>" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i> Editar
                            </a>
                            <a href="excluir.php?id=<?php echo $adocao['id'] ?>" class="btn btn-sm btn-outline-danger"
                               onclick="return confirm('Excluir esta adoção? O animal voltará a ficar disponível.')">
                                <i class="bi bi-trash"></i> Excluir
                            </a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

<?php include '../rodape.php'; ?>
