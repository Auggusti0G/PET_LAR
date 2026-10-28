<?php

    // =================================================================
    // sair.php - LOGOUT
    // Apaga os dados da sessão e volta para a tela de login.
    // =================================================================

    session_start();
    session_unset();     // limpa as variáveis da sessão
    session_destroy();   // destrói a sessão no servidor

    header("Location: index.php");
    exit;

?>
