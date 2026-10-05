<?php
/**
 * TELA DE LOGIN
 *
 * Layout implementado a partir do Figma (Desktop - Light mode tela de login).
 * 
 * Contrato com o backend (backend/controllers/login.php):
 * - o form envia POST com os campos email e password;
 * - em caso de erro, o backend volta para esta tela deixando na sessão:
 *   $_SESSION['notification'] = ['type' => 'error', 'code' => 'CODIGO'];
 *   $_SESSION['old_input'] = ['email' => 'email digitado'];
 * - em caso de sucesso, o backend redireciona para a próxima tela.
 */

session_start();

// Email digitado na tentativa anterior, para o usuário não redigitar
$oldEmail = $_SESSION['old_input']['email'] ?? '';
unset($_SESSION['old_input']);
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login — Poupa Pig</title>
    <?php include __DIR__ . '/../components/theme.php'; ?>
</head>
<body class="min-h-screen bg-background font-sans text-primary">
    <?php include __DIR__ . '/../components/notification.php'; ?>

    <main class="flex min-h-screen flex-col items-center justify-center gap-5 px-4 py-10">
        <?php include __DIR__ . '/../components/logo.php'; ?>

        <section class="flex w-full flex-col items-start gap-5 rounded-[32px] border border-primary px-6 py-10 shadow-card sm:w-auto sm:px-[120px]">
            <h1 class="text-3xl font-bold sm:text-[42px]">Seja bem vindo(a)</h1>

            <form action="../../backend/controllers/login.php" method="post" class="flex w-full flex-col items-center justify-center gap-5 px-5">
                <div class="flex w-full flex-col items-start gap-[5px]">
                    <label for="email" class="text-[22px] font-bold">E-mail</label>
                    <input type="email" id="email" name="email" autocomplete="email" required
                        value="<?=  htmlspecialchars($oldEmail) ?>"
                        class="h-[50px] w-full rounded-[32px] border border-primary bg-transparent px-5 text-lg outline-none focus:ring-2 focus:ring-secondary">
                </div>

                <div class="flex w-full flex-col items-start gap-[5px]">
                    <label for="password" class="text-[22px] font-bold">Senha</label>
                    <input type="password" id="password" name="password" autocomplete="current-password" required
                        class="h-[50px] w-full rounded-[32px] border border-primary bg-transparent px-5 text-lg outline-none focus:ring-2 focus:ring-secondary">
                </div>

                <div class="flex w-full flex-col items-center justify-center gap-2.5 py-5">
                    <button type="submit"
                        class="h-[68px] w-[200px] cursor-pointer rounded-[48px] bg-secondary text-2xl text-background transition hover:brightness-95 focus:outline-none focus:ring-2 focus:ring-primary">
                        Entrar
                    </button>
                    <a href="register.php" class="text-base font-light hover:underline">Cadastre-se</a>
                </div>
            </form>

            <a href="#" class="w-full text-right text-lg font-extralight hover:underline">Esqueceu a senha?</a>
        </section>

        <?php include __DIR__ . '/../components/footer.php'; ?>
    </main>
</body>
</html>
