<?php
/**
 * Regras da tela de Configurações: validação do nome e da senha, sessão e redirects.
 *
 * Usado pelos controllers update-account.php, update-password.php e delete-account.php
 * (todos em backend/controllers). Não conversa com o banco: isso é do UserModel.
 */

const ACCOUNT_NAME_MAX_LENGTH = 120;     // coluna users.name é VARCHAR(120)
const ACCOUNT_PASSWORD_MIN_LENGTH = 8;   // mesma regra do cadastro
// Caminhos das telas, a partir de backend/controllers
const SETTINGS_PAGE = '../../frontend/pages/settings.php';
const ACCOUNT_LOGIN_PAGE = '../../frontend/pages/login.php';

/**
 * Lê um campo de texto do formulário. Com $trim = false o valor vai como veio (senhas).
 */
function accountField(array $post, string $field, bool $trim = true): string
{
    if (!isset($post[$field]) || !is_string($post[$field])) {
        return '';
    }
    return $trim ? trim($post[$field]) : $post[$field];
}

/**
 * Valida o nome. Retorna o código do mapa no erro, ou null se estiver certo.
 */
function accountNameError(string $name): ?string
{
    if ($name === '') {
        return 'REQUIRED_FIELD';
    }
    if (mb_strlen($name) > ACCOUNT_NAME_MAX_LENGTH) {
        return 'INVALID_NAME';
    }
    return null;
}

/**
 * Valida os campos de senha, na ordem do formulário, sem o banco
 * (a senha atual é conferida depois, com o hash do usuário).
 * Retorna: ['code' => ..., 'field' => ...] no primeiro erro, ou null.
 */
function passwordInputError(string $current, string $new, string $confirm): ?array
{
    if ($current === '') {
        return ['code' => 'REQUIRED_FIELD', 'field' => 'current_password'];
    }
    if ($new === '') {
        return ['code' => 'REQUIRED_FIELD', 'field' => 'new_password'];
    }
    if (mb_strlen($new) < ACCOUNT_PASSWORD_MIN_LENGTH) {
        return ['code' => 'WEAK_PASSWORD', 'field' => 'new_password'];
    }
    if ($confirm === '') {
        return ['code' => 'REQUIRED_FIELD', 'field' => 'confirm_password'];
    }
    if (!hash_equals($new, $confirm)) {
        return ['code' => 'PASSWORD_MISMATCH', 'field' => 'confirm_password'];
    }
    return null;
}

/**
 * Volta para a tela de configurações. $query ex.: '?section=security'.
 */
function redirectSettings(string $query = ''): void
{
    header('Location: ' . SETTINGS_PAGE . $query);
    exit;
}

/**
 * Erro: grava notification (com o campo) na sessão e volta para a seção.
 * Nunca guarda senha na sessão.
 */
function redirectSettingsWithError(string $code, string $field, string $query): void
{
    $_SESSION['notification'] = ['type' => 'error', 'code' => $code, 'field' => $field];
    redirectSettings($query);
}

/**
 * Sucesso: grava a mensagem (códigos em backend/config/success.php) e volta para a seção.
 */
function redirectSettingsWithSuccess(string $code, string $query): void
{
    $_SESSION['notification'] = ['type' => 'success', 'code' => $code];
    redirectSettings($query);
}

/**
 * Encerra a sessão do usuário e volta para o login com a mensagem.
 * Usado quando a conta foi excluída (ou não existe mais).
 */
function endSessionAndRedirectToLogin(string $type, string $code): void
{
    $_SESSION = [];
    session_regenerate_id(true);
    $_SESSION['notification'] = ['type' => $type, 'code' => $code];
    header('Location: ' . ACCOUNT_LOGIN_PAGE);
    exit;
}
