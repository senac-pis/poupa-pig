<?php
/** @var array $categoryForm */
// Antes do include: $categoryForm = ['action' => '<endpoint>', 'category' => $row ou null, 'submitLabel' => 'Criar']
// 'category' null = Nova categoria; com o $row (mesmo formato da linha da tabela) = Editar
// Opcionais: 'old' (o que foi digitado antes do erro) e 'error' (o $fieldError do notification.php)
// Sem ícone ou cor válidos, os seletores abrem sem nada marcado e o backend usa o padrão ao criar
$categoryStyles ??= require __DIR__ . '/../../../backend/config/categories.php';

$formCategory = $categoryForm['category'];
// Voltando de um erro, o que foi digitado vale mais que o salvo no banco
$formValues = array_merge($formCategory ?? [], $categoryForm['old'] ?? []);
$errorFor = $categoryForm['error']['field'] ?? '';
$errorMessage = $categoryForm['error']['message'] ?? '';
$formIcon = in_array($formValues['icon'] ?? '', $categoryStyles['icons'], true) ? $formValues['icon'] : '';
$formColor = in_array($formValues['color'] ?? '', $categoryStyles['colors'], true) ? $formValues['color'] : $categoryStyles['colors'][0];
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

        $colorPicker = [
            'value' => $formColor,
            'label' => $formCategory ? 'Alterar cor' : 'Selecionar cor',
        ];
        include __DIR__ . '/../color-picker.php';
        ?>
    </div>
    <?php if ($errorFor === 'icon' || $errorFor === 'color'): ?>
        <p role="alert" class="-mt-2.5 px-4 text-center text-sm text-red-400"><?= htmlspecialchars($errorMessage) ?></p>
    <?php endif; ?>

    <?php
    $field = [
        'name' => 'name',
        'label' => 'Nome',
        'value' => $formValues['name'] ?? '',
        'placeholder' => 'Informe o nome da categoria',
        'required' => true,
        'autofocus' => $errorFor === '',
        'attributes' => ['maxlength' => '120'],
        'error' => $errorFor === 'name' ? $errorMessage : '',
    ];
    include __DIR__ . '/../text-field.php';

    $field = [
        'name' => 'description',
        'label' => 'Descrição',
        'value' => $formValues['description'] ?? '',
        'placeholder' => 'Descreva a categoria',
        'error' => $errorFor === 'description' ? $errorMessage : '',
    ];
    include __DIR__ . '/../text-field.php';
    ?>

    <button type="submit" class="h-11 w-36 cursor-pointer rounded-[48px] bg-secondary text-lg text-background transition hover:bg-pink-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary">
        <?= htmlspecialchars($categoryForm['submitLabel']) ?>
    </button>
</form>
