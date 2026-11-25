<?php
/** @var array $row vem do foreach do table.php */
// Uma linha da tabela de Itens; o table.php inclui este arquivo para cada $row
// $row vem do banco já com o JOIN da categoria:
// ['id', 'name', 'image', 'price', 'link', 'start_date', 'end_date', 'status',
//  'category_id', 'category_name', 'category_icon', 'category_color']
// ('link' e 'category_id' não aparecem na linha, mas o formulário de editar usa o mesmo $row)

// Status vem do banco como chave; valor desconhecido mostra "—"
$statusLabels = ['thinking' => 'Pensando', 'bought' => 'Comprou', 'gave_up' => 'Desistiu'];
$rowStatus = $statusLabels[$row['status'] ?? ''] ?? '—';

// Datas chegam como Y-m-d; se vier algo inválido, mostra "—" em vez de quebrar a linha
$rowDates = [];
foreach (['start_date', 'end_date'] as $dateField) {
    $rowDate = DateTime::createFromFormat('!Y-m-d', (string) ($row[$dateField] ?? ''));
    $rowDates[$dateField] = $rowDate ? $rowDate->format('d/m/y') : '—';
}

$cell = [
    'name' => $row['name'],
    'photo' => $row['image'],
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
    <td><?= $rowStatus ?></td>
    <td><?php include __DIR__ . '/../row-actions.php'; ?></td>
</tr>
