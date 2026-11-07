<?php
/** @var array $field */
// Antes do include (montado de novo a cada campo):
// $field = ['name' => 'name', 'label' => 'Nome', 'value' => '', 'placeholder' => 'Informe o nome do produto']
// Opcionais: 'type' (padrão 'text'), 'required', 'autofocus' e 'attributes' (ex.: ['maxlength' => '100'])
$fieldId = 'field-' . preg_replace('/[^a-z0-9_-]/i', '', $field['name']);
$fieldType = $field['type'] ?? 'text';

// Só atributos desta lista passam, para nada como onfocus="..." entrar por engano
$allowedAttributes = ['maxlength', 'min', 'max', 'step', 'inputmode', 'autocomplete'];
$extraAttributes = '';
foreach ($field['attributes'] ?? [] as $attribute => $attributeValue) {
    if (in_array($attribute, $allowedAttributes, true)) {
        $extraAttributes .= ' ' . $attribute . '="' . htmlspecialchars($attributeValue) . '"';
    }
}
?>
<div class="flex flex-col">
    <label for="<?= $fieldId ?>" class="text-2xl font-bold"><?= htmlspecialchars($field['label']) ?></label>
    <input type="<?= htmlspecialchars($fieldType) ?>" id="<?= $fieldId ?>" name="<?= htmlspecialchars($field['name']) ?>"
        value="<?= htmlspecialchars($field['value'] ?? '') ?>" placeholder="<?= htmlspecialchars($field['placeholder'] ?? '') ?>"
        <?= !empty($field['required']) ? 'required' : '' ?> <?= !empty($field['autofocus']) ? 'autofocus' : '' ?><?= $extraAttributes ?>
        class="w-full rounded-lg bg-white-100 px-5 py-2.5 text-2xl outline-none placeholder:text-black-200 focus:ring-2 focus:ring-secondary [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none">
</div>
