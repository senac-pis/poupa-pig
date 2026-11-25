<?php
/**
 * Controller: trocar a senha (Configurações > Segurança).
 *
 * Recebe (POST): current_password, new_password e confirm_password.
 * Confere a senha atual com o hash do banco antes de trocar.
 * Erro: grava notification (com 'field') na sessão e volta para ?section=security.
 *       As senhas nunca vão para a sessão.
 * Sucesso: grava o hash novo e a data da troca, renova o id da sessão e grava PASSWORD_UPDATED.
 */

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../services/AccountService.php';

$user = requireLogin('../../frontend/pages/login.php');

// Aceita só POST. Acesso direto pela URL volta para a tela.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectSettings('?section=security');
}

$query = '?section=security';

// Senha não passa por trim, como no login e no cadastro
$current = accountField($_POST, 'current_password', false);
$new = accountField($_POST, 'new_password', false);
$confirm = accountField($_POST, 'confirm_password', false);

$error = passwordInputError($current, $new, $confirm);
if ($error !== null) {
    redirectSettingsWithError($error['code'], $error['field'], $query);
}

try {
    require_once __DIR__ . '/../config/connection.php';
    require_once __DIR__ . '/../models/UserModel.php';

    $dbUser = findUserById($pdo, $user['id']);
    if ($dbUser === null) {
        endSessionAndRedirectToLogin('error', 'UNKNOWN_ERROR');
    }

    if (!password_verify($current, $dbUser['password_hash'])) {
        redirectSettingsWithError('WRONG_PASSWORD', 'current_password', $query);
    }

    updateUserPassword($pdo, $user['id'], password_hash($new, PASSWORD_DEFAULT));
} catch (Throwable $e) {
    error_log('[update-password] ' . $e->getMessage());
    redirectSettingsWithError('UNKNOWN_ERROR', '', $query);
}

// Senha nova: um id de sessão antigo que tenha vazado deixa de valer
session_regenerate_id(true);
redirectSettingsWithSuccess('PASSWORD_UPDATED', $query);
