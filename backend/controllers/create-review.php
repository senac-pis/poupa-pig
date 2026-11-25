<?php
/**
 * Controller: criar review (modal "Nova review" da página do produto).
 *
 * Recebe (POST): product_id, rating (1 a 5) e text (opcional, até 1000 caracteres).
 * Só avalia quem tem um item com o nome do produto, e uma vez por produto.
 * Erro: grava notification na sessão e volta para a página do produto.
 * Sucesso: grava REVIEW_CREATED e volta para a página do produto.
 */

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../services/ReviewService.php';

$user = requireLogin('../../frontend/pages/login.php');

$rawProductId = $_POST['product_id'] ?? '';
$productId = is_string($rawProductId) && ctype_digit($rawProductId) ? (int) $rawProductId : 0;

// Aceita só POST. Acesso direto pela URL volta para a lista.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectProductReviews(0);
}

if ($productId <= 0) {
    redirectProductReviewsWithError('PRODUCT_NOT_FOUND', 0);
}

$input = reviewInputFromPost($_POST);
if ($input['error'] !== null) {
    redirectProductReviewsWithError($input['error'], $productId);
}

try {
    require_once __DIR__ . '/../config/connection.php';
    require_once __DIR__ . '/../models/ReviewModel.php';

    $product = findProductById($pdo, $productId);
    if ($product === null) {
        redirectProductReviewsWithError('PRODUCT_NOT_FOUND', 0);
    }

    if (!userHasProductItem($pdo, $user['id'], $product['name'])) {
        redirectProductReviewsWithError('REVIEW_NOT_ALLOWED', $productId);
    }

    if (userReviewedProduct($pdo, $user['id'], $productId)) {
        redirectProductReviewsWithError('REVIEW_ALREADY_EXISTS', $productId);
    }

    createReview($pdo, $productId, $user['id'], $input['rating'], $input['text'] !== '' ? $input['text'] : null);
} catch (PDOException $e) {
    // Dois envios ao mesmo tempo: o UNIQUE de product_id + user_id recusa o segundo
    if ($e->getCode() === '23000') {
        redirectProductReviewsWithError('REVIEW_ALREADY_EXISTS', $productId);
    }
    error_log('[create-review] ' . $e->getMessage());
    redirectProductReviewsWithError('UNKNOWN_ERROR', $productId);
} catch (Throwable $e) {
    error_log('[create-review] ' . $e->getMessage());
    redirectProductReviewsWithError('UNKNOWN_ERROR', $productId);
}

redirectProductReviewsWithSuccess('REVIEW_CREATED', $productId);
