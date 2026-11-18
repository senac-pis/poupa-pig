<?php
/** @var array $itemForm */
// Antes do include: $itemForm = ['action' => '<endpoint>', 'item' => $row ou null, 'categories' => [...], 'submitLabel' => 'Criar']
// 'item' null = Novo item; com o $row (mesmo formato da linha da tabela + 'category_id') = Editar
// Opcionais: 'old' (o que foi digitado antes do erro) e 'error' (o $fieldError do notification.php)
// O backend valida tudo de novo; na foto: tamanho, tipo real pelo conteúdo e nome gerado por ele
$formItem = $itemForm['item'];
// Voltando de um erro, o que foi digitado vale mais que o salvo no banco
$formValues = array_merge($formItem ?? [], $itemForm['old'] ?? []);
$errorFor = $itemForm['error']['field'] ?? '';
$errorMessage = $itemForm['error']['message'] ?? '';
$formPhoto = $formItem['image'] ?? '';
$formCategoryId = (int) ($formValues['category_id'] ?? 0);

// Datas chegam como Y-m-d; no Novo sem nada digitado, o Início já vem com hoje
if ($formValues) {
    $formStart = DateTime::createFromFormat('!Y-m-d', (string) ($formValues['start_date'] ?? '')) ?: null;
    $formEnd = DateTime::createFromFormat('!Y-m-d', (string) ($formValues['end_date'] ?? '')) ?: null;
} else {
    $formStart = new DateTime('today');
    $formEnd = null;
}
?>
<form method="post" action="<?= htmlspecialchars($itemForm['action']) ?>" enctype="multipart/form-data" class="flex flex-col gap-4">
    <?php if ($formItem): ?>
        <input type="hidden" name="id" value="<?= (int) $formItem['id'] ?>">
    <?php endif; ?>

    <!-- A caixa toda é o label: clicar em qualquer lugar abre a escolha do arquivo -->
    <label class="flex cursor-pointer flex-col items-center justify-center gap-[5px] rounded-2xl border border-dashed <?= $errorFor === 'image' ? 'border-red-400' : 'border-primary' ?> p-4 has-[:focus-visible]:ring-2 has-[:focus-visible]:ring-secondary">
        <input type="file" name="image" accept="image/png,image/jpeg" class="sr-only" data-photo-input
            <?= $errorFor === 'image' ? 'aria-invalid="true" aria-describedby="image-error" autofocus' : '' ?>>
        <img src="<?= htmlspecialchars($formPhoto) ?>" alt="Foto do item" width="48" height="48" class="size-12 rounded-lg object-cover <?= $formPhoto ? '' : 'hidden' ?>" data-photo-preview>
        <img src="../assets/icons/camera.svg" alt="" width="48" height="48" class="size-12 <?= $formPhoto ? 'hidden' : '' ?>" data-photo-placeholder>
        <span class="text-lg font-thin"><?= $formPhoto ? 'Alterar foto' : 'Adicionar foto' ?></span>
        <span class="text-xs font-thin text-black-200">PNG, JPG ou JPEG</span>
        <span class="text-xs font-thin text-black-200">(máx 5 MB)</span>
    </label>
    <?php if ($errorFor === 'image'): ?>
        <p id="image-error" class="-mt-2.5 px-4 text-sm text-red-400"><?= htmlspecialchars($errorMessage) ?></p>
    <?php endif; ?>

    <?php
    $field = [
        'name' => 'name',
        'label' => 'Nome',
        'value' => $formValues['name'] ?? '',
        'placeholder' => 'Informe o nome do produto',
        'required' => true,
        // Com erro em outro campo, o foco vai para ele
        'autofocus' => $errorFor === '',
        'error' => $errorFor === 'name' ? $errorMessage : '',
        'attributes' => ['maxlength' => '100'],
    ];
    include __DIR__ . '/../text-field.php';
    ?>

    <div class="flex flex-col">
        <label for="field-category" class="text-base font-bold">Categoria</label>
        <div class="relative">
            <!-- Fica cinza enquanto nenhuma categoria foi escolhida (:invalid, porque é obrigatório) -->
            <select id="field-category" name="category_id" required
                <?= $errorFor === 'category_id' ? 'aria-invalid="true" aria-describedby="field-category-error" autofocus' : '' ?>
                class="w-full cursor-pointer appearance-none rounded-lg bg-white-100 py-2 pl-4 pr-12 text-base outline-none invalid:text-black-200 focus:ring-2 focus:ring-secondary <?= $errorFor === 'category_id' ? 'ring-1 ring-red-400' : '' ?>">
                <option value="" disabled <?= $formCategoryId ? '' : 'selected' ?>>Selecione a categoria do produto</option>
                <?php foreach ($itemForm['categories'] as $category): ?>
                    <option value="<?= (int) $category['id'] ?>" class="text-primary" <?= (int) $category['id'] === $formCategoryId ? 'selected' : '' ?>>
                        <?= htmlspecialchars($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <img src="../assets/icons/chevron-expand.svg" alt="" width="24" height="24" class="pointer-events-none absolute right-4 top-1/2 size-6 -translate-y-1/2">
        </div>
        <?php if ($errorFor === 'category_id'): ?>
            <p id="field-category-error" class="mt-[5px] px-4 text-sm text-red-400"><?= htmlspecialchars($errorMessage) ?></p>
        <?php endif; ?>
    </div>

    <?php
    $field = [
        'name' => 'price',
        'label' => 'Valor',
        'type' => 'number',
        'value' => $formValues['price'] ?? '',
        'placeholder' => 'Informe o preço do produto',
        'required' => true,
        'attributes' => ['step' => '0.01', 'min' => '0.01'],
        'error' => $errorFor === 'price' ? $errorMessage : '',
    ];
    include __DIR__ . '/../text-field.php';

    $field = [
        'name' => 'link',
        'label' => 'Link',
        'type' => 'url',
        'value' => $formValues['link'] ?? '',
        'placeholder' => 'Informe o link do produto',
        'attributes' => ['maxlength' => '255'],
        'error' => $errorFor === 'link' ? $errorMessage : '',
    ];
    include __DIR__ . '/../text-field.php';
    ?>

    <fieldset>
        <legend class="mb-2 text-base font-bold">Selecionar período</legend>
        <?php
        // O backend manda start_date/end_date; o date-period usa start/end
        $periodFields = ['start_date' => 'start', 'end_date' => 'end'];
        $periodError = isset($periodFields[$errorFor]) ? ['field' => $periodFields[$errorFor], 'message' => $errorMessage] : null;
        $datePeriod = ['start' => $formStart, 'end' => $formEnd, 'background' => 'bg-white-100', 'required' => true, 'error' => $periodError];
        include __DIR__ . '/../date-period.php';
        ?>
    </fieldset>

    <button type="submit" class="h-11 w-36 cursor-pointer rounded-[48px] bg-secondary text-lg text-background transition hover:bg-pink-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary">
        <?= htmlspecialchars($itemForm['submitLabel']) ?>
    </button>

    <script>
        {
            const form = document.currentScript.closest('form');
            const input = form.querySelector('[data-photo-input]');
            const preview = form.querySelector('[data-photo-preview]');
            const placeholder = form.querySelector('[data-photo-placeholder]');
            const maxSize = 5 * 1024 * 1024;

            // Só conforto para quem escolhe a foto: o backend valida de novo tamanho e tipo
            input.addEventListener('change', () => {
                const file = input.files[0];
                if (!file) {
                    return;
                }
                if (file.size > maxSize) {
                    alert('A foto precisa ter no máximo 5 MB.');
                    input.value = '';
                    return;
                }
                preview.src = URL.createObjectURL(file);
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');
            });
        }
    </script>
</form>
