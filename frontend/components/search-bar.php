<?php
/** @var string $searchPlaceholder */
// Antes do include: $searchPlaceholder e $categories (repassado para o filters.php)
// Opcional: $filtersEnabled = false esconde o botão de filtros (telas sem filtro, como Categorias)
// Opcional: $filtersTemplate troca o painel (padrão: filters.php, de categoria e período);
// o painel precisa definir $activeFilters com quantos filtros estão aplicados
// A busca vai pela URL (?search=ps5) e aplica com Enter
$rawSearch = $_GET['search'] ?? '';
$searchTerm = is_string($rawSearch) ? mb_substr(trim($rawSearch), 0, 100) : '';
$filtersEnabled ??= true;
$filtersTemplate ??= __DIR__ . '/filters.php';
$filterLabel = 'Todos';

if ($filtersEnabled) {
    // Renderiza o painel antes para saber quantos filtros estão ativos ($activeFilters vem dele);
    // o HTML entra no painel lá embaixo
    ob_start();
    include $filtersTemplate;
    $filtersHtml = ob_get_clean();
    /** @var int $activeFilters */

    $filterLabel = $activeFilters ? "Filtros ($activeFilters)" : 'Todos';
}

$filterButtonClass = 'flex cursor-pointer list-none items-center gap-2.5 rounded-[32px] bg-secondary-200 px-4 py-2.5 text-base font-bold text-secondary-strong transition hover:brightness-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary';
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
                <span class="size-5 shrink-0 bg-secondary-strong" style="mask: url(../assets/icons/filter.svg) center / contain no-repeat; -webkit-mask: url(../assets/icons/filter.svg) center / contain no-repeat;"></span>
                <?= $filterLabel ?>
            </summary>

            <div class="absolute right-0 top-full z-20 mt-2.5 w-[400px] rounded-2xl shadow-card">
                <?= $filtersHtml ?>
            </div>
        </details>
    <?php endif; ?>
</div>
