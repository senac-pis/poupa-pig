<?php
/**
 * Controller: trocar a foto do usuário (Configurações > Conta).
 *
 * Recebe (POST multipart): photo. PNG ou JPG até 5 MB, conferido pelo conteúdo do arquivo
 * (mesma regra das fotos dos itens). O nome do arquivo é gerado aqui.
 * Erro: grava notification (com 'field' => 'photo') na sessão e volta para ?section=account.
 * Sucesso: grava a foto nova, apaga a antiga, atualiza a sessão (a sidebar usa) e grava PHOTO_UPDATED.
 */

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../services/AccountService.php';
require_once __DIR__ . '/../services/ItemService.php';

$user = requireLogin('../../frontend/pages/login.php');

// Aceita só POST. Acesso direto pela URL volta para a tela.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectSettings();
}

$query = '?section=account';

// Pedido acima do post_max_size chega sem $_FILES
if (itemRequestTooLarge()) {
    redirectSettingsWithError('IMAGE_TOO_LARGE', 'photo', $query);
}

$savedPhoto = null;
$oldPhoto = null;
try {
    // Dentro do try: se faltar algo no PHP (ex.: extensão fileinfo), vira UNKNOWN_ERROR
    $image = inspectItemImage($_FILES['photo'] ?? null);
    if ($image['error'] !== null) {
        redirectSettingsWithError($image['error']['code'], 'photo', $query);
    }
    // Aqui a foto é o próprio pedido: sem arquivo é campo vazio
    if ($image['tmp_name'] === null) {
        redirectSettingsWithError('REQUIRED_FIELD', 'photo', $query);
    }

    require_once __DIR__ . '/../config/connection.php';
    require_once __DIR__ . '/../models/UserModel.php';

    $dbUser = findUserById($pdo, $user['id']);
    if ($dbUser === null) {
        endSessionAndRedirectToLogin('error', 'UNKNOWN_ERROR');
    }

    $savedPhoto = storeUserPhoto($image);
    updateUserPhoto($pdo, $user['id'], $savedPhoto);

    // Só marca a antiga para apagar depois que o banco já aceitou a nova
    $oldPhoto = $dbUser['photo'];
} catch (Throwable $e) {
    deleteUserPhotoFile($savedPhoto);
    error_log('[update-photo] ' . $e->getMessage());
    redirectSettingsWithError('UNKNOWN_ERROR', '', $query);
}

deleteUserPhotoFile($oldPhoto);
$_SESSION['user']['photo'] = $savedPhoto;
redirectSettingsWithSuccess('PHOTO_UPDATED', $query);
