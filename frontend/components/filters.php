<?php
// Antes do include: $categories = [['id' => 1, 'name' => 'Eletrônico', 'count' => 2], ...]
// Os filtros vão pela URL (?category=1&start=2026-01-01&end=2026-01-31) e aplicam sozinhos
$categories = $categories ?? [];
$categoryNames = array_column($categories, 'name', 'id');

$rawCategory = $_GET['category'] ?? '';
$selectedCategory = is_string($rawCategory) && ctype_digit($rawCategory) ? (int) $rawCategory : 0;
if (!isset($categoryNames[$selectedCategory])) {
    $selectedCategory = 0;
}
$categoryLabel = $selectedCategory ? $categoryNames[$selectedCategory] : 'Todas as categorias';

// Só aceita datas reais no formato do <input type="date">
$period = [];
foreach (['start', 'end'] as $field) {
    $value = $_GET[$field] ?? '';
    $date = is_string($value) ? DateTime::createFromFormat('!Y-m-d', $value) : false;
    $period[$field] = $date && $date->format('Y-m-d') === $value ? $date : null;
}

if ($period['start'] && $period['end']) {
    $periodLabel = $period['start']->format('d/m/Y') . ' a ' . $period['end']->format('d/m/Y');
} elseif ($period['start']) {
    $periodLabel = 'A partir de ' . $period['start']->format('d/m/Y');
} elseif ($period['end']) {
    $periodLabel = 'Até ' . $period['end']->format('d/m/Y');
} else {
    $periodLabel = 'Todo o período';
}

$summaryClass = 'flex cursor-pointer list-none items-center justify-between rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary [&::-webkit-details-marker]:hidden';
$optionClass = 'flex cursor-pointer gap-2.5 rounded hover:text-secondary has-[:checked]:text-secondary has-[:focus-visible]:ring-2 has-[:focus-visible]:ring-secondary';
?>
<form method="get" class="flex w-full max-w-[571px] flex-col gap-[30px] rounded-2xl bg-white-100 p-[30px] text-primary">
    <h2 class="text-[32px] font-medium">Filtros</h2>
    <hr class="border-pink-100">

    <details class="group/category">
        <summary class="<?= $summaryClass ?>">
            <span class="flex flex-col text-2xl">
                <span class="font-medium text-secondary">Categorias</span>
                <span class="font-light"><?= htmlspecialchars($categoryLabel) ?></span>
            </span>
            <img src="../assets/icons/chevron-down.svg" alt="" width="32" height="32" class="size-8 transition group-open/category:rotate-180">
        </summary>

        <details class="group/combo relative mt-5">
            <summary class="flex h-[75px] cursor-pointer list-none items-center justify-between rounded-2xl bg-background p-5 text-2xl font-medium focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary [&::-webkit-details-marker]:hidden">
                <?= htmlspecialchars($categoryLabel) ?>
                <img src="../assets/icons/chevron-expand.svg" alt="" width="30" height="30" class="size-[30px]">
            </summary>

            <div class="absolute inset-x-[18px] top-full z-10 mt-2.5 flex flex-col gap-5 rounded-2xl bg-white-100 py-5 shadow-card">
                <label class="flex items-center gap-2.5 px-5">
                    <img src="../assets/icons/search.svg" alt="" width="20" height="20" class="size-5">
                    <input type="search" placeholder="Buscar categoria..." aria-label="Buscar categoria" data-category-search
                        class="w-full bg-transparent text-2xl outline-none placeholder:text-gray-300">
                </label>
                <hr class="border-pink-100">

                <div class="flex max-h-60 flex-col gap-2.5 overflow-y-auto px-10 text-2xl">
                    <label class="<?= $optionClass ?>">
                        <input type="radio" name="category" value="" class="sr-only" <?= $selectedCategory ? '' : 'checked' ?>>
                        Todas as categorias
                    </label>
                    <?php foreach ($categories as $category): ?>
                        <label class="<?= $optionClass ?>" data-category-name="<?= htmlspecialchars(mb_strtolower($category['name'])) ?>">
                            <input type="radio" name="category" value="<?= (int) $category['id'] ?>" class="sr-only" <?= (int) $category['id'] === $selectedCategory ? 'checked' : '' ?>>
                            <span class="min-w-3.5 text-gray-200"><?= (int) $category['count'] ?></span>
                            <?= htmlspecialchars($category['name']) ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
        </details>
    </details>
    <hr class="border-pink-100">

    <div class="flex items-center justify-between">
        <span class="flex flex-col text-2xl">
            <span class="font-medium text-secondary">Valor</span>
            <span class="font-light">Todos os preços</span>
        </span>
        <img src="../assets/icons/chevron-down.svg" alt="" width="32" height="32" class="size-8">
    </div>
    <hr class="border-pink-100">

    <div class="flex items-center justify-between">
        <span class="flex flex-col text-2xl">
            <span class="font-medium text-secondary">Status</span>
            <span class="font-light">Todos os status</span>
        </span>
        <img src="../assets/icons/chevron-down.svg" alt="" width="32" height="32" class="size-8">
    </div>
    <hr class="border-pink-100">

    <!-- Fica aberto quando já tem data, para dar para escolher a outra sem reabrir -->
    <details class="group/period" <?= $period['start'] || $period['end'] ? 'open' : '' ?>>
        <summary class="<?= $summaryClass ?>">
            <span class="flex flex-col text-2xl">
                <span class="font-medium text-secondary">Período</span>
                <span class="font-light"><?= $periodLabel ?></span>
            </span>
            <img src="../assets/icons/chevron-down.svg" alt="" width="32" height="32" class="size-8 transition group-open/period:rotate-180">
        </summary>

        <div class="mt-5 flex gap-2.5">
            <?php foreach (['start' => 'Início', 'end' => 'Fim'] as $field => $label): ?>
                <label class="relative flex flex-1 flex-col gap-[11px] rounded-2xl bg-background p-[15px] has-[:focus-visible]:ring-2 has-[:focus-visible]:ring-secondary">
                    <span class="text-lg font-medium"><?= $label ?></span>
                    <span class="flex items-center gap-2.5">
                        <img src="../assets/icons/calendar-range.svg" alt="" width="24" height="24" class="size-6">
                        <!-- O ícone nativo do calendário cobre a caixa toda, invisível: clicar em qualquer lugar abre o seletor -->
                        <input type="date" name="<?= $field ?>" value="<?= $period[$field]?->format('Y-m-d') ?>"
                            class="w-full bg-transparent text-xl font-medium outline-none <?= $period[$field] ? '' : 'text-gray-300' ?> [&::-webkit-calendar-picker-indicator]:absolute [&::-webkit-calendar-picker-indicator]:inset-0 [&::-webkit-calendar-picker-indicator]:size-full [&::-webkit-calendar-picker-indicator]:cursor-pointer [&::-webkit-calendar-picker-indicator]:opacity-0">
                    </span>
                </label>
            <?php endforeach; ?>
        </div>
    </details>

    <script>
        {
            const form = document.currentScript.closest('form');
            const search = form.querySelector('[data-category-search]');

            // Aplica o filtro assim que algo muda (a busca não tem name, então não dispara)
            form.addEventListener('change', (event) => {
                if (event.target.name) {
                    // Campo vazio desabilitado não vai para a URL (evita ?start=&end=)
                    form.querySelectorAll('[name]').forEach((field) => {
                        field.disabled = field.value === '';
                    });
                    form.submit();
                }
            });

            search.addEventListener('input', () => {
                const term = search.value.trim().toLowerCase();
                form.querySelectorAll('[data-category-name]').forEach((option) => {
                    option.hidden = !option.dataset.categoryName.includes(term);
                });
            });

            // Enter na busca não envia o form
            search.addEventListener('keydown', (event) => {
                if (event.key === 'Enter') {
                    event.preventDefault();
                }
            });
        }
    </script>
</form>
