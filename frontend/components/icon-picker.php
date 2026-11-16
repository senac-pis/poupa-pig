<?php
/** @var array $iconPicker */
// Antes do include: $iconPicker = ['value' => 'smartphone' ou '', 'label' => 'Selecionar ícone']
// O campo se chama icon, como no banco. O valor chega já validado por quem usa (category-form)
$categoryStyles ??= require __DIR__ . '/../../backend/config/categories.php';

$pickerValue = $iconPicker['value'];
$pickerMask = $pickerValue ? "url(../assets/icons/$pickerValue.svg) center / contain no-repeat" : '';

$pickerOptionClass = 'group flex size-9 cursor-pointer items-center justify-center rounded-lg bg-secondary-100 transition hover:bg-secondary-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary aria-pressed:bg-secondary-300';
?>
<div class="relative" data-icon-picker>
    <input type="hidden" name="icon" value="<?= htmlspecialchars($pickerValue) ?>" data-icon-input>

    <button type="button" aria-haspopup="dialog" aria-expanded="false" data-icon-box
        class="flex w-40 cursor-pointer flex-col items-center gap-2.5 rounded-2xl border border-primary p-4 text-base font-thin focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary">
        <!-- Sem ícone escolhido mostra a câmera; depois de escolher, o JS troca pela máscara -->
        <img src="../assets/icons/camera.svg" alt="" width="48" height="48" class="size-12" <?= $pickerValue ? 'hidden' : '' ?> data-icon-empty>
        <span class="size-12 bg-primary" style="mask: <?= $pickerMask ?>; -webkit-mask: <?= $pickerMask ?>;" <?= $pickerValue ? '' : 'hidden' ?> data-icon-preview></span>
        <span><?= htmlspecialchars($iconPicker['label']) ?></span>
    </button>

    <div hidden role="dialog" aria-label="Escolher ícone" data-icon-popup
        class="absolute left-0 top-full z-30 mt-2 flex w-96 flex-col gap-3 rounded-2xl bg-white-100 p-4 text-primary shadow-card">
        <?php foreach ($categoryStyles['icon_groups'] as $groupLabel => $groupIcons): ?>
            <div class="flex flex-col gap-2">
                <span class="text-sm font-light uppercase"><?= $groupLabel ?></span>
                <div class="flex justify-between">
                    <?php foreach ($groupIcons as $groupIcon): ?>
                        <?php $optionMask = "url(../assets/icons/$groupIcon.svg) center / contain no-repeat"; ?>
                        <button type="button" aria-pressed="false" aria-label="<?= $groupIcon ?>" title="<?= $groupIcon ?>" data-icon="<?= $groupIcon ?>" class="<?= $pickerOptionClass ?>">
                            <span class="size-5 bg-primary group-aria-pressed:bg-white-100" style="mask: <?= $optionMask ?>; -webkit-mask: <?= $optionMask ?>;"></span>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="mt-28 flex items-center justify-end gap-2">
            <button type="button" data-icon-done
                class="h-9 cursor-pointer rounded-[48px] bg-sky-100 px-4 text-base font-medium text-sky-400 transition hover:bg-sky-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary">
                Selecionar
            </button>
            <button type="button" aria-label="Cancelar" title="Cancelar" data-icon-cancel
                class="flex size-9 cursor-pointer items-center justify-center rounded-full bg-secondary-200 transition hover:bg-secondary-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary">
                <img src="../assets/icons/x.svg" alt="" width="16" height="16" class="size-4">
            </button>
        </div>
    </div>

    <script>
        {
            const picker = document.currentScript.closest('[data-icon-picker]');
            const input = picker.querySelector('[data-icon-input]');
            const box = picker.querySelector('[data-icon-box]');
            const popup = picker.querySelector('[data-icon-popup]');
            const empty = picker.querySelector('[data-icon-empty]');
            const preview = picker.querySelector('[data-icon-preview]');
            const options = [...picker.querySelectorAll('[data-icon]')];

            // Ícone marcado no popup: só vai para o input no Selecionar
            let picked = '';

            function mark(icon) {
                picked = icon;
                options.forEach((option) => {
                    option.setAttribute('aria-pressed', option.dataset.icon === icon);
                });
            }

            function open() {
                mark(input.value);
                popup.hidden = false;
                box.setAttribute('aria-expanded', 'true');
                (options.find((option) => option.dataset.icon === picked) ?? options[0]).focus();
            }

            function close(returnFocus) {
                if (popup.hidden) {
                    return;
                }
                popup.hidden = true;
                box.setAttribute('aria-expanded', 'false');
                if (returnFocus) {
                    box.focus();
                }
            }

            function done() {
                if (picked) {
                    const mask = `url(../assets/icons/${picked}.svg) center / contain no-repeat`;
                    input.value = picked;
                    preview.style.mask = mask;
                    preview.style.webkitMask = mask;
                    preview.hidden = false;
                    empty.hidden = true;
                }
                close(true);
            }

            box.addEventListener('click', () => {
                if (popup.hidden) {
                    open();
                } else {
                    close(true);
                }
            });

            options.forEach((option) => {
                option.addEventListener('click', () => {
                    mark(option.dataset.icon);
                });
            });

            picker.querySelector('[data-icon-done]').addEventListener('click', done);
            picker.querySelector('[data-icon-cancel]').addEventListener('click', () => {
                close(true);
            });

            // Clique fora fecha sem mudar nada
            document.addEventListener('click', (event) => {
                if (!popup.hidden && !picker.contains(event.target)) {
                    close(false);
                }
            });

            picker.addEventListener('keydown', (event) => {
                if (event.key !== 'Escape' || popup.hidden) {
                    return;
                }
                // Sem isso o mesmo Esc também fecharia o drawer em volta
                event.preventDefault();
                close(true);
            });
        }
    </script>
</div>
