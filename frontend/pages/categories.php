<?php
/**
 * TELA DE CATEGORIAS
 *
 * Layout implementado a partir do Figma (Desktop - Light mode categorias).
 *
 * Contrato com o backend:
 * - exige usuário logado (requireLogin) e lista só as categorias dele;
 * - $categories vem do banco (CategoryModel), já filtrada por ?search e ?page;
 * - cada categoria segue o formato descrito em components/rows/category-row.php,
 *   com item_count e item_percent já calculados;
 * - $summary traz os números dos cards do topo (todas as categorias do usuário, sem a busca);
 * - os forms enviam POST para backend/controllers/create-category.php, update-category.php e delete-category.php;
 * - depois de salvar, o backend volta para esta tela deixando a mensagem em $_SESSION['notification'];
 *   num erro de formulário, volta em ?action=new ou ?action=edit&id=N com
 *   $_SESSION['notification'] = ['type' => 'error', 'code' => ..., 'field' => ...] e
 *   $_SESSION['old_input'] com o que foi digitado.
 */

require_once __DIR__ . '/../../backend/config/auth.php';
require_once __DIR__ . '/../../backend/services/CategoryService.php';

$user = requireLogin('login.php');

// O que foi digitado antes de um erro de formulário: lê uma vez só e apaga, como no login
$oldInput = $_SESSION['old_input'] ?? [];
unset($_SESSION['old_input']);

const CATEGORIES_PER_PAGE = 10;

$activePage = 'categories';

// Parâmetros da URL, todos validados
$rawSearch = $_GET['search'] ?? '';
$search = is_string($rawSearch) ? mb_substr(trim($rawSearch), 0, 100) : '';

// Drawer e modal abrem pela URL: ?action=new, ?action=edit&id=1 ou ?action=remove&id=1
$action = $_GET['action'] ?? '';
$rawId = $_GET['id'] ?? '';
$categoryId = is_string($rawId) && ctype_digit($rawId) ? (int) $rawId : 0;

$rawPage = $_GET['page'] ?? '1';
$requestedPage = is_string($rawPage) && ctype_digit($rawPage) ? max(1, (int) $rawPage) : 1;

$categories = [];
$summary = ['category_count' => 0, 'item_count' => 0, 'most_used' => ''];
$selectedCategory = null;
$totalItems = 0;
$totalPages = 1;
$currentPage = 1;

try {
    require_once __DIR__ . '/../../backend/config/connection.php';
    require_once __DIR__ . '/../../backend/models/CategoryModel.php';

    $summary = categorySummary($pdo, $user['id']);

    $totalItems = countCategories($pdo, $user['id'], $search);
    $totalPages = max(1, (int) ceil($totalItems / CATEGORIES_PER_PAGE));
    $currentPage = min($requestedPage, $totalPages);

    $categories = listCategories($pdo, $user['id'], $search, CATEGORIES_PER_PAGE, ($currentPage - 1) * CATEGORIES_PER_PAGE);

    // Busca a categoria da URL direto no banco (ela pode estar em outra página da lista).
    // Id inexistente ou de outro usuário: nada abre.
    if (in_array($action, ['edit', 'remove'], true) && $categoryId > 0) {
        $selectedCategory = findCategoryById($pdo, $categoryId, $user['id']);
    }
} catch (Throwable $e) {
    // Detalhe interno só no log do servidor; a tela mostra o aviso genérico
    error_log('[categories] ' . $e->getMessage());
    $_SESSION['notification'] = ['type' => 'error', 'code' => 'UNKNOWN_ERROR', 'field' => ''];
}

// % de cada categoria sobre todos os itens do usuário (não só os da página)
$categories = array_map(
    fn(array $category): array => array_merge($category, [
        'item_percent' => categoryItemPercent($category['item_count'], $summary['item_count']),
    ]),
    $categories
);

$showForm = $action === 'new' || ($action === 'edit' && $selectedCategory);
$showRemove = $action === 'remove' && $selectedCategory;

$shownItems = count($categories);

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
            'old' => $oldInput,
            // $fieldError vem do notification.php, incluído no começo do <body>
            'error' => $fieldError,
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

        // Categoria com itens não pode ser removida: o modal só avisa, sem o botão Remover.
        // O delete-category.php também recusa (CATEGORY_HAS_ITEMS), porque o POST pode chegar direto
        $itemCount = (int) $selectedCategory['item_count'];
        if ($itemCount === 1) {
            $confirmDialog['blocked'] = "{$selectedCategory['name']} tem 1 item. Mova ou remova o item antes de remover a categoria.";
        } elseif ($itemCount > 1) {
            $confirmDialog['blocked'] = "{$selectedCategory['name']} tem $itemCount itens. Mova ou remova os itens antes de remover a categoria.";
        }

        include __DIR__ . '/../components/confirm-dialog.php';
        ?>
    <?php endif; ?>
</body>
</html>
