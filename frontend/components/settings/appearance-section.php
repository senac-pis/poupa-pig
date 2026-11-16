<?php
/** @var array $categoryStyles */
// Seção Aparência da tela de Configurações (pages/settings.php)
// $categoryStyles e applyAccentColor() vêm do accent-color.php, incluído no <head>
// Clicar numa cor já aplica e salva no navegador, sem botão de salvar (como no Figma)
$themeModes = [
    'light' => ['label' => 'Claro', 'icon' => 'brightness-alt-high'],
    'dark' => ['label' => 'Escuro', 'icon' => 'brightness-alt-low'],
    'system' => ['label' => 'Sistema', 'icon' => 'display'],
];
$accentTones = ['300', '400', '500'];
$checkMask = 'url(../assets/icons/check-small.svg) center / contain no-repeat';

$swatchClass = 'group flex size-10 cursor-pointer items-center justify-center rounded-lg border-white-100 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-primary aria-pressed:border-[7px]';
?>
<div class="flex flex-col gap-6 p-6" data-accent-picker>
    <!-- Só o Claro existe por enquanto; o modo escuro tem issue própria -->
    <div class="grid grid-cols-3 gap-4" role="group" aria-label="Tema">
        <?php foreach ($themeModes as $mode => $themeMode): ?>
            <?php $isLight = $mode === 'light'; ?>
            <button type="button" aria-pressed="<?= $isLight ? 'true' : 'false' ?>" <?= $isLight ? '' : 'aria-disabled="true" title="Em breve"' ?>
                class="flex h-20 flex-col items-center justify-center gap-2 rounded-2xl border border-black-300 text-sm aria-disabled:cursor-not-allowed aria-disabled:opacity-50 aria-pressed:border-2 aria-pressed:border-primary">
                <img src="../assets/icons/<?= $themeMode['icon'] ?>.svg" alt="" width="20" height="20" class="size-5">
                <?= $themeMode['label'] ?>
            </button>
        <?php endforeach; ?>
    </div>

    <div class="flex flex-col gap-4" role="group" aria-label="Cor principal">
        <?php foreach ($categoryStyles['color_groups'] as $swatchLabel => $swatchFamily): ?>
            <div class="flex flex-col gap-2">
                <span class="text-sm"><?= $swatchLabel ?></span>
                <div class="flex gap-2.5">
                    <?php foreach ($accentTones as $swatchTone): ?>
                        <?php
                        $swatchColor = "$swatchFamily-$swatchTone";
                        // O check segue a mesma regra do seletor de cor das categorias
                        $checkTone = $categoryStyles['icon_tones'][$swatchTone];
                        $checkClass = $checkTone === 'white' ? 'bg-white-100' : "bg-$swatchFamily-$checkTone";
                        ?>
                        <button type="button" aria-pressed="false" aria-label="<?= "$swatchLabel $swatchTone" ?>" title="<?= "$swatchLabel $swatchTone" ?>" data-accent-color="<?= $swatchColor ?>" class="<?= $swatchClass ?> bg-<?= $swatchColor ?>">
                            <span class="hidden size-6 group-aria-pressed:block <?= $checkClass ?>" style="mask: <?= $checkMask ?>; -webkit-mask: <?= $checkMask ?>;"></span>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <script>
        {
            const picker = document.currentScript.closest('[data-accent-picker]');
            const options = [...picker.querySelectorAll('[data-accent-color]')];

            function mark(color) {
                options.forEach((option) => {
                    option.setAttribute('aria-pressed', option.dataset.accentColor === color);
                });
            }

            // Sem cor salva (ou com valor inválido), o marcado é o rosa padrão do tema
            let saved = null;
            try {
                saved = localStorage.getItem('accent-color');
            } catch {}
            mark(accentColors.includes(saved) ? saved : 'pink-400');

            options.forEach((option) => {
                option.addEventListener('click', () => {
                    const color = option.dataset.accentColor;
                    applyAccentColor(color);
                    mark(color);
                    try {
                        localStorage.setItem('accent-color', color);
                    } catch {}
                });
            });
        }
    </script>
</div>
