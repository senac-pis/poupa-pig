<?php
/**
 * Controller de cadastro.
 *
 * Recebe (POST): name, email e password.
 * Erro: grava notification (com o campo com problema) + old_input na sessão
 *       e volta para a tela de cadastro.
 * Sucesso: cria o usuário e redireciona para o login.
 */

session_start();

const REGISTER_PAGE = '../../frontend/pages/register.php';
// TODO: combinar com o Kaio se, após cadastrar, vai para o login ou entra logado
const SUCCESS_PAGE = '../../frontend/pages/login.php';

const MIN_PASSWORD_LENGTH = 8;
const MAX_NAME_LENGTH = 120;   // coluna users.name é VARCHAR(120)
const MAX_EMAIL_LENGTH = 180;  // coluna users.email é VARCHAR(180)

/**
 * Grava o erro na sessão e volta para o cadastro.
 * $field diz ao front em qual input mostrar a mensagem. Nunca guarda a senha.
 */
function redirectWithError(string $code, string $field, string $name = '', string $email = ''): void
{
    $_SESSION['notification'] = ['type' => 'error', 'code' => $code, 'field' => $field];
    $_SESSION['old_input'] = ['name' => $name, 'email' => $email];
    header('Location: ' . REGISTER_PAGE);
    exit;
}

// 1. Aceita só POST. Acesso direto pela URL volta para o cadastro.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . REGISTER_PAGE);
    exit;
}

// 2. Lê as entradas. Nome e email com trim; a senha NÃO passa por trim.
$name = isset($_POST['name']) && is_string($_POST['name']) ? trim($_POST['name']) : '';
$email = isset($_POST['email']) && is_string($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) && is_string($_POST['password']) ? $_POST['password'] : '';

// 3. Valida no servidor (um erro por vez, na ordem dos campos do formulário).
if ($name === '') {
    redirectWithError('REQUIRED_FIELD', 'name', $name, $email);
}

// TODO: exige o código INVALID_NAME no errors.php (e a mensagem do Kaio).
// Se não quiser criar o código, apague este bloco e use maxlength="120" no front.
if (mb_strlen($name) > MAX_NAME_LENGTH) {
    redirectWithError('INVALID_NAME', 'name', $name, $email);
}

if ($email === '') {
    redirectWithError('REQUIRED_FIELD', 'email', $name, $email);
}

if (mb_strlen($email) > MAX_EMAIL_LENGTH || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    redirectWithError('INVALID_EMAIL', 'email', $name, $email);
}

if ($password === '') {
    redirectWithError('REQUIRED_FIELD', 'password', $name, $email);
}

if (mb_strlen($password) < MIN_PASSWORD_LENGTH) {
    redirectWithError('WEAK_PASSWORD', 'password', $name, $email);
}

// 4. Verifica se o email já existe e cria o usuário.
//    O require fica dentro do try para que falha de conexão vire UNKNOWN_ERROR.
try {
    require_once __DIR__ . '/../config/connection.php';
    require_once __DIR__ . '/../models/UserModel.php';

    if (findUserByEmail($pdo, $email) !== null) {
        redirectWithError('EMAIL_ALREADY_EXISTS', 'email', $name, $email);
    }

    // Senha salva só como hash, nunca em texto puro.
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    createUser($pdo, $name, $email, $passwordHash);
} catch (PDOException $e) {
    // Duas requisições ao mesmo tempo com o mesmo email: o UNIQUE do banco barra
    // (erro 1062 do MySQL), então trata como email já cadastrado.
    if (($e->errorInfo[1] ?? null) === 1062) {
        redirectWithError('EMAIL_ALREADY_EXISTS', 'email', $name, $email);
    }
    error_log('[register] ' . $e->getMessage());
    redirectWithError('UNKNOWN_ERROR', '', $name, $email);
} catch (Throwable $e) {
    // Detalhe interno só no log do servidor, nunca para o usuário.
    error_log('[register] ' . $e->getMessage());
    redirectWithError('UNKNOWN_ERROR', '', $name, $email);
}

// 5. Sucesso: limpa o que sobrou na sessão e segue.
unset($_SESSION['old_input'], $_SESSION['notification']);

header('Location: ' . SUCCESS_PAGE);
exit;