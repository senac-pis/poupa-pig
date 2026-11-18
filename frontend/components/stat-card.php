<?php
/** @var array $statCard */
// Antes do include: $statCard = ['label' => 'Total de categorias', 'value' => '2', 'icon' => 'tags', 'color' => 'pink']
// Cores aceitas: pink, sky e green; o ícone é um SVG de assets/icons, desenhado como máscara
$statCardColors = [
    'pink' => ['card' => 'bg-pink-100', 'icon' => 'bg-pink-200'],
    'sky' => ['card' => 'bg-sky-100', 'icon' => 'bg-sky-200'],
    'green' => ['card' => 'bg-green-100', 'icon' => 'bg-green-200'],
];
$statCardColor = $statCardColors[$statCard['color']] ?? $statCardColors['pink'];
$statCardMask = 'url(../assets/icons/' . $statCard['icon'] . '.svg) center / contain no-repeat';
?>
<div class="relative flex h-28 min-w-0 flex-1 flex-col gap-1 overflow-hidden rounded-2xl p-4 <?= $statCardColor['card'] ?>">
    <span class="relative text-lg font-medium"><?= htmlspecialchars($statCard['label']) ?></span>
    <span class="relative truncate text-[28px] font-thin"><?= htmlspecialchars($statCard['value']) ?></span>
    <!-- Ícone decorativo, cortado no canto como no Figma -->
    <span aria-hidden="true" class="absolute -right-3 top-9 size-24 <?= $statCardColor['icon'] ?>"
        style="mask: <?= $statCardMask ?>; -webkit-mask: <?= $statCardMask ?>;"></span>
</div>
