<?php
/**
 * COR PRINCIPAL DO USUÁRIO
 *
 * Incluir no <head> das telas logadas, logo depois do theme.php.
 * Aplica a cor escolhida em Configurações > Aparência antes da página aparecer.
 * A cor vem do usuário (users.accent_color), guardada na sessão pelo login e pelo
 * backend/controllers/update-accent-color.php. Sem cor salva, fica o rosa padrão.
 */
$categoryStyles ??= require __DIR__ . '/../../backend/config/categories.php';
require_once __DIR__ . '/../../backend/services/AccountService.php';

$accentColors = accentColorOptions();
$userAccent = $_SESSION['user']['accent_color'] ?? null;
?>
<script>
    const accentColors = <?= json_encode($accentColors) ?>;
    // null quando o usuário nunca escolheu uma cor
    const userAccentColor = <?= json_encode(in_array($userAccent, $accentColors, true) ? $userAccent : null) ?>;

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

    applyAccentColor(userAccentColor);
</script>
