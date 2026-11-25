<?php
/**
 * TELA DE REVIEWS
 *
 * Layout implementado a partir do Figma (Desktop - Light mode reviews).
 *
 * Contrato com o backend:
 * - $products é fixo por enquanto; o backend troca pela consulta ao banco,
 *   já filtrada por ?search, ?rating e ?page;
 * - cada produto segue o formato descrito em components/rows/review-row.php,
 *   com a média e as contagens já calculadas;
 * - ?rating=4 traz os produtos com a média arredondada em 4 (de 3.5 até antes de 4.5).
 */

session_start();

$activePage = 'review';

// Dados fixos até o backend existir; para ver o estado vazio, troque por $products = [];
$products = [
    ['id' => 1, 'name' => 'Moto G7', 'user_count' => 45, 'review_count' => 45, 'average' => 4.2],
    ['id' => 2, 'name' => 'Playstation 5', 'user_count' => 6, 'review_count' => 6, 'average' => 3.6],
    ['id' => 3, 'name' => 'Iphone 11', 'user_count' => 67, 'review_count' => 67, 'average' => 4.8],
    ['id' => 4, 'name' => 'Boné LS', 'user_count' => 3, 'review_count' => 3, 'average' => 2.3],
    ['id' => 5, 'name' => 'Hyundai i30', 'user_count' => 2, 'review_count' => 2, 'average' => 5],
    ['id' => 6, 'name' => 'Tenis adidas', 'user_count' => 10, 'review_count' => 10, 'average' => 1.5],
];

// Paginação: o backend vai devolver os totais; por enquanto é tudo uma página só
$rawPage = $_GET['page'] ?? '1';
$currentPage = is_string($rawPage) && ctype_digit($rawPage) ? (int) $rawPage : 1;
$totalPages = 1;
$shownItems = count($products);
$totalItems = count($products);
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
