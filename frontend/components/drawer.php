<?php
/** @var string $drawerTitle */
/** @var string $drawerTemplate */
// Antes do include: $drawerTitle e $drawerTemplate (o arquivo com o conteúdo, ex.: o form do item)
// A página só inclui o drawer quando a URL pede (?action=new ou ?action=edit&id=5)

// Fechar volta para a mesma URL sem action e id, mantendo busca, filtros e página
$closeUrl = htmlspecialchars('?' . http_build_query(array_diff_key($_GET, ['action' => '', 'id' => ''])));
?>
<style>
    /* A lista atrás não rola enquanto o drawer está aberto */
    body:has(dialog[open]) {
        overflow: hidden;
    }
</style>

<!-- O <dialog> com showModal() já dá Esc, foco preso dentro dele e o resto da página ignorado -->
<dialog aria-labelledby="drawer-title" data-close-url="<?= $closeUrl ?>"
    class="fixed inset-y-0 left-auto right-0 m-0 h-full max-h-none w-[640px] max-w-full bg-background p-0 text-primary backdrop:bg-blue-alpha-500">
    <!-- O conteúdo cobre o dialog todo: assim um clique que chega no próprio dialog só pode ter sido no fundo -->
    <div class="flex h-full flex-col gap-[30px] overflow-y-auto p-[30px]">
        <div class="flex items-center gap-[30px]">
            <a href="<?= $closeUrl ?>" aria-label="Voltar" title="Voltar"
                class="shrink-0 rounded-full focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary">
                <img src="../assets/icons/arrow-left-circle-pink.svg" alt="" width="48" height="48" class="size-12">
            </a>
            <h2 id="drawer-title" class="text-5xl font-bold text-secondary"><?= htmlspecialchars($drawerTitle) ?></h2>
        </div>

        <?php include $drawerTemplate; ?>
    </div>
</dialog>

<script>
    {
        const dialog = document.currentScript.previousElementSibling;
        const close = () => {
            window.location.href = dialog.dataset.closeUrl;
        };

        dialog.showModal();

        // Esc: em vez de só esconder, volta para a URL sem o drawer
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
