<?php
/**
 * Controller: remover item.
 *
 * Recebe (POST): id.
 * Apaga o item do usuário logado e a foto dele (se tiver).
 * Erro: grava notification na sessão e volta para a lista.
 * Sucesso: grava ITEM_DELETED e volta para a lista.
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

$image = null;
try {
    require_once __DIR__ . '/../config/connection.php';
    require_once __DIR__ . '/../models/ItemModel.php';

    $item = findItemById($pdo, $itemId, $user['id']);
    if ($item === null) {
        redirectItemsWithError('ITEM_NOT_FOUND', '', '', []);
    }

    deleteItem($pdo, $itemId, $user['id']);
    $image = $item['image'];
} catch (Throwable $e) {
    error_log('[delete-item] ' . $e->getMessage());
    redirectItemsWithError('UNKNOWN_ERROR', '', '', []);
}

// A foto só é apagada depois que o banco removeu o item
deleteItemImageFile($image);
redirectItemsWithSuccess('ITEM_DELETED');