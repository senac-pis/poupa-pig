<?php
/** @var array $items */
/** @var array $stats */
/** @var string $selectedMonth */
/** @var string $currentMonth */
/** @var string[] $monthNames */
// Conteúdo do drawer "Resumo de <mês>"; antes do include: $items, $stats (da monthStats()),
// $selectedMonth, $currentMonth e $monthNames. As abas e a página vêm da URL (?status=gave_up&page=2)

// Gráfico mês a mês: 4 meses antes, o escolhido e o seguinte; no mês atual, os 5 anteriores e ele
$chartStart = (new DateTime($selectedMonth . '-01'))->modify($selectedMonth === $currentMonth ? '-5 months' : '-4 months');
$chartMonths = [];
for ($i = 0; $i < 6; $i++) {
    $chartMonth = (clone $chartStart)->modify("+$i months")->format('Y-m');
    $chartMonths[] = ['month' => $chartMonth, 'percent' => monthStats($items, $chartMonth)['percent']];
}
$shortMonths = ['jan', 'fev', 'mar', 'abr', 'mai', 'jun', 'jul', 'ago', 'set', 'out', 'nov', 'dez'];

// Desejos por categoria: soma por status dos itens criados no mês, da categoria com mais valor para a com menos
$categoryTotals = [];
foreach ($stats['created'] as $item) {
    $categoryName = $item['category_name'];
    $categoryTotals[$categoryName] ??= ['gave_up' => 0, 'bought' => 0, 'thinking' => 0];
    $categoryTotals[$categoryName][$item['status']] += $item['price'];
}
uasort($categoryTotals, fn($a, $b) => array_sum($b) <=> array_sum($a));
$biggestCategory = $categoryTotals ? max(array_map('array_sum', $categoryTotals)) : 0;
$segmentColors = ['gave_up' => 'bg-green-500', 'bought' => 'bg-gray-300', 'thinking' => 'bg-purple-300'];

// Abas por status: ?status=gave_up; qualquer outro valor mostra todos
$statusTabs = ['' => 'Todos', 'gave_up' => 'Desistiu', 'bought' => 'Comprou', 'thinking' => 'Pensando'];
$rawStatus = $_GET['status'] ?? '';
$selectedStatus = is_string($rawStatus) && isset($statusTabs[$rawStatus]) ? $rawStatus : '';
$tableItems = array_filter($stats['created'], fn($item) => $selectedStatus === '' || $item['status'] === $selectedStatus);
usort($tableItems, fn($a, $b) => strcmp($b['created_at'], $a['created_at']));

// Paginação: 8 itens por página
$perPage = 8;
$totalItems = count($tableItems);
$totalPages = max(1, (int) ceil($totalItems / $perPage));
$rawPage = $_GET['page'] ?? '1';
$currentPage = is_string($rawPage) && ctype_digit($rawPage) ? min(max((int) $rawPage, 1), $totalPages) : 1;
$pageItems = array_slice($tableItems, ($currentPage - 1) * $perPage, $perPage);
$shownItems = count($pageItems);
?>
<p class="text-base font-medium text-gray-400">
    <?= $stats['percent'] ?>% cheio · R$ <?= number_format($stats['saved'], 2, ',', '.') ?> economizados ·
    <?= $stats['counts']['gave_up'] ?> de <?= $stats['desired_count'] ?> desistências
</p>

<div class="grid grid-cols-2 gap-4">
    <section aria-labelledby="chart-months-title" class="flex flex-col gap-2 rounded-2xl border border-secondary-300 bg-white-100 p-4">
        <h3 id="chart-months-title" class="text-lg font-bold">Porquinho mês a mês</h3>
        <p class="text-xs text-gray-400">Clique em um mês para ver o resumo dele</p>

        <ul class="flex h-40 items-end justify-between gap-2 pt-2">
            <?php foreach ($chartMonths as $chartItem): ?>
                <?php
                $isSelectedBar = $chartItem['month'] === $selectedMonth;
                $isCurrentBar = $chartItem['month'] === $currentMonth;
                $barClass = $isSelectedBar ? 'bg-secondary' : 'bg-secondary-200';
                $barClass .= $isCurrentBar && !$isSelectedBar ? ' border-2 border-dashed border-secondary' : '';
                $barLabel = $shortMonths[(int) substr($chartItem['month'], 5, 2) - 1] . ($isCurrentBar ? ' · atual' : '');
                ?>
                <li class="h-full flex-1">
                    <a href="?<?= htmlspecialchars(http_build_query(['month' => $chartItem['month'], 'action' => 'summary'])) ?>"
                        <?= $isSelectedBar ? 'aria-current="true"' : '' ?>
                        class="group flex h-full flex-col items-center justify-end gap-1 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary">
                        <span class="text-xs font-bold <?= $isSelectedBar ? '' : 'text-gray-400' ?>"><?= $chartItem['percent'] ?>%</span>
                        <span class="w-full max-w-10 rounded-t-lg transition group-hover:brightness-95 <?= $barClass ?>" style="height: <?= max($chartItem['percent'], 2) ?>%"></span>
                        <span class="whitespace-nowrap text-xs <?= $isSelectedBar ? 'font-bold' : ($isCurrentBar ? 'text-secondary-strong' : 'text-gray-400') ?>"><?= $barLabel ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>

    <section aria-labelledby="chart-categories-title" class="flex flex-col gap-2 rounded-2xl border border-secondary-300 bg-white-100 p-4">
        <h3 id="chart-categories-title" class="text-lg font-bold">Desejos por categoria</h3>
        <p class="flex gap-3 text-xs text-gray-400">
            <span class="flex items-center gap-1"><span class="size-2 rounded-full bg-green-500"></span>Desistiu</span>
            <span class="flex items-center gap-1"><span class="size-2 rounded-full bg-gray-300"></span>Comprou</span>
            <span class="flex items-center gap-1"><span class="size-2 rounded-full bg-purple-300"></span>Pensando</span>
        </p>

        <?php if ($categoryTotals): ?>
            <ul class="flex flex-col gap-2 pt-1">
                <?php foreach ($categoryTotals as $categoryName => $categoryTotal): ?>
                    <li class="flex items-center gap-2 text-xs">
                        <span class="w-20 shrink-0 truncate"><?= htmlspecialchars($categoryName) ?></span>
                        <span aria-hidden="true" class="flex h-3 flex-1">
                            <!-- Cada pedaço é a fatia do status em relação à maior categoria -->
                            <?php foreach ($segmentColors as $segmentStatus => $segmentColor): ?>
                                <?php if ($categoryTotal[$segmentStatus] > 0): ?>
                                    <span class="h-full first:rounded-l-full last:rounded-r-full <?= $segmentColor ?>" style="width: <?= $categoryTotal[$segmentStatus] / $biggestCategory * 100 ?>%"></span>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </span>
                        <span class="w-16 shrink-0 text-right font-bold">R$ <?= number_format(array_sum($categoryTotal), 0, ',', '.') ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="py-6 text-center text-sm">Nenhum desejo neste mês</p>
        <?php endif; ?>
    </section>
</div>

<nav aria-label="Filtrar por status" class="flex gap-2">
    <?php foreach ($statusTabs as $tabStatus => $tabLabel): ?>
        <?php
        $isActiveTab = $tabStatus === $selectedStatus;
        $tabCount = $tabStatus === '' ? $stats['desired_count'] : $stats['counts'][$tabStatus];
        $tabUrl = '?' . http_build_query(array_filter(['month' => $selectedMonth, 'action' => 'summary', 'status' => $tabStatus]));
        ?>
        <a href="<?= htmlspecialchars($tabUrl) ?>" <?= $isActiveTab ? 'aria-current="true"' : '' ?>
            class="rounded-full px-4 py-1.5 text-sm font-bold transition focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary <?= $isActiveTab ? 'bg-secondary text-white-100' : 'border border-secondary-200 bg-white-100 hover:bg-secondary-100' ?>">
            <?= $tabLabel ?> (<?= $tabCount ?>)
        </a>
    <?php endforeach; ?>
</nav>

<?php
$columns = ['Item', 'Categoria', 'Valor', 'Adicionado', 'Decidido', 'Status'];
$rows = $pageItems;
$rowTemplate = __DIR__ . '/../rows/decision-row.php';
$emptyMessage = 'Nenhum item neste filtro';
include __DIR__ . '/../table.php';

include __DIR__ . '/../pagination.php';
?>
