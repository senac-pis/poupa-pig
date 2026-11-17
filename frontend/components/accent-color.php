<?php
/**
 * COR PRINCIPAL DO USUÁRIO
 *
 * Incluir no <head> das telas logadas, logo depois do theme.php.
 * Aplica a cor escolhida em Configurações > Aparência antes da página aparecer.
 * Por enquanto a cor fica salva no navegador; com o backend ela passa a vir do usuário.
 */
$categoryStyles ??= require __DIR__ . '/../../backend/config/categories.php';

// Mesma paleta das categorias, só os tons 300 a 500 (os mais claros não dão contraste com texto branco)
$accentColors = [];
foreach ($categoryStyles['color_groups'] as $accentLabel => $accentFamily) {
    foreach (['300', '400', '500'] as $accentTone) {
        $accentColors[] = "$accentFamily-$accentTone";
    }
}
?>
<script>
    const accentColors = <?= json_encode($accentColors) ?>;

    // Troca a secondary e os tons 100 a 500 da família pela cor escolhida (ex.: green-400)
    function applyAccentColor(color) {
        if (!accentColors.includes(color)) {
            return;
        }
        const [family, tone] = color.split('-');
        const root = document.documentElement.style;
        root.setProperty('--color-secondary', `var(--color-${color})`);
        ['100', '200', '300', '400', '500'].forEach((shade) => {
            root.setProperty(`--color-secondary-${shade}`, `var(--color-${family}-${shade})`);
        });
        const strongTone = tone === '300' ? '500' : tone;
        root.setProperty('--color-secondary-strong', `var(--color-${family}-${strongTone})`);
    }

    // O localStorage pode falhar (aba anônima, bloqueio); aí fica o rosa padrão
    try {
        applyAccentColor(localStorage.getItem('accent-color'));
    } catch {}
</script>
