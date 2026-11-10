<?php
/** @var string $searchPlaceholder */
// Antes do include: $searchPlaceholder e $categories (repassado para o filters.php)
// Opcional: $filtersEnabled = false mostra o botão "Todos" sem abrir o painel de filtros
// A busca vai pela URL (?search=ps5) e aplica com Enter
$rawSearch = $_GET['search'] ?? '';
$searchTerm = is_string($rawSearch) ? mb_substr(trim($rawSearch), 0, 100) : '';
$filtersEnabled ??= true;
$filterLabel = 'Todos';

if ($filtersEnabled) {
    // Renderiza os filtros antes para saber quantos estão ativos (o filters.php valida e deixa
    // $selectedCategory e $period prontos); o HTML entra no painel lá embaixo
    ob_start();
    include __DIR__ . '/filters.php';
    $filtersHtml = ob_get_clean();
    /** @var int $selectedCategory */
    /** @var array $period */

    $activeFilters = ($selectedCategory ? 1 : 0) + ($period['start'] || $period['end'] ? 1 : 0);
    $filterLabel = $activeFilters ? "Filtros ($activeFilters)" : 'Todos';
}

$filterButtonClass = 'flex cursor-pointer list-none items-center gap-2.5 rounded-[32px] bg-pink-200 px-4 py-2.5 text-base font-bold text-secondary transition hover:bg-pink-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary';
?>
<div class="flex items-center gap-2.5">
    <form method="get" role="search" class="flex flex-1 items-center gap-2.5 rounded-[32px] border border-primary bg-background px-4 py-2.5 has-[:focus-visible]:ring-2 has-[:focus-visible]:ring-secondary">
        <!-- Mantém os filtros da URL; a página volta para a 1 numa busca nova -->
        <?php foreach ($_GET as $name => $value): ?>
            <?php if (is_string($value) && !in_array($name, ['search', 'page'], true)): ?>
                <input type="hidden" name="<?= htmlspecialchars($name) ?>" value="<?= htmlspecialchars($value) ?>">
            <?php endif; ?>
        <?php endforeach; ?>

        <img src="../assets/icons/search.svg" alt="" width="20" height="20" class="size-5">
        <input type="search" name="search" value="<?= htmlspecialchars($searchTerm) ?>" maxlength="100"
            placeholder="<?= htmlspecialchars($searchPlaceholder) ?>" aria-label="<?= htmlspecialchars($searchPlaceholder) ?>"
            class="w-full bg-transparent text-base font-bold outline-none placeholder:text-gray-200">
    </form>

    <?php if ($filtersEnabled): ?>
        <details class="relative shrink-0">
            <summary class="<?= $filterButtonClass ?> [&::-webkit-details-marker]:hidden">
                <img src="../assets/icons/filter.svg" alt="" width="20" height="20" class="size-5">
                <?= $filterLabel ?>
            </summary>

            <div class="absolute right-0 top-full z-20 mt-2.5 w-[400px] rounded-2xl shadow-card">
                <?= $filtersHtml ?>
            </div>
        </details>
    <?php else: ?>
        <!-- Sem função por enquanto: a equipe ainda vai decidir se esta tela tem filtro -->
        <button type="button" class="<?= $filterButtonClass ?> shrink-0">
            <img src="../assets/icons/filter.svg" alt="" width="20" height="20" class="size-5">
            <?= $filterLabel ?>
        </button>
    <?php endif; ?>
</div>
