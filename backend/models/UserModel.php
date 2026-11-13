<?php
/**
 * Acesso a dados da tabela users.
 *
 * Aqui ficam só as queries, sem regra de negócio.
 * Validação, hash de senha e erros do mapa ficam com o backend.
 *
 * Toda função recebe a conexão $pdo (criada em backend/config/connection.php).
 * Em caso de falha do banco, a exceção sobe para o controller tratar.
 */
 
/**
 * Busca um usuário pelo email.
 *
 * Recebe: $pdo (conexão) e $email do usuário.
 * Retorna: array com id, name, email, password_hash e role,
 *          ou null se não existir nenhum usuário com esse email.
 * Lança: PDOException se o banco falhar.
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
 * Recebe: $pdo (conexão), $name, $email e $passwordHash
 *         (hash feito com password_hash, nunca a senha pura).
 * Retorna: id do usuário criado.
 * Lança: PDOException se o banco falhar (inclusive email duplicado).
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
 