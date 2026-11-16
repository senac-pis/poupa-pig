<?php
/** @var array $colorPicker */
// Antes do include: $colorPicker = ['value' => 'purple-400', 'label' => 'Selecionar cor']
// O campo se chama color, como no banco. O valor chega já validado por quem usa (category-form)
$categoryStyles ??= require __DIR__ . '/../../backend/config/categories.php';

$colorPickerValue = $colorPicker['value'];
$checkMask = 'url(../assets/icons/check-small.svg) center / contain no-repeat';

$swatchClass = 'group flex size-10 cursor-pointer items-center justify-center rounded-lg border-white-100 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary aria-pressed:border-[7px]';
?>
<div class="relative" data-color-picker>
    <input type="hidden" name="color" value="<?= htmlspecialchars($colorPickerValue) ?>" data-color-input>

    <button type="button" aria-haspopup="dialog" aria-expanded="false" data-color-box
        class="flex w-40 cursor-pointer flex-col items-center gap-2.5 rounded-2xl border border-primary p-4 text-base font-thin focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary">
        <span class="size-12 rounded-2xl bg-<?= $colorPickerValue ?>" data-color-preview></span>
        <span><?= htmlspecialchars($colorPicker['label']) ?></span>
    </button>

    <!-- Abre alinhado à direita: a caixa de cor fica no lado direito do drawer -->
    <div hidden role="dialog" aria-label="Escolher cor" data-color-popup
        class="absolute right-0 top-full z-30 mt-2 flex w-80 flex-col gap-3 rounded-2xl bg-white-100 p-6 text-primary shadow-card">
        <?php foreach ($categoryStyles['color_groups'] as $swatchLabel => $swatchFamily): ?>
            <div class="flex flex-col gap-2">
                <span class="text-sm font-light uppercase"><?= $swatchLabel ?></span>
                <div class="flex justify-between">
                    <?php foreach ($categoryStyles['color_tones'] as $swatchTone): ?>
                        <?php
                        $swatchColor = "$swatchFamily-$swatchTone";
                        // O check segue a mesma regra do ícone, para aparecer em qualquer tom
                        $checkTone = $categoryStyles['icon_tones'][$swatchTone];
                        $checkClass = $checkTone === 'white' ? 'bg-white-100' : "bg-$swatchFamily-$checkTone";
                        ?>
                        <button type="button" aria-pressed="false" aria-label="<?= "$swatchLabel $swatchTone" ?>" title="<?= "$swatchLabel $swatchTone" ?>" data-color="<?= $swatchColor ?>" class="<?= $swatchClass ?> bg-<?= $swatchColor ?>">
                            <span class="hidden size-6 group-aria-pressed:block <?= $checkClass ?>" style="mask: <?= $checkMask ?>; -webkit-mask: <?= $checkMask ?>;"></span>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="mt-3 flex items-center justify-end gap-2">
            <button type="button" data-color-done
                class="h-9 cursor-pointer rounded-[48px] bg-sky-100 px-4 text-base font-medium text-sky-400 transition hover:bg-sky-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary">
                Selecionar
            </button>
            <button type="button" aria-label="Cancelar" title="Cancelar" data-color-cancel
                class="flex size-9 cursor-pointer items-center justify-center rounded-full bg-secondary-200 transition hover:brightness-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary">
                <span class="size-4 shrink-0 bg-secondary" style="mask: url(../assets/icons/x.svg) center / contain no-repeat; -webkit-mask: url(../assets/icons/x.svg) center / contain no-repeat;"></span>
            </button>
        </div>
    </div>

    <script>
        {
            const picker = document.currentScript.closest('[data-color-picker]');
            const input = picker.querySelector('[data-color-input]');
            const box = picker.querySelector('[data-color-box]');
            const popup = picker.querySelector('[data-color-popup]');
            const preview = picker.querySelector('[data-color-preview]');
            const options = [...picker.querySelectorAll('[data-color]')];

            // Cor marcada no popup: só vai para o input no Selecionar
            let picked = '';

            function mark(color) {
                picked = color;
                options.forEach((option) => {
                    option.setAttribute('aria-pressed', option.dataset.color === color);
                });
            }

            function open() {
                mark(input.value);
                popup.hidden = false;
                box.setAttribute('aria-expanded', 'true');
                (options.find((option) => option.dataset.color === picked) ?? options[0]).focus();
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
                // picked começa com o valor do input: só troca a classe se mudou de fato
                if (picked && picked !== input.value) {
                    preview.classList.replace(`bg-${input.value}`, `bg-${picked}`);
                    input.value = picked;
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
                    mark(option.dataset.color);
                });
            });

            picker.querySelector('[data-color-done]').addEventListener('click', done);
            picker.querySelector('[data-color-cancel]').addEventListener('click', () => {
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
