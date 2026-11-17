<?php
/** @var array $reviewDialog */
// Antes do include: $reviewDialog = ['productId' => 1, 'action' => '<endpoint do backend>']
// A página só inclui quando a URL pede (?id=1&action=new); quem salva é o POST do botão Resenhar
// A nota (1 a 5) é obrigatória e a resenha é opcional; o backend precisa validar as duas de novo

// Cancelar volta para a mesma URL sem o action (o id do produto fica)
$cancelUrl = htmlspecialchars('?' . http_build_query(array_diff_key($_GET, ['action' => ''])));
?>
<style>
    /* A página atrás não rola enquanto o modal está aberto */
    body:has(dialog[open]) {
        overflow: hidden;
    }

    /* Acende do 1 até o escolhido; com o mouse em cima, mostra até onde o mouse está */
    .pillar-input:not(:hover) label:has(input:checked) .pillar-full,
    .pillar-input:not(:hover) label:has(~ label input:checked) .pillar-full,
    .pillar-input label:hover .pillar-full,
    .pillar-input label:has(~ label:hover) .pillar-full {
        opacity: 1;
    }
</style>

<!-- Mesma base do confirm-dialog: o <dialog> com showModal() já dá Esc e foco preso dentro dele -->
<dialog aria-labelledby="review-title" data-close-url="<?= $cancelUrl ?>"
    class="m-auto w-full max-w-[640px] rounded-2xl border border-black-100 bg-background p-0 text-primary backdrop:bg-blue-alpha-500">
    <!-- O conteúdo cobre o dialog todo: assim um clique que chega no próprio dialog só pode ter sido no fundo -->
    <form method="post" action="<?= htmlspecialchars($reviewDialog['action']) ?>" class="flex flex-col items-center gap-4 p-6">
        <input type="hidden" name="product_id" value="<?= (int) $reviewDialog['productId'] ?>">

        <fieldset class="flex flex-col items-center">
            <legend id="review-title" class="mb-3 text-center text-lg">Quantos pillars esse produto merece?</legend>
            <!-- Sem gap entre as moedas: o mouse no espaço entre elas apagaria todas -->
            <div class="pillar-input flex">
                <?php for ($pillar = 1; $pillar <= 5; $pillar++): ?>
                    <label class="cursor-pointer rounded-full p-0.5 has-[:focus-visible]:ring-2 has-[:focus-visible]:ring-secondary">
                        <input type="radio" name="rating" value="<?= $pillar ?>" required class="sr-only"
                            aria-label="<?= $pillar === 1 ? '1 pillar' : "$pillar pillars" ?>" <?= $pillar === 1 ? 'autofocus' : '' ?>>
                        <span class="relative block size-12">
                            <img src="../assets/icons/pillar-empty.svg" alt="" class="absolute inset-0 size-full">
                            <img src="../assets/icons/pillar.svg" alt="" class="pillar-full absolute inset-0 size-full opacity-0 transition">
                        </span>
                    </label>
                <?php endfor; ?>
            </div>
        </fieldset>

        <label for="review-text" class="sr-only">Resenha (opcional)</label>
        <textarea id="review-text" name="text" maxlength="1000" rows="8" placeholder="Faça sua resenha..."
            class="w-full resize-none rounded-2xl border border-black-100 bg-transparent p-3 text-base outline-none placeholder:text-black-400 focus-visible:ring-2 focus-visible:ring-secondary"></textarea>

        <div class="flex gap-4">
            <a href="<?= $cancelUrl ?>"
                class="flex h-11 w-36 items-center justify-center rounded-[48px] border border-primary text-lg transition hover:bg-blue-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary">
                Cancelar
            </a>
            <button type="submit"
                class="h-11 w-36 cursor-pointer rounded-[48px] bg-secondary text-lg text-background transition hover:brightness-90 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary">
                Resenhar
            </button>
        </div>
    </form>
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
