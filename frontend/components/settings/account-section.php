<?php
/** @var array $user */
/** @var string $action */
/** @var array|null $fieldError */
// Seção Conta da tela de Configurações (pages/settings.php)
// O nome vira campo pela URL (?section=account&action=edit-name); excluir abre o modal (?action=delete)
// Erro volta do backend como no login: $_SESSION['notification'] com 'field' => 'name' ou 'photo'
// A foto ($user['photo'], caminho pronto ou '') é enviada assim que o arquivo é escolhido
$nameError = ($fieldError['field'] ?? '') === 'name' ? $fieldError['message'] : '';
$photoError = ($fieldError['field'] ?? '') === 'photo' ? $fieldError['message'] : '';
$isEditingName = $action === 'edit-name' || $nameError;
$cameraMask = 'url(../assets/icons/camera.svg) center / contain no-repeat';
$trashMask = 'url(../assets/icons/trash3.svg) center / contain no-repeat';
?>
<div class="flex h-full flex-col items-center gap-4 p-6">
    <form method="post" action="../../backend/controllers/update-photo.php" enctype="multipart/form-data" class="flex flex-col items-center gap-1">
        <!-- O círculo todo é o label: clicar abre a escolha do arquivo -->
        <label title="Alterar foto"
            class="group relative flex size-32 cursor-pointer items-center justify-center overflow-hidden rounded-full bg-gray-100 has-[:focus-visible]:ring-2 has-[:focus-visible]:ring-secondary <?= $photoError ? 'ring-2 ring-red-400' : '' ?>">
            <input type="file" name="photo" accept="image/png,image/jpeg" class="sr-only" data-user-photo-input
                <?= $photoError ? 'aria-invalid="true" aria-describedby="user-photo-error"' : '' ?>>
            <span class="sr-only">Alterar foto</span>
            <?php if ($user['photo']): ?>
                <img src="<?= htmlspecialchars($user['photo']) ?>" alt="" width="128" height="128" class="size-full object-cover">
                <!-- Câmera por cima só ao passar o mouse, para mostrar que dá para trocar -->
                <span class="absolute inset-0 hidden items-center justify-center bg-blue-alpha-500 group-hover:flex">
                    <span class="size-10 bg-white-100" style="mask: <?= $cameraMask ?>; -webkit-mask: <?= $cameraMask ?>;"></span>
                </span>
            <?php else: ?>
                <span class="size-16 bg-gray-300 transition group-hover:bg-gray-400" style="mask: <?= $cameraMask ?>; -webkit-mask: <?= $cameraMask ?>;"></span>
            <?php endif; ?>
        </label>
        <span class="text-xs font-thin text-black-300">PNG ou JPG (máx 5 MB)</span>
        <?php if ($photoError): ?>
            <p id="user-photo-error" class="text-sm text-red-400"><?= htmlspecialchars($photoError) ?></p>
        <?php endif; ?>
        <!-- Sem JavaScript, o envio fica num botão -->
        <noscript>
            <button type="submit" class="mt-1 rounded-[48px] border border-primary px-4 py-1 text-sm">Enviar foto</button>
        </noscript>
    </form>

    <script>
        {
            // Escolheu o arquivo, já envia (como no Figma, sem botão de salvar)
            const input = document.currentScript.previousElementSibling.querySelector('[data-user-photo-input]');
            input.addEventListener('change', () => {
                if (input.files.length > 0) {
                    input.form.submit();
                }
            });
        }
    </script>

    <?php if ($isEditingName): ?>
        <form method="post" action="../../backend/controllers/update-account.php" class="flex w-full max-w-md flex-wrap items-center gap-2">
            <label for="account-name" class="sr-only">Nome</label>
            <input type="text" id="account-name" name="name" value="<?= htmlspecialchars($user['name']) ?>"
                autocomplete="name" required maxlength="120" autofocus
                <?= $nameError ? 'aria-invalid="true" aria-describedby="account-name-error"' : '' ?>
                class="h-9 min-w-0 flex-1 rounded-[32px] border <?= $nameError ? 'border-red-400' : 'border-primary' ?> bg-transparent px-4 text-base outline-none focus:ring-2 focus:ring-secondary">
            <button type="submit"
                class="h-9 shrink-0 cursor-pointer rounded-[48px] bg-secondary px-4 text-base text-background transition hover:brightness-90 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary">
                Salvar
            </button>
            <a href="?section=account"
                class="flex h-9 shrink-0 items-center rounded-[48px] border border-primary px-4 text-base transition hover:bg-blue-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary">
                Cancelar
            </a>
            <?php if ($nameError): ?>
                <p id="account-name-error" class="w-full px-4 text-sm text-red-400"><?= htmlspecialchars($nameError) ?></p>
            <?php endif; ?>
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
