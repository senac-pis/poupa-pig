<?php
/** @var array $row vem do foreach do table.php */
// Uma linha da tabela de Categorias; o table.php inclui este arquivo para cada $row
// $row vem do backend já com a contagem e o percentual calculados:
// ['id', 'name', 'description', 'icon', 'color', 'item_count', 'item_percent']
$categoryStyles ??= require __DIR__ . '/../../../backend/config/categories.php';

// Cor da barra: o tom mais escuro entre o fundo e o ícone (bar_tones); fora da lista, usa a padrão
$rowColor = in_array($row['color'] ?? '', $categoryStyles['colors'], true) ? $row['color'] : $categoryStyles['colors'][0];
[$rowFamily, $rowTone] = explode('-', $rowColor);
$rowBarColor = "$rowFamily-" . $categoryStyles['bar_tones'][$rowTone];
$rowPercent = min(max((int) $row['item_percent'], 0), 100);

$cell = [
    'name' => $row['name'],
    'description' => $row['description'],
    'icon' => $row['icon'],
    'color' => $row['color'],
];
$rowActions = ['id' => $row['id'], 'name' => $row['name']];
?>
<tr>
    <td><?php include __DIR__ . '/../name-cell.php'; ?></td>
    <td><?= (int) $row['item_count'] ?></td>
    <td>
        <div class="flex w-48 flex-col gap-1">
            <span><?= $rowPercent ?>%</span>
            <!-- Só visual: o número acima já diz o percentual -->
            <div aria-hidden="true" class="h-1.5 rounded-full bg-white-300">
                <div class="h-full rounded-full bg-<?= $rowBarColor ?>" style="width: <?= $rowPercent ?>%"></div>
            </div>
        </div>
    </td>
    <td><?php include __DIR__ . '/../row-actions.php'; ?></td>
</tr>
