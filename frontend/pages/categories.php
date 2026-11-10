<?php
/**
 * TELA DE CATEGORIAS
 *
 * Layout implementado a partir do Figma (Desktop - Light mode categorias).
 *
 * Contrato com o backend:
 * - $categories e $summary são fixos por enquanto; o backend troca pela consulta ao banco,
 *   já filtrada por ?search e ?page;
 * - cada categoria segue o formato descrito em components/rows/category-row.php,
 *   com item_count e item_percent já calculados;
 * - $summary traz os números dos cards do topo (todas as categorias do usuário, sem filtro);
 * - os forms enviam POST para backend/controllers/create-category.php, update-category.php e delete-category.php;
 * - depois de salvar, o backend volta para esta tela deixando a mensagem em $_SESSION['notification'].
 */

session_start();

$activePage = 'categories';

// Dados fixos até o backend existir; para ver o estado vazio, troque por $categories = [];
$categories = [
    [
        'id' => 1,
        'name' => 'Eletrônico',
        'description' => 'Celulares, videogames e acessórios',
        'icon' => 'phone-vibrate',
        'color' => 'sky',
        'item_count' => 2,
        'item_percent' => 66,
    ],
    [
        'id' => 2,
        'name' => 'Roupas',
        'description' => 'Blusas, calças e calçados',
        'icon' => 'eyeglasses',
        'color' => 'purple',
        'item_count' => 1,
        'item_percent' => 33,
    ],
];
$summary = [
    'category_count' => 2,
    'item_count' => 3,
    'most_used' => 'Eletrônico',
];

// Drawer e modal abrem pela URL: ?action=new, ?action=edit&id=1 ou ?action=remove&id=1
$action = $_GET['action'] ?? '';
$rawId = $_GET['id'] ?? '';
$categoryId = is_string($rawId) && ctype_digit($rawId) ? (int) $rawId : 0;

// Procura a categoria da URL na lista; se não existir, nada abre
$selectedCategory = null;
foreach ($categories as $category) {
    if ($category['id'] === $categoryId) {
        $selectedCategory = $category;
    }
}

$showForm = $action === 'new' || ($action === 'edit' && $selectedCategory);
$showRemove = $action === 'remove' && $selectedCategory;

// Paginação: o backend vai devolver os totais; por enquanto é tudo uma página só
$rawPage = $_GET['page'] ?? '1';
$currentPage = is_string($rawPage) && ctype_digit($rawPage) ? (int) $rawPage : 1;
$totalPages = 1;
$shownItems = count($categories);
$totalItems = count($categories);

$statCards = [
    ['label' => 'Total de categorias', 'value' => (string) $summary['category_count'], 'icon' => 'tags', 'color' => 'pink'],
    ['label' => 'Total de itens', 'value' => (string) $summary['item_count'], 'icon' => 'handbag', 'color' => 'sky'],
    ['label' => 'Categoria mais usada', 'value' => $summary['most_used'] ?: '—', 'icon' => 'graph-up-arrow', 'color' => 'green'],
];
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Categorias — Poupa Pig</title>
    <?php include __DIR__ . '/../components/theme.php'; ?>
</head>
<body class="flex gap-0.5 bg-background font-sans text-primary">
    <?php include __DIR__ . '/../components/notification.php'; ?>
    <?php include __DIR__ . '/../components/sidebar.php'; ?>

    <main class="flex h-screen min-w-0 flex-1 flex-col gap-[15px] rounded-2xl border border-black-200 px-5 py-2">
        <?php
        $pageTitle = 'Categorias';
        $actionLabel = 'Nova categoria';
        $actionHref = '?' . http_build_query(array_merge($_GET, ['action' => 'new']));
        include __DIR__ . '/../components/page-header.php';
        ?>

        <div class="flex gap-4">
            <?php foreach ($statCards as $statCard): ?>
                <?php include __DIR__ . '/../components/stat-card.php'; ?>
            <?php endforeach; ?>
        </div>

        <?php
        $searchPlaceholder = 'Pesquisar categorias...';
        $filtersEnabled = false;
        include __DIR__ . '/../components/search-bar.php';

        $columns = ['Categoria', 'Itens', '% do total', 'Ações'];
        $rows = $categories;
        $rowTemplate = __DIR__ . '/../components/rows/category-row.php';
        $emptyMessage = 'Nenhuma categoria cadastrada';
        include __DIR__ . '/../components/table.php';

        include __DIR__ . '/../components/pagination.php';
        ?>
    </main>

    <?php if ($showForm): ?>
        <?php
        $isEdit = $action === 'edit';
        $categoryForm = [
            'action' => $isEdit ? '../../backend/controllers/update-category.php' : '../../backend/controllers/create-category.php',
            'category' => $isEdit ? $selectedCategory : null,
            'submitLabel' => $isEdit ? 'Salvar' : 'Criar',
        ];
        $drawerTitle = $isEdit ? 'Editar categoria' : 'Nova categoria';
        $drawerTemplate = __DIR__ . '/../components/forms/category-form.php';
        include __DIR__ . '/../components/drawer.php';
        ?>
    <?php endif; ?>

    <?php if ($showRemove): ?>
        <?php
        $confirmDialog = [
            'title' => 'Remover categoria',
            'name' => $selectedCategory['name'],
            'id' => $selectedCategory['id'],
            'action' => '../../backend/controllers/delete-category.php',
        ];
        include __DIR__ . '/../components/confirm-dialog.php';
        ?>
    <?php endif; ?>
</body>
</html>
