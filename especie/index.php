<?php

    // =================================================================
    // especie/index.php - TELA DE ESPÉCIES
    // Mesma ideia do aluno/index.php visto em aula:
    //   - parte de cima: formulário (cadastrar ou editar)
    //   - parte de baixo: tabela com todas as espécies (listagem)
    // =================================================================

    $raiz = '../';                     // estamos dentro de uma subpasta
    include '../verifica_login.php';   // protege a página (sessão)
    include '../conexao.php';

    // --- Listagem completa ----
    // A subconsulta conta quantos animais existem de cada espécie
    $sql = "SELECT especie.*,
                   (SELECT COUNT(*) FROM animal WHERE animal.especie_id = especie.id) AS qtd_animais
            FROM especie
            ORDER BY especie.nome";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $especies = $stmt->fetchAll();
    // ------ Fim Listagem -------

    // --- Selecionar para edição -----
    // Quando clica em "Editar", a página abre com ?id=X na URL
    $id = isset($_GET['id']) ? $_GET['id'] : '';
    $sqlEdit = "SELECT * FROM especie WHERE id =:id";
    $stmtEdit = $pdo->prepare($sqlEdit);
    $stmtEdit->bindParam(":id", $id);
    $stmtEdit->execute();
    $especieSelecionada = $stmtEdit->fetch();
    // ---- Fim da seleção para edição ------

    $titulo = 'Espécies';
    $menu   = 'especie';
    include '../cabecalho.php';
?>

    <h1 class="h3 titulo-pagina mb-4"><i class="bi bi-tags"></i> Espécies</h1>

    <!-- ===== FORMULÁRIO (CREATE / UPDATE) ===== -->
    <div class="card mb-4">
        <div class="card-header">
            <?php echo $especieSelecionada ? 'Editar espécie' : 'Nova espécie'; ?>
        </div>
        <div class="card-body">
            <form action="inserir.php" class="row g-3">

                <!-- id escondido: vazio = INSERT, preenchido = UPDATE -->
                <input type="hidden" name="id"
                                     value="<?php echo isset($especieSelecionada['id']) ?
                                                             $especieSelecionada['id']  :
                                                             ''; ?>">

                <div class="col-md-4">
                    <label class="form-label">Nome</label>
                    <input type="text" name="nome" class="form-control" required maxlength="100"
                           value="<?php echo isset($especieSelecionada['nome']) ?
                                                   htmlspecialchars($especieSelecionada['nome']) :
                                                   ''; ?>">
                </div>

                <div class="col-md-8">
                    <label class="form-label">Descrição</label>
                    <input type="text" name="descricao" class="form-control" maxlength="255"
                           value="<?php echo isset($especieSelecionada['descricao']) ?
                                                   htmlspecialchars($especieSelecionada['descricao']) :
                                                   ''; ?>">
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-pet"><i class="bi bi-check-lg"></i> Salvar</button>
                    <?php if($especieSelecionada) { ?>
                        <a href="index.php" class="btn btn-outline-secondary">Cancelar</a>
                    <?php } ?>
                </div>
            </form>
        </div>
    </div>

    <!-- ===== LISTAGEM (READ) ===== -->
    <div class="card">
        <div class="card-header">Espécies cadastradas (<?php echo count($especies) ?>)</div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Descrição</th>
                        <th class="text-center">Animais</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($especies as $especie) { ?>
                    <tr>
                        <td class="fw-semibold"><?php echo htmlspecialchars($especie['nome']) ?></td>
                        <td><?php echo htmlspecialchars($especie['descricao']) ?></td>
                        <td class="text-center"><span class="badge text-bg-secondary"><?php echo $especie['qtd_animais'] ?></span></td>
                        <td class="text-end text-nowrap">
                            <!-- Editar: recarrega esta página com o id -->
                            <a href="index.php?id=<?php echo $especie['id'] ?>" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i> Editar
                            </a>
                            <!-- Excluir: confirma antes de apagar (DELETE) -->
                            <a href="excluir.php?id=<?php echo $especie['id'] ?>" class="btn btn-sm btn-outline-danger"
                               onclick="return confirm('Deseja excluir esta espécie?')">
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
