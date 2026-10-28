<?php

    // =================================================================
    // animal/index.php - TELA DE ANIMAIS
    // Tem três partes:
    //   1. Formulário para cadastrar/editar um animal
    //   2. BUSCA AVANÇADA com 5 filtros (texto, lista, data...)
    //   3. Tabela com o resultado + botão para gerar PDF
    // =================================================================

    $raiz = '../';
    include '../verifica_login.php';
    include '../conexao.php';

    // --- Espécies para preencher o <select> do formulário e do filtro ----
    $sql = "SELECT * FROM especie ORDER BY nome";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $especies = $stmt->fetchAll();

    // --- Busca avançada (filtros) ----
    // Os filtros chegam pela URL com o prefixo f_ para não misturar
    // com os campos do formulário de cadastro.
    $fNome    = isset($_GET['f_nome'])    ? trim($_GET['f_nome']) : '';
    $fEspecie = isset($_GET['f_especie']) ? $_GET['f_especie']    : '';
    $fStatus  = isset($_GET['f_status'])  ? $_GET['f_status']     : '';
    $fPorte   = isset($_GET['f_porte'])   ? $_GET['f_porte']      : '';
    $fEntrada = isset($_GET['f_entrada']) ? $_GET['f_entrada']    : '';

    // Montamos o WHERE aos poucos: só entra o filtro que foi preenchido
    $condicoes  = array();
    $parametros = array();

    if($fNome != '') {                                   // filtro de TEXTO
        $condicoes[] = "animal.nome LIKE :nome";
        $parametros[':nome'] = '%' . $fNome . '%';       // % = "contém"
    }
    if($fEspecie != '') {                                // filtro de LISTA (chave estrangeira)
        $condicoes[] = "animal.especie_id = :especie";
        $parametros[':especie'] = $fEspecie;
    }
    if($fStatus != '') {                                 // filtro de LISTA (situação)
        $condicoes[] = "animal.status = :status";
        $parametros[':status'] = $fStatus;
    }
    if($fPorte != '') {                                  // filtro de LISTA (porte)
        $condicoes[] = "animal.porte = :porte";
        $parametros[':porte'] = $fPorte;
    }
    if($fEntrada != '') {                                // filtro de DATA
        $condicoes[] = "animal.data_entrada >= :entrada";
        $parametros[':entrada'] = $fEntrada;
    }

    // --- Listagem completa (com os filtros, se houver) ----
    $sql = "SELECT animal.*, especie.nome AS especie
            FROM animal
            INNER JOIN especie ON especie.id = animal.especie_id";
    if(count($condicoes) > 0) {
        $sql .= " WHERE " . implode(" AND ", $condicoes);   // junta com AND
    }
    $sql .= " ORDER BY animal.nome";

    $stmt = $pdo->prepare($sql);
    // bindValue dentro do foreach: liga cada :parametro ao seu valor
    foreach ($parametros as $chave => $valor) {
        $stmt->bindValue($chave, $valor);
    }
    $stmt->execute();

    $animais = $stmt->fetchAll();
    // ------ Fim Listagem -------

    // Mesmos filtros em formato de URL, usados no botão "Gerar PDF"
    $filtrosUrl = http_build_query(array(
        'f_nome' => $fNome, 'f_especie' => $fEspecie, 'f_status' => $fStatus,
        'f_porte' => $fPorte, 'f_entrada' => $fEntrada
    ));

    // --- Selecionar para edição -----
    $id = isset($_GET['id']) ? $_GET['id'] : '';
    $sqlEdit = "SELECT * FROM animal WHERE id =:id";
    $stmtEdit = $pdo->prepare($sqlEdit);
    $stmtEdit->bindParam(":id", $id);
    $stmtEdit->execute();
    $animalSelecionado = $stmtEdit->fetch();
    // ---- Fim da seleção para edição ------

    // Valores atuais do animal em edição (ou vazio quando é novo)
    $atualEspecie = isset($animalSelecionado['especie_id']) ? $animalSelecionado['especie_id'] : '';
    $atualSexo    = isset($animalSelecionado['sexo'])       ? $animalSelecionado['sexo']       : '';
    $atualPorte   = isset($animalSelecionado['porte'])      ? $animalSelecionado['porte']      : '';
    $atualStatus  = isset($animalSelecionado['status'])     ? $animalSelecionado['status']     : 'Disponível';

    $titulo = 'Animais';
    $menu   = 'animal';
    include '../cabecalho.php';
?>

    <h1 class="h3 titulo-pagina mb-4"><i class="bi bi-clipboard-heart"></i> Animais</h1>

    <!-- ===== FORMULÁRIO (CREATE / UPDATE) ===== -->
    <div class="card mb-4">
        <div class="card-header">
            <?php echo $animalSelecionado ? 'Editar animal' : 'Novo animal'; ?>
        </div>
        <div class="card-body">
            <form action="inserir.php" class="row g-3">

                <input type="hidden" name="id"
                                     value="<?php echo isset($animalSelecionado['id']) ?
                                                             $animalSelecionado['id']  :
                                                             ''; ?>">

                <div class="col-md-4">
                    <label class="form-label">Nome</label>
                    <input type="text" name="nome" class="form-control" required maxlength="100"
                           value="<?php echo isset($animalSelecionado['nome']) ?
                                                   htmlspecialchars($animalSelecionado['nome']) :
                                                   ''; ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Espécie</label>
                    <select name="especie_id" class="form-select" required>
                        <option value="">Selecione...</option>
                        <?php foreach ($especies as $especie) { ?>
                            <option value="<?php echo $especie['id'] ?>" <?php echo selecionado($especie['id'], $atualEspecie) ?>>
                                <?php echo htmlspecialchars($especie['nome']) ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Sexo</label>
                    <select name="sexo" class="form-select" required>
                        <option value="Macho" <?php echo selecionado('Macho', $atualSexo) ?>>Macho</option>
                        <option value="Fêmea" <?php echo selecionado('Fêmea', $atualSexo) ?>>Fêmea</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Porte</label>
                    <select name="porte" class="form-select" required>
                        <option value="Pequeno" <?php echo selecionado('Pequeno', $atualPorte) ?>>Pequeno</option>
                        <option value="Médio"   <?php echo selecionado('Médio', $atualPorte) ?>>Médio</option>
                        <option value="Grande"  <?php echo selecionado('Grande', $atualPorte) ?>>Grande</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Idade (anos)</label>
                    <input type="number" name="idade" class="form-control" min="0" max="30" required
                           value="<?php echo isset($animalSelecionado['idade']) ?
                                                   $animalSelecionado['idade']  :
                                                   ''; ?>">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Data de entrada</label>
                    <input type="date" name="data_entrada" class="form-control" required
                           max="<?php echo date('Y-m-d') ?>"
                           value="<?php echo isset($animalSelecionado['data_entrada']) ?
                                                   $animalSelecionado['data_entrada']  :
                                                   date('Y-m-d'); ?>">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Situação</label>
                    <?php if($atualStatus == 'Adotado') { ?>
                        <!-- RN: o status "Adotado" só muda pelo módulo de Adoções -->
                        <input type="hidden" name="status" value="Adotado">
                        <input type="text" class="form-control" value="Adotado" disabled>
                        <div class="form-text">Controlado pelo módulo de Adoções.</div>
                    <?php } else { ?>
                        <select name="status" class="form-select">
                            <option value="Disponível"    <?php echo selecionado('Disponível', $atualStatus) ?>>Disponível</option>
                            <option value="Em tratamento" <?php echo selecionado('Em tratamento', $atualStatus) ?>>Em tratamento</option>
                        </select>
                    <?php } ?>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Observação</label>
                    <input type="text" name="observacao" class="form-control" maxlength="255"
                           value="<?php echo isset($animalSelecionado['observacao']) ?
                                                   htmlspecialchars($animalSelecionado['observacao']) :
                                                   ''; ?>">
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-pet"><i class="bi bi-check-lg"></i> Salvar</button>
                    <?php if($animalSelecionado) { ?>
                        <a href="index.php" class="btn btn-outline-secondary">Cancelar</a>
                    <?php } ?>
                </div>
            </form>
        </div>
    </div>

    <!-- ===== BUSCA AVANÇADA (5 filtros) ===== -->
    <div class="card mb-4">
        <div class="card-header"><i class="bi bi-funnel"></i> Busca avançada</div>
        <div class="card-body">
            <!-- Envia para esta mesma página (index.php) com os filtros na URL -->
            <form action="index.php" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Nome contém</label>
                    <input type="text" name="f_nome" class="form-control" placeholder="Ex.: Mel"
                           value="<?php echo htmlspecialchars($fNome) ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Espécie</label>
                    <select name="f_especie" class="form-select">
                        <option value="">Todas</option>
                        <?php foreach ($especies as $especie) { ?>
                            <option value="<?php echo $especie['id'] ?>" <?php echo selecionado($especie['id'], $fEspecie) ?>>
                                <?php echo htmlspecialchars($especie['nome']) ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Situação</label>
                    <select name="f_status" class="form-select">
                        <option value="">Todas</option>
                        <option value="Disponível"    <?php echo selecionado('Disponível', $fStatus) ?>>Disponível</option>
                        <option value="Em tratamento" <?php echo selecionado('Em tratamento', $fStatus) ?>>Em tratamento</option>
                        <option value="Adotado"       <?php echo selecionado('Adotado', $fStatus) ?>>Adotado</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Porte</label>
                    <select name="f_porte" class="form-select">
                        <option value="">Todos</option>
                        <option value="Pequeno" <?php echo selecionado('Pequeno', $fPorte) ?>>Pequeno</option>
                        <option value="Médio"   <?php echo selecionado('Médio', $fPorte) ?>>Médio</option>
                        <option value="Grande"  <?php echo selecionado('Grande', $fPorte) ?>>Grande</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Entrada a partir de</label>
                    <input type="date" name="f_entrada" class="form-control" value="<?php echo htmlspecialchars($fEntrada) ?>">
                </div>
                <div class="col-12 d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-pet"><i class="bi bi-search"></i> Buscar</button>
                    <a href="index.php" class="btn btn-outline-secondary">Limpar filtros</a>
                    <!-- Gera o PDF com os MESMOS filtros da busca -->
                    <a href="../pdf/animais.php?<?php echo $filtrosUrl ?>" target="_blank" class="btn btn-outline-danger ms-md-auto">
                        <i class="bi bi-filetype-pdf"></i> Gerar PDF da listagem
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- ===== LISTAGEM (READ) ===== -->
    <div class="card">
        <div class="card-header">Resultado: <?php echo count($animais) ?> animal(is)</div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Espécie</th>
                        <th>Sexo</th>
                        <th>Porte</th>
                        <th>Idade</th>
                        <th>Entrada</th>
                        <th>Situação</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($animais as $animal) { ?>
                    <tr>
                        <td class="fw-semibold"><?php echo htmlspecialchars($animal['nome']) ?></td>
                        <td><?php echo htmlspecialchars($animal['especie']) ?></td>
                        <td><?php echo $animal['sexo'] ?></td>
                        <td><?php echo $animal['porte'] ?></td>
                        <td><?php echo $animal['idade'] ?> ano(s)</td>
                        <td><?php echo dataBR($animal['data_entrada']) ?></td>
                        <td><span class="badge <?php echo classeStatus($animal['status']) ?>"><?php echo $animal['status'] ?></span></td>
                        <td class="text-end text-nowrap">
                            <a href="index.php?id=<?php echo $animal['id'] ?>" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i> Editar
                            </a>
                            <a href="excluir.php?id=<?php echo $animal['id'] ?>" class="btn btn-sm btn-outline-danger"
                               onclick="return confirm('Deseja excluir este animal? As vacinas dele também serão apagadas.')">
                                <i class="bi bi-trash"></i> Excluir
                            </a>
                        </td>
                    </tr>
                <?php } ?>
                <?php if(count($animais) == 0) { ?>
                    <tr><td colspan="8" class="text-center text-secondary py-4">Nenhum animal encontrado com esses filtros.</td></tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

<?php include '../rodape.php'; ?>
