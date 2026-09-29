<?php
/**
 * TEMA COMPARTILHADO
 *
 * Incluir dentro do <head> de todas as páginas:
 * <?php include __DIR__ . '/../components/theme.php'; ?>
 *
 * Carrega a fonte, o Tailwind CSS via CDN e o tema com as cores do Figma.
 */
?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Google+Sans+Flex:wght@200;300;400;700;800;900&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
<style type="text/tailwindcss">
    @theme {
        /* Paleta do Figma (coleção "color"): pink/400 no Figma = pink-400 aqui */
        --color-pink-100: #fce8eb;
        --color-pink-200: #f9d0d7;
        --color-pink-300: #f5adba;
        --color-pink-400: #f18a9c;
        --color-pink-500: #d97c8c;

        --color-blue-100: #d0d6df;
        --color-blue-200: #a2adc0;
        --color-blue-300: #7384a0;
        --color-blue-400: #455b81;
        --color-blue-500: #163261;
        --color-blue-alpha-500: #16326133;

        --color-sky-100: #daedff;
        --color-sky-200: #b5dbff;
        --color-sky-300: #7ec0ff;
        --color-sky-400: #46a5ff;
        --color-sky-500: #3f95e6;

        --color-green-100: #cef1cc;
        --color-green-200: #9ee499;
        --color-green-300: #55cf4d;
        --color-green-400: #0cbb00;
        --color-green-500: #0ba800;

        --color-red-100: #ffcccc;
        --color-red-200: #ff9999;
        --color-red-300: #ff4d4d;
        --color-red-400: #ff0000;
        --color-red-500: #e60000;

        --color-purple-100: #eee0ff;
        --color-purple-200: #ddc1ff;
        --color-purple-300: #c493ff;
        --color-purple-400: #aa65ff;
        --color-purple-500: #995be6;

        --color-gray-100: #e4e4e4;
        --color-gray-200: #bebebe;
        --color-gray-300: #969696;
        --color-gray-400: #6e6e6e;
        --color-gray-500: #464646;

        --color-black-100: #cccccc;
        --color-black-200: #999999;
        --color-black-300: #666666;
        --color-black-400: #333333;
        --color-black-500: #000000;

        --color-white-100: #ffffff;
        --color-white-200: #f2f2f2;
        --color-white-300: #e6e6e6;
        --color-white-400: #d9d9d9;
        --color-white-500: #cccccc;

        /* Cores principais da identidade visual */
        --color-primary: var(--color-blue-500);     /* textos, bordas e título */
        --color-secondary: var(--color-pink-400);   /* botão principal e "PIG" da logo */
        --color-background: var(--color-white-200); /* fundo e texto do botão */

        --font-sans: "Google Sans Flex", ui-sans-serif, system-ui, sans-serif;
        --shadow-card: 2px 2px 8px 0 rgba(9, 30, 66, 0.25);
    }
</style>
