<?php
/** @var array $cell */
// Antes do include: $cell = ['name' => 'Ps5', 'description' => '', 'photo' => '', 'icon' => 'phone-vibrate', 'color' => 'sky']
// Monte o $cell de novo a cada linha, para não sobrar foto ou descrição da linha anterior
$categoryStyles ??= require __DIR__ . '/../../backend/config/categories.php';

// Ícone e cor vêm do banco: fora da lista, usa o primeiro (padrão)
$cellIcon = in_array($cell['icon'] ?? '', $categoryStyles['icons'], true) ? $cell['icon'] : $categoryStyles['icons'][0];
$cellColor = in_array($cell['color'] ?? '', $categoryStyles['colors'], true) ? $cell['color'] : $categoryStyles['colors'][0];
$cellPhoto = $cell['photo'] ?? '';
$cellDescription = $cell['description'] ?? '';
$iconMask = "url(../assets/icons/$cellIcon.svg) center / contain no-repeat";
?>
<div class="flex items-center gap-2.5">
    <?php if ($cellPhoto): ?>
        <img src="<?= htmlspecialchars($cellPhoto) ?>" alt="" width="40" height="40" class="size-10 shrink-0 rounded-xl object-cover">
    <?php else: ?>
        <!-- O SVG vira máscara: o formato vem do ícone e a cor vem da classe -->
        <!-- O Tailwind via CDN lê classes montadas na hora; se passar a compilar, pôr bg-*-200 e bg-*-400 numa safelist -->
        <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-<?= $cellColor ?>-200">
            <span class="size-8 bg-<?= $cellColor ?>-400" style="mask: <?= $iconMask ?>; -webkit-mask: <?= $iconMask ?>;"></span>
        </span>
    <?php endif; ?>

    <span class="flex flex-col">
        <span class="font-light"><?= htmlspecialchars($cell['name']) ?></span>
        <?php if ($cellDescription): ?>
            <span class="text-xs font-thin text-black-300"><?= htmlspecialchars($cellDescription) ?></span>
        <?php endif; ?>
    </span>
</div>
