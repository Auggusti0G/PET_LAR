<?php

    // =================================================================
    // verifica_login.php - PROTEÇÃO DAS PÁGINAS
    // Este arquivo é incluído no topo de TODA página interna.
    // Se não houver usuário na sessão, manda de volta para o login.
    // =================================================================

    session_start();

    // Tempo máximo sem usar o sistema: 30 minutos (em segundos)
    $tempoLimite = 30 * 60;

    // $raiz indica o caminho até a pasta principal ('' ou '../')
    $raiz = isset($raiz) ? $raiz : '';

    // 1. Não está logado? Volta para o login
    if(!isset($_SESSION['usuario'])) {
        header("Location: " . $raiz . "index.php?erro=acesso");
        exit;
    }

    // 2. Ficou parado tempo demais? Encerra a sessão
    if(time() - $_SESSION['ultimo_acesso'] > $tempoLimite) {
        session_unset();
        session_destroy();
        header("Location: " . $raiz . "index.php?erro=expirou");
        exit;
    }

    // 3. Está tudo certo: atualiza a hora do último acesso
    $_SESSION['ultimo_acesso'] = time();

?>
