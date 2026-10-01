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
    // TODO (Lucas): implementar a query
    throw new RuntimeException('findUserByEmail ainda não implementada');
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
    // TODO (Lucas): implementar a query
    throw new RuntimeException('createUser ainda não implementada');
}
?>