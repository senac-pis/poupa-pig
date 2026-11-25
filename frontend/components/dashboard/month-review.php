<?php
/** @var array $stats */
/** @var array $previousStats */
/** @var string $selectedMonthName */
/** @var string $summaryUrl */
/** @var string[] $monthNames */
// Antes do include: $stats e $previousStats (da monthStats()), $selectedMonthName, $summaryUrl e $monthNames
// Aparece no lugar de "Decida em breve" quando o mês escolhido já passou
$gaveUpItems = array_filter($stats['created'], fn($item) => $item['status'] === 'gave_up');
usort($gaveUpItems, fn($a, $b) => $b['price'] <=> $a['price']);
$biggestGaveUp = $gaveUpItems[0] ?? null;

$gaveUpCount = $stats['counts']['gave_up'];
$boughtCount = $stats['counts']['bought'];
$previousName = monthName($monthNames, $previousStats['month']);
$percentDiff = $stats['percent'] - $previousStats['percent'];
$comparisonTitle = match (true) {
    $percentDiff > 0 => 'Porquinho mais cheio',
    $percentDiff < 0 => 'Porquinho mais vazio',
    default => 'Porquinho igual',
};
$comparisonMask = 'url(../assets/icons/graph-up-arrow.svg) center / contain no-repeat';
?>
<section aria-labelledby="review-title" class="flex flex-col gap-2 rounded-2xl border border-secondary-300 bg-white-100 p-5">
    <div class="flex items-center justify-between">
        <h2 id="review-title" class="text-xl font-bold">Resumo de <?= $selectedMonthName ?></h2>
        <a href="<?= htmlspecialchars($summaryUrl) ?>" class="text-sm font-bold text-secondary-strong hover:underline">ver detalhes</a>
    </div>

    <?php if ($stats['desired_count'] === 0): ?>
        <p class="py-6 text-center text-sm">Nenhum desejo em <?= $selectedMonthName ?></p>
    <?php else: ?>
        <p class="text-2xl font-bold">
            Você desistiu de <span class="text-green-500"><?= $gaveUpCount ?> dos <?= $stats['desired_count'] ?></span> desejos de <?= $selectedMonthName ?>
        </p>
        <p class="text-sm font-medium text-gray-400">
            <?= match (true) {
                $boughtCount === 0 => 'Nenhum virou compra. Tudo ficou no porquinho.',
                $boughtCount === 1 => 'Só 1 virou compra. O resto ficou no porquinho.',
                default => "$boughtCount viraram compra.",
            } ?>
        </p>

        <?php if ($biggestGaveUp): ?>
            <?php
            $cell = [
                'name' => $biggestGaveUp['name'],
                'description' => $biggestGaveUp['category_name'],
                'icon' => $biggestGaveUp['category_icon'],
                'color' => $biggestGaveUp['category_color'],
            ];
            ?>
            <h3 class="pt-2 text-xs font-bold uppercase text-gray-400">Maior desistência</h3>
            <div class="flex items-center justify-between gap-3">
                <?php include __DIR__ . '/../name-cell.php'; ?>
                <span class="text-base font-bold text-green-500">+R$ <?= number_format($biggestGaveUp['price'], 2, ',', '.') ?></span>
            </div>
        <?php endif; ?>

        <h3 class="border-t border-gray-100 pt-3 text-xs font-bold uppercase text-gray-400">Comparado a <?= $previousName ?></h3>
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-2.5">
                <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-green-100">
                    <span class="size-5 bg-green-500" style="mask: <?= $comparisonMask ?>; -webkit-mask: <?= $comparisonMask ?>;"></span>
                </span>
                <span class="flex flex-col">
                    <span class="font-light"><?= $comparisonTitle ?></span>
                    <span class="text-xs font-thin text-black-300"><?= ucfirst($previousName) ?> fechou em <?= $previousStats['percent'] ?>%</span>
                </span>
            </div>
        </div>
    <?php endif; ?>
</section>
