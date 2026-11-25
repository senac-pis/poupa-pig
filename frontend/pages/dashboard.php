<?php
/**
 * TELA DE DASHBOARD
 *
 * Layout implementado a partir do Figma (Desktop - Light mode Dashboard, mês anterior e resumo do mês).
 *
 * Contrato com o backend:
 * - exige usuário logado (requireLogin); $items vem do banco (listDashboardItems), com todos os itens
 *   do usuário no formato de components/rows/item-row.php mais 'created_at' (a data de inclusão,
 *   start_date) e 'decided_at' (Y-m-d ou null);
 * - todos os números da tela saem de $items pela monthStats();
 * - Comprei e Desisti enviam POST para backend/controllers/decide-item.php com id e decision
 *   ('bought' ou 'gave_up'); o backend volta para esta tela deixando a mensagem em $_SESSION['notification'];
 * - o dia de hoje vem do banco (databaseToday), porque as datas de decisão são gravadas pelo MySQL.
 */

require_once __DIR__ . '/../../backend/config/auth.php';

$user = requireLogin('login.php');

$activePage = 'dashboard';

$monthNames = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];

// Sem o banco, a tela abre vazia no mês do relógio do PHP
$items = [];
$today = new DateTime('today');

try {
    require_once __DIR__ . '/../../backend/config/connection.php';
    require_once __DIR__ . '/../../backend/models/ItemModel.php';

    $today = DateTime::createFromFormat('!Y-m-d', databaseToday($pdo)) ?: $today;
    $items = listDashboardItems($pdo, $user['id']);
} catch (Throwable $e) {
    // Detalhe interno só no log do servidor; a tela mostra o aviso genérico
    error_log('[dashboard] ' . $e->getMessage());
    $_SESSION['notification'] = ['type' => 'error', 'code' => 'UNKNOWN_ERROR', 'field' => ''];
}

$currentMonth = $today->format('Y-m');

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
