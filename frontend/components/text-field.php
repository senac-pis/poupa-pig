<?php
/** @var array $field */
// Antes do include (montado de novo a cada campo):
// $field = ['name' => 'name', 'label' => 'Nome', 'value' => '', 'placeholder' => 'Informe o nome do produto']
// Opcionais: 'type' (padrão 'text'), 'required', 'autofocus' e 'attributes' (ex.: ['maxlength' => '100'];
// 'list' liga o campo a um <datalist> de sugestões; a setinha do navegador abre a lista sem digitar)
// e 'error' (mensagem): marca o campo, põe o foco nele e mostra a mensagem embaixo
$fieldId = 'field-' . preg_replace('/[^a-z0-9_-]/i', '', $field['name']);
$fieldType = $field['type'] ?? 'text';
$fieldMessage = $field['error'] ?? '';

// Só atributos desta lista passam, para nada como onfocus="..." entrar por engano
$allowedAttributes = ['maxlength', 'min', 'max', 'step', 'inputmode', 'autocomplete', 'list'];
$extraAttributes = '';
foreach ($field['attributes'] ?? [] as $attribute => $attributeValue) {
    if (in_array($attribute, $allowedAttributes, true)) {
        $extraAttributes .= ' ' . $attribute . '="' . htmlspecialchars($attributeValue) . '"';
    }
}
?>
<div class="flex flex-col">
    <label for="<?= $fieldId ?>" class="text-base font-bold"><?= htmlspecialchars($field['label']) ?></label>
    <input type="<?= htmlspecialchars($fieldType) ?>" id="<?= $fieldId ?>" name="<?= htmlspecialchars($field['name']) ?>"
        value="<?= htmlspecialchars($field['value'] ?? '') ?>" placeholder="<?= htmlspecialchars($field['placeholder'] ?? '') ?>"
        <?= !empty($field['required']) ? 'required' : '' ?> <?= !empty($field['autofocus']) || $fieldMessage ? 'autofocus' : '' ?><?= $extraAttributes ?>
        <?= $fieldMessage ? 'aria-invalid="true" aria-describedby="' . $fieldId . '-error"' : '' ?>
        class="w-full rounded-lg bg-white-100 px-4 py-2 text-base outline-none placeholder:text-black-200 focus:ring-2 focus:ring-secondary [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none <?= $fieldMessage ? 'ring-1 ring-red-400' : '' ?>">
    <?php if ($fieldMessage): ?>
        <p id="<?= $fieldId ?>-error" class="mt-[5px] px-4 text-sm text-red-400"><?= htmlspecialchars($fieldMessage) ?></p>
    <?php endif; ?>
</div>
