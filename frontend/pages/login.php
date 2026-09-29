<?php
/**
 * TELA DE LOGIN
 *
 * Layout implementado a partir do Figma (Desktop - Light mode tela de login).
 * Próximas etapas da equipe:
 * - autenticação consultando usuários no MySQL;
 * - password_verify();
 * - sessão PHP;
 * - redirecionamento para área administrativa protegida.
 */
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login — Poupa Pig</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans+Flex:wght@200;300;400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style type="text/tailwindcss">
        @theme {
            --color-blue-500: #163261;
            --color-pink-400: #f18a9c;
            --color-white-200: #f2f2f2;
            --font-sans: "Google Sans Flex", ui-sans-serif, system-ui, sans-serif;
            --shadow-card: 2px 2px 8px 0 rgba(9, 30, 66, 0.25);
        }
    </style>
</head>
<body class="min-h-screen bg-white-200 font-sans text-blue-500">
    <main class="flex min-h-screen flex-col items-center justify-center gap-5 px-4 py-10">
        <div class="flex items-center justify-center">
            <span class="text-4xl font-bold sm:text-[64px]">POUPA</span>
            <div class="relative size-24 shrink-0 overflow-hidden sm:size-[173px]">
                <img src="../assets/logos/logo.png" alt="Mascote Poupa Pig" class="absolute left-[-17.99%] top-[-12.36%] h-[150.43%] w-[140.51%] max-w-none">
            </div>
            <span class="text-4xl font-bold text-pink-400 sm:text-[64px]">PIG</span>
        </div>

        <section class="flex w-full flex-col items-start gap-5 rounded-[32px] border border-blue-500 px-6 py-10 shadow-card sm:w-auto sm:px-[120px]">
            <h1 class="text-3xl font-bold sm:text-[42px]">Seja bem vindo(a)</h1>

            <form action="" method="post" class="flex w-full flex-col items-center justify-center gap-5 px-5">
                <div class="flex w-full flex-col items-start gap-[5px]">
                    <label for="email" class="text-[22px] font-bold">E-mail</label>
                    <input type="email" id="email" name="email" autocomplete="email" required
                        class="h-[50px] w-full rounded-[32px] border border-blue-500 bg-transparent px-5 text-lg outline-none focus:ring-2 focus:ring-pink-400">
                </div>

                <div class="flex w-full flex-col items-start gap-[5px]">
                    <label for="password" class="text-[22px] font-bold">Senha</label>
                    <input type="password" id="password" name="password" autocomplete="current-password" required
                        class="h-[50px] w-full rounded-[32px] border border-blue-500 bg-transparent px-5 text-lg outline-none focus:ring-2 focus:ring-pink-400">
                </div>

                <div class="flex w-full flex-col items-center justify-center gap-2.5 py-5">
                    <button type="submit"
                        class="h-[68px] w-[200px] cursor-pointer rounded-[48px] bg-pink-400 text-2xl text-white-200 transition hover:brightness-95 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Entrar
                    </button>
                    <a href="#" class="text-base font-light hover:underline">Cadastre-se</a>
                </div>
            </form>

            <a href="#" class="w-full text-right text-lg font-extralight hover:underline">Esqueceu a senha?</a>
        </section>

        <footer class="text-center text-lg font-extralight">
            © PoupaPig • <a href="#" class="hover:underline">Contato</a> • <a href="#" class="hover:underline">Política de Privacidade</a>
        </footer>
    </main>
</body>
</html>
