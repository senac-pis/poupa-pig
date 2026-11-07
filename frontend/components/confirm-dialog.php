<?php
/** @var array $confirmDialog */
// Antes do include: $confirmDialog = ['title' => 'Remover item', 'name' => 'Ps5', 'id' => 5, 'action' => '<endpoint do backend>']
// A página só inclui quando a URL pede (?action=remove&id=5); quem apaga é o POST do botão Remover

// Cancelar volta para a mesma URL sem action e id, mantendo busca, filtros e página
$cancelUrl = htmlspecialchars('?' . http_build_query(array_diff_key($_GET, ['action' => '', 'id' => ''])));
?>
<style>
    /* A lista atrás não rola enquanto o modal está aberto */
    body:has(dialog[open]) {
        overflow: hidden;
    }
</style>

<!-- Mesma base do drawer: o <dialog> com showModal() já dá Esc e foco preso dentro dele -->
<dialog aria-labelledby="confirm-title" data-close-url="<?= $cancelUrl ?>"
    class="m-auto w-full max-w-[400px] rounded-2xl border border-black-100 bg-background p-0 backdrop:bg-blue-alpha-500">
    <!-- O conteúdo cobre o dialog todo: assim um clique que chega no próprio dialog só pode ter sido no fundo -->
    <div class="flex flex-col items-center gap-6 p-8 text-center">
        <h2 id="confirm-title" class="text-[28px] font-bold text-red-400"><?= htmlspecialchars($confirmDialog['title']) ?></h2>
        <p class="text-xl font-light text-black-500">
            Tem certeza que deseja remover <?= htmlspecialchars($confirmDialog['name']) ?>?
        </p>

        <form method="post" action="<?= htmlspecialchars($confirmDialog['action']) ?>" class="flex gap-4">
            <input type="hidden" name="id" value="<?= (int) $confirmDialog['id'] ?>">
            <button type="submit"
                class="h-11 w-36 cursor-pointer rounded-[48px] bg-red-400 text-lg text-background transition hover:bg-red-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary">
                Remover
            </button>
            <!-- O foco começa aqui: um Enter sem querer não apaga nada -->
            <a href="<?= $cancelUrl ?>" autofocus
                class="flex h-11 w-36 items-center justify-center rounded-[48px] border border-primary text-lg text-primary transition hover:bg-blue-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary">
                Cancelar
            </a>
        </form>
    </div>
</dialog>

<script>
    {
        const dialog = document.currentScript.previousElementSibling;
        const close = () => {
            window.location.href = dialog.dataset.closeUrl;
        };

        dialog.showModal();

        // Esc: em vez de só esconder, volta para a URL sem o modal
        dialog.addEventListener('cancel', (event) => {
            event.preventDefault();
            close();
        });

        // Clique no fundo escurecido
        dialog.addEventListener('click', (event) => {
            if (event.target === dialog) {
                close();
            }
        });
    }
</script>
