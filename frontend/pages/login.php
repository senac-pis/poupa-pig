<?php 
// Lógica: receber $_POST, validar,
// password_verify(), iniciar sessão, etc.
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login — Poupa Pig</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <div class="container-fluid min-vh-100 d-flex flex-column py-4">
        <header class="text-center mb-4">
            <img src="../assets/logos/logo.svg" alt="Poupa Pig" height="90">
        </header>

        <main class="row justify-content-center align-items-center flex-grow-1">
            <div class="col-11 col-sm-8 col-md-6 col-lg-6">
                <div class="login-wrapper position-relative">
                    <img src="../assets/images/character-lateral.svg" alt="" class="login-character d-none d-lg-block">

                    <div class="card login-card">
                    <div class="card-body p-4 p-md-5">
                        <h1 class="h3 text-center fw-bold mb-4 login-title">
                            Seja bem vindo(a)
                        </h1>

                        <form action="" method="post">
                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold">E-mail</label>
                                <input type="email" id="email" name="email" class="form-control" required>
                            </div>

                            <div class="mb-1">
                                <label for="password" class="form-label fw-semibold">Senha</label>
                                <input type="password" id="password" name="password" class="form-control" required>
                            </div>

                            <div class="text-end mb-4">
                                <a href="#" class="forgot-password-link small">Esqueceu a senha?</a>
                            </div>

                            <div class="d-flex gap-3">
                                <button type="submit" class="btn btn-enter flex-fill">ENTRAR</button>
                                <a href="#" class="btn btn-register flex-fill">CADASTRAR</a>
                            </div>
                        </form>
                    </div>
                    </div>
                </div>
            </div>
        </main>

        <footer class="login-footer text-center small text-body-secondary mt-4">
            © PoupaPig · Contato · Política de Privacidade
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
