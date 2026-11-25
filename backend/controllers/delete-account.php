<?php
/**
 * Controller: excluir a conta (Configurações > Conta).
 *
 * Recebe (POST): nada além da confirmação do modal. O usuário é sempre o da sessão.
 * Apaga os itens, as categorias e o usuário numa transação; depois apaga as fotos dos itens
 * e a foto do usuário.
 * Erro: grava UNKNOWN_ERROR e volta para a tela (nada foi apagado).
 * Sucesso: encerra a sessão e volta para o login com ACCOUNT_DELETED.
 */

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../services/AccountService.php';
require_once __DIR__ . '/../services/ItemService.php';

$user = requireLogin('../../frontend/pages/login.php');

// Aceita só POST. Acesso direto pela URL volta para a tela, sem apagar nada.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectSettings();
}

$deleted = ['item_images' => [], 'photo' => null];
try {
    require_once __DIR__ . '/../config/connection.php';
    require_once __DIR__ . '/../models/UserModel.php';

    $deleted = deleteUserAccount($pdo, $user['id']);
} catch (Throwable $e) {
    error_log('[delete-account] ' . $e->getMessage());
    redirectSettingsWithError('UNKNOWN_ERROR', '', '');
}

// As fotos só são apagadas depois que o banco confirmou
foreach ($deleted['item_images'] as $image) {
    deleteItemImageFile($image);
}
deleteUserPhotoFile($deleted['photo']);

endSessionAndRedirectToLogin('success', 'ACCOUNT_DELETED');
