<?php
/**
 * Controller: remover categoria.
 *
 * Recebe (POST): id.
 * Categoria com itens não é removida (CATEGORY_HAS_ITEMS): a tela já esconde o botão,
 * mas o pedido pode chegar direto. O banco também recusa (ON DELETE RESTRICT).
 * Erro: grava notification na sessão e volta para a lista.
 * Sucesso: grava CATEGORY_DELETED e volta para a lista.
 * Categoria inexistente e categoria de outro usuário dão o mesmo erro (CATEGORY_NOT_FOUND).
 */

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../services/CategoryService.php';

$user = requireLogin('../../frontend/pages/login.php');

// Aceita só POST. Acesso direto pela URL volta para a lista.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectCategories();
}

$rawId = $_POST['id'] ?? '';
$categoryId = is_string($rawId) && ctype_digit($rawId) ? (int) $rawId : 0;

if ($categoryId <= 0) {
    redirectCategoriesWithError('CATEGORY_NOT_FOUND', '', '', []);
}

try {
    require_once __DIR__ . '/../config/connection.php';
    require_once __DIR__ . '/../models/CategoryModel.php';

    $category = findCategoryById($pdo, $categoryId, $user['id']);
    if ($category === null) {
        redirectCategoriesWithError('CATEGORY_NOT_FOUND', '', '', []);
    }

    if ($category['item_count'] > 0) {
        redirectCategoriesWithError('CATEGORY_HAS_ITEMS', '', '', []);
    }

    deleteCategory($pdo, $categoryId, $user['id']);
} catch (PDOException $e) {
    // Um item criado entre a contagem e o DELETE: a chave estrangeira recusa (SQLSTATE 23000)
    if ($e->getCode() === '23000') {
        redirectCategoriesWithError('CATEGORY_HAS_ITEMS', '', '', []);
    }
    error_log('[delete-category] ' . $e->getMessage());
    redirectCategoriesWithError('UNKNOWN_ERROR', '', '', []);
} catch (Throwable $e) {
    error_log('[delete-category] ' . $e->getMessage());
    redirectCategoriesWithError('UNKNOWN_ERROR', '', '', []);
}

redirectCategoriesWithSuccess('CATEGORY_DELETED');
