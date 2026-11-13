<?php
/**
 * LANDING PAGE
 *
 * Layout implementado a partir do Figma (Landing page).
 * Apresenta o Poupa Pig e direciona para o cadastro e o login.
 */
?>
<!doctype html>
<html lang="pt-BR" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Deu vontade de comprar? Cadastre o desejo, espere a quarentena passar e só depois decida.">
    <title>Poupa Pig</title>
    <?php include __DIR__ . '/components/theme.php'; ?>
</head>
<body class="min-h-screen bg-primary font-sans text-primary">
    <header class="flex flex-wrap items-center justify-between gap-4 border-b border-gray-100 bg-white-100 px-4 py-5 sm:px-10 lg:px-20">
        <a href="index.php" class="flex items-center gap-2.5">
            <div class="relative size-11 shrink-0 overflow-hidden">
                <img src="assets/logos/logo.png" alt="" class="absolute left-[-17.99%] top-[-12.36%] h-[150.43%] w-[140.51%] max-w-none">
            </div>
            <span class="text-[26px] font-bold">Poupa <span class="text-secondary">pig</span></span>
        </a>

        <nav class="flex items-center gap-2.5 text-base font-bold">
            <a href="#como-funciona" class="hidden hover:underline sm:inline">Como funciona</a>
            <a href="pages/login.php" class="rounded-full border border-primary px-5 py-3 transition hover:bg-pink-100">Entrar</a>
            <a href="pages/register.php" class="rounded-full bg-secondary px-[22px] py-3 text-white-100 transition hover:brightness-95">Criar conta</a>
        </nav>
    </header>

    <main>
        <section class="relative overflow-hidden bg-white-100 px-4 pb-[88px] pt-[72px] sm:px-10 lg:bg-transparent lg:px-20">
            <div aria-hidden="true" class="pointer-events-none absolute inset-0 hidden lg:block">
                <video src="assets/images/hero.mp4" autoplay muted loop playsinline class="size-full object-cover object-[center_70%]"></video>
                <div class="absolute inset-0 bg-linear-to-r from-blue-alpha-500 from-10% to-transparent"></div>
            </div>

            <div class="relative grid gap-x-12 lg:grid-cols-2">
                <div class="flex max-w-[520px] flex-col items-start gap-6">
                    <span class="rounded-2xl bg-secondary px-3.5 py-1.5 text-sm text-white-100">Uma pausa antes do "comprar agora"</span>
                    <h1 class="text-4xl font-black sm:text-[60px]">
                        Deu vontade de comprar? Deixa o <span class="text-secondary">porquinho</span> segurar por uns dias.
                    </h1>
                    <p class="text-lg sm:text-xl lg:text-white-100">
                        Cadastre o que você está com vontade de comprar, espere a quarentena passar e só depois decida. Menos arrependimento, mais dinheiro guardado.
                    </p>
                    <div class="flex flex-wrap items-center gap-4 text-lg">
                        <a href="pages/register.php" class="rounded-full bg-secondary px-7 py-4 text-white-100 transition hover:brightness-95">Criar minha conta</a>
                        <a href="#como-funciona" class="rounded-full border border-primary px-7 py-4 transition hover:bg-pink-100 lg:border-white-100 lg:text-white-100 lg:hover:bg-white-100/10">Ver como funciona</a>
                    </div>
                </div>
            </div>
        </section>

        <section class="flex flex-col gap-10 border-y border-gray-100 bg-white-100 px-4 py-16 sm:px-10 lg:p-20">
            <div class="flex max-w-[720px] flex-col gap-3">
                <h2 class="text-3xl font-extrabold sm:text-[44px]">Comprou, chegou… e se arrependeu?</h2>
                <p class="text-lg">Se isso parece familiar, você não está sozinho. A compra por impulso quase nunca é sobre o produto.</p>
            </div>

            <div class="grid gap-6 md:grid-cols-3">
                <article class="flex flex-col gap-3 rounded-[20px] bg-pink-100 p-7">
                    <img src="assets/icons/phone.svg" alt="" width="36" height="36">
                    <h3 class="text-xl font-bold">A vontade bate na hora</h3>
                    <p>No feed, no marketplace, na vitrine. Um clique e pronto.</p>
                </article>
                <article class="flex flex-col gap-3 rounded-[20px] bg-pink-100 p-7">
                    <img src="assets/icons/box-seam.svg" alt="" width="36" height="36">
                    <h3 class="text-xl font-bold">O produto chega sem graça</h3>
                    <p>Quando a caixa chega, a vontade já passou e o item vai pra gaveta.</p>
                </article>
                <article class="flex flex-col gap-3 rounded-[20px] bg-pink-100 p-7">
                    <img src="assets/icons/question-circle.svg" alt="" width="36" height="36">
                    <h3 class="text-xl font-bold">O dinheiro "escorre"</h3>
                    <p>No fim do mês, fica difícil saber quanto foi por impulso e quanto foi necessidade.</p>
                </article>
            </div>
        </section>

        <section id="como-funciona" class="flex flex-col gap-12 px-4 py-[88px] sm:px-10 lg:px-20">
            <div class="flex flex-col items-center gap-3 text-center">
                <h2 class="text-3xl font-extrabold text-secondary sm:text-[44px]">Como funciona</h2>
                <p class="text-lg text-white-100">Três passos. Nada de planilha.</p>
            </div>

            <ol class="grid gap-8 md:grid-cols-3">
                <li class="flex flex-col items-start gap-3.5 rounded-3xl bg-white-100 p-8">
                    <span class="rounded-full bg-primary px-[18px] py-[9px] text-2xl font-extrabold text-white-100">1</span>
                    <h3 class="text-[22px] font-bold">Cadastre o desejo</h3>
                    <p>Nome, preço, categoria e o motivo pelo qual você quer o item. Em vez de comprar, você registra.</p>
                </li>
                <li class="flex flex-col items-start gap-3.5 rounded-3xl bg-white-100 p-8">
                    <span class="rounded-full bg-primary px-[18px] py-[9px] text-2xl font-extrabold text-white-100">2</span>
                    <h3 class="text-[22px] font-bold">Espere a quarentena</h3>
                    <p>Você escolhe quantos dias o item fica "de molho". A pressa passa, a decisão fica.</p>
                </li>
                <li class="flex flex-col items-start gap-3.5 rounded-3xl bg-white-100 p-8">
                    <span class="rounded-full bg-primary px-[18px] py-[9px] text-2xl font-extrabold text-white-100">3</span>
                    <h3 class="text-[22px] font-bold">Decida com calma</h3>
                    <p>Quando o prazo vence, o Poupa Pig te mostra. Aí é com você: comprei, desisti ou ainda pensando.</p>
                </li>
            </ol>
        </section>

        <section class="flex justify-center px-4 pb-20 sm:px-10">
            <div class="flex flex-col items-start gap-10 rounded-[32px] bg-pink-100 p-8 sm:p-16 lg:flex-row lg:items-center">
                <div class="flex max-w-[660px] flex-col gap-3 lg:w-[660px]">
                    <h2 class="text-3xl font-extrabold sm:text-[40px]">Da próxima vez que der vontade, deixa o porquinho segurar.</h2>
                    <p class="text-lg">Crie sua conta e cadastre seu primeiro desejo.</p>
                </div>
                <div class="flex shrink-0 flex-col items-center gap-3">
                    <a href="pages/register.php" class="rounded-full bg-primary px-8 py-4 text-lg font-extrabold text-white-100 transition hover:brightness-125">Criar minha conta</a>
                    <a href="pages/login.php" class="font-bold underline">Já tenho conta — Entrar</a>
                </div>
            </div>
        </section>
    </main>

    <footer class="flex flex-col items-center gap-3 border-t border-gray-100 bg-white-100 px-4 py-8 text-center sm:px-10 lg:flex-row lg:justify-between lg:px-20">
        <p class="text-[15px]">Poupa <span class="text-secondary">Pig</span> · Projeto Integrador — Senac</p>
        <p>Kaio · Levi · Lucas</p>
        <a href="https://github.com/senac-pis/poupa-pig" target="_blank" rel="noopener" class="underline">GitHub do projeto</a>
    </footer>
</body>
</html>
