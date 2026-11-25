<?php
/**
 * Regras da tela de Categorias: validação dos campos, percentual e redirects.
 *
 * Usado pelos controllers create-category.php, update-category.php e delete-category.php
 * (todos em backend/controllers) e pela tela frontend/pages/categories.php.
 * Não conversa com o banco: isso é do CategoryModel.
 */

const CATEGORY_NAME_MAX_LENGTH = 120;            // coluna varchar(120)
const CATEGORY_DESCRIPTION_MAX_BYTES = 65535;    // limite da coluna text (em bytes)
// Caminho da tela de categorias, a partir de backend/controllers
const CATEGORIES_PAGE = '../../frontend/pages/categories.php';

/**
 * Lê os campos do formulário (sempre string, com trim).
 */
function categoryInputFromPost(array $post): array
{
    $fields = ['name', 'description', 'icon', 'color'];
    $input = [];
    foreach ($fields as $field) {
        $input[$field] = isset($post[$field]) && is_string($post[$field]) ? trim($post[$field]) : '';
    }
    return $input;
}

/**
 * Valida os campos, na ordem do formulário (ícone, cor, nome, descrição).
 *
 * Ícone e cor vazios são aceitos: viram o padrão (ver categoryIcon e categoryColor).
 * Preenchidos, só valem se estiverem em backend/config/categories.php.
 * Retorna: ['code' => 'CODIGO_DO_MAPA', 'field' => 'campo'] no primeiro erro, ou null se estiver tudo certo.
 */
function categoryInputError(array $in): ?array
{
    $styles = require __DIR__ . '/../config/categories.php';

    if ($in['icon'] !== '' && !in_array($in['icon'], $styles['icons'], true)) {
        return ['code' => 'INVALID_ICON', 'field' => 'icon'];
    }

    if ($in['color'] !== '' && !in_array($in['color'], $styles['colors'], true)) {
        return ['code' => 'INVALID_COLOR', 'field' => 'color'];
    }

    if ($in['name'] === '') {
        return ['code' => 'REQUIRED_FIELD', 'field' => 'name'];
    }
    if (mb_strlen($in['name']) > CATEGORY_NAME_MAX_LENGTH) {
        return ['code' => 'INVALID_NAME', 'field' => 'name'];
    }

    if (strlen($in['description']) > CATEGORY_DESCRIPTION_MAX_BYTES) {
        return ['code' => 'INVALID_DESCRIPTION', 'field' => 'description'];
    }

    return null;
}

/**
 * Ícone que vai para o banco: o escolhido ou, sem escolha, o padrão (box-seam).
 * Recebe o valor já validado por categoryInputError.
 */
function categoryIcon(string $icon): string
{
    $styles = require __DIR__ . '/../config/categories.php';
    return $icon !== '' ? $icon : $styles['icons'][0];
}

/**
 * Cor que vai para o banco: a escolhida ou, sem escolha, a padrão (gray-200).
 * Recebe o valor já validado por categoryInputError.
 */
function categoryColor(string $color): string
{
    $styles = require __DIR__ . '/../config/categories.php';
    return $color !== '' ? $color : $styles['colors'][0];
}

/**
 * Percentual dos itens do usuário que estão na categoria, arredondado (0 a 100).
 * Sem nenhum item, é 0.
 */
function categoryItemPercent(int $categoryItems, int $totalItems): int
{
    if ($totalItems <= 0) {
        return 0;
    }
    return (int) round($categoryItems * 100 / $totalItems);
}

/**
 * Volta para a tela de categorias. $query é '' (só a lista), '?action=new' ou '?action=edit&id=5'.
 */
function redirectCategories(string $query = ''): void
{
    header('Location: ' . CATEGORIES_PAGE . $query);
    exit;
}

/**
 * Erro: grava notification (com o campo) e old_input na sessão e volta para a tela.
 */
function redirectCategoriesWithError(string $code, string $field, string $query, array $input): void
{
    $_SESSION['notification'] = ['type' => 'error', 'code' => $code, 'field' => $field];
    $_SESSION['old_input'] = $input;
    redirectCategories($query);
}

/**
 * Sucesso: grava a mensagem (códigos em backend/config/success.php) e volta para a lista.
 */
function redirectCategoriesWithSuccess(string $code): void
{
    unset($_SESSION['old_input']);
    $_SESSION['notification'] = ['type' => 'success', 'code' => $code];
    redirectCategories();
}
