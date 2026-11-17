<?php
/**
 * TELA DE REVIEWS DO PRODUTO
 *
 * Layout implementado a partir do Figma (Desktop - Light mode reviews produto).
 *
 * Contrato com o backend:
 * - $products, $ratingSummary e $reviews são fixos por enquanto; o backend troca pela consulta
 *   ao banco do produto do ?id, com as reviews já paginadas por ?page;
 * - $ratingSummary segue o formato descrito em components/reviews/rating-summary.php
 *   e conta todas as reviews do produto, sem paginação;
 * - cada review segue o formato descrito em components/reviews/review-card.php;
 * - o modal envia POST para backend/controllers/create-review.php (product_id, rating e text);
 * - depois de salvar, o backend volta para esta tela deixando a mensagem em $_SESSION['notification'].
 */

session_start();

$activePage = 'review';

// Dados fixos até o backend existir; todo produto mostra as mesmas reviews
$products = [
    ['id' => 1, 'name' => 'Moto G7'],
    ['id' => 2, 'name' => 'Playstation 5'],
    ['id' => 3, 'name' => 'Iphone 11'],
    ['id' => 4, 'name' => 'Boné LS'],
    ['id' => 5, 'name' => 'Hyundai i30'],
    ['id' => 6, 'name' => 'Tenis adidas'],
];

$rawId = $_GET['id'] ?? '';
$productId = is_string($rawId) && ctype_digit($rawId) ? (int) $rawId : 0;

// Procura o produto da URL na lista; se não existir, volta para a lista de reviews
$product = null;
foreach ($products as $productOption) {
    if ($productOption['id'] === $productId) {
        $product = $productOption;
    }
}

if (!$product) {
    header('Location: review.php');
    exit;
}

$ratingSummary = [
    'review_count' => 10,
    'average' => 3.6,
    'distribution' => [5 => 3, 4 => 3, 3 => 2, 2 => 1, 1 => 1],
];

// Para ver o estado vazio, troque por $reviews = [];
$reviews = [
    [
        'user_name' => 'Ana Souza',
        'user_review_count' => 3,
        'rating' => 5,
        'text' => "Comprei depois de pensar bastante e valeu cada centavo.\nA bateria dura o dia todo.",
        'created_at' => '2026-09-18',
    ],
    [
        'user_name' => 'Bruno Lima',
        'user_review_count' => 1,
        'rating' => 4,
        'text' => '',
        'created_at' => '2026-09-12',
    ],
    [
        'user_name' => 'Carla Mendes',
        'user_review_count' => 7,
        'rating' => 2,
        'text' => 'Achei caro pelo que entrega. Esperaria uma promoção.',
        'created_at' => '2026-08-30',
    ],
];

$showReviewForm = ($_GET['action'] ?? '') === 'new';

// Paginação: o backend vai devolver os totais; por enquanto é tudo uma página só
$rawPage = $_GET['page'] ?? '1';
$currentPage = is_string($rawPage) && ctype_digit($rawPage) ? (int) $rawPage : 1;
$totalPages = 1;
$shownItems = count($reviews);
$totalItems = count($reviews);
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($product['name']) ?> — Reviews — Poupa Pig</title>
    <?php include __DIR__ . '/../components/theme.php'; ?>
    <?php include __DIR__ . '/../components/accent-color.php'; ?>
</head>
<body class="flex gap-0.5 bg-background font-sans text-primary">
    <?php include __DIR__ . '/../components/notification.php'; ?>
    <?php include __DIR__ . '/../components/sidebar.php'; ?>

    <main class="flex h-screen min-w-0 flex-1 flex-col gap-[15px] rounded-2xl border border-black-200 px-5 py-2">
        <?php
        $pageTitle = $product['name'];
        $backHref = 'review.php';
        $actionLabel = 'Nova review';
        $actionHref = '?' . http_build_query(array_merge($_GET, ['action' => 'new']));
        include __DIR__ . '/../components/page-header.php';

        include __DIR__ . '/../components/reviews/rating-summary.php';
        ?>

        <!-- Com muitas reviews, a lista rola por dentro e o resumo fica fixo no topo -->
        <div class="min-h-0 flex-1 overflow-y-auto px-4">
            <?php if ($reviews): ?>
                <?php foreach ($reviews as $review): ?>
                    <?php include __DIR__ . '/../components/reviews/review-card.php'; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="py-10 text-center text-sm">Nenhuma review ainda</p>
            <?php endif; ?>
        </div>

        <?php include __DIR__ . '/../components/pagination.php'; ?>
    </main>

    <?php if ($showReviewForm): ?>
        <?php
        $reviewDialog = [
            'productId' => $product['id'],
            'action' => '../../backend/controllers/create-review.php',
        ];
        include __DIR__ . '/../components/review-dialog.php';
        ?>
    <?php endif; ?>
</body>
</html>
