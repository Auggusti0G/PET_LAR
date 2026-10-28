<?php

    // =================================================================
    // painel.php - PÁGINA INICIAL (depois do login)
    // Mostra números rápidos, atalhos para os cadastros e as
    // últimas adoções.
    // =================================================================

    $raiz = '';                     // painel fica na pasta principal
    include 'verifica_login.php';   // só entra quem está logado
    include 'conexao.php';

    // --- Números do topo ----
    // Cada consulta devolve só um número (COUNT)
    $sql = "SELECT COUNT(*) AS total FROM animal";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $totalAnimais = $stmt->fetch()['total'];

    $sql = "SELECT COUNT(*) AS total FROM animal WHERE status = 'Disponível'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $totalDisponiveis = $stmt->fetch()['total'];

    $sql = "SELECT COUNT(*) AS total FROM adocao";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $totalAdocoes = $stmt->fetch()['total'];

    $sql = "SELECT COUNT(*) AS total FROM adotante";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $totalAdotantes = $stmt->fetch()['total'];
    // ---- Fim dos números ----

    // --- Últimas 5 adoções (JOIN junta as tabelas para mostrar os nomes) ----
    $sql = "SELECT adocao.data_adocao, animal.nome AS animal, adotante.nome AS adotante
            FROM adocao
            INNER JOIN animal   ON animal.id   = adocao.animal_id
            INNER JOIN adotante ON adotante.id = adocao.adotante_id
            ORDER BY adocao.data_adocao DESC
            LIMIT 5";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $ultimasAdocoes = $stmt->fetchAll();
    // ---- Fim últimas adoções ----

    $titulo = 'Painel';
    $menu   = 'painel';
    include 'cabecalho.php';
?>

    <h1 class="h3 titulo-pagina mb-1">Olá, <?php echo htmlspecialchars($_SESSION['nome']) ?>!</h1>
    <p class="text-secondary mb-4">Veja como está o Pet Lar hoje.</p>

    <!-- ===== Cartões com números ===== -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card cartao-numero h-100"><div class="card-body d-flex justify-content-between align-items-center">
                <div><div class="text-secondary small">Animais cadastrados</div><div class="numero"><?php echo $totalAnimais ?></div></div>
                <div class="icone escuro"><i class="bi bi-clipboard-heart"></i></div>
            </div></div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card cartao-numero h-100"><div class="card-body d-flex justify-content-between align-items-center">
                <div><div class="text-secondary small">Disponíveis para adoção</div><div class="numero"><?php echo $totalDisponiveis ?></div></div>
                <div class="icone verde"><i class="bi bi-heart"></i></div>
            </div></div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card cartao-numero h-100"><div class="card-body d-flex justify-content-between align-items-center">
                <div><div class="text-secondary small">Adoções realizadas</div><div class="numero"><?php echo $totalAdocoes ?></div></div>
                <div class="icone"><i class="bi bi-house-heart"></i></div>
            </div></div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card cartao-numero h-100"><div class="card-body d-flex justify-content-between align-items-center">
                <div><div class="text-secondary small">Adotantes</div><div class="numero"><?php echo $totalAdotantes ?></div></div>
                <div class="icone amarelo"><i class="bi bi-people"></i></div>
            </div></div>
        </div>
    </div>

    <div class="row g-4">
        <!-- ===== Atalhos para os cadastros ===== -->
        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-header"><i class="bi bi-grid"></i> Acesso rápido</div>
                <div class="card-body">
                    <div class="row g-3 text-center">
                        <div class="col-6 col-md-4"><a class="card atalho p-3 h-100" href="animal/index.php"><i class="bi bi-clipboard-heart"></i><span class="mt-2">Animais</span></a></div>
                        <div class="col-6 col-md-4"><a class="card atalho p-3 h-100" href="especie/index.php"><i class="bi bi-tags"></i><span class="mt-2">Espécies</span></a></div>
                        <div class="col-6 col-md-4"><a class="card atalho p-3 h-100" href="adotante/index.php"><i class="bi bi-people"></i><span class="mt-2">Adotantes</span></a></div>
                        <div class="col-6 col-md-4"><a class="card atalho p-3 h-100" href="voluntario/index.php"><i class="bi bi-person-badge"></i><span class="mt-2">Voluntários</span></a></div>
                        <div class="col-6 col-md-4"><a class="card atalho p-3 h-100" href="adocao/index.php"><i class="bi bi-house-heart"></i><span class="mt-2">Adoções</span></a></div>
                        <div class="col-6 col-md-4"><a class="card atalho p-3 h-100" href="vacinacao/index.php"><i class="bi bi-capsule"></i><span class="mt-2">Vacinação</span></a></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== Últimas adoções ===== -->
        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between">
                    <span><i class="bi bi-clock-history"></i> Últimas adoções</span>
                    <a href="relatorio/index.php" class="small">Ver relatório</a>
                </div>
                <ul class="list-group list-group-flush">
                    <?php foreach ($ultimasAdocoes as $adocao) { ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>
                                <strong><?php echo htmlspecialchars($adocao['animal']) ?></strong>
                                <span class="text-secondary">foi para</span>
                                <?php echo htmlspecialchars($adocao['adotante']) ?>
                            </span>
                            <span class="badge text-bg-light"><?php echo dataBR($adocao['data_adocao']) ?></span>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </div>

<?php include 'rodape.php'; ?>
