<?php
// Antes do include: $rowActions = ['id' => 5, 'name' => 'Ps5'] (montado de novo a cada linha)
// Os links só abrem o drawer ou o modal; quem apaga de verdade é o POST do modal
$actionId = (int) $rowActions['id'];
$actionName = htmlspecialchars($rowActions['name']);

// Mantém busca, filtros e página da URL, para a lista atrás continuar igual
$editUrl = htmlspecialchars('?' . http_build_query(array_merge($_GET, ['action' => 'edit', 'id' => $actionId])));
$removeUrl = htmlspecialchars('?' . http_build_query(array_merge($_GET, ['action' => 'remove', 'id' => $actionId])));

$actionClass = 'flex size-8 items-center justify-center rounded-lg border bg-background transition focus:outline-none focus:ring-2';
?>
<div class="flex items-center gap-1">
    <a href="<?= $editUrl ?>" aria-label="Editar <?= $actionName ?>" title="Editar <?= $actionName ?>"
        class="<?= $actionClass ?> border-primary hover:bg-blue-100 focus:ring-primary">
        <img src="../assets/icons/pencil.svg" alt="" width="24" height="24" class="size-6">
    </a>
    <a href="<?= $removeUrl ?>" aria-label="Remover <?= $actionName ?>" title="Remover <?= $actionName ?>"
        class="<?= $actionClass ?> border-red-400 hover:bg-red-100 focus:ring-red-400">
        <img src="../assets/icons/trash3.svg" alt="" width="24" height="24" class="size-6">
    </a>
</div>
