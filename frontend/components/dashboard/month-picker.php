<?php
/** @var string $selectedMonth */
/** @var string $currentMonth */
/** @var string[] $monthNames */
// Antes do include: $selectedMonth e $currentMonth ('Y-m', já validados) e $monthNames
// Tudo é link para ?month=...; os dropdowns usam <details>, então não precisa de JS
$pickerYear = (int) substr($selectedMonth, 0, 4);
$pickerMonth = (int) substr($selectedMonth, 5, 2);
$pickerCurrentYear = (int) substr($currentMonth, 0, 4);
$pickerPrevious = (new DateTime($selectedMonth . '-01'))->modify('-1 month')->format('Y-m');
$pickerNext = (new DateTime($selectedMonth . '-01'))->modify('+1 month')->format('Y-m');

$pickerNavClass = 'rounded-full p-1 hover:bg-secondary-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary';
$pickerToggleClass = 'flex cursor-pointer list-none items-center justify-between gap-1.5 rounded-lg border border-gray-100 bg-white-100 px-3 py-1 text-base font-medium focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary [&::-webkit-details-marker]:hidden';
$pickerListClass = 'absolute right-0 top-full z-10 mt-1 flex max-h-44 flex-col overflow-y-auto rounded-lg bg-white-100 py-1 shadow-card';
$pickerOptionClass = 'flex items-center justify-between gap-2 px-3 py-0.5 text-sm hover:bg-secondary-100 focus:outline-none focus-visible:bg-secondary-100';
?>
<nav aria-label="Escolher mês" class="flex shrink-0 items-center gap-2">
    <?php if ($selectedMonth > '2000-01'): ?>
        <a href="?month=<?= $pickerPrevious ?>" aria-label="Mês anterior" title="Mês anterior" class="<?= $pickerNavClass ?>">
            <img src="../assets/icons/chevron-down.svg" alt="" width="20" height="20" class="size-5 rotate-90">
        </a>
    <?php else: ?>
        <span aria-hidden="true" class="p-1 opacity-30"><img src="../assets/icons/chevron-down.svg" alt="" width="20" height="20" class="size-5 rotate-90"></span>
    <?php endif; ?>

    <details class="relative">
        <summary class="<?= $pickerToggleClass ?> w-32">
            <?= $monthNames[$pickerMonth - 1] ?>
            <img src="../assets/icons/chevron-down.svg" alt="" width="12" height="12" class="size-3">
        </summary>
        <div class="<?= $pickerListClass ?> w-32">
            <?php foreach ($monthNames as $monthIndex => $monthName): ?>
                <?php $optionMonth = sprintf('%04d-%02d', $pickerYear, $monthIndex + 1); ?>
                <?php if ($optionMonth > $currentMonth): ?>
                    <span class="px-3 py-0.5 text-sm opacity-30"><?= $monthName ?></span>
                <?php else: ?>
                    <a href="?month=<?= $optionMonth ?>" <?= $optionMonth === $selectedMonth ? 'aria-current="true"' : '' ?> class="<?= $pickerOptionClass ?>">
                        <?= $monthName ?>
                        <?php if ($optionMonth === $selectedMonth): ?>
                            <span aria-hidden="true" class="text-secondary">✓</span>
                        <?php endif; ?>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </details>

    <details class="relative">
        <summary class="<?= $pickerToggleClass ?> w-24">
            <?= $pickerYear ?>
            <img src="../assets/icons/chevron-down.svg" alt="" width="12" height="12" class="size-3">
        </summary>
        <div class="<?= $pickerListClass ?> w-24">
            <?php for ($optionYear = $pickerCurrentYear; $optionYear >= 2000; $optionYear--): ?>
                <?php
                // Mantém o mês ao trocar de ano; se cair no futuro, vai para o mês atual
                $optionMonth = min(sprintf('%04d-%02d', $optionYear, $pickerMonth), $currentMonth);
                ?>
                <a href="?month=<?= $optionMonth ?>" <?= $optionYear === $pickerYear ? 'aria-current="true"' : '' ?> class="<?= $pickerOptionClass ?>">
                    <?= $optionYear ?>
                    <?php if ($optionYear === $pickerYear): ?>
                        <span aria-hidden="true" class="text-secondary">✓</span>
                    <?php endif; ?>
                </a>
            <?php endfor; ?>
        </div>
    </details>

    <?php if ($selectedMonth < $currentMonth): ?>
        <a href="?month=<?= $pickerNext ?>" aria-label="Próximo mês" title="Próximo mês" class="<?= $pickerNavClass ?>">
            <img src="../assets/icons/chevron-down.svg" alt="" width="20" height="20" class="size-5 -rotate-90">
        </a>
    <?php else: ?>
        <span aria-hidden="true" class="p-1 opacity-30"><img src="../assets/icons/chevron-down.svg" alt="" width="20" height="20" class="size-5 -rotate-90"></span>
    <?php endif; ?>
</nav>
