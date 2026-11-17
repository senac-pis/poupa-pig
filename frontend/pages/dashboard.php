<?php
/**
 * TELA DE DASHBOARD
 *
 * Layout implementado a partir do Figma (Desktop - Light mode Dashboard, mês anterior e resumo do mês).
 *
 * Contrato com o backend:
 * - $items é fixo por enquanto; o backend troca pela consulta aos itens do usuário logado,
 *   com o mesmo formato de components/rows/item-row.php mais 'created_at' e 'decided_at' (Y-m-d ou null);
 * - todos os números da tela saem de $items pela monthStats(), então trocar a lista já basta;
 * - Comprei e Desisti enviam POST com id e decision ('bought' ou 'gave_up'); por enquanto a própria
 *   página guarda a decisão na sessão, depois o form passa a apontar para o controller do backend.
 */

session_start();

$activePage = 'dashboard';

$today = new DateTime('today');
$currentMonth = $today->format('Y-m');
$monthNames = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];

// Dados fixos até o backend existir (de junho a novembro de 2026)
$items = [
    // Novembro: 8 desejos, 5 desistências, 1 compra e 2 pensando (70% cheio)
    ['id' => 1, 'name' => 'Ventilador', 'price' => 159.9, 'created_at' => '2026-11-02', 'end_date' => '2026-11-17', 'decided_at' => '2026-11-17', 'status' => 'gave_up', 'category_name' => 'Casa', 'category_icon' => 'kitchen', 'category_color' => 'green-200'],
    ['id' => 2, 'name' => 'Monitor', 'price' => 350, 'created_at' => '2026-11-01', 'end_date' => '2026-11-16', 'decided_at' => '2026-11-16', 'status' => 'bought', 'category_name' => 'Eletrônico', 'category_icon' => 'tv', 'category_color' => 'sky-200'],
    ['id' => 3, 'name' => 'Jaqueta', 'price' => 289, 'created_at' => '2026-11-03', 'end_date' => '2026-11-12', 'decided_at' => '2026-11-12', 'status' => 'gave_up', 'category_name' => 'Moda', 'category_icon' => 'checkroom', 'category_color' => 'purple-200'],
    ['id' => 4, 'name' => 'Smartwatch', 'price' => 899, 'created_at' => '2026-11-01', 'end_date' => '2026-11-10', 'decided_at' => '2026-11-10', 'status' => 'gave_up', 'category_name' => 'Eletrônico', 'category_icon' => 'watch', 'category_color' => 'sky-200'],
    ['id' => 5, 'name' => 'Caixa de som', 'price' => 249, 'created_at' => '2026-11-04', 'end_date' => '2026-11-09', 'decided_at' => '2026-11-09', 'status' => 'gave_up', 'category_name' => 'Eletrônico', 'category_icon' => 'headphones', 'category_color' => 'sky-200'],
    ['id' => 6, 'name' => 'Luminária', 'price' => 139.1, 'created_at' => '2026-11-05', 'end_date' => '2026-11-08', 'decided_at' => '2026-11-08', 'status' => 'gave_up', 'category_name' => 'Casa', 'category_icon' => 'table-lamp', 'category_color' => 'green-200'],
    ['id' => 7, 'name' => 'Fone Bluetooth', 'price' => 249, 'created_at' => '2026-11-10', 'end_date' => '2026-11-18', 'decided_at' => null, 'status' => 'thinking', 'category_name' => 'Eletrônico', 'category_icon' => 'earbuds', 'category_color' => 'sky-200'],
    ['id' => 8, 'name' => 'Tênis de corrida', 'price' => 145, 'created_at' => '2026-11-11', 'end_date' => '2026-11-20', 'decided_at' => null, 'status' => 'thinking', 'category_name' => 'Moda', 'category_icon' => 'apparel', 'category_color' => 'purple-200'],
    // Outubro: 6 desejos, 5 desistências e 1 compra (82% cheio)
    ['id' => 9, 'name' => 'Cadeira gamer', 'price' => 749, 'created_at' => '2026-10-02', 'end_date' => '2026-10-28', 'decided_at' => '2026-10-28', 'status' => 'gave_up', 'category_name' => 'Casa', 'category_icon' => 'chair', 'category_color' => 'green-200'],
    ['id' => 10, 'name' => 'Mochila', 'price' => 351, 'created_at' => '2026-10-05', 'end_date' => '2026-10-21', 'decided_at' => '2026-10-21', 'status' => 'bought', 'category_name' => 'Moda', 'category_icon' => 'backpack', 'category_color' => 'purple-200'],
    ['id' => 11, 'name' => 'Livro', 'price' => 90, 'created_at' => '2026-10-10', 'end_date' => '2026-10-18', 'decided_at' => '2026-10-18', 'status' => 'gave_up', 'category_name' => 'Casa', 'category_icon' => 'weekend', 'category_color' => 'green-200'],
    ['id' => 12, 'name' => 'Perfume', 'price' => 320, 'created_at' => '2026-10-01', 'end_date' => '2026-10-15', 'decided_at' => '2026-10-15', 'status' => 'gave_up', 'category_name' => 'Beleza', 'category_icon' => 'diamond', 'category_color' => 'pink-200'],
    ['id' => 13, 'name' => 'Tênis', 'price' => 280, 'created_at' => '2026-10-03', 'end_date' => '2026-10-09', 'decided_at' => '2026-10-09', 'status' => 'gave_up', 'category_name' => 'Moda', 'category_icon' => 'apparel', 'category_color' => 'purple-200'],
    ['id' => 14, 'name' => 'Fone', 'price' => 160, 'created_at' => '2026-10-06', 'end_date' => '2026-10-08', 'decided_at' => '2026-10-08', 'status' => 'gave_up', 'category_name' => 'Eletrônico', 'category_icon' => 'headphones', 'category_color' => 'sky-200'],
    // Setembro a junho: poucos itens, só para o gráfico e a navegação terem histórico
    ['id' => 15, 'name' => 'Bicicleta', 'price' => 1200, 'created_at' => '2026-09-04', 'end_date' => '2026-09-20', 'decided_at' => '2026-09-20', 'status' => 'gave_up', 'category_name' => 'Esporte', 'category_icon' => 'sports-esports', 'category_color' => 'red-200'],
    ['id' => 16, 'name' => 'Relógio', 'price' => 220, 'created_at' => '2026-09-08', 'end_date' => '2026-09-12', 'decided_at' => '2026-09-12', 'status' => 'bought', 'category_name' => 'Moda', 'category_icon' => 'watch', 'category_color' => 'purple-200'],
    ['id' => 17, 'name' => 'Controle extra', 'price' => 389.9, 'created_at' => '2026-09-25', 'end_date' => '2026-11-23', 'decided_at' => null, 'status' => 'thinking', 'category_name' => 'Eletrônico', 'category_icon' => 'sports-esports', 'category_color' => 'sky-200'],
    ['id' => 18, 'name' => 'Celular', 'price' => 2000, 'created_at' => '2026-08-02', 'end_date' => '2026-08-20', 'decided_at' => '2026-08-20', 'status' => 'gave_up', 'category_name' => 'Eletrônico', 'category_icon' => 'smartphone', 'category_color' => 'sky-200'],
    ['id' => 19, 'name' => 'Mesa', 'price' => 450, 'created_at' => '2026-08-05', 'end_date' => '2026-08-15', 'decided_at' => '2026-08-15', 'status' => 'bought', 'category_name' => 'Casa', 'category_icon' => 'table-lamp', 'category_color' => 'green-200'],
    ['id' => 20, 'name' => 'Videogame', 'price' => 2500, 'created_at' => '2026-07-01', 'end_date' => '2026-07-30', 'decided_at' => '2026-07-30', 'status' => 'gave_up', 'category_name' => 'Eletrônico', 'category_icon' => 'sports-esports', 'category_color' => 'sky-200'],
    ['id' => 21, 'name' => 'Camisa', 'price' => 100, 'created_at' => '2026-07-10', 'end_date' => '2026-07-12', 'decided_at' => '2026-07-12', 'status' => 'bought', 'category_name' => 'Moda', 'category_icon' => 'apparel', 'category_color' => 'purple-200'],
    ['id' => 22, 'name' => 'Sapato', 'price' => 80, 'created_at' => '2026-06-03', 'end_date' => '2026-06-20', 'decided_at' => '2026-06-20', 'status' => 'gave_up', 'category_name' => 'Moda', 'category_icon' => 'styler', 'category_color' => 'purple-200'],
    ['id' => 23, 'name' => 'Blusa', 'price' => 320, 'created_at' => '2026-06-10', 'end_date' => '2026-06-15', 'decided_at' => '2026-06-15', 'status' => 'bought', 'category_name' => 'Moda', 'category_icon' => 'checkroom', 'category_color' => 'purple-200'],
];

// Comprei / Desisti: valida, guarda na sessão e volta para a mesma URL (um F5 não reenvia o form)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rawDecisionId = $_POST['id'] ?? '';
    $decision = $_POST['decision'] ?? '';
    $isValidDecision = is_string($rawDecisionId) && ctype_digit($rawDecisionId) && in_array($decision, ['bought', 'gave_up'], true);

    if ($isValidDecision) {
        $_SESSION['dashboard_decisions'][(int) $rawDecisionId] = $decision;
    }

    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}

// Aplica as decisões da sessão nos itens que ainda estavam pensando
$sessionDecisions = $_SESSION['dashboard_decisions'] ?? [];
foreach ($items as $index => $item) {
    if ($item['status'] === 'thinking' && isset($sessionDecisions[$item['id']])) {
        $items[$index]['status'] = $sessionDecisions[$item['id']];
        $items[$index]['decided_at'] = $today->format('Y-m-d');
    }
}

// Mês pela URL: ?month=2026-10; inválido, antes de 2000 ou no futuro abre o mês atual
$rawMonth = $_GET['month'] ?? '';
$monthDate = is_string($rawMonth) && preg_match('/^\d{4}-\d{2}$/', $rawMonth) ? DateTime::createFromFormat('!Y-m', $rawMonth) : false;
$isValidMonth = $monthDate && $monthDate->format('Y-m') === $rawMonth && $rawMonth >= '2000-01' && $rawMonth <= $currentMonth;
$selectedMonth = $isValidMonth ? $rawMonth : $currentMonth;
$isCurrentMonth = $selectedMonth === $currentMonth;

/**
 * Números de um mês ('Y-m'): o porquinho conta os itens criados no mês,
 * a lista de decisões conta os itens decididos no mês.
 */
function monthStats(array $items, string $month): array
{
    $created = array_filter($items, fn($item) => str_starts_with($item['created_at'], $month));
    $decided = array_filter($items, fn($item) => str_starts_with((string) $item['decided_at'], $month));
    usort($decided, fn($a, $b) => strcmp($b['decided_at'], $a['decided_at']));

    $totals = ['thinking' => 0, 'bought' => 0, 'gave_up' => 0];
    $counts = ['thinking' => 0, 'bought' => 0, 'gave_up' => 0];
    foreach ($created as $item) {
        $totals[$item['status']] += $item['price'];
        $counts[$item['status']]++;
    }

    $desired = array_sum($totals);

    return [
        'month' => $month,
        'created' => array_values($created),
        'decided' => $decided,
        'desired' => $desired,
        'desired_count' => count($created),
        'saved' => $totals['gave_up'],
        'bought' => $totals['bought'],
        'thinking' => $totals['thinking'],
        'counts' => $counts,
        'percent' => $desired > 0 ? (int) floor($totals['gave_up'] / $desired * 100) : 0,
    ];
}

/** Nome do mês em português: '2026-10' vira 'outubro' */
function monthName(array $monthNames, string $month): string
{
    return mb_strtolower($monthNames[(int) substr($month, 5, 2) - 1]);
}

$stats = monthStats($items, $selectedMonth);
$previousMonth = (new DateTime($selectedMonth . '-01'))->modify('-1 month')->format('Y-m');
$previousStats = monthStats($items, $previousMonth);
$selectedMonthName = monthName($monthNames, $selectedMonth);

// Itens pensando (de qualquer mês), do prazo mais próximo para o mais distante
$pendingItems = array_values(array_filter($items, fn($item) => $item['status'] === 'thinking'));
usort($pendingItems, fn($a, $b) => strcmp($a['end_date'], $b['end_date']));

// Resumo do mês abre pela URL: ?month=2026-10&action=summary
$showSummary = ($_GET['action'] ?? '') === 'summary';
$summaryUrl = '?' . http_build_query(['month' => $selectedMonth, 'action' => 'summary']);
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard — Poupa Pig</title>
    <?php include __DIR__ . '/../components/theme.php'; ?>
    <?php include __DIR__ . '/../components/accent-color.php'; ?>
</head>
<body class="flex gap-0.5 bg-background font-sans text-primary">
    <?php include __DIR__ . '/../components/notification.php'; ?>
    <?php include __DIR__ . '/../components/sidebar.php'; ?>

    <main class="flex h-screen min-w-0 flex-1 flex-col gap-4 overflow-y-auto rounded-2xl border border-black-200 px-5 py-2">
        <header class="flex items-center justify-between gap-5">
            <div class="flex min-w-0 flex-col">
                <h1 class="truncate text-[40px] font-bold">Olá, <span class="text-secondary">Pigger</span>!</h1>
                <p class="text-base font-medium text-gray-400">
                    <?= $monthNames[(int) substr($selectedMonth, 5, 2) - 1] ?> de <?= substr($selectedMonth, 0, 4) ?> ·
                    <?php if ($isCurrentMonth): ?>
                        veja como anda o seu porquinho
                    <?php else: ?>
                        <a href="dashboard.php" class="text-secondary-strong underline hover:brightness-90">voltar para o mês atual</a>
                    <?php endif; ?>
                </p>
            </div>

            <?php include __DIR__ . '/../components/dashboard/month-picker.php'; ?>
        </header>

        <div class="grid grid-cols-[1fr_260px] gap-4">
            <?php include __DIR__ . '/../components/dashboard/piggy-card.php'; ?>
            <?php include __DIR__ . '/../components/dashboard/month-stats.php'; ?>
        </div>

        <div class="grid flex-1 grid-cols-2 gap-4 pb-2">
            <?php if ($isCurrentMonth): ?>
                <?php include __DIR__ . '/../components/dashboard/pending-list.php'; ?>
            <?php else: ?>
                <?php include __DIR__ . '/../components/dashboard/month-review.php'; ?>
            <?php endif; ?>

            <?php include __DIR__ . '/../components/dashboard/decision-list.php'; ?>
        </div>
    </main>

    <?php if ($showSummary): ?>
        <?php
        $drawerTitle = 'Resumo de ' . $selectedMonthName;
        $drawerWidth = 'w-[960px]';
        $drawerTemplate = __DIR__ . '/../components/dashboard/month-summary.php';
        include __DIR__ . '/../components/drawer.php';
        ?>
    <?php endif; ?>
</body>
</html>
