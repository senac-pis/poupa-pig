<?php
/**
 * Regras de Reviews: validação da review e redirects.
 *
 * Usado pelo controller create-review.php.
 * Não conversa com o banco: isso é do ReviewModel.
 */

const REVIEW_TEXT_MAX_LENGTH = 1000;   // o modal limita a 1000 caracteres
// Caminho da página do produto, a partir de backend/controllers
const PRODUCT_REVIEWS_PAGE = '../../frontend/pages/product-reviews.php';

/**
 * Lê e valida a review do formulário.
 *
 * Retorna: ['error' => null|código do mapa, 'rating' => int, 'text' => string].
 * A nota é obrigatória (1 a 5); a resenha é opcional, até 1000 caracteres.
 */
function reviewInputFromPost(array $post): array
{
    $rawRating = $post['rating'] ?? '';
    $text = isset($post['text']) && is_string($post['text']) ? trim($post['text']) : '';

    if (!is_string($rawRating) || $rawRating === '') {
        return ['error' => 'REQUIRED_FIELD', 'rating' => 0, 'text' => $text];
    }
    if (!ctype_digit($rawRating) || (int) $rawRating < 1 || (int) $rawRating > 5) {
        return ['error' => 'INVALID_RATING', 'rating' => 0, 'text' => $text];
    }
    if (mb_strlen($text) > REVIEW_TEXT_MAX_LENGTH) {
        return ['error' => 'INVALID_REVIEW_TEXT', 'rating' => (int) $rawRating, 'text' => $text];
    }

    return ['error' => null, 'rating' => (int) $rawRating, 'text' => $text];
}

/**
 * Volta para a página do produto ($productId > 0) ou para a lista de Reviews.
 */
function redirectProductReviews(int $productId): void
{
    $page = $productId > 0 ? PRODUCT_REVIEWS_PAGE . '?id=' . $productId : '../../frontend/pages/review.php';
    header('Location: ' . $page);
    exit;
}

/**
 * Erro: grava a notification na sessão e volta para a página do produto.
 */
function redirectProductReviewsWithError(string $code, int $productId): void
{
    $_SESSION['notification'] = ['type' => 'error', 'code' => $code, 'field' => ''];
    redirectProductReviews($productId);
}

/**
 * Sucesso: grava a mensagem (códigos em backend/config/success.php) e volta para a página do produto.
 */
function redirectProductReviewsWithSuccess(string $code, int $productId): void
{
    $_SESSION['notification'] = ['type' => 'success', 'code' => $code];
    redirectProductReviews($productId);
}
