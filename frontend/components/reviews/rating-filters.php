<?php
// Painel de filtros da tela de Reviews (o search-bar.php inclui pelo $filtersTemplate)
// O filtro vai pela URL (?rating=4) e aplica sozinho; fora de 1 a 5, vale "Todas as avaliações"
$rawRating = $_GET['rating'] ?? '';
$selectedRating = is_string($rawRating) && ctype_digit($rawRating) ? (int) $rawRating : 0;
if ($selectedRating > 5) {
    $selectedRating = 0;
}

if (!$selectedRating) {
    $ratingFilterLabel = 'Todas as avaliações';
} elseif ($selectedRating === 1) {
    $ratingFilterLabel = '1 pillar';
} else {
    $ratingFilterLabel = "$selectedRating pillars";
}

// O search-bar.php mostra esse número no botão ("Filtros (1)")
$activeFilters = $selectedRating ? 1 : 0;

$optionClass = 'flex cursor-pointer items-center gap-2.5 rounded hover:text-secondary has-[:checked]:text-secondary has-[:focus-visible]:ring-2 has-[:focus-visible]:ring-secondary';
?>
<form method="get" class="flex w-full max-w-[400px] flex-col gap-4 rounded-2xl bg-white-100 p-5 text-primary">
    <!-- Mantém a busca da URL; a página volta para a 1 num filtro novo -->
    <?php foreach ($_GET as $name => $value): ?>
        <?php if (is_string($value) && !in_array($name, ['rating', 'page'], true)): ?>
            <input type="hidden" name="<?= htmlspecialchars($name) ?>" value="<?= htmlspecialchars($value) ?>">
        <?php endif; ?>
    <?php endforeach; ?>

    <h2 class="text-2xl font-medium">Filtros</h2>
    <hr class="border-secondary-100">

    <fieldset class="flex flex-col gap-3">
        <legend class="mb-3 text-lg">
            <span class="block font-medium text-secondary">Avaliação</span>
            <span class="block font-light"><?= $ratingFilterLabel ?></span>
        </legend>

        <div class="flex flex-col gap-2 px-2 text-lg">
            <label class="<?= $optionClass ?>">
                <input type="radio" name="rating" value="" class="sr-only" <?= $selectedRating ? '' : 'checked' ?>>
                Todas as avaliações
            </label>
            <?php for ($ratingOption = 5; $ratingOption >= 1; $ratingOption--): ?>
                <?php $rating = ['value' => $ratingOption, 'size' => 'size-4']; ?>
                <label class="<?= $optionClass ?>">
                    <input type="radio" name="rating" value="<?= $ratingOption ?>" class="sr-only" <?= $ratingOption === $selectedRating ? 'checked' : '' ?>>
                    <!-- Os pillars só enfeitam: o texto ao lado já diz a nota -->
                    <span aria-hidden="true" class="flex"><?php include __DIR__ . '/../rating.php'; ?></span>
                    <?= $ratingOption === 1 ? '1 pillar' : "$ratingOption pillars" ?>
                </label>
            <?php endfor; ?>
        </div>
    </fieldset>

    <script>
        {
            const form = document.currentScript.closest('form');

            // Depois de aplicar o filtro, o painel volta aberto para dar para seguir filtrando
            const panel = form.closest('details');
            if (panel && sessionStorage.getItem('filtersOpen')) {
                sessionStorage.removeItem('filtersOpen');
                panel.open = true;
            }

            // Aplica assim que muda; "Todas" (valor vazio) não vai para a URL
            form.addEventListener('change', (event) => {
                event.target.disabled = event.target.value === '';
                sessionStorage.setItem('filtersOpen', '1');
                form.submit();
            });
        }
    </script>
</form>
