<?php

    // =================================================================
    // vacinacao/index.php - TELA DE VACINAÇÃO
    // Registra as vacinas aplicadas em cada animal e avisa quando a
    // próxima dose está chegando (30 dias).
    // =================================================================

    $raiz = '../';
    include '../verifica_login.php';
    include '../conexao.php';

    // --- Animais para o <select> ----
    $sql = "SELECT animal.id, animal.nome, especie.nome AS especie
            FROM animal
            INNER JOIN especie ON especie.id = animal.especie_id
            ORDER BY animal.nome";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $animais = $stmt->fetchAll();

    // --- Filtro simples por animal ----
    $fAnimal = isset($_GET['f_animal']) ? $_GET['f_animal'] : '';

    // --- Listagem completa ----
    $sql = "SELECT vacinacao.*, animal.nome AS animal
            FROM vacinacao
            INNER JOIN animal ON animal.id = vacinacao.animal_id";
    if($fAnimal != '') {
        $sql .= " WHERE vacinacao.animal_id =:animal";
    }
    $sql .= " ORDER BY vacinacao.data_aplicacao DESC";

    $stmt = $pdo->prepare($sql);
    if($fAnimal != '') {
        $stmt->bindParam(":animal", $fAnimal);
    }
    $stmt->execute();

    $vacinacoes = $stmt->fetchAll();
    // ------ Fim Listagem -------

    // --- Selecionar para edição -----
    $id = isset($_GET['id']) ? $_GET['id'] : '';
    $sqlEdit = "SELECT * FROM vacinacao WHERE id =:id";
    $stmtEdit = $pdo->prepare($sqlEdit);
    $stmtEdit->bindParam(":id", $id);
    $stmtEdit->execute();
    $vacinacaoSelecionada = $stmtEdit->fetch();
    // ---- Fim da seleção para edição ------

    $atualAnimal = isset($vacinacaoSelecionada['animal_id']) ? $vacinacaoSelecionada['animal_id'] : '';

    // Datas usadas para pintar o aviso de "próxima dose"
    $hoje       = date('Y-m-d');
    $daqui30    = date('Y-m-d', strtotime('+30 days'));

    $titulo = 'Vacinação';
    $menu   = 'vacinacao';
    include '../cabecalho.php';
?>

    <h1 class="h3 titulo-pagina mb-4"><i class="bi bi-capsule"></i> Vacinação</h1>

    <!-- ===== FORMULÁRIO (CREATE / UPDATE) ===== -->
    <div class="card mb-4">
        <div class="card-header">
            <?php echo $vacinacaoSelecionada ? 'Editar vacina' : 'Registrar vacina'; ?>
        </div>
        <div class="card-body">
            <form action="inserir.php" class="row g-3">

                <input type="hidden" name="id"
                                     value="<?php echo isset($vacinacaoSelecionada['id']) ?
                                                             $vacinacaoSelecionada['id']  :
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
                </div>

                <div class="col-md-4">
                    <label class="form-label">Vacina</label>
                    <!-- list + datalist: sugere nomes, mas aceita qualquer texto -->
                    <input type="text" name="vacina" class="form-control" required maxlength="100" list="sugestoesVacina"
                           value="<?php echo isset($vacinacaoSelecionada['vacina']) ?
                                                   htmlspecialchars($vacinacaoSelecionada['vacina']) :
                                                   ''; ?>">
                    <datalist id="sugestoesVacina">
                        <option value="V10 (polivalente)">
                        <option value="Antirrábica">
                        <option value="V4 felina">
                        <option value="Gripe canina">
                    </datalist>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Veterinário(a)</label>
                    <input type="text" name="veterinario" class="form-control" required maxlength="200"
                           value="<?php echo isset($vacinacaoSelecionada['veterinario']) ?
                                                   htmlspecialchars($vacinacaoSelecionada['veterinario']) :
                                                   ''; ?>">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Data de aplicação</label>
                    <input type="date" name="data_aplicacao" class="form-control" required max="<?php echo $hoje ?>"
                           value="<?php echo isset($vacinacaoSelecionada['data_aplicacao']) ?
                                                   $vacinacaoSelecionada['data_aplicacao']  :
                                                   $hoje; ?>">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Próxima dose <span class="text-secondary">(opcional)</span></label>
                    <input type="date" name="proxima_dose" class="form-control"
                           value="<?php echo isset($vacinacaoSelecionada['proxima_dose']) ?
                                                   $vacinacaoSelecionada['proxima_dose']  :
                                                   ''; ?>">
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-pet"><i class="bi bi-check-lg"></i> Salvar</button>
                    <?php if($vacinacaoSelecionada) { ?>
                        <a href="index.php" class="btn btn-outline-secondary">Cancelar</a>
                    <?php } ?>
                </div>
            </form>
        </div>
    </div>

    <!-- ===== LISTAGEM (READ) ===== -->
    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span>Vacinas registradas (<?php echo count($vacinacoes) ?>)</span>
            <!-- Filtro rápido por animal -->
            <form action="index.php" class="d-flex gap-2">
                <select name="f_animal" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Todos os animais</option>
                    <?php foreach ($animais as $animal) { ?>
                        <option value="<?php echo $animal['id'] ?>" <?php echo selecionado($animal['id'], $fAnimal) ?>>
                            <?php echo htmlspecialchars($animal['nome']) ?>
                        </option>
                    <?php } ?>
                </select>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Animal</th>
                        <th>Vacina</th>
                        <th>Aplicação</th>
                        <th>Próxima dose</th>
                        <th>Veterinário(a)</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($vacinacoes as $vacinacao) { ?>
                    <tr>
                        <td class="fw-semibold"><?php echo htmlspecialchars($vacinacao['animal']) ?></td>
                        <td><?php echo htmlspecialchars($vacinacao['vacina']) ?></td>
                        <td><?php echo dataBR($vacinacao['data_aplicacao']) ?></td>
                        <td>
                            <?php echo dataBR($vacinacao['proxima_dose']) ?>
                            <!-- Aviso: dose vencida ou vencendo nos próximos 30 dias -->
                            <?php if($vacinacao['proxima_dose'] && $vacinacao['proxima_dose'] < $hoje) { ?>
                                <span class="badge text-bg-danger">Atrasada</span>
                            <?php } else if($vacinacao['proxima_dose'] && $vacinacao['proxima_dose'] <= $daqui30) { ?>
                                <span class="badge text-bg-warning">Em breve</span>
                            <?php } ?>
                        </td>
                        <td><?php echo htmlspecialchars($vacinacao['veterinario']) ?></td>
                        <td class="text-end text-nowrap">
                            <a href="index.php?id=<?php echo $vacinacao['id'] ?>" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i> Editar
                            </a>
                            <a href="excluir.php?id=<?php echo $vacinacao['id'] ?>" class="btn btn-sm btn-outline-danger"
                               onclick="return confirm('Deseja excluir este registro de vacina?')">
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
