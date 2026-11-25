<?php
/**
 * Controller: salvar a cor principal (Configurações > Aparência).
 *
 * Diferente dos outros, é chamado por fetch: a cor muda no clique, sem recarregar a página.
 * Por isso responde só com o status HTTP, sem redirect e sem notification:
 *   204 salvou | 405 não é POST | 422 cor fora da lista | 500 falha do banco.
 * Recebe (POST): accent_color, só com os valores de accentColorOptions() (ex.: 'green-400').
 * Sucesso: grava no banco e na sessão (o accent-color.php aplica em todas as telas).
 */

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../services/AccountService.php';

$user = requireLogin('../../frontend/pages/login.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$color = accountField($_POST, 'accent_color');
if (!in_array($color, accentColorOptions(), true)) {
    http_response_code(422);
    exit;
}

try {
    require_once __DIR__ . '/../config/connection.php';
    require_once __DIR__ . '/../models/UserModel.php';

    updateUserAccentColor($pdo, $user['id'], $color);
} catch (Throwable $e) {
    error_log('[update-accent-color] ' . $e->getMessage());
    http_response_code(500);
    exit;
}

$_SESSION['user']['accent_color'] = $color;
http_response_code(204);
