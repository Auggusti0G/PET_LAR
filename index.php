<?php

    // =================================================================
    // index.php - TELA DE LOGIN
    // Primeira página do sistema. Mostra o formulário de usuário e
    // senha, que é enviado (POST) para o login.php.
    // =================================================================

    session_start();

    // Se a pessoa já está logada, não precisa ver o login de novo
    if(isset($_SESSION['usuario'])) {
        header("Location: painel.php");
        exit;
    }

    // Mensagem de erro que o login.php ou o verifica_login.php mandam pela URL
    $erro = isset($_GET['erro']) ? $_GET['erro'] : '';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar | Pet Lar</title>
    <!-- Bootstrap (framework de estilos) e ícones, carregados pela internet -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Nosso CSS próprio (cores e detalhes do Pet Lar) -->
    <link href="css/estilo.css" rel="stylesheet">
</head>
<body class="pagina-login">

    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-12 col-sm-10 col-md-8 col-lg-5">

                <div class="card cartao-login shadow-lg border-0">
                    <div class="card-body p-4 p-md-5">

                        <!-- Logo e nome do sistema -->
                        <div class="text-center mb-4">
                            <div class="logo-circulo mx-auto mb-3">
                                <i class="bi bi-house-heart-fill"></i>
                            </div>
                            <h1 class="h3 fw-bold mb-1">Pet Lar</h1>
                            <p class="text-secondary mb-0">Gestão de adoção de animais</p>
                        </div>

                        <!-- Alerta de erro (só aparece se existir mensagem) -->
                        <?php if($erro == 'senha') { ?>
                            <div class="alert alert-danger py-2">
                                <i class="bi bi-exclamation-triangle"></i> Usuário ou senha inválidos.
                            </div>
                        <?php } else if($erro == 'acesso') { ?>
                            <div class="alert alert-warning py-2">
                                <i class="bi bi-lock"></i> Faça login para acessar o sistema.
                            </div>
                        <?php } else if($erro == 'expirou') { ?>
                            <div class="alert alert-warning py-2">
                                <i class="bi bi-clock-history"></i> Sua sessão expirou. Entre novamente.
                            </div>
                        <?php } ?>

                        <!-- Formulário de login: envia por POST para não mostrar a senha na URL -->
                        <form action="login.php" method="post">

                            <div class="mb-3">
                                <label for="usuario" class="form-label">Usuário</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                    <input type="text" class="form-control" id="usuario" name="usuario"
                                           placeholder="Ex.: rafael.ag" required autofocus>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="senha" class="form-label">Senha</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-key"></i></span>
                                    <input type="password" class="form-control" id="senha" name="senha"
                                           placeholder="Sua senha" required>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-pet btn-lg">
                                    <i class="bi bi-box-arrow-in-right"></i> Entrar
                                </button>
                                <button type="reset" class="btn btn-outline-secondary">Limpar</button>
                            </div>
                        </form>

                    </div>
                </div>

                <p class="text-center small text-white-50 mt-3 mb-0">
                    UniCEUB · Desenvolvimento Web com PHP · 2026
                </p>

            </div>
        </div>
    </div>

</body>
</html>
