<?php
/** @var string $pageTitle */
// Antes do include: $pageTitle e, se a tela tiver botão, $actionLabel e $actionHref
$actionLabel = $actionLabel ?? '';
$actionHref = $actionHref ?? '';
?>
<header class="flex items-center justify-between gap-5">
    <h1 class="text-[40px] font-bold text-secondary"><?= htmlspecialchars($pageTitle) ?></h1>

    <?php if ($actionLabel && $actionHref): ?>
        <a href="<?= htmlspecialchars($actionHref) ?>"
            class="flex shrink-0 items-center gap-2.5 rounded-[32px] bg-secondary-200 px-4 py-2.5 text-base font-bold text-secondary-strong transition hover:brightness-95 focus:outline-none focus:ring-2 focus:ring-secondary">
            <span class="size-5 shrink-0 bg-secondary-strong" style="mask: url(../assets/icons/plus.svg) center / contain no-repeat; -webkit-mask: url(../assets/icons/plus.svg) center / contain no-repeat;"></span>
            <?= htmlspecialchars($actionLabel) ?>
        </a>
    <?php endif; ?>
</header>
