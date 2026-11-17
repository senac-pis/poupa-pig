<?php
/** @var array $row vem do foreach do table.php */
// Uma linha da tabela do resumo do mês (dashboard); o table.php inclui este arquivo para cada $row
// $row tem o formato dos itens do dashboard: o do item-row.php mais 'created_at' e 'decided_at'

// Status vem do banco como chave; valor desconhecido mostra "—"
$statusStyles = [
    'thinking' => ['label' => 'Pensando', 'class' => 'bg-purple-100 text-purple-500'],
    'bought' => ['label' => 'Comprou', 'class' => 'bg-gray-100 text-gray-500'],
    'gave_up' => ['label' => 'Desistiu', 'class' => 'bg-green-100 text-green-500'],
];
$rowStatus = $statusStyles[$row['status'] ?? ''] ?? null;

// Datas chegam como Y-m-d; vazio ou inválido mostra "—" (item ainda pensando não tem decided_at)
$rowDates = [];
foreach (['created_at', 'decided_at'] as $dateField) {
    $rowDate = DateTime::createFromFormat('!Y-m-d', (string) ($row[$dateField] ?? ''));
    $rowDates[$dateField] = $rowDate ? $rowDate->format('d/m/y') : '—';
}

$cell = [
    'name' => $row['name'],
    'icon' => $row['category_icon'],
    'color' => $row['category_color'],
];
?>
<tr>
    <td><?php include __DIR__ . '/../name-cell.php'; ?></td>
    <td><?= htmlspecialchars($row['category_name']) ?></td>
    <td class="whitespace-nowrap">R$ <?= number_format((float) $row['price'], 2, ',', '.') ?></td>
    <td class="whitespace-nowrap"><?= $rowDates['created_at'] ?></td>
    <td class="whitespace-nowrap"><?= $rowDates['decided_at'] ?></td>
    <td>
        <?php if ($rowStatus): ?>
            <span class="whitespace-nowrap rounded-full px-2.5 py-0.5 text-xs font-bold <?= $rowStatus['class'] ?>"><?= $rowStatus['label'] ?></span>
        <?php else: ?>
            —
        <?php endif; ?>
    </td>
</tr>
