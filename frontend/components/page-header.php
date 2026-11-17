<?php
/** @var string $pageTitle */
// Antes do include: $pageTitle e, se a tela tiver botão, $actionLabel e $actionHref
// Opcional: $backHref mostra a seta de voltar antes do título (telas de detalhe)
$actionLabel = $actionLabel ?? '';
$actionHref = $actionHref ?? '';
$backHref = $backHref ?? '';
?>
<header class="flex items-center justify-between gap-5">
    <div class="flex min-w-0 items-center gap-4">
        <?php if ($backHref): ?>
            <a href="<?= htmlspecialchars($backHref) ?>" aria-label="Voltar" title="Voltar"
                class="shrink-0 rounded-full focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary">
                <span class="block size-9 bg-secondary" style="mask: url(../assets/icons/arrow-left-circle-pink.svg) center / contain no-repeat; -webkit-mask: url(../assets/icons/arrow-left-circle-pink.svg) center / contain no-repeat;"></span>
            </a>
        <?php endif; ?>
        <h1 class="truncate text-[40px] font-bold text-secondary"><?= htmlspecialchars($pageTitle) ?></h1>
    </div>

    <?php if ($actionLabel && $actionHref): ?>
        <a href="<?= htmlspecialchars($actionHref) ?>"
            class="flex shrink-0 items-center gap-2.5 rounded-[32px] bg-secondary-200 px-4 py-2.5 text-base font-bold text-secondary-strong transition hover:brightness-95 focus:outline-none focus:ring-2 focus:ring-secondary">
            <span class="size-5 shrink-0 bg-secondary-strong" style="mask: url(../assets/icons/plus.svg) center / contain no-repeat; -webkit-mask: url(../assets/icons/plus.svg) center / contain no-repeat;"></span>
            <?= htmlspecialchars($actionLabel) ?>
        </a>
    <?php endif; ?>
</header>
