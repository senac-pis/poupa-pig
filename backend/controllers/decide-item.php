<?php
/**
 * Controller: decidir um item (Comprei ou Desisti do Dashboard).
 *
 * Recebe (POST): id e decision ('bought' ou 'gave_up').
 * Só decide item do usuário logado que ainda está pensando; grava o status e a data da decisão.
 * Erro: grava notification na sessão e volta para o Dashboard.
 * Sucesso: grava ITEM_BOUGHT ou ITEM_GAVE_UP e volta para o Dashboard.
 * Item inexistente e item de outro usuário dão o mesmo erro (ITEM_NOT_FOUND).
 */

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../services/DashboardService.php';

$user = requireLogin('../../frontend/pages/login.php');

// Aceita só POST. Acesso direto pela URL volta para o Dashboard.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectDashboard();
}

$itemId = decisionItemId($_POST);
if ($itemId <= 0) {
    redirectDashboardWithError('ITEM_NOT_FOUND');
}

$decision = decisionStatus($_POST);
if ($decision === '') {
    redirectDashboardWithError('INVALID_DECISION');
}

try {
    require_once __DIR__ . '/../config/connection.php';
    require_once __DIR__ . '/../models/ItemModel.php';

    $item = findItemById($pdo, $itemId, $user['id']);
    if ($item === null) {
        redirectDashboardWithError('ITEM_NOT_FOUND');
    }

    // Já decidido (ou decidido por outro clique entre a busca e o UPDATE)
    if ($item['status'] !== 'thinking' || !decideItem($pdo, $itemId, $user['id'], $decision)) {
        redirectDashboardWithError('ITEM_ALREADY_DECIDED');
    }
} catch (Throwable $e) {
    error_log('[decide-item] ' . $e->getMessage());
    redirectDashboardWithError('UNKNOWN_ERROR');
}

redirectDashboardWithSuccess($decision === 'gave_up' ? 'ITEM_GAVE_UP' : 'ITEM_BOUGHT');
