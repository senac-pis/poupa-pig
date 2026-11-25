<?php
/**
 * TELA DE REVIEWS
 *
 * Layout implementado a partir do Figma (Desktop - Light mode reviews).
 *
 * Contrato com o backend:
 * - exige usuário logado (requireLogin); os produtos são compartilhados, então a lista mostra
 *   os produtos avaliados por qualquer usuário (ReviewModel), já filtrados por ?search, ?rating e ?page;
 * - cada produto segue o formato descrito em components/rows/review-row.php,
 *   com a média e as contagens já calculadas;
 * - ?rating=4 traz os produtos com a média arredondada em 4 (de 3.5 até antes de 4.5);
 * - um produto entra na lista quando recebe a primeira review, pela página dele
 *   (aberta pelo "Avaliar" da tela de Itens).
 */

require_once __DIR__ . '/../../backend/config/auth.php';

$user = requireLogin('login.php');

const PRODUCTS_PER_PAGE = 10;

$activePage = 'review';

// Parâmetros da URL, todos validados (o rating-filters.php faz a mesma leitura do ?rating)
$rawSearch = $_GET['search'] ?? '';
$search = is_string($rawSearch) ? mb_substr(trim($rawSearch), 0, 100) : '';

$rawRating = $_GET['rating'] ?? '';
$ratingFilter = is_string($rawRating) && ctype_digit($rawRating) && (int) $rawRating <= 5 ? (int) $rawRating : 0;

$rawPage = $_GET['page'] ?? '1';
$requestedPage = is_string($rawPage) && ctype_digit($rawPage) ? max(1, (int) $rawPage) : 1;

$products = [];
$totalItems = 0;
$totalPages = 1;
$currentPage = 1;

try {
    require_once __DIR__ . '/../../backend/config/connection.php';
    require_once __DIR__ . '/../../backend/models/ReviewModel.php';

    $totalItems = countReviewedProducts($pdo, $search, $ratingFilter);
    $totalPages = max(1, (int) ceil($totalItems / PRODUCTS_PER_PAGE));
    $currentPage = min($requestedPage, $totalPages);

    $products = listReviewedProducts($pdo, $search, $ratingFilter, PRODUCTS_PER_PAGE, ($currentPage - 1) * PRODUCTS_PER_PAGE);
} catch (Throwable $e) {
    // Detalhe interno só no log do servidor; a tela mostra o aviso genérico
    error_log('[review] ' . $e->getMessage());
    $_SESSION['notification'] = ['type' => 'error', 'code' => 'UNKNOWN_ERROR', 'field' => ''];
}

$shownItems = count($products);
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reviews — Poupa Pig</title>
    <?php include __DIR__ . '/../components/theme.php'; ?>
    <?php include __DIR__ . '/../components/accent-color.php'; ?>
</head>
<body class="flex gap-0.5 bg-background font-sans text-primary">
    <?php include __DIR__ . '/../components/notification.php'; ?>
    <?php include __DIR__ . '/../components/sidebar.php'; ?>

    <main class="flex h-screen min-w-0 flex-1 flex-col gap-[15px] rounded-2xl border border-black-200 px-5 py-2">
        <?php
        $pageTitle = 'Reviews';
        include __DIR__ . '/../components/page-header.php';

        $searchPlaceholder = 'Pesquisar produtos...';
        $filtersTemplate = __DIR__ . '/../components/reviews/rating-filters.php';
        include __DIR__ . '/../components/search-bar.php';

        $columns = ['Produto', 'Usuários', 'Reviews', 'Avaliação'];
        $rows = $products;
        $rowTemplate = __DIR__ . '/../components/rows/review-row.php';
        $emptyMessage = 'Nenhum produto avaliado';
        include __DIR__ . '/../components/table.php';

        include __DIR__ . '/../components/pagination.php';
        ?>
    </main>
</body>
</html>
