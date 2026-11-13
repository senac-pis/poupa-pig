<?php
/**
 * TELA DE CADASTRO
 *
 * Layout implementado a partir do Figma (Desktop - Light mode tela de cadastro).
 *
 * Contrato com o backend (backend/controllers/register.php):
 * - o form envia POST com os campos name, email e password;
 * - em caso de erro, o backend volta para esta tela deixando na sessão:
 *   $_SESSION['notification'] = ['type' => 'error', 'code' => 'CODIGO', 'field' => 'email'];
 *   $_SESSION['old_input'] = ['name' => 'nome digitado', 'email' => 'email digitado'];
 * - em caso de sucesso, o backend redireciona para o login.
 */

session_start();

// Nome e email da tentativa anterior, para o usuário não redigitar (a senha nunca volta)
$oldName = $_SESSION['old_input']['name'] ?? '';
$oldEmail = $_SESSION['old_input']['email'] ?? '';
unset($_SESSION['old_input']);
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cadastro — Poupa Pig</title>
    <?php include __DIR__ . '/../components/theme.php'; ?>
</head>
<body class="min-h-screen bg-background font-sans text-primary">
    <?php include __DIR__ . '/../components/notification.php'; ?>
    <?php $errorFor = $fieldError['field'] ?? ''; ?>

    <main class="flex min-h-screen flex-col items-center justify-center gap-5 px-4 py-10">
        <?php include __DIR__ . '/../components/logo.php'; ?>

        <section class="flex w-full flex-col items-start gap-5 rounded-[32px] border border-primary px-6 py-8 shadow-card sm:w-auto sm:px-16">
            <div class="flex items-center gap-4 sm:gap-6">
                <a href="login.php" aria-label="Voltar para o login" class="shrink-0 rounded-full transition hover:opacity-80 focus:outline-none focus:ring-2 focus:ring-secondary">
                    <img src="../assets/icons/arrow-left-circle.svg" alt="" width="36" height="36" class="size-9">
                </a>
                <h1 class="text-2xl font-bold sm:text-[30px]">Crie sua conta</h1>
            </div>

            <form action="../../backend/controllers/register.php" method="post" class="flex w-full flex-col items-center justify-center gap-4 px-5">
                <div class="flex w-full flex-col items-start gap-[5px]">
                    <label for="name" class="text-base font-bold">Nome</label>
                    <input type="text" id="name" name="name" autocomplete="name" required maxlength="120"
                        value="<?= htmlspecialchars($oldName) ?>"
                        <?= $errorFor === 'name' ? 'aria-invalid="true" aria-describedby="name-error" autofocus' : '' ?>
                        class="h-11 w-full rounded-[32px] border <?= $errorFor === 'name' ? 'border-red-400' : 'border-primary' ?> bg-transparent px-4 text-base outline-none focus:ring-2 focus:ring-secondary">
                    <?php if ($errorFor === 'name'): ?>
                        <p id="name-error" class="px-4 text-sm text-red-400"><?= htmlspecialchars($fieldError['message']) ?></p>
                    <?php endif; ?>
                </div>

                <div class="flex w-full flex-col items-start gap-[5px]">
                    <label for="email" class="text-base font-bold">E-mail</label>
                    <input type="email" id="email" name="email" autocomplete="email" required maxlength="180"
                        value="<?= htmlspecialchars($oldEmail) ?>"
                        <?= $errorFor === 'email' ? 'aria-invalid="true" aria-describedby="email-error" autofocus' : '' ?>
                        class="h-11 w-full rounded-[32px] border <?= $errorFor === 'email' ? 'border-red-400' : 'border-primary' ?> bg-transparent px-4 text-base outline-none focus:ring-2 focus:ring-secondary">
                    <?php if ($errorFor === 'email'): ?>
                        <p id="email-error" class="px-4 text-sm text-red-400"><?= htmlspecialchars($fieldError['message']) ?></p>
                    <?php endif; ?>
                </div>

                <div class="flex w-full flex-col items-start gap-[5px]">
                    <label for="password" class="text-base font-bold">Senha</label>
                    <input type="password" id="password" name="password" autocomplete="new-password" required minlength="8"
                        <?= $errorFor === 'password' ? 'aria-invalid="true" aria-describedby="password-error" autofocus' : '' ?>
                        class="h-11 w-full rounded-[32px] border <?= $errorFor === 'password' ? 'border-red-400' : 'border-primary' ?> bg-transparent px-4 text-base outline-none focus:ring-2 focus:ring-secondary">
                    <?php if ($errorFor === 'password'): ?>
                        <p id="password-error" class="px-4 text-sm text-red-400"><?= htmlspecialchars($fieldError['message']) ?></p>
                    <?php endif; ?>
                </div>

                <div class="flex w-full flex-col items-center justify-center py-3">
                    <button type="submit"
                        class="h-11 w-36 cursor-pointer rounded-[48px] bg-secondary text-lg text-background transition hover:brightness-95 focus:outline-none focus:ring-2 focus:ring-primary">
                        Cadastrar
                    </button>
                </div>
            </form>
        </section>

        <?php include __DIR__ . '/../components/footer.php'; ?>
    </main>
</body>
</html>
