<?php

    // =================================================================
    // relatorio/index.php - RELATÓRIO CONSOLIDADO
    // Junta (consolida) os dados do abrigo usando COUNT, SUM e
    // GROUP BY. Também tem filtro de período e botão de PDF.
    // =================================================================

    $raiz = '../';
    include '../verifica_login.php';
    include '../conexao.php';

    // --- Período do relatório (padrão: ano atual inteiro) ----
    $inicio = isset($_GET['inicio']) && $_GET['inicio'] != '' ? $_GET['inicio'] : date('Y') . '-01-01';
    $fim    = isset($_GET['fim'])    && $_GET['fim']    != '' ? $_GET['fim']    : date('Y') . '-12-31';

    // --- 1. Situação geral dos animais ----
    // SUM(CASE ...) conta só as linhas que atendem à condição
    $sql = "SELECT COUNT(*) AS total,
                   SUM(CASE WHEN status = 'Disponível'    THEN 1 ELSE 0 END) AS disponiveis,
                   SUM(CASE WHEN status = 'Em tratamento' THEN 1 ELSE 0 END) AS tratamento,
                   SUM(CASE WHEN status = 'Adotado'       THEN 1 ELSE 0 END) AS adotados
            FROM animal";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $geral = $stmt->fetch();

    // Taxa de adoção em % (evita divisão por zero)
    $taxaAdocao = $geral['total'] > 0 ? round($geral['adotados'] * 100 / $geral['total']) : 0;

    // --- 2. Animais por espécie (GROUP BY) ----
    $sql = "SELECT especie.nome AS especie,
                   COUNT(animal.id) AS total,
                   SUM(CASE WHEN animal.status = 'Disponível'    THEN 1 ELSE 0 END) AS disponiveis,
                   SUM(CASE WHEN animal.status = 'Em tratamento' THEN 1 ELSE 0 END) AS tratamento,
                   SUM(CASE WHEN animal.status = 'Adotado'       THEN 1 ELSE 0 END) AS adotados
            FROM especie
            LEFT JOIN animal ON animal.especie_id = especie.id
            GROUP BY especie.id, especie.nome
            ORDER BY total DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $porEspecie = $stmt->fetchAll();

    // --- 3. Adoções por mês dentro do período ----
    $sql = "SELECT YEAR(data_adocao) AS ano, MONTH(data_adocao) AS mes, COUNT(*) AS total
            FROM adocao
            WHERE data_adocao BETWEEN :inicio AND :fim
            GROUP BY YEAR(data_adocao), MONTH(data_adocao)
            ORDER BY ano, mes";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":inicio", $inicio);
    $stmt->bindParam(":fim", $fim);
    $stmt->execute();
    $porMes = $stmt->fetchAll();

    // Soma das adoções do período e o maior mês (para o tamanho das barras)
    $totalPeriodo = 0;
    $maiorMes = 1;
    foreach ($porMes as $linha) {
        $totalPeriodo += $linha['total'];
        if($linha['total'] > $maiorMes) { $maiorMes = $linha['total']; }
    }
    $nomesMeses = array(1 => 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
                        'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro');

    // --- 4. Voluntários que mais acompanharam adoções no período ----
    $sql = "SELECT voluntario.nome, voluntario.funcao, COUNT(adocao.id) AS total
            FROM voluntario
            INNER JOIN adocao ON adocao.voluntario_id = voluntario.id
            WHERE adocao.data_adocao BETWEEN :inicio AND :fim
            GROUP BY voluntario.id, voluntario.nome, voluntario.funcao
            ORDER BY total DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":inicio", $inicio);
    $stmt->bindParam(":fim", $fim);
    $stmt->execute();
    $rankingVoluntarios = $stmt->fetchAll();

    // --- 5. Vacinas com próxima dose nos próximos 30 dias ----
    $hoje    = date('Y-m-d');
    $daqui30 = date('Y-m-d', strtotime('+30 days'));
    $sql = "SELECT animal.nome AS animal, vacinacao.vacina, vacinacao.proxima_dose
            FROM vacinacao
            INNER JOIN animal ON animal.id = vacinacao.animal_id
            WHERE vacinacao.proxima_dose BETWEEN :hoje AND :daqui30
            ORDER BY vacinacao.proxima_dose";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":hoje", $hoje);
    $stmt->bindParam(":daqui30", $daqui30);
    $stmt->execute();
    $proximasVacinas = $stmt->fetchAll();

    $titulo = 'Relatório';
    $menu   = 'relatorio';
    include '../cabecalho.php';
?>

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <h1 class="h3 titulo-pagina mb-0"><i class="bi bi-bar-chart"></i> Relatório consolidado</h1>
        <a href="../pdf/relatorio.php?inicio=<?php echo urlencode($inicio) ?>&fim=<?php echo urlencode($fim) ?>"
           target="_blank" class="btn btn-outline-danger">
            <i class="bi bi-filetype-pdf"></i> Baixar relatório em PDF
        </a>
    </div>

    <!-- ===== Filtro de período ===== -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="index.php" class="row g-3 align-items-end">
                <div class="col-sm-4 col-md-3">
                    <label class="form-label">De</label>
                    <input type="date" name="inicio" class="form-control" value="<?php echo htmlspecialchars($inicio) ?>">
                </div>
                <div class="col-sm-4 col-md-3">
                    <label class="form-label">Até</label>
                    <input type="date" name="fim" class="form-control" value="<?php echo htmlspecialchars($fim) ?>">
                </div>
                <div class="col-sm-4 col-md-3">
                    <button type="submit" class="btn btn-pet w-100"><i class="bi bi-arrow-repeat"></i> Atualizar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ===== 1. Cartões gerais ===== -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg">
            <div class="card cartao-numero h-100"><div class="card-body">
                <div class="text-secondary small">Total de animais</div><div class="numero"><?php echo $geral['total'] ?></div>
            </div></div>
        </div>
        <div class="col-6 col-lg">
            <div class="card cartao-numero h-100"><div class="card-body">
                <div class="text-secondary small">Disponíveis</div><div class="numero text-success"><?php echo $geral['disponiveis'] ?></div>
            </div></div>
        </div>
        <div class="col-6 col-lg">
            <div class="card cartao-numero h-100"><div class="card-body">
                <div class="text-secondary small">Em tratamento</div><div class="numero text-warning"><?php echo $geral['tratamento'] ?></div>
            </div></div>
        </div>
        <div class="col-6 col-lg">
            <div class="card cartao-numero h-100"><div class="card-body">
                <div class="text-secondary small">Adotados</div><div class="numero"><?php echo $geral['adotados'] ?></div>
            </div></div>
        </div>
        <div class="col-12 col-lg">
            <div class="card cartao-numero h-100"><div class="card-body">
                <div class="text-secondary small">Taxa de adoção</div><div class="numero"><?php echo $taxaAdocao ?>%</div>
                <div class="progress mt-2"><div class="progress-bar" style="width: <?php echo $taxaAdocao ?>%"></div></div>
            </div></div>
        </div>
    </div>

    <div class="row g-4">
        <!-- ===== 2. Por espécie ===== -->
        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-header">Animais por espécie</div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr><th>Espécie</th><th class="text-center">Total</th><th class="text-center">Disponíveis</th><th class="text-center">Tratamento</th><th class="text-center">Adotados</th></tr>
                        </thead>
                        <tbody>
                        <?php foreach ($porEspecie as $linha) { ?>
                            <tr>
                                <td class="fw-semibold"><?php echo htmlspecialchars($linha['especie']) ?></td>
                                <td class="text-center"><?php echo $linha['total'] ?></td>
                                <td class="text-center"><?php echo (int)$linha['disponiveis'] ?></td>
                                <td class="text-center"><?php echo (int)$linha['tratamento'] ?></td>
                                <td class="text-center"><?php echo (int)$linha['adotados'] ?></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ===== 3. Adoções por mês (barras feitas com Bootstrap) ===== -->
        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header">Adoções por mês · <?php echo $totalPeriodo ?> no período</div>
                <div class="card-body">
                    <?php foreach ($porMes as $linha) { ?>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between small">
                                <span><?php echo $nomesMeses[(int)$linha['mes']] ?>/<?php echo $linha['ano'] ?></span>
                                <strong><?php echo $linha['total'] ?></strong>
                            </div>
                            <div class="progress"><div class="progress-bar" style="width: <?php echo round($linha['total'] * 100 / $maiorMes) ?>%"></div></div>
                        </div>
                    <?php } ?>
                    <?php if(count($porMes) == 0) { ?>
                        <p class="text-secondary mb-0">Nenhuma adoção neste período.</p>
                    <?php } ?>
                </div>
            </div>
        </div>

        <!-- ===== 4. Ranking de voluntários ===== -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">Voluntários que mais acompanharam adoções</div>
                <ol class="list-group list-group-flush list-group-numbered">
                    <?php foreach ($rankingVoluntarios as $linha) { ?>
                        <li class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="ms-2 me-auto">
                                <div class="fw-semibold"><?php echo htmlspecialchars($linha['nome']) ?></div>
                                <span class="small text-secondary"><?php echo htmlspecialchars($linha['funcao']) ?></span>
                            </div>
                            <span class="badge rounded-pill text-bg-dark"><?php echo $linha['total'] ?></span>
                        </li>
                    <?php } ?>
                </ol>
            </div>
        </div>

        <!-- ===== 5. Próximas vacinas ===== -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">Vacinas a aplicar nos próximos 30 dias</div>
                <ul class="list-group list-group-flush">
                    <?php foreach ($proximasVacinas as $linha) { ?>
                        <li class="list-group-item d-flex justify-content-between">
                            <span><strong><?php echo htmlspecialchars($linha['animal']) ?></strong> · <?php echo htmlspecialchars($linha['vacina']) ?></span>
                            <span class="badge text-bg-warning"><?php echo dataBR($linha['proxima_dose']) ?></span>
                        </li>
                    <?php } ?>
                    <?php if(count($proximasVacinas) == 0) { ?>
                        <li class="list-group-item text-secondary">Nenhuma vacina pendente.</li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </div>

<?php include '../rodape.php'; ?>
