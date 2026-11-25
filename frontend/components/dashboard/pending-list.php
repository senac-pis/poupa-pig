<?php
/** @var array $pendingItems */
/** @var DateTime $today */
// Antes do include: $pendingItems (pensando, já em ordem de prazo) e $today
// Mostra os 3 primeiros; o resto fica na tela de Itens
$shownPending = array_slice($pendingItems, 0, 3);
?>
<section aria-labelledby="pending-title" class="flex flex-col gap-2 rounded-2xl border border-secondary-300 bg-white-100 p-5">
    <div class="flex items-center justify-between">
        <h2 id="pending-title" class="text-xl font-bold">Decida em breve</h2>
        <a href="items.php" class="text-sm font-bold text-secondary-strong hover:underline">ver todos</a>
    </div>

    <?php if ($shownPending): ?>
        <ul class="flex flex-col divide-y divide-gray-100">
            <?php foreach ($shownPending as $item): ?>
                <?php
                $cell = [
                    'name' => $item['name'],
                    'description' => 'R$ ' . number_format($item['price'], 2, ',', '.'),
                    'icon' => $item['category_icon'],
                    'color' => $item['category_color'],
                ];

                // Prazo: até amanhã fica em vermelho, até 3 dias na cor principal, depois cinza
                $endDate = DateTime::createFromFormat('!Y-m-d', $item['end_date']);
                $daysLeft = $endDate ? (int) $today->diff($endDate)->format('%r%a') : 0;
                $deadline = match (true) {
                    $daysLeft < 0 => ['text' => 'prazo passou', 'class' => 'bg-red-100 text-red-500'],
                    $daysLeft === 0 => ['text' => 'vence hoje', 'class' => 'bg-red-100 text-red-500'],
                    $daysLeft === 1 => ['text' => 'vence amanhã', 'class' => 'bg-red-100 text-red-500'],
                    $daysLeft <= 3 => ['text' => "faltam $daysLeft dias", 'class' => 'bg-secondary-100 text-secondary-strong'],
                    default => ['text' => "faltam $daysLeft dias", 'class' => 'bg-gray-100 text-gray-400'],
                };
                ?>
                <li class="flex items-center gap-3 py-2.5">
                    <div class="min-w-0 flex-1"><?php include __DIR__ . '/../name-cell.php'; ?></div>
                    <span class="shrink-0 rounded-full px-2.5 py-0.5 text-xs font-bold <?= $deadline['class'] ?>"><?= $deadline['text'] ?></span>
                    <form method="post" class="flex shrink-0 gap-2">
                        <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                        <button type="submit" name="decision" value="bought"
                            class="cursor-pointer rounded-full border border-primary px-3 py-1 text-sm font-bold transition hover:bg-blue-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary">
                            Comprei
                        </button>
                        <button type="submit" name="decision" value="gave_up"
                            class="cursor-pointer rounded-full bg-secondary px-3 py-1 text-sm font-bold text-white-100 transition hover:brightness-90 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary">
                            Desisti
                        </button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p class="py-6 text-center text-sm">Nada esperando decisão</p>
    <?php endif; ?>
</section>
