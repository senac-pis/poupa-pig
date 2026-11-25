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
 * Retorna: array com id, name, email, password_hash, role, photo e accent_color
 *          (os dois últimos podem ser null), ou null se não existir nenhum usuário com esse email.
 * Lança: PDOException se o banco falhar.
 */
function findUserByEmail(PDO $pdo, string $email): ?array
{
    $stmt = $pdo->prepare(
        'SELECT id, name, email, password_hash, role, photo, accent_color
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
 
/**
 * Busca um usuário pelo id (para a tela de Configurações).
 *
 * Recebe: $pdo e $id do usuário (sempre o da sessão).
 * Retorna: array com id, name, password_hash, photo, accent_color e password_changed_days
 *          (dias desde a última troca de senha), ou null se o usuário não existir mais.
 *          Os dias são contados no próprio MySQL, para não depender do fuso do PHP.
 * Lança: PDOException se o banco falhar.
 */
function findUserById(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare(
        'SELECT id, name, password_hash, photo, accent_color,
                DATEDIFF(CURRENT_DATE, DATE(password_changed_at)) AS password_changed_days
         FROM users
         WHERE id = :id
         LIMIT 1'
    );
    $stmt->execute([':id' => $id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user === false) {
        return null;
    }

    $user['id'] = (int) $user['id'];
    $user['password_changed_days'] = (int) $user['password_changed_days'];
    return $user;
}

/**
 * Troca o nome do usuário.
 *
 * Recebe: $pdo, $id (da sessão) e $name já validado.
 * Lança: PDOException se o banco falhar.
 */
function updateUserName(PDO $pdo, int $id, string $name): void
{
    $stmt = $pdo->prepare('UPDATE users SET name = :name WHERE id = :id');
    $stmt->execute([':name' => $name, ':id' => $id]);
}

/**
 * Troca a senha do usuário e marca a data da troca.
 *
 * Recebe: $pdo, $id (da sessão) e $passwordHash (feito com password_hash, nunca a senha pura).
 * Lança: PDOException se o banco falhar.
 */
function updateUserPassword(PDO $pdo, int $id, string $passwordHash): void
{
    $stmt = $pdo->prepare(
        'UPDATE users
         SET password_hash = :password_hash, password_changed_at = CURRENT_TIMESTAMP
         WHERE id = :id'
    );
    $stmt->execute([':password_hash' => $passwordHash, ':id' => $id]);
}

/**
 * Troca a foto do usuário.
 *
 * Recebe: $pdo, $id (da sessão) e $photo (nome do arquivo gerado pelo servidor).
 * Lança: PDOException se o banco falhar.
 */
function updateUserPhoto(PDO $pdo, int $id, string $photo): void
{
    $stmt = $pdo->prepare('UPDATE users SET photo = :photo WHERE id = :id');
    $stmt->execute([':photo' => $photo, ':id' => $id]);
}

/**
 * Troca a cor principal do usuário.
 *
 * Recebe: $pdo, $id (da sessão) e $color já validada (ex.: 'green-400').
 * Lança: PDOException se o banco falhar.
 */
function updateUserAccentColor(PDO $pdo, int $id, string $color): void
{
    $stmt = $pdo->prepare('UPDATE users SET accent_color = :color WHERE id = :id');
    $stmt->execute([':color' => $color, ':id' => $id]);
}

/**
 * Exclui o usuário com os itens e as categorias dele, tudo ou nada (transação).
 *
 * Os itens saem antes das categorias, porque items.category_id é ON DELETE RESTRICT.
 * Recebe: $pdo e $id (da sessão).
 * Retorna: ['item_images' => nomes das fotos dos itens, 'photo' => foto do usuário ou null],
 *          para o controller apagar os arquivos só depois que o banco confirmou.
 * Lança: PDOException se o banco falhar (nada é apagado nesse caso).
 */
function deleteUserAccount(PDO $pdo, int $id): array
{
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare('SELECT image FROM items WHERE user_id = :id AND image IS NOT NULL');
        $stmt->execute([':id' => $id]);
        $images = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $stmt = $pdo->prepare('SELECT photo FROM users WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $photo = $stmt->fetchColumn();

        foreach (['items', 'categories'] as $table) {
            $stmt = $pdo->prepare("DELETE FROM $table WHERE user_id = :id");
            $stmt->execute([':id' => $id]);
        }

        $stmt = $pdo->prepare('DELETE FROM users WHERE id = :id');
        $stmt->execute([':id' => $id]);

        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }

    return ['item_images' => $images, 'photo' => is_string($photo) ? $photo : null];
}
