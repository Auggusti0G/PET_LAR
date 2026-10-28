<?php

    // =================================================================
    // adotante/index.php - TELA DE ADOTANTES
    // Formulário (cadastrar/editar) + tabela com os adotantes.
    // =================================================================

    $raiz = '../';
    include '../verifica_login.php';
    include '../conexao.php';

    // --- Listagem completa ----
    // A subconsulta conta quantas adoções cada pessoa já fez
    $sql = "SELECT adotante.*,
                   (SELECT COUNT(*) FROM adocao WHERE adocao.adotante_id = adotante.id) AS qtd_adocoes
            FROM adotante
            ORDER BY adotante.nome";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $adotantes = $stmt->fetchAll();
    // ------ Fim Listagem -------

    // --- Selecionar para edição -----
    $id = isset($_GET['id']) ? $_GET['id'] : '';
    $sqlEdit = "SELECT * FROM adotante WHERE id =:id";
    $stmtEdit = $pdo->prepare($sqlEdit);
    $stmtEdit->bindParam(":id", $id);
    $stmtEdit->execute();
    $adotanteSelecionado = $stmtEdit->fetch();
    // ---- Fim da seleção para edição ------

    $titulo = 'Adotantes';
    $menu   = 'adotante';
    include '../cabecalho.php';
?>

    <h1 class="h3 titulo-pagina mb-4"><i class="bi bi-people"></i> Adotantes</h1>

    <!-- ===== FORMULÁRIO (CREATE / UPDATE) ===== -->
    <div class="card mb-4">
        <div class="card-header">
            <?php echo $adotanteSelecionado ? 'Editar adotante' : 'Novo adotante'; ?>
        </div>
        <div class="card-body">
            <form action="inserir.php" class="row g-3">

                <input type="hidden" name="id"
                                     value="<?php echo isset($adotanteSelecionado['id']) ?
                                                             $adotanteSelecionado['id']  :
                                                             ''; ?>">

                <div class="col-md-5">
                    <label class="form-label">Nome completo</label>
                    <input type="text" name="nome" class="form-control" required maxlength="200"
                           value="<?php echo isset($adotanteSelecionado['nome']) ?
                                                   htmlspecialchars($adotanteSelecionado['nome']) :
                                                   ''; ?>">
                </div>

                <div class="col-md-3">
                    <label class="form-label">CPF</label>
                    <!-- pattern: o navegador só aceita o formato 000.000.000-00 -->
                    <input type="text" name="cpf" class="form-control" required
                           placeholder="000.000.000-00" pattern="\d{3}\.\d{3}\.\d{3}-\d{2}"
                           value="<?php echo isset($adotanteSelecionado['cpf']) ?
                                                   $adotanteSelecionado['cpf']  :
                                                   ''; ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Data de nascimento</label>
                    <input type="date" name="data_nascimento" class="form-control" required
                           value="<?php echo isset($adotanteSelecionado['data_nascimento']) ?
                                                   $adotanteSelecionado['data_nascimento']  :
                                                   ''; ?>">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Telefone</label>
                    <input type="text" name="telefone" class="form-control" required placeholder="(61) 99999-9999"
                           value="<?php echo isset($adotanteSelecionado['telefone']) ?
                                                   htmlspecialchars($adotanteSelecionado['telefone']) :
                                                   ''; ?>">
                </div>

                <div class="col-md-5">
                    <label class="form-label">E-mail</label>
                    <input type="email" name="email" class="form-control" maxlength="150"
                           value="<?php echo isset($adotanteSelecionado['email']) ?
                                                   htmlspecialchars($adotanteSelecionado['email']) :
                                                   ''; ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Cidade</label>
                    <input type="text" name="cidade" class="form-control" required maxlength="100"
                           value="<?php echo isset($adotanteSelecionado['cidade']) ?
                                                   htmlspecialchars($adotanteSelecionado['cidade']) :
                                                   ''; ?>">
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-pet"><i class="bi bi-check-lg"></i> Salvar</button>
                    <?php if($adotanteSelecionado) { ?>
                        <a href="index.php" class="btn btn-outline-secondary">Cancelar</a>
                    <?php } ?>
                </div>
            </form>
        </div>
    </div>

    <!-- ===== LISTAGEM (READ) ===== -->
    <div class="card">
        <div class="card-header">Adotantes cadastrados (<?php echo count($adotantes) ?>)</div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>CPF</th>
                        <th>Telefone</th>
                        <th>E-mail</th>
                        <th>Cidade</th>
                        <th class="text-center">Adoções</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($adotantes as $adotante) { ?>
                    <tr>
                        <td class="fw-semibold"><?php echo htmlspecialchars($adotante['nome']) ?></td>
                        <td><?php echo $adotante['cpf'] ?></td>
                        <td><?php echo htmlspecialchars($adotante['telefone']) ?></td>
                        <td><?php echo htmlspecialchars($adotante['email']) ?></td>
                        <td><?php echo htmlspecialchars($adotante['cidade']) ?></td>
                        <td class="text-center"><span class="badge text-bg-secondary"><?php echo $adotante['qtd_adocoes'] ?></span></td>
                        <td class="text-end text-nowrap">
                            <a href="index.php?id=<?php echo $adotante['id'] ?>" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i> Editar
                            </a>
                            <a href="excluir.php?id=<?php echo $adotante['id'] ?>" class="btn btn-sm btn-outline-danger"
                               onclick="return confirm('Deseja excluir este adotante?')">
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
