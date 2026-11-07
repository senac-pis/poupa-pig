<?php
/** @var string[] $columns */
/** @var array $rows */
/** @var string $rowTemplate */
/** @var string $emptyMessage */
// Antes do include: $columns, $rows, $rowTemplate e $emptyMessage
// $columns = ['Item', 'Categoria', 'Valor', ...]
// $rowTemplate = __DIR__ . '/rows/item-row.php' (desenha um <tr> usando $row e escapa o que mostra)
?>
<div class="flex-1 overflow-hidden rounded-2xl border border-pink-400">
    <!-- O padding das células fica aqui, então os templates de linha escrevem só <td> -->
    <table class="w-full text-left text-lg font-medium [&_td]:px-5 [&_td]:py-2.5">
        <thead class="bg-pink-200 text-xl font-bold">
            <tr>
                <?php foreach ($columns as $column): ?>
                    <th scope="col" class="h-[50px] px-5"><?= htmlspecialchars($column) ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php if ($rows): ?>
                <?php foreach ($rows as $row): ?>
                    <?php include $rowTemplate; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="<?= count($columns) ?>" class="text-center"><?= htmlspecialchars($emptyMessage) ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
