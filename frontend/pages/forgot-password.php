<?php
/**
 * TELA DE RECUPERAR ACESSO (E-MAIL)
 *
 * Layout implementado a partir do Figma (Desktop - Light mode tela de inserir email).
 * Próximas etapas da equipe:
 * - validar o e-mail no servidor;
 * - gerar um código de acesso de 6 dígitos com validade;
 * - enviar o código para o e-mail informado;
 * - responder igual para e-mail cadastrado ou não (evita descobrir contas).
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

        <section class="flex w-full flex-col items-start gap-5 rounded-[32px] border border-primary px-6 py-10 shadow-card sm:w-auto sm:px-[120px]">
            <div class="flex items-center gap-6 sm:gap-10">
                <a href="login.php" aria-label="Voltar para o login" class="shrink-0 rounded-full transition hover:opacity-80 focus:outline-none focus:ring-2 focus:ring-secondary">
                    <img src="../assets/icons/arrow-left-circle.svg" alt="" width="48" height="48">
                </a>
                <h1 class="text-3xl font-bold sm:text-[42px]">Recuperar acesso</h1>
            </div>

            <form action="verify-code.php" method="post" class="flex w-full flex-col items-center justify-center gap-5 px-5">
                <div class="flex w-full flex-col items-start gap-[5px]">
                    <label for="email" class="text-[22px] font-bold">E-mail</label>
                    <input type="email" id="email" name="email" autocomplete="email" required
                        class="h-[50px] w-full rounded-[32px] border border-primary bg-transparent px-5 text-lg outline-none focus:ring-2 focus:ring-secondary">
                </div>

                <div class="flex w-full flex-col items-center justify-center py-5">
                    <button type="submit"
                        class="h-[68px] w-[200px] cursor-pointer rounded-[48px] bg-secondary text-2xl text-background transition hover:brightness-95 focus:outline-none focus:ring-2 focus:ring-primary">
                        Recuperar
                    </button>
                </div>
            </form>
        </section>

        <?php include __DIR__ . '/../components/footer.php'; ?>
    </main>
</body>
</html>
