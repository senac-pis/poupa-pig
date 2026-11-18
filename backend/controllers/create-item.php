<?php
/**
 * Controller: criar item.
 *
 * Recebe (POST multipart): image (opcional), name, category_id, price,
 * link (opcional), start_date e end_date.
 * Erro: grava notification (com 'field') e old_input na sessão e volta para ?action=new.
 * Sucesso: grava ITEM_CREATED e volta para a lista.
 * O dono do item é sempre o usuário da sessão, nunca um valor do formulário.
 */

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../services/ItemService.php';

$user = requireLogin('../../frontend/pages/login.php');

// Aceita só POST. Acesso direto pela URL volta para a lista.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectItems();
}

$query = '?action=new';

if (itemRequestTooLarge()) {
    redirectItemsWithError('IMAGE_TOO_LARGE', 'image', $query, []);
}

$input = itemInputFromPost($_POST);

$error = itemInputError($input);
if ($error !== null) {
    redirectItemsWithError($error['code'], $error['field'], $query, $input);
}

$image = inspectItemImage($_FILES['image'] ?? null);
if ($image['error'] !== null) {
    redirectItemsWithError($image['error']['code'], $image['error']['field'], $query, $input);
}

$savedImage = null;
try {
    require_once __DIR__ . '/../config/connection.php';
    require_once __DIR__ . '/../models/ItemModel.php';

    if (!categoryBelongsToUser($pdo, (int) $input['category_id'], $user['id'])) {
        redirectItemsWithError('INVALID_CATEGORY', 'category_id', $query, $input);
    }

    $savedImage = $image['tmp_name'] !== null ? storeItemImage($image) : null;

    createItem(
        $pdo,
        $user['id'],
        (int) $input['category_id'],
        $input['name'],
        $input['price'],
        $input['link'] !== '' ? $input['link'] : null,
        $savedImage,
        $input['start_date'],
        $input['end_date']
    );
} catch (Throwable $e) {
    // Se a foto já tinha sido salva e o banco falhou, não deixa o arquivo órfão
    deleteItemImageFile($savedImage);
    error_log('[create-item] ' . $e->getMessage());
    redirectItemsWithError('UNKNOWN_ERROR', '', $query, $input);
}

redirectItemsWithSuccess('ITEM_CREATED');