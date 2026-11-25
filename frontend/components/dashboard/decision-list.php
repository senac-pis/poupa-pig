<?php
/** @var array $stats */
/** @var bool $isCurrentMonth */
/** @var string $selectedMonthName */
/** @var string $summaryUrl */
/** @var DateTime $today */
// Antes do include: $stats (da monthStats()), $isCurrentMonth, $selectedMonthName, $summaryUrl e $today
// Mostra as 4 decisões mais novas do mês; "ver tudo" abre o resumo do mês
$shownDecisions = array_slice($stats['decided'], 0, 4);
$shortMonths = ['jan', 'fev', 'mar', 'abr', 'mai', 'jun', 'jul', 'ago', 'set', 'out', 'nov', 'dez'];
$yesterday = (clone $today)->modify('-1 day')->format('Y-m-d');
$decisionIcons = [
    'gave_up' => ['icon' => 'check-small', 'class' => 'bg-green-100', 'iconClass' => 'bg-green-500'],
    'bought' => ['icon' => 'shopping-cart', 'class' => 'bg-secondary-100', 'iconClass' => 'bg-secondary-strong'],
];
?>
<section aria-labelledby="decisions-title" class="flex flex-col gap-2 rounded-2xl border border-secondary-300 bg-white-100 p-5">
    <div class="flex items-center justify-between">
        <h2 id="decisions-title" class="text-xl font-bold"><?= $isCurrentMonth ? 'Decisões recentes' : "Decisões de $selectedMonthName" ?></h2>
        <a href="<?= htmlspecialchars($summaryUrl) ?>" class="text-sm font-bold text-secondary-strong hover:underline">ver tudo</a>
    </div>

    <?php if ($shownDecisions): ?>
        <ul class="flex flex-col divide-y divide-gray-100">
            <?php foreach ($shownDecisions as $item): ?>
                <?php
                $isGaveUp = $item['status'] === 'gave_up';
                $decisionIcon = $decisionIcons[$item['status']];
                $decisionMask = "url(../assets/icons/{$decisionIcon['icon']}.svg) center / contain no-repeat";
                $decidedDay = match ($item['decided_at']) {
                    $today->format('Y-m-d') => 'hoje',
                    $yesterday => 'ontem',
                    default => (int) substr($item['decided_at'], 8, 2) . ' ' . $shortMonths[(int) substr($item['decided_at'], 5, 2) - 1],
                };
                ?>
                <li class="flex items-center justify-between gap-3 py-2.5">
                    <div class="flex items-center gap-2.5">
                        <span class="flex size-10 shrink-0 items-center justify-center rounded-full <?= $decisionIcon['class'] ?>">
                            <span class="size-5 <?= $decisionIcon['iconClass'] ?>" style="mask: <?= $decisionMask ?>; -webkit-mask: <?= $decisionMask ?>;"></span>
                        </span>
                        <span class="flex flex-col">
                            <span class="font-light"><?= htmlspecialchars($item['name']) ?></span>
                            <span class="text-xs font-thin text-black-300"><?= $isGaveUp ? 'Desistiu' : 'Comprou' ?> · <?= $decidedDay ?></span>
                        </span>
                    </div>
                    <span class="text-base font-bold <?= $isGaveUp ? 'text-green-500' : '' ?>">
                        <?= $isGaveUp ? '+' : '' ?>R$ <?= number_format($item['price'], 2, ',', '.') ?>
                    </span>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p class="py-6 text-center text-sm">Nenhuma decisão neste mês</p>
    <?php endif; ?>
</section>
