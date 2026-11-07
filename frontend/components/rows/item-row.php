<?php
/** @var array $row vem do foreach do table.php */
// Uma linha da tabela de Itens; o table.php inclui este arquivo para cada $row
// $row vem do banco já com o JOIN da categoria:
// ['id', 'name', 'photo', 'price', 'start_date', 'end_date', 'category_name', 'category_icon', 'category_color']

// Datas chegam como Y-m-d; se vier algo inválido, mostra "—" em vez de quebrar a linha
$rowDates = [];
foreach (['start_date', 'end_date'] as $dateField) {
    $rowDate = DateTime::createFromFormat('!Y-m-d', (string) ($row[$dateField] ?? ''));
    $rowDates[$dateField] = $rowDate ? $rowDate->format('d/m/y') : '—';
}

$cell = [
    'name' => $row['name'],
    'photo' => $row['photo'],
    'icon' => $row['category_icon'],
    'color' => $row['category_color'],
];
$rowActions = ['id' => $row['id'], 'name' => $row['name']];
?>
<tr>
    <td><?php include __DIR__ . '/../name-cell.php'; ?></td>
    <td><?= htmlspecialchars($row['category_name']) ?></td>
    <td>R$ <?= number_format((float) $row['price'], 2, ',', '.') ?></td>
    <td><?= $rowDates['start_date'] ?></td>
    <td><?= $rowDates['end_date'] ?></td>
    <!-- Comprou/Desistiu chegam com a tela de decisão; até lá, todo item sem decisão está "Pensando" -->
    <td>Pensando</td>
    <td><?php include __DIR__ . '/../row-actions.php'; ?></td>
</tr>
