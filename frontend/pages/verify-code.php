<?php
/**
 * TELA DE RECUPERAR ACESSO (CÓDIGO)
 *
 * Layout implementado a partir do Figma (Desktop - Light mode tela de recuperar acesso).
 * Próximas etapas da equipe:
 * - validar o código de 6 dígitos no servidor;
 * - conferir se o código pertence ao e-mail e ainda está dentro da validade;
 * - limitar o número de tentativas;
 * - exibir a notificação "E-mail enviado!" ao chegar nesta tela.
 */
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recuperar acesso — Poupa Pig</title>
    <?php include __DIR__ . '/../components/theme.php'; ?>
</head>
<body class="min-h-screen bg-background font-sans text-primary">
    <main class="flex min-h-screen flex-col items-center justify-center gap-5 px-4 py-10">
        <?php include __DIR__ . '/../components/logo.php'; ?>

        <section class="flex w-full flex-col items-start gap-5 rounded-[32px] border border-primary px-6 py-8 shadow-card sm:w-auto sm:px-16">
            <div class="flex items-center gap-4 sm:gap-6">
                <a href="forgot-password.php" aria-label="Voltar para a tela de e-mail" class="shrink-0 rounded-full transition hover:opacity-80 focus:outline-none focus:ring-2 focus:ring-secondary">
                    <img src="../assets/icons/arrow-left-circle.svg" alt="" width="36" height="36" class="size-9">
                </a>
                <h1 class="text-2xl font-bold sm:text-[30px]">Recuperar acesso</h1>
            </div>

            <form action="reset-password.php" method="post" class="flex w-full flex-col items-center justify-center gap-4 px-5">
                <div class="flex w-full flex-col items-start gap-[5px]">
                    <label for="code" class="text-base font-bold">Código de acesso</label>
                    <input type="text" id="code" name="code" inputmode="numeric" autocomplete="one-time-code" required
                        minlength="6" maxlength="6" pattern="[0-9]{6}"
                        class="h-11 w-full rounded-[32px] border border-primary bg-transparent px-4 text-base outline-none focus:ring-2 focus:ring-secondary">
                </div>

                <div class="flex w-full flex-col items-center justify-center py-3">
                    <button type="submit"
                        class="h-11 w-36 cursor-pointer rounded-[48px] bg-secondary text-lg text-background transition hover:brightness-95 focus:outline-none focus:ring-2 focus:ring-primary">
                        Recuperar
                    </button>
                </div>
            </form>
        </section>

        <?php include __DIR__ . '/../components/footer.php'; ?>
    </main>
</body>
</html>
