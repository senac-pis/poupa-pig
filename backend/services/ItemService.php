<?php
/**
 * Regras da tela de Itens: validação dos campos, upload da foto e redirects.
 *
 * Usado pelos controllers create-item.php, update-item.php e delete-item.php
 * (todos em backend/controllers). Não conversa com o banco: isso é do ItemModel.
 */

const ITEM_NAME_MAX_LENGTH = 100;   // o formulário limita a 100 (a coluna aceita 180)
const ITEM_LINK_MAX_LENGTH = 255;
const ITEM_IMAGE_MAX_BYTES = 5 * 1024 * 1024;
// Fotos ficam fora do Git (ver .gitignore). No banco vai só o nome do arquivo.
const ITEM_IMAGE_DIR = __DIR__ . '/../../frontend/uploads/items';
// Caminho da tela de itens, a partir de backend/controllers
const ITEMS_PAGE = '../../frontend/pages/items.php';

/**
 * Lê os campos de texto do formulário (sempre string, com trim).
 * A foto não entra aqui: vem em $_FILES.
 */
function itemInputFromPost(array $post): array
{
    $fields = ['name', 'category_id', 'price', 'link', 'start_date', 'end_date'];
    $input = [];
    foreach ($fields as $field) {
        $input[$field] = isset($post[$field]) && is_string($post[$field]) ? trim($post[$field]) : '';
    }
    return $input;
}

/**
 * Valida os campos de texto, na ordem do formulário.
 *
 * Retorna: ['code' => 'CODIGO_DO_MAPA', 'field' => 'campo'] no primeiro erro, ou null se estiver tudo certo.
 * Não confere se a categoria é do usuário: isso exige o banco (categoryBelongsToUser).
 */
function itemInputError(array $in): ?array
{
    if ($in['name'] === '') {
        return ['code' => 'REQUIRED_FIELD', 'field' => 'name'];
    }
    if (mb_strlen($in['name']) > ITEM_NAME_MAX_LENGTH) {
        return ['code' => 'INVALID_NAME', 'field' => 'name'];
    }

    if ($in['category_id'] === '') {
        return ['code' => 'REQUIRED_FIELD', 'field' => 'category_id'];
    }
    if (!ctype_digit($in['category_id']) || (int) $in['category_id'] <= 0) {
        return ['code' => 'INVALID_CATEGORY', 'field' => 'category_id'];
    }

    if ($in['price'] === '') {
        return ['code' => 'REQUIRED_FIELD', 'field' => 'price'];
    }
    // Até 8 dígitos e 2 casas (coluna DECIMAL(10,2)) e maior que zero
    if (!preg_match('/^\d{1,8}(\.\d{1,2})?$/', $in['price']) || (float) $in['price'] <= 0) {
        return ['code' => 'INVALID_PRICE', 'field' => 'price'];
    }

    if ($in['link'] !== '' && (mb_strlen($in['link']) > ITEM_LINK_MAX_LENGTH || !isHttpUrl($in['link']))) {
        return ['code' => 'INVALID_URL', 'field' => 'link'];
    }

    foreach (['start_date', 'end_date'] as $field) {
        if ($in[$field] === '') {
            return ['code' => 'REQUIRED_FIELD', 'field' => $field];
        }
        if (!isValidItemDate($in[$field])) {
            return ['code' => 'INVALID_DATE', 'field' => $field];
        }
    }

    // Y-m-d compara certo como texto
    if ($in['end_date'] < $in['start_date']) {
        return ['code' => 'INVALID_PERIOD', 'field' => 'end_date'];
    }

    return null;
}

/**
 * Só aceita http e https (filter_var sozinho deixa passar esquemas como javascript:).
 */
function isHttpUrl(string $url): bool
{
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return false;
    }
    $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
    return in_array($scheme, ['http', 'https'], true);
}

/**
 * Só aceita data real no formato Y-m-d.
 */
function isValidItemDate(string $value): bool
{
    $date = DateTime::createFromFormat('!Y-m-d', $value);
    return $date !== false && $date->format('Y-m-d') === $value;
}

/**
 * Detecta pedido maior que o post_max_size do PHP: nesse caso o PHP descarta
 * $_POST e $_FILES inteiros, e sem esta checagem pareceria "nome vazio".
 */
function itemRequestTooLarge(): bool
{
    return empty($_POST) && empty($_FILES) && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0;
}

/**
 * Confere a foto enviada, sem salvar ainda.
 *
 * Recebe: $_FILES['image'] (ou null).
 * Retorna: ['error' => null|['code','field'], 'tmp_name' => ?string, 'extension' => ?string].
 *          Sem foto enviada: tudo null (a foto é opcional).
 * O tipo é conferido pelo conteúdo do arquivo (finfo + getimagesize), nunca pelo nome ou
 * pelo tipo que o navegador informa. Só PNG e JPG, até 5 MB.
 */
function inspectItemImage(?array $file): array
{
    $ok = ['error' => null, 'tmp_name' => null, 'extension' => null];
    $tooLarge = ['error' => ['code' => 'IMAGE_TOO_LARGE', 'field' => 'image'], 'tmp_name' => null, 'extension' => null];
    $invalid = ['error' => ['code' => 'INVALID_IMAGE', 'field' => 'image'], 'tmp_name' => null, 'extension' => null];

    if ($file === null || !isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return $ok;
    }

    // upload_max_filesize do php.ini (no XAMPP o padrão é 2 MB)
    if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE) {
        return $tooLarge;
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return $invalid;
    }

    $tmp = (string) ($file['tmp_name'] ?? '');
    if (!is_uploaded_file($tmp)) {
        return $invalid;
    }
    if ((int) filesize($tmp) > ITEM_IMAGE_MAX_BYTES) {
        return $tooLarge;
    }

    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($tmp);
    $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png'];
    if (!is_string($mime) || !isset($extensions[$mime]) || @getimagesize($tmp) === false) {
        return $invalid;
    }

    return ['error' => null, 'tmp_name' => $tmp, 'extension' => $extensions[$mime]];
}

/**
 * Salva a foto conferida na pasta de uploads, com nome gerado aqui.
 *
 * Retorna: o nome do arquivo (é o que vai para a coluna items.image).
 * Lança: RuntimeException se não conseguir salvar (o controller devolve UNKNOWN_ERROR).
 */
function storeItemImage(array $image): string
{
    if (!is_dir(ITEM_IMAGE_DIR) && !mkdir(ITEM_IMAGE_DIR, 0755, true) && !is_dir(ITEM_IMAGE_DIR)) {
        throw new RuntimeException('Não foi possível criar a pasta de fotos.');
    }

    $filename = bin2hex(random_bytes(16)) . '.' . $image['extension'];
    if (!move_uploaded_file($image['tmp_name'], ITEM_IMAGE_DIR . '/' . $filename)) {
        throw new RuntimeException('Não foi possível salvar a foto.');
    }

    return $filename;
}

/**
 * Apaga uma foto da pasta de uploads. Só apaga nomes no formato que o próprio
 * sistema gera, para um valor estranho no banco nunca apontar para outro arquivo.
 */
function deleteItemImageFile(?string $filename): void
{
    if ($filename === null || !preg_match('/^[a-f0-9]{32}\.(jpg|png)$/', $filename)) {
        return;
    }
    @unlink(ITEM_IMAGE_DIR . '/' . $filename);
}

/**
 * Volta para a tela de itens. $query é '' (só a lista), '?action=new' ou '?action=edit&id=5'.
 */
function redirectItems(string $query = ''): void
{
    header('Location: ' . ITEMS_PAGE . $query);
    exit;
}

/**
 * Erro: grava notification (com o campo) e old_input na sessão e volta para a tela.
 * $input tem só texto do formulário, nunca a foto.
 */
function redirectItemsWithError(string $code, string $field, string $query, array $input): void
{
    $_SESSION['notification'] = ['type' => 'error', 'code' => $code, 'field' => $field];
    $_SESSION['old_input'] = $input;
    redirectItems($query);
}

/**
 * Sucesso: grava a mensagem (códigos em backend/config/success.php) e volta para a lista.
 */
function redirectItemsWithSuccess(string $code): void
{
    unset($_SESSION['old_input']);
    $_SESSION['notification'] = ['type' => 'success', 'code' => $code];
    redirectItems();
}