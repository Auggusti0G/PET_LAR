<?php

    // =================================================================
    // voluntario/index.php - TELA DE VOLUNTÁRIOS
    // Formulário (cadastrar/editar) + tabela com os voluntários.
    // =================================================================

    $raiz = '../';
    include '../verifica_login.php';
    include '../conexao.php';

    // --- Listagem completa ----
    $sql = "SELECT voluntario.*,
                   (SELECT COUNT(*) FROM adocao WHERE adocao.voluntario_id = voluntario.id) AS qtd_adocoes
            FROM voluntario
            ORDER BY voluntario.nome";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $voluntarios = $stmt->fetchAll();
    // ------ Fim Listagem -------

    // --- Selecionar para edição -----
    $id = isset($_GET['id']) ? $_GET['id'] : '';
    $sqlEdit = "SELECT * FROM voluntario WHERE id =:id";
    $stmtEdit = $pdo->prepare($sqlEdit);
    $stmtEdit->bindParam(":id", $id);
    $stmtEdit->execute();
    $voluntarioSelecionado = $stmtEdit->fetch();
    // ---- Fim da seleção para edição ------

    $atualFuncao = isset($voluntarioSelecionado['funcao']) ? $voluntarioSelecionado['funcao'] : '';

    $titulo = 'Voluntários';
    $menu   = 'voluntario';
    include '../cabecalho.php';
?>

    <h1 class="h3 titulo-pagina mb-4"><i class="bi bi-person-badge"></i> Voluntários</h1>

    <!-- ===== FORMULÁRIO (CREATE / UPDATE) ===== -->
    <div class="card mb-4">
        <div class="card-header">
            <?php echo $voluntarioSelecionado ? 'Editar voluntário' : 'Novo voluntário'; ?>
        </div>
        <div class="card-body">
            <form action="inserir.php" class="row g-3">

                <input type="hidden" name="id"
                                     value="<?php echo isset($voluntarioSelecionado['id']) ?
                                                             $voluntarioSelecionado['id']  :
                                                             ''; ?>">

                <div class="col-md-5">
                    <label class="form-label">Nome</label>
                    <input type="text" name="nome" class="form-control" required maxlength="200"
                           value="<?php echo isset($voluntarioSelecionado['nome']) ?
                                                   htmlspecialchars($voluntarioSelecionado['nome']) :
                                                   ''; ?>">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Telefone</label>
                    <input type="text" name="telefone" class="form-control" required placeholder="(61) 99999-9999"
                           value="<?php echo isset($voluntarioSelecionado['telefone']) ?
                                                   htmlspecialchars($voluntarioSelecionado['telefone']) :
                                                   ''; ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Função</label>
                    <select name="funcao" class="form-select" required>
                        <option value="">Selecione...</option>
                        <option value="Veterinária"   <?php echo selecionado('Veterinária', $atualFuncao) ?>>Veterinária</option>
                        <option value="Entrevistador" <?php echo selecionado('Entrevistador', $atualFuncao) ?>>Entrevistador</option>
                        <option value="Cuidadora"     <?php echo selecionado('Cuidadora', $atualFuncao) ?>>Cuidadora</option>
                        <option value="Motorista"     <?php echo selecionado('Motorista', $atualFuncao) ?>>Motorista</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">E-mail</label>
                    <input type="email" name="email" class="form-control" maxlength="150"
                           value="<?php echo isset($voluntarioSelecionado['email']) ?
                                                   htmlspecialchars($voluntarioSelecionado['email']) :
                                                   ''; ?>">
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-pet"><i class="bi bi-check-lg"></i> Salvar</button>
                    <?php if($voluntarioSelecionado) { ?>
                        <a href="index.php" class="btn btn-outline-secondary">Cancelar</a>
                    <?php } ?>
                </div>
            </form>
        </div>
    </div>

    <!-- ===== LISTAGEM (READ) ===== -->
    <div class="card">
        <div class="card-header">Voluntários cadastrados (<?php echo count($voluntarios) ?>)</div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Função</th>
                        <th>Telefone</th>
                        <th>E-mail</th>
                        <th class="text-center">Adoções acompanhadas</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($voluntarios as $voluntario) { ?>
                    <tr>
                        <td class="fw-semibold"><?php echo htmlspecialchars($voluntario['nome']) ?></td>
                        <td><?php echo htmlspecialchars($voluntario['funcao']) ?></td>
                        <td><?php echo htmlspecialchars($voluntario['telefone']) ?></td>
                        <td><?php echo htmlspecialchars($voluntario['email']) ?></td>
                        <td class="text-center"><span class="badge text-bg-secondary"><?php echo $voluntario['qtd_adocoes'] ?></span></td>
                        <td class="text-end text-nowrap">
                            <a href="index.php?id=<?php echo $voluntario['id'] ?>" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i> Editar
                            </a>
                            <a href="excluir.php?id=<?php echo $voluntario['id'] ?>" class="btn btn-sm btn-outline-danger"
                               onclick="return confirm('Deseja excluir este voluntário?')">
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
