<?php
/**
 * Controller: salvar o nome do usuário (Configurações > Conta).
 *
 * Recebe (POST): name.
 * Erro: grava notification (com 'field') na sessão e volta para ?section=account&action=edit-name.
 * Sucesso: atualiza o nome no banco e na sessão (a sidebar usa) e grava ACCOUNT_UPDATED.
 * O usuário é sempre o da sessão, nunca um valor do formulário.
 */

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../services/AccountService.php';

$user = requireLogin('../../frontend/pages/login.php');

// Aceita só POST. Acesso direto pela URL volta para a tela.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectSettings();
}

$query = '?section=account&action=edit-name';

$name = accountField($_POST, 'name');

$error = accountNameError($name);
if ($error !== null) {
    redirectSettingsWithError($error, 'name', $query);
}

try {
    require_once __DIR__ . '/../config/connection.php';
    require_once __DIR__ . '/../models/UserModel.php';

    updateUserName($pdo, $user['id'], $name);
} catch (Throwable $e) {
    error_log('[update-account] ' . $e->getMessage());
    redirectSettingsWithError('UNKNOWN_ERROR', '', $query);
}

$_SESSION['user']['name'] = $name;
redirectSettingsWithSuccess('ACCOUNT_UPDATED', '?section=account');
