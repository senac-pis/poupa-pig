<?php
/**
 * Acesso a dados da tabela users.
 *
 * Aqui ficam só as queries, sem regra de negócio.
 * Validação, hash de senha e erros do mapa ficam com o backend.
 *
 * Toda função recebe a conexão $pdo (criada em backend/config/connection.php).
 */

/**
 * Busca um usuário pelo email.
 *
 * Recebe: $email do usuário.
 * Retorna: array com id, name, email, password_hash e role,
 *          ou null se não existir nenhum usuário com esse email.
 */
function findUserByEmail(PDO $pdo, string $email): ?array
{
    $stmt = $pdo->prepare(
        'SELECT id, name, email, password_hash, role
         FROM users
         WHERE email = :email
         LIMIT 1'
    );
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user === false) {
        return null;
    }

    $user['id'] = (int) $user['id'];
    return $user;
}

/**
 * Cria um novo usuário.
 *
 * Recebe: $name, $email e $passwordHash (a senha já chega com hash,
 *         gerado pelo backend com password_hash()).
 * Retorna: o id do usuário criado.
 */
function createUser(PDO $pdo, string $name, string $email, string $passwordHash): int
{
    $stmt = $pdo->prepare(
        'INSERT INTO users (name, email, password_hash)
         VALUES (:name, :email, :password_hash)'
    );
    $stmt->execute([
        ':name'          => $name,
        ':email'         => $email,
        ':password_hash' => $passwordHash,
    ]);

    return (int) $pdo->lastInsertId();
}