<?php
/**
 * Controller: editar item.
 *
 * Recebe (POST multipart): id, image (opcional), name, category_id, price,
 * link (opcional), start_date e end_date. O status não muda aqui.
 * Sem foto nova, a foto atual continua. Com foto nova, a antiga é apagada.
 * Erro: grava notification (com 'field') e old_input na sessão e volta para ?action=edit&id=N.
 * Sucesso: grava ITEM_UPDATED e volta para a lista.
 * Item inexistente e item de outro usuário dão o mesmo erro (ITEM_NOT_FOUND).
 */

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../services/ItemService.php';

$user = requireLogin('../../frontend/pages/login.php');

// Aceita só POST. Acesso direto pela URL volta para a lista.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectItems();
}

$rawId = $_POST['id'] ?? '';
$itemId = is_string($rawId) && ctype_digit($rawId) ? (int) $rawId : 0;

if ($itemId <= 0) {
    redirectItemsWithError('ITEM_NOT_FOUND', '', '', []);
}

$query = '?action=edit&id=' . $itemId;

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
$oldImage = null;
try {
    require_once __DIR__ . '/../config/connection.php';
    require_once __DIR__ . '/../models/ItemModel.php';

    $item = findItemById($pdo, $itemId, $user['id']);
    if ($item === null) {
        redirectItemsWithError('ITEM_NOT_FOUND', '', '', []);
    }

    if (!categoryBelongsToUser($pdo, (int) $input['category_id'], $user['id'])) {
        redirectItemsWithError('INVALID_CATEGORY', 'category_id', $query, $input);
    }

    $savedImage = $image['tmp_name'] !== null ? storeItemImage($image) : null;

    updateItem(
        $pdo,
        $itemId,
        $user['id'],
        (int) $input['category_id'],
        $input['name'],
        $input['price'],
        $input['link'] !== '' ? $input['link'] : null,
        $savedImage ?? $item['image'],
        $input['start_date'],
        $input['end_date']
    );

    // Só marca a foto antiga para apagar depois que o banco já aceitou a nova
    if ($savedImage !== null) {
        $oldImage = $item['image'];
    }
} catch (Throwable $e) {
    deleteItemImageFile($savedImage);
    error_log('[update-item] ' . $e->getMessage());
    redirectItemsWithError('UNKNOWN_ERROR', '', $query, $input);
}

deleteItemImageFile($oldImage);
redirectItemsWithSuccess('ITEM_UPDATED');