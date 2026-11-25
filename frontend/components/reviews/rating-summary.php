<?php
/** @var array $ratingSummary */
// Antes do include: $ratingSummary = ['review_count' => 10, 'average' => 3.6, 'distribution' => [5 => 3, 4 => 3, 3 => 2, 2 => 1, 1 => 1]]
// distribution: quantas reviews deram cada nota; a barra é a porcentagem do total
$summaryCount = max((int) $ratingSummary['review_count'], 0);
$summaryAverage = min(max((float) $ratingSummary['average'], 0), 5);
$rating = ['value' => $summaryAverage, 'size' => 'size-7'];

// Cada nota tem um tom da cor principal: quanto maior a nota, mais forte a cor (5 = 500, 1 = 100)
$noteColors = [5 => 'bg-secondary-500', 4 => 'bg-secondary-400', 3 => 'bg-secondary-300', 2 => 'bg-secondary-200', 1 => 'bg-secondary-100'];
?>
<section aria-label="Resumo das avaliações" class="grid grid-cols-3 items-center border-b border-black-100 pb-4">
    <div class="flex flex-col items-center gap-3">
        <span class="text-sm font-bold">Total de avaliações</span>
        <span class="text-[28px] font-light"><?= $summaryCount ?></span>
    </div>

    <div class="flex flex-col items-center gap-3">
        <span class="text-sm font-bold">Média das avaliações</span>
        <span class="flex items-center gap-2.5">
            <span class="text-[28px] font-light"><?= number_format($summaryAverage, 1, ',', '') ?></span>
            <?php include __DIR__ . '/../rating.php'; ?>
        </span>
    </div>

    <ul class="flex flex-col gap-1.5 justify-self-center text-sm">
        <?php for ($note = 5; $note >= 1; $note--): ?>
            <?php
            $noteCount = max((int) ($ratingSummary['distribution'][$note] ?? 0), 0);
            $notePercent = $summaryCount ? min(round($noteCount / $summaryCount * 100), 100) : 0;
            ?>
            <li class="flex items-center gap-1.5">
                <span class="sr-only"><?= $note ?> pillars: <?= $noteCount ?> de <?= $summaryCount ?> reviews</span>
                <span aria-hidden="true" class="w-2 text-right"><?= $note ?></span>
                <img src="../assets/icons/pillar.svg" alt="" class="size-3">
                <div aria-hidden="true" class="h-3 w-32 rounded-full bg-gray-100">
                    <div class="h-full rounded-full <?= $noteColors[$note] ?>" style="width: <?= $notePercent ?>%"></div>
                </div>
            </li>
        <?php endfor; ?>
    </ul>
</section>
