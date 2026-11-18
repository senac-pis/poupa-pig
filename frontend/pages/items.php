<?php
/**
 * TELA DE ITENS
 *
 * Layout implementado a partir do Figma (Desktop - Light mode itens).
 *
 * Contrato com o backend:
 * - exige usuário logado (requireLogin) e lista só os itens dele;
 * - $items e $categories vêm do banco (ItemModel), já filtrados por
 *   ?search, ?category, ?start_date, ?end_date e ?page;
 * - cada item segue o formato descrito em components/rows/item-row.php;
 * - os forms enviam POST para backend/controllers/create-item.php, update-item.php e delete-item.php;
 * - depois de salvar, o backend volta para esta tela deixando a mensagem em $_SESSION['notification'];
 *   num erro de formulário, volta em ?action=new ou ?action=edit&id=N com
 *   $_SESSION['notification'] = ['type' => 'error', 'code' => ..., 'field' => ...] e
 *   $_SESSION['old_input'] com o que foi digitado (nunca a foto).
 */

require_once __DIR__ . '/../../backend/config/auth.php';

$user = requireLogin('login.php');

const ITEMS_PER_PAGE = 10;

$activePage = 'items';

/**
 * Só aceita data real no formato do <input type="date">; senão ignora o filtro.
 */
function queryDate($value): ?string
{
    if (!is_string($value)) {
        return null;
    }
    $date = DateTime::createFromFormat('!Y-m-d', $value);
    return $date && $date->format('Y-m-d') === $value ? $value : null;
}

/**
 * Ajusta o item do banco para o que os templates esperam:
 * 'image' vira o caminho da foto a partir desta pasta (ou '' sem foto) e 'link' nunca é null.
 */
function prepareItemRow(array $row): array
{
    $row['image'] = !empty($row['image']) ? '../uploads/items/' . rawurlencode($row['image']) : '';
    $row['link'] = (string) ($row['link'] ?? '');
    return $row;
}

// Parâmetros da URL, todos validados
$rawSearch = $_GET['search'] ?? '';
$search = is_string($rawSearch) ? mb_substr(trim($rawSearch), 0, 100) : '';

$rawCategory = $_GET['category'] ?? '';
$categoryFilter = is_string($rawCategory) && ctype_digit($rawCategory) ? (int) $rawCategory : 0;

$startDate = queryDate($_GET['start_date'] ?? '');
$endDate = queryDate($_GET['end_date'] ?? '');

// Drawer e modal abrem pela URL: ?action=new, ?action=edit&id=5 ou ?action=remove&id=5
$action = $_GET['action'] ?? '';
$rawId = $_GET['id'] ?? '';
$itemId = is_string($rawId) && ctype_digit($rawId) ? (int) $rawId : 0;

$rawPage = $_GET['page'] ?? '1';
$requestedPage = is_string($rawPage) && ctype_digit($rawPage) ? max(1, (int) $rawPage) : 1;

$items = [];
$categories = [];
$selectedItem = null;
$totalItems = 0;
$totalPages = 1;
$currentPage = 1;

try {
    require_once __DIR__ . '/../../backend/config/connection.php';
    require_once __DIR__ . '/../../backend/models/ItemModel.php';

    $categories = listCategoriesWithCount($pdo, $user['id']);

    // Categoria que não é do usuário não filtra nada
    if (!in_array($categoryFilter, array_column($categories, 'id'), true)) {
        $categoryFilter = 0;
    }

    $filters = [
        'search'      => $search,
        'category_id' => $categoryFilter,
        'start_date'  => $startDate,
        'end_date'    => $endDate,
    ];

    $totalItems = countItems($pdo, $user['id'], $filters);
    $totalPages = max(1, (int) ceil($totalItems / ITEMS_PER_PAGE));
    $currentPage = min($requestedPage, $totalPages);

    $items = array_map(
        'prepareItemRow',
        listItems($pdo, $user['id'], $filters, ITEMS_PER_PAGE, ($currentPage - 1) * ITEMS_PER_PAGE)
    );

    // Busca o item da URL direto no banco (ele pode estar em outra página da lista).
    // Id inexistente ou de outro usuário: nada abre.
    if (in_array($action, ['edit', 'remove'], true) && $itemId > 0) {
        $found = findItemById($pdo, $itemId, $user['id']);
        $selectedItem = $found ? prepareItemRow($found) : null;
    }
} catch (Throwable $e) {
    // Detalhe interno só no log do servidor; a tela mostra o aviso genérico
    error_log('[items] ' . $e->getMessage());
    $_SESSION['notification'] = ['type' => 'error', 'code' => 'UNKNOWN_ERROR', 'field' => ''];
}

$showForm = $action === 'new' || ($action === 'edit' && $selectedItem);
$showRemove = $action === 'remove' && $selectedItem;

$shownItems = count($items);
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