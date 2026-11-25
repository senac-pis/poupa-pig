<?php
/**
 * Regras da tela de Configurações: validação do nome e da senha, sessão e redirects.
 *
 * Usado pelos controllers update-account.php, update-password.php e delete-account.php
 * (todos em backend/controllers). Não conversa com o banco: isso é do UserModel.
 */

const ACCOUNT_NAME_MAX_LENGTH = 120;     // coluna users.name é VARCHAR(120)
const ACCOUNT_PASSWORD_MIN_LENGTH = 8;   // mesma regra do cadastro
// Fotos dos usuários ficam fora do Git (ver .gitignore). No banco vai só o nome do arquivo.
// A conferência do arquivo é a mesma das fotos dos itens (inspectItemImage, no ItemService).
const USER_PHOTO_DIR = __DIR__ . '/../../frontend/uploads/users';
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
 * Cores que podem ser a cor principal: a paleta das categorias, só os tons 300 a 500
 * (os mais claros não dão contraste com texto branco). Ex.: ['sky-300', 'sky-400', ...].
 */
function accentColorOptions(): array
{
    $styles = require __DIR__ . '/../config/categories.php';
    $colors = [];
    foreach ($styles['color_groups'] as $family) {
        foreach (['300', '400', '500'] as $tone) {
            $colors[] = "$family-$tone";
        }
    }
    return $colors;
}

/**
 * Salva a foto conferida por inspectItemImage na pasta dos usuários, com nome gerado aqui.
 *
 * Retorna: o nome do arquivo (é o que vai para a coluna users.photo).
 * Lança: RuntimeException se não conseguir salvar (o controller devolve UNKNOWN_ERROR).
 */
function storeUserPhoto(array $image): string
{
    if (!is_dir(USER_PHOTO_DIR) && !mkdir(USER_PHOTO_DIR, 0755, true) && !is_dir(USER_PHOTO_DIR)) {
        throw new RuntimeException('Não foi possível criar a pasta de fotos dos usuários.');
    }

    $filename = bin2hex(random_bytes(16)) . '.' . $image['extension'];
    if (!move_uploaded_file($image['tmp_name'], USER_PHOTO_DIR . '/' . $filename)) {
        throw new RuntimeException('Não foi possível salvar a foto do usuário.');
    }

    return $filename;
}

/**
 * Apaga uma foto de usuário. Só apaga nomes no formato que o próprio sistema gera,
 * para um valor estranho no banco nunca apontar para outro arquivo.
 */
function deleteUserPhotoFile(?string $filename): void
{
    if ($filename === null || !preg_match('/^[a-f0-9]{32}\.(jpg|png)$/', $filename)) {
        return;
    }
    @unlink(USER_PHOTO_DIR . '/' . $filename);
}

/**
 * Caminho da foto a partir de frontend/pages, ou '' sem foto (o front mostra o ícone).
 */
function userPhotoUrl(?string $filename): string
{
    if ($filename === null || !preg_match('/^[a-f0-9]{32}\.(jpg|png)$/', $filename)) {
        return '';
    }
    return '../uploads/users/' . $filename;
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
