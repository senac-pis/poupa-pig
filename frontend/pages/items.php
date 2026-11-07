<?php
/**
 * TELA DE ITENS
 *
 * Layout implementado a partir do Figma (Desktop - Light mode itens).
 *
 * Contrato com o backend:
 * - $items e $categories são fixos por enquanto; o backend troca pela consulta ao banco,
 *   já filtrada por ?search, ?category, ?start, ?end e ?page;
 * - cada item segue o formato descrito em components/rows/item-row.php;
 * - os forms enviam POST para backend/controllers/create-item.php, update-item.php e delete-item.php;
 * - depois de salvar, o backend volta para esta tela deixando a mensagem em $_SESSION['notification'].
 */

session_start();

$activePage = 'items';

// Dados fixos até o backend existir; para ver o estado vazio, troque por $items = [];
$items = [
    [
        'id' => 5,
        'name' => 'Ps5',
        'photo' => '',
        'price' => 4000,
        'link' => 'https://www.casasbahia.com.br',
        'start_date' => '2026-09-11',
        'end_date' => '2026-10-11',
        'category_id' => 1,
        'category_name' => 'Eletrônico',
        'category_icon' => 'phone-vibrate',
        'category_color' => 'sky',
    ],
    [
        'id' => 6,
        'name' => 'Celular',
        'photo' => '',
        'price' => 2000,
        'link' => '',
        'start_date' => '2026-09-05',
        'end_date' => '2026-09-15',
        'category_id' => 1,
        'category_name' => 'Eletrônico',
        'category_icon' => 'phone-vibrate',
        'category_color' => 'sky',
    ],
    [
        'id' => 7,
        'name' => 'Blusa',
        'photo' => '',
        'price' => 4000,
        'link' => '',
        'start_date' => '2026-09-04',
        'end_date' => '2026-09-07',
        'category_id' => 2,
        'category_name' => 'Roupas',
        'category_icon' => 'eyeglasses',
        'category_color' => 'purple',
    ],
];
$categories = [
    ['id' => 1, 'name' => 'Eletrônico', 'count' => 2],
    ['id' => 2, 'name' => 'Roupas', 'count' => 1],
];

// Drawer e modal abrem pela URL: ?action=new, ?action=edit&id=5 ou ?action=remove&id=5
$action = $_GET['action'] ?? '';
$rawId = $_GET['id'] ?? '';
$itemId = is_string($rawId) && ctype_digit($rawId) ? (int) $rawId : 0;

// Procura o item da URL na lista; se não existir, nada abre
$selectedItem = null;
foreach ($items as $item) {
    if ($item['id'] === $itemId) {
        $selectedItem = $item;
    }
}

$showForm = $action === 'new' || ($action === 'edit' && $selectedItem);
$showRemove = $action === 'remove' && $selectedItem;

// Paginação: o backend vai devolver os totais; por enquanto é tudo uma página só
$rawPage = $_GET['page'] ?? '1';
$currentPage = is_string($rawPage) && ctype_digit($rawPage) ? (int) $rawPage : 1;
$totalPages = 1;
$shownItems = count($items);
$totalItems = count($items);
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Itens — Poupa Pig</title>
    <?php include __DIR__ . '/../components/theme.php'; ?>
</head>
<body class="flex gap-0.5 bg-background font-sans text-primary">
    <?php include __DIR__ . '/../components/notification.php'; ?>
    <?php include __DIR__ . '/../components/sidebar.php'; ?>

    <main class="flex h-screen min-w-0 flex-1 flex-col gap-[15px] rounded-2xl border border-black-200 px-5 py-2">
        <?php
        $pageTitle = 'Itens';
        $actionLabel = 'Novo item';
        $actionHref = '?' . http_build_query(array_merge($_GET, ['action' => 'new']));
        include __DIR__ . '/../components/page-header.php';

        $searchPlaceholder = 'Pesquisar itens...';
        include __DIR__ . '/../components/search-bar.php';

        $columns = ['Item', 'Categoria', 'Valor', 'Data de inclusão', 'Data final', 'Status', 'Ações'];
        $rows = $items;
        $rowTemplate = __DIR__ . '/../components/rows/item-row.php';
        $emptyMessage = 'Nenhum item cadastrado';
        include __DIR__ . '/../components/table.php';

        include __DIR__ . '/../components/pagination.php';
        ?>
    </main>

    <?php if ($showForm): ?>
        <?php
        $isEdit = $action === 'edit';
        $itemForm = [
            'action' => $isEdit ? '../../backend/controllers/update-item.php' : '../../backend/controllers/create-item.php',
            'item' => $isEdit ? $selectedItem : null,
            'categories' => $categories,
            'submitLabel' => $isEdit ? 'Salvar' : 'Criar',
        ];
        $drawerTitle = $isEdit ? 'Editar item' : 'Novo item';
        $drawerTemplate = __DIR__ . '/../components/forms/item-form.php';
        include __DIR__ . '/../components/drawer.php';
        ?>
    <?php endif; ?>

    <?php if ($showRemove): ?>
        <?php
        $confirmDialog = [
            'title' => 'Remover item',
            'name' => $selectedItem['name'],
            'id' => $selectedItem['id'],
            'action' => '../../backend/controllers/delete-item.php',
        ];
        include __DIR__ . '/../components/confirm-dialog.php';
        ?>
    <?php endif; ?>
</body>
</html>
