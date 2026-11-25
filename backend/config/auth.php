<?php
/**
 * Proteção de acesso: exige usuário logado.
 *
 * Uso: $user = requireLogin('<caminho do login a partir de quem chama>');
 *   - controllers (backend/controllers): '../../frontend/pages/login.php'
 *   - páginas (frontend/pages):          'login.php'
 *
 * Quem não está logado volta para o login. Retorna ['id' => int, 'name' => string].
 * O id do usuário vem sempre da sessão, nunca de formulário ou URL.
 */

function requireLogin(string $loginUrl): array
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $user = $_SESSION['user'] ?? null;
    $id = is_array($user) ? (int) ($user['id'] ?? 0) : 0;

    if ($id <= 0) {
        header('Location: ' . $loginUrl);
        exit;
    }

    return ['id' => $id, 'name' => (string) ($user['name'] ?? '')];
}