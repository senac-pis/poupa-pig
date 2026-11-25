<?php
/**
 * Controller: editar categoria.
 *
 * Recebe (POST): id, name, description (opcional), icon e color (opcionais: vazios viram o padrão).
 * Erro: grava notification (com 'field') e old_input na sessão e volta para ?action=edit&id=N.
 * Sucesso: grava CATEGORY_UPDATED e volta para a lista.
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

$query = '?action=edit&id=' . $categoryId;

$input = categoryInputFromPost($_POST);

$error = categoryInputError($input);
if ($error !== null) {
    redirectCategoriesWithError($error['code'], $error['field'], $query, $input);
}

try {
    require_once __DIR__ . '/../config/connection.php';
    require_once __DIR__ . '/../models/CategoryModel.php';

    if (findCategoryById($pdo, $categoryId, $user['id']) === null) {
        redirectCategoriesWithError('CATEGORY_NOT_FOUND', '', '', []);
    }

    updateCategory(
        $pdo,
        $categoryId,
        $user['id'],
        $input['name'],
        $input['description'] !== '' ? $input['description'] : null,
        categoryIcon($input['icon']),
        categoryColor($input['color'])
    );
} catch (Throwable $e) {
    error_log('[update-category] ' . $e->getMessage());
    redirectCategoriesWithError('UNKNOWN_ERROR', '', $query, $input);
}

redirectCategoriesWithSuccess('CATEGORY_UPDATED');
