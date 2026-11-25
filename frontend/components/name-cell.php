<?php
/** @var array $cell */
// Antes do include: $cell = ['name' => 'Ps5', 'description' => '', 'photo' => '', 'icon' => 'smartphone', 'color' => 'sky-200']
// Monte o $cell de novo a cada linha, para não sobrar foto ou descrição da linha anterior
$categoryStyles ??= require __DIR__ . '/../../backend/config/categories.php';

// Ícone e cor vêm do banco: fora da lista, usa o primeiro (padrão)
$cellIcon = in_array($cell['icon'] ?? '', $categoryStyles['icons'], true) ? $cell['icon'] : $categoryStyles['icons'][0];
$cellColor = in_array($cell['color'] ?? '', $categoryStyles['colors'], true) ? $cell['color'] : $categoryStyles['colors'][0];
// A cor é o fundo (ex.: purple-400); o tom do ícone sai do icon_tones (400 = branco)
[$cellFamily, $cellTone] = explode('-', $cellColor);
$cellIconTone = $categoryStyles['icon_tones'][$cellTone];
$cellIconClass = $cellIconTone === 'white' ? 'bg-white-100' : "bg-$cellFamily-$cellIconTone";
$cellPhoto = $cell['photo'] ?? '';
$cellDescription = $cell['description'] ?? '';
$iconMask = "url(../assets/icons/$cellIcon.svg) center / contain no-repeat";
?>
<div class="flex items-center gap-2.5">
    <?php if ($cellPhoto): ?>
        <img src="<?= htmlspecialchars($cellPhoto) ?>" alt="" width="40" height="40" class="size-10 shrink-0 rounded-xl object-cover">
    <?php else: ?>
        <!-- O SVG vira máscara: o formato vem do ícone e a cor vem da classe -->
        <!-- O Tailwind via CDN lê classes montadas na hora; se passar a compilar, pôr as bg-* das cores numa safelist -->
        <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-<?= $cellColor ?>">
            <span class="size-8 <?= $cellIconClass ?>" style="mask: <?= $iconMask ?>; -webkit-mask: <?= $iconMask ?>;"></span>
        </span>
    <?php endif; ?>

    <span class="flex min-w-0 flex-col">
        <!-- Nome longo corta com "..." e mostra o nome inteiro ao passar o mouse -->
        <span class="max-w-60 truncate font-light" title="<?= htmlspecialchars($cell['name']) ?>"><?= htmlspecialchars($cell['name']) ?></span>
        <?php if ($cellDescription): ?>
            <span class="text-xs font-thin text-black-300"><?= htmlspecialchars($cellDescription) ?></span>
        <?php endif; ?>
    </span>
</div>
