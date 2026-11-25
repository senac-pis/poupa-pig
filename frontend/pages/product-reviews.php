<?php
/**
 * TELA DE REVIEWS DO PRODUTO
 *
 * Layout implementado a partir do Figma (Desktop - Light mode reviews produto).
 *
 * Contrato com o backend:
 * - exige usuário logado (requireLogin); o produto e as reviews são de todos os usuários (ReviewModel);
 * - ?item=N (o "Avaliar" da tela de Itens) acha ou cria o produto pelo nome do item do usuário
 *   e redireciona para ?id=; item que não é do usuário volta para a lista com PRODUCT_NOT_FOUND;
 * - $ratingSummary segue o formato descrito em components/reviews/rating-summary.php
 *   e conta todas as reviews do produto, sem paginação;
 * - cada review segue o formato descrito em components/reviews/review-card.php, paginada por ?page;
 * - quem não tem o item só abre produto do catálogo ou que já tem review; os outros voltam para a lista;
 * - "Nova review" só aparece para quem tem um item com o nome do produto e ainda não avaliou;
 * - o modal envia POST para backend/controllers/create-review.php (product_id, rating e text);
 * - depois de salvar, o backend volta para esta tela deixando a mensagem em $_SESSION['notification'].
 */

require_once __DIR__ . '/../../backend/config/auth.php';
require_once __DIR__ . '/../../backend/services/ReviewService.php';
require_once __DIR__ . '/../../backend/services/AccountService.php';

$user = requireLogin('login.php');

const REVIEWS_PER_PAGE = 10;

$activePage = 'review';

$rawItem = $_GET['item'] ?? '';
$itemId = is_string($rawItem) && ctype_digit($rawItem) ? (int) $rawItem : 0;

$rawId = $_GET['id'] ?? '';
$productId = is_string($rawId) && ctype_digit($rawId) ? (int) $rawId : 0;

$rawPage = $_GET['page'] ?? '1';
$requestedPage = is_string($rawPage) && ctype_digit($rawPage) ? max(1, (int) $rawPage) : 1;

$product = null;
$ratingSummary = ['review_count' => 0, 'average' => 0, 'distribution' => []];
$reviews = [];
$canReview = false;
$totalItems = 0;
$totalPages = 1;
$currentPage = 1;

try {
    require_once __DIR__ . '/../../backend/config/connection.php';
    require_once __DIR__ . '/../../backend/models/ReviewModel.php';

    // Veio do "Avaliar" de um item: troca o item pelo produto e recarrega com ?id=
    if ($itemId > 0) {
        require_once __DIR__ . '/../../backend/models/ItemModel.php';

        $item = findItemById($pdo, $itemId, $user['id']);
        $productName = $item ? normalizeProductName($item['name']) : '';
        if ($productName === '') {
            $_SESSION['notification'] = ['type' => 'error', 'code' => 'PRODUCT_NOT_FOUND', 'field' => ''];
            header('Location: review.php');
            exit;
        }

        header('Location: product-reviews.php?id=' . findOrCreateProduct($pdo, $productName));
        exit;
    }

    $product = $productId > 0 ? findProductById($pdo, $productId) : null;

    // Quem não tem o item só vê produto do catálogo ou que já tem review;
    // os outros contam como inexistentes (não revelam o nome do item de ninguém)
    $hasItem = $product && userHasProductItem($pdo, $user['id'], $product['name']);
    if ($product && !$hasItem && !productIsPublic($pdo, $product['id'])) {
        $product = null;
    }

    if ($product) {
        $ratingSummary = productRatingSummary($pdo, $product['id']);

        $totalItems = $ratingSummary['review_count'];
        $totalPages = max(1, (int) ceil($totalItems / REVIEWS_PER_PAGE));
        $currentPage = min($requestedPage, $totalPages);

        $reviews = array_map(
            fn(array $review): array => array_merge($review, ['user_photo' => userPhotoUrl($review['user_photo'])]),
            listProductReviews($pdo, $product['id'], REVIEWS_PER_PAGE, ($currentPage - 1) * REVIEWS_PER_PAGE)
        );

        $canReview = $hasItem && !userReviewedProduct($pdo, $user['id'], $product['id']);
    }
} catch (Throwable $e) {
    // Detalhe interno só no log do servidor; volta para a lista com o aviso genérico
    error_log('[product-reviews] ' . $e->getMessage());
    $_SESSION['notification'] = ['type' => 'error', 'code' => 'UNKNOWN_ERROR', 'field' => ''];
    header('Location: review.php');
    exit;
}

// Produto que não existe: volta para a lista de reviews
if (!$product) {
    header('Location: review.php');
    exit;
}

$showReviewForm = $canReview && ($_GET['action'] ?? '') === 'new';

$shownItems = count($reviews);
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
        // Só quem tem o produto nos itens e ainda não avaliou vê o botão
        $actionLabel = $canReview ? 'Nova review' : '';
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
