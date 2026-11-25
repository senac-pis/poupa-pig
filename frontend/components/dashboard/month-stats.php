<?php
/** @var array $stats */
/** @var string $selectedMonthName */
/** @var bool $isCurrentMonth */
// Antes do include: $stats (da monthStats()), $selectedMonthName e $isCurrentMonth
$statsSuffix = $isCurrentMonth ? 'no mês' : "em $selectedMonthName";
$thinkingCount = $stats['counts']['thinking'];
$boughtCount = $stats['counts']['bought'];

$monthCards = [
    [
        'label' => "Desejado $statsSuffix",
        'value' => $stats['desired'],
        'caption' => $stats['desired_count'] === 1 ? '1 item adicionado' : "{$stats['desired_count']} itens adicionados",
        'icon' => 'heart',
        'color' => 'sky',
    ],
    [
        'label' => $isCurrentMonth ? 'Pensando' : 'Ainda pensando',
        'value' => $stats['thinking'],
        'caption' => match (true) {
            $thinkingCount === 0 => 'nenhum item esperando',
            $thinkingCount === 1 => '1 item esperando decisão',
            default => "$thinkingCount itens esperando decisão",
        },
        'icon' => 'hourglass',
        'color' => 'purple',
    ],
    [
        'label' => "Comprado $statsSuffix",
        'value' => $stats['bought'],
        'caption' => match (true) {
            $boughtCount === 0 => 'nenhuma compra',
            $boughtCount === 1 => '1 compra',
            default => "$boughtCount compras",
        },
        'icon' => 'shopping-cart',
        'color' => 'pink',
    ],
];
?>
<section aria-label="Números do mês" class="flex flex-col gap-4">
    <?php foreach ($monthCards as $monthCard): ?>
        <?php $cardMask = "url(../assets/icons/{$monthCard['icon']}.svg) center / contain no-repeat"; ?>
        <div class="flex flex-1 items-center gap-3 rounded-2xl border border-secondary-300 bg-white-100 px-4 py-3">
            <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-<?= $monthCard['color'] ?>-100">
                <span class="size-5 bg-<?= $monthCard['color'] ?>-500" style="mask: <?= $cardMask ?>; -webkit-mask: <?= $cardMask ?>;"></span>
            </span>
            <span class="flex min-w-0 flex-col">
                <span class="text-sm font-medium text-gray-400"><?= $monthCard['label'] ?></span>
                <span class="text-2xl font-bold">R$ <?= number_format($monthCard['value'], 2, ',', '.') ?></span>
                <span class="text-xs text-gray-400"><?= $monthCard['caption'] ?></span>
            </span>
        </div>
    <?php endforeach; ?>
</section>
