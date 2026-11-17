<?php
/** @var array $stats */
/** @var string $selectedMonthName */
/** @var bool $isCurrentMonth */
// Antes do include: $stats (da monthStats()), $selectedMonthName e $isCurrentMonth
// A imagem é a do nível arredondado para baixo: 70% usa a pig-60.png
$piggyLevel = intdiv(min($stats['percent'], 100), 20) * 20;

// Selo de humor por faixa de %
if ($stats['percent'] >= 67) {
    $piggyMood = ['text' => 'Excelente! Continue assim.', 'image' => 'pig-smile.png', 'class' => 'bg-green-100 text-green-500'];
} elseif ($stats['percent'] >= 34) {
    $piggyMood = ['text' => 'Melhore! Dá para encher mais.', 'image' => 'pig-neutral.png', 'class' => 'bg-sky-100 text-sky-500'];
} else {
    $piggyMood = ['text' => 'Ruim. Segura mais um pouco.', 'image' => 'pig-sad.png', 'class' => 'bg-red-100 text-red-500'];
}
?>
<section aria-labelledby="piggy-title" class="flex items-center gap-8 rounded-2xl border border-secondary-300 bg-white-100 p-6">
    <img src="../assets/images/pig-<?= $piggyLevel ?>.png" alt="Porquinho <?= $piggyLevel ?>% cheio" width="200" height="200" class="size-[200px] shrink-0">

    <div class="flex min-w-0 flex-1 flex-col gap-2">
        <h2 id="piggy-title" class="text-base font-bold text-secondary-strong">Porquinho de <?= $selectedMonthName ?></h2>

        <?php if ($stats['desired_count'] === 0): ?>
            <p class="text-xl font-bold">Nenhum desejo neste mês</p>
            <p class="text-sm text-gray-400">Adicione um item para o porquinho começar a contar.</p>
        <?php else: ?>
            <p class="text-[40px] font-bold leading-tight">R$ <?= number_format($stats['saved'], 2, ',', '.') ?></p>
            <p class="text-base font-medium">que você quis, mas não gastou <?= $isCurrentMonth ? 'este mês' : "em $selectedMonthName" ?></p>

            <div aria-hidden="true" class="h-3 w-full rounded-full bg-secondary-100">
                <div class="h-full rounded-full bg-secondary" style="width: <?= min($stats['percent'], 100) ?>%"></div>
            </div>
            <p class="flex items-baseline gap-2">
                <span class="text-base font-bold"><?= $stats['percent'] ?>% cheio</span>
                <span class="text-sm text-gray-400">de R$ <?= number_format($stats['desired'], 2, ',', '.') ?> desejados</span>
            </p>

            <p class="flex w-fit items-center gap-2 rounded-full py-1 pl-2 pr-4 text-sm font-bold <?= $piggyMood['class'] ?>">
                <img src="../assets/images/<?= $piggyMood['image'] ?>" alt="" width="34" height="24" class="w-[34px]">
                <?= $piggyMood['text'] ?>
            </p>
            <p class="text-xs text-gray-400">Cada desejo novo esvazia um pouco o porquinho; cada desistência enche.</p>
        <?php endif; ?>
    </div>
</section>
