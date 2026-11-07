<?php
// Antes do include: $currentPage, $totalPages, $shownItems e $totalItems (a página valida o ?page=)
$totalPages = max(1, (int) $totalPages);
$currentPage = min(max(1, (int) $currentPage), $totalPages);

// Primeira, última e as vizinhas da atual; o resto vira "…"
$visiblePages = array_unique([1, $currentPage - 1, $currentPage, $currentPage + 1, $totalPages]);
$visiblePages = array_filter($visiblePages, fn($page) => $page >= 1 && $page <= $totalPages);
sort($visiblePages);

// Mantém os outros parâmetros da URL (filtros, busca) ao trocar de página
$pageUrls = [];
foreach ($visiblePages as $page) {
    $pageUrls[$page] = htmlspecialchars('?' . http_build_query(array_merge($_GET, ['page' => $page])));
}

$linkClass = 'rounded px-1 hover:text-secondary focus:outline-none focus:ring-2 focus:ring-secondary';
?>
<div class="grid w-full grid-cols-[1fr_auto_1fr] items-center rounded-lg border border-pink-400 bg-background p-3 text-xs font-bold">
    <div class="flex items-center gap-2.5">
        <img src="../assets/icons/tags.svg" alt="" width="16" height="16" class="size-4">
        <span><?= (int) $shownItems ?> de <?= (int) $totalItems ?></span>
    </div>

    <nav aria-label="Paginação" class="flex items-center gap-1">
        <?php if ($currentPage > 1): ?>
            <a href="<?= $pageUrls[$currentPage - 1] ?>" aria-label="Página anterior" class="<?= $linkClass ?>">&lt;</a>
        <?php else: ?>
            <span aria-hidden="true" class="px-1 opacity-40">&lt;</span>
        <?php endif; ?>

        <?php $previousPage = 0; ?>
        <?php foreach ($visiblePages as $page): ?>
            <?php if ($page - $previousPage > 1): ?>
                <span aria-hidden="true">…</span>
            <?php endif; ?>

            <?php if ($page === $currentPage): ?>
                <span aria-current="page" class="px-1 text-secondary"><?= $page ?></span>
            <?php else: ?>
                <a href="<?= $pageUrls[$page] ?>" aria-label="Página <?= $page ?>" class="<?= $linkClass ?>"><?= $page ?></a>
            <?php endif; ?>
            <?php $previousPage = $page; ?>
        <?php endforeach; ?>

        <?php if ($currentPage < $totalPages): ?>
            <a href="<?= $pageUrls[$currentPage + 1] ?>" aria-label="Próxima página" class="<?= $linkClass ?>">&gt;</a>
        <?php else: ?>
            <span aria-hidden="true" class="px-1 opacity-40">&gt;</span>
        <?php endif; ?>
    </nav>
</div>
