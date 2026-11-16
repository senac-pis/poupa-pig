<?php
/** @var array $categoryForm */
// Antes do include: $categoryForm = ['action' => '<endpoint>', 'category' => $row ou null, 'submitLabel' => 'Criar']
// 'category' null = Nova categoria; com o $row (mesmo formato da linha da tabela) = Editar
// A cor ainda não tem seletor: a caixa só mostra a atual, e o backend usa o padrão ao criar
$categoryStyles ??= require __DIR__ . '/../../../backend/config/categories.php';

$formCategory = $categoryForm['category'];
$formIcon = in_array($formCategory['icon'] ?? '', $categoryStyles['icons'], true) ? $formCategory['icon'] : '';
$formColor = in_array($formCategory['color'] ?? '', $categoryStyles['colors'], true) ? $formCategory['color'] : $categoryStyles['colors'][0];

$pickerClass = 'flex w-40 flex-col items-center gap-2.5 rounded-2xl border border-primary p-4 text-base font-thin';
?>
<form method="post" action="<?= htmlspecialchars($categoryForm['action']) ?>" class="flex flex-col gap-4">
    <?php if ($formCategory): ?>
        <input type="hidden" name="id" value="<?= (int) $formCategory['id'] ?>">
    <?php endif; ?>

    <div class="flex justify-around">
        <?php
        $iconPicker = [
            'value' => $formIcon,
            'label' => $formCategory ? 'Alterar ícone' : 'Selecionar ícone',
        ];
        include __DIR__ . '/../icon-picker.php';
        ?>
        <!-- Só visual por enquanto: o seletor de cor entra no próximo passo -->
        <div class="<?= $pickerClass ?>">
            <span class="size-12 rounded-2xl bg-<?= $formColor ?>-200"></span>
            <span><?= $formCategory ? 'Alterar cor' : 'Selecionar cor' ?></span>
        </div>
    </div>

    <?php
    $field = [
        'name' => 'name',
        'label' => 'Nome',
        'value' => $formCategory['name'] ?? '',
        'placeholder' => 'Informe o nome da categoria',
        'required' => true,
        'autofocus' => true,
        'attributes' => ['maxlength' => '50'],
    ];
    include __DIR__ . '/../text-field.php';

    $field = [
        'name' => 'description',
        'label' => 'Descrição',
        'value' => $formCategory['description'] ?? '',
        'placeholder' => 'Descreva a categoria',
        'attributes' => ['maxlength' => '255'],
    ];
    include __DIR__ . '/../text-field.php';
    ?>

    <button type="submit" class="h-11 w-36 cursor-pointer rounded-[48px] bg-secondary text-lg text-background transition hover:bg-pink-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary">
        <?= htmlspecialchars($categoryForm['submitLabel']) ?>
    </button>
</form>
