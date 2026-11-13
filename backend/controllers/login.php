<?php
/**
 * Controller de login.
 *
 * Recebe (POST): email e password.
 * Erro: grava notification + old_input na sessão e volta para o login.
 * Sucesso: guarda o usuário na sessão e redireciona para a próxima tela.
 */

session_start();

const LOGIN_PAGE = '../../frontend/pages/login.php';
const NEXT_PAGE = '../../frontend/pages/items.php';

/**
 * Grava o erro na sessão e volta para o login. Nunca guarda a senha.
 * $field diz ao front qual input marcar ('' quando o erro não é de um campo).
 */
function redirectWithError(string $code, string $field, string $email = ''): void
{
    $_SESSION['notification'] = ['type' => 'error', 'code' => $code, 'field' => $field];
    $_SESSION['old_input'] = ['email' => $email];
    header('Location: ' . LOGIN_PAGE);
    exit;
}

// 1. Aceita só POST. Acesso direto pela URL volta para o login.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . LOGIN_PAGE);
    exit;
}

// 2. Lê as entradas. Email com trim; a senha NÃO passa por trim.
$email = isset($_POST['email']) && is_string($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) && is_string($_POST['password']) ? $_POST['password'] : '';

// 3. Valida no servidor.
if ($email === '') {
    redirectWithError('REQUIRED_FIELD', 'email', $email);
}

if ($password === '') {
    redirectWithError('REQUIRED_FIELD', 'password', $email);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    redirectWithError('INVALID_EMAIL', 'email', $email);
}

// 4. Busca o usuário e confere a senha.
//    O require fica dentro do try para que falha de conexão vire UNKNOWN_ERROR.
try {
    // Conexão ($pdo). Se falhar, lança exceção e cai no catch (UNKNOWN_ERROR).
    require_once __DIR__ . '/../config/connection.php';
    require_once __DIR__ . '/../models/UserModel.php';

    $user = findUserByEmail($pdo, $email);

    // Email inexistente e senha errada: mesmo código, para não revelar quais emails existem.
    if ($user === null || !password_verify($password, $user['password_hash'])) {
        redirectWithError('INVALID_CREDENTIALS', '', $email);
    }
} catch (Throwable $e) {
    // Detalhe interno só no log do servidor, nunca para o usuário.
    error_log('[login] ' . $e->getMessage());
    redirectWithError('UNKNOWN_ERROR', '', $email);
}

// 5. Sucesso: renova o id da sessão e guarda só o necessário (nunca o hash).
session_regenerate_id(true);
$_SESSION['user'] = [
    'id'   => $user['id'],
    'name' => $user['name'],
];
unset($_SESSION['old_input'], $_SESSION['notification']);

header('Location: ' . NEXT_PAGE);
exit;