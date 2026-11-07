<?php
// Antes do include: $pageTitle e, se a tela tiver botão, $actionLabel e $actionHref
$actionLabel = $actionLabel ?? '';
$actionHref = $actionHref ?? '';
?>
<header class="flex items-center justify-between gap-5">
    <h1 class="text-[64px] font-bold text-secondary"><?= htmlspecialchars($pageTitle) ?></h1>

    <?php if ($actionLabel && $actionHref): ?>
        <a href="<?= htmlspecialchars($actionHref) ?>"
            class="flex shrink-0 items-center gap-[15px] rounded-[32px] bg-pink-200 p-5 text-xl font-bold text-secondary transition hover:bg-pink-300 focus:outline-none focus:ring-2 focus:ring-secondary">
            <img src="../assets/icons/plus.svg" alt="" width="25" height="25" class="size-[25px]">
            <?= htmlspecialchars($actionLabel) ?>
        </a>
    <?php endif; ?>
</header>
