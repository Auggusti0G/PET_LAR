<?php

    // =================================================================
    // cabecalho.php - PARTE DE CIMA DE TODAS AS PÁGINAS INTERNAS
    // Monta o <head> com o Bootstrap, o menu superior e as mensagens.
    //
    // Antes de fazer include deste arquivo, a página define:
    //   $titulo -> texto da aba do navegador
    //   $menu   -> qual item do menu fica destacado
    //   $raiz   -> caminho até a pasta principal ('' ou '../')
    // =================================================================

    // Função pequena para destacar o item do menu da página atual
    function ativo($item, $menu) {
        return $item == $menu ? 'active' : '';
    }

    // Devolve a classe CSS da etiqueta colorida de cada status do animal
    function classeStatus($status) {
        if($status == 'Disponível')    return 'status-disponivel';
        if($status == 'Em tratamento') return 'status-tratamento';
        return 'status-adotado';
    }

    // Converte data do banco (2026-10-28) para o formato brasileiro (28/10/2026)
    function dataBR($data) {
        return $data ? date('d/m/Y', strtotime($data)) : '-';
    }

    // Marca a opção certa de um <select> ao editar um registro
    function selecionado($opcao, $valorAtual) {
        return $opcao == $valorAtual ? 'selected' : '';
    }

    // Mensagem de retorno enviada pelos arquivos inserir/excluir
    // Ex.: index.php?msg=Salvo com sucesso&tipo=success
    $msg  = isset($_GET['msg'])  ? $_GET['msg']  : '';
    $tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'success';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo ?> | Pet Lar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?php echo $raiz ?>css/estilo.css" rel="stylesheet">
</head>
<body>

    <!-- ===== MENU SUPERIOR (navbar do Bootstrap) ===== -->
    <nav class="navbar navbar-expand-lg navbar-dark barra-pet sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?php echo $raiz ?>painel.php">
                <i class="bi bi-house-heart-fill"></i> Pet Lar
            </a>

            <!-- Botão "hambúrguer" que aparece no celular -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuPrincipal">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="menuPrincipal">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link <?php echo ativo('painel', $menu) ?>"     href="<?php echo $raiz ?>painel.php">Painel</a></li>
                    <li class="nav-item"><a class="nav-link <?php echo ativo('animal', $menu) ?>"     href="<?php echo $raiz ?>animal/index.php">Animais</a></li>
                    <li class="nav-item"><a class="nav-link <?php echo ativo('especie', $menu) ?>"    href="<?php echo $raiz ?>especie/index.php">Espécies</a></li>
                    <li class="nav-item"><a class="nav-link <?php echo ativo('adotante', $menu) ?>"   href="<?php echo $raiz ?>adotante/index.php">Adotantes</a></li>
                    <li class="nav-item"><a class="nav-link <?php echo ativo('voluntario', $menu) ?>" href="<?php echo $raiz ?>voluntario/index.php">Voluntários</a></li>
                    <li class="nav-item"><a class="nav-link <?php echo ativo('adocao', $menu) ?>"     href="<?php echo $raiz ?>adocao/index.php">Adoções</a></li>
                    <li class="nav-item"><a class="nav-link <?php echo ativo('vacinacao', $menu) ?>"  href="<?php echo $raiz ?>vacinacao/index.php">Vacinação</a></li>
                    <li class="nav-item"><a class="nav-link <?php echo ativo('relatorio', $menu) ?>"  href="<?php echo $raiz ?>relatorio/index.php">Relatório</a></li>
                </ul>

                <!-- Nome de quem está logado (vem da SESSÃO) e botão Sair -->
                <span class="navbar-text me-3 small">
                    <i class="bi bi-person-circle"></i>
                    <?php echo htmlspecialchars($_SESSION['nome']) ?>
                </span>
                <a href="<?php echo $raiz ?>sair.php" class="btn btn-sm btn-light">
                    <i class="bi bi-box-arrow-right"></i> Sair
                </a>
            </div>
        </div>
    </nav>

    <main class="container py-4">

        <!-- Alerta de sucesso/erro (aparece depois de salvar ou excluir) -->
        <?php if($msg) { ?>
            <div class="alert alert-<?php echo htmlspecialchars($tipo) ?> alert-dismissible fade show">
                <?php echo htmlspecialchars($msg) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php } ?>
