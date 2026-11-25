<?php
/**
 * Regras do Dashboard: validação da decisão (Comprei ou Desisti) e redirects.
 *
 * Usado pelo controller decide-item.php (backend/controllers).
 * Não conversa com o banco: isso é do ItemModel.
 */

const ITEM_DECISIONS = ['bought', 'gave_up'];
// Caminho do Dashboard, a partir de backend/controllers
const DASHBOARD_PAGE = '../../frontend/pages/dashboard.php';

/**
 * Lê o id do item do formulário. Retorna 0 quando não é um número positivo.
 */
function decisionItemId(array $post): int
{
    $rawId = $post['id'] ?? '';
    return is_string($rawId) && ctype_digit($rawId) ? (int) $rawId : 0;
}

/**
 * Lê a decisão do formulário. Retorna '' quando não é 'bought' nem 'gave_up'.
 */
function decisionStatus(array $post): string
{
    $decision = $post['decision'] ?? '';
    return is_string($decision) && in_array($decision, ITEM_DECISIONS, true) ? $decision : '';
}

/**
 * Volta para o Dashboard (no mês atual, onde fica "Decida em breve").
 */
function redirectDashboard(): void
{
    header('Location: ' . DASHBOARD_PAGE);
    exit;
}

/**
 * Erro: grava a notification na sessão e volta para o Dashboard.
 */
function redirectDashboardWithError(string $code): void
{
    $_SESSION['notification'] = ['type' => 'error', 'code' => $code, 'field' => ''];
    redirectDashboard();
}

/**
 * Sucesso: grava a mensagem (códigos em backend/config/success.php) e volta para o Dashboard.
 */
function redirectDashboardWithSuccess(string $code): void
{
    $_SESSION['notification'] = ['type' => 'success', 'code' => $code];
    redirectDashboard();
}
