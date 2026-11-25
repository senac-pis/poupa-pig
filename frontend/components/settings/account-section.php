<?php
/** @var array $user */
/** @var string $action */
// Seção Conta da tela de Configurações (pages/settings.php)
// O nome vira campo pela URL (?section=account&action=edit-name); excluir abre o modal (?action=delete)
$isEditingName = $action === 'edit-name';
$cameraMask = 'url(../assets/icons/camera.svg) center / contain no-repeat';
$trashMask = 'url(../assets/icons/trash3.svg) center / contain no-repeat';
?>
<div class="flex h-full flex-col items-center gap-4 p-6">
    <!-- A troca de foto entra junto com o backend -->
    <div class="flex size-32 items-center justify-center rounded-full bg-gray-100">
        <span class="size-16 bg-gray-300" style="mask: <?= $cameraMask ?>; -webkit-mask: <?= $cameraMask ?>;"></span>
    </div>

    <?php if ($isEditingName): ?>
        <form method="post" action="../../backend/controllers/update-account.php" class="flex w-full max-w-md items-center gap-2">
            <label for="account-name" class="sr-only">Nome</label>
            <input type="text" id="account-name" name="name" value="<?= htmlspecialchars($user['name']) ?>"
                autocomplete="name" required maxlength="120" autofocus
                class="h-9 min-w-0 flex-1 rounded-[32px] border border-primary bg-transparent px-4 text-base outline-none focus:ring-2 focus:ring-secondary">
            <button type="submit"
                class="h-9 shrink-0 cursor-pointer rounded-[48px] bg-secondary px-4 text-base text-background transition hover:brightness-90 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary">
                Salvar
            </button>
            <a href="?section=account"
                class="flex h-9 shrink-0 items-center rounded-[48px] border border-primary px-4 text-base transition hover:bg-blue-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary">
                Cancelar
            </a>
        </form>
    <?php else: ?>
        <div class="flex items-center gap-2">
            <h3 class="text-2xl font-bold"><?= htmlspecialchars($user['name']) ?></h3>
            <a href="?section=account&action=edit-name" aria-label="Editar nome" title="Editar nome"
                class="rounded-lg p-1 transition hover:bg-blue-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary">
                <img src="../assets/icons/pencil.svg" alt="" width="20" height="20" class="size-5">
            </a>
        </div>
    <?php endif; ?>

    <a href="?section=account&action=delete"
        class="group mt-auto flex items-center gap-2 rounded-lg px-2 py-1 text-lg font-light text-black-400 transition hover:text-red-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400">
        <span class="size-6 bg-black-400 group-hover:bg-red-400" style="mask: <?= $trashMask ?>; -webkit-mask: <?= $trashMask ?>;"></span>
        Excluir conta
    </a>
</div>
