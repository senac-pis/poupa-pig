<?php
/**
 * Controller: criar categoria.
 *
 * Recebe (POST): name, description (opcional), icon e color (opcionais: vazios viram o padrão).
 * Erro: grava notification (com 'field') e old_input na sessão e volta para ?action=new.
 * Sucesso: grava CATEGORY_CREATED e volta para a lista.
 * O dono da categoria é sempre o usuário da sessão, nunca um valor do formulário.
 */

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../services/CategoryService.php';

$user = requireLogin('../../frontend/pages/login.php');

// Aceita só POST. Acesso direto pela URL volta para a lista.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectCategories();
}

$query = '?action=new';

$input = categoryInputFromPost($_POST);

$error = categoryInputError($input);
if ($error !== null) {
    redirectCategoriesWithError($error['code'], $error['field'], $query, $input);
}

try {
    require_once __DIR__ . '/../config/connection.php';
    require_once __DIR__ . '/../models/CategoryModel.php';

    createCategory(
        $pdo,
        $user['id'],
        $input['name'],
        $input['description'] !== '' ? $input['description'] : null,
        categoryIcon($input['icon']),
        categoryColor($input['color'])
    );
} catch (Throwable $e) {
    error_log('[create-category] ' . $e->getMessage());
    redirectCategoriesWithError('UNKNOWN_ERROR', '', $query, $input);
}

redirectCategoriesWithSuccess('CATEGORY_CREATED');
