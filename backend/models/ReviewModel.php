<?php
/**
 * Acesso a dados das tabelas products e reviews.
 *
 * Aqui ficam só as queries, sem regra de negócio.
 * Validação e erros do mapa ficam com o controller e o ReviewService.
 *
 * Os produtos são compartilhados: todo usuário logado vê os produtos e as reviews de todos.
 * O que é de cada um é a review: só o dono do item pode avaliar, uma vez por produto.
 * O produto é o nome do item, comparado pela collation do banco (sem diferenciar maiúsculas
 * nem acentos) e com os espaços juntados (normalizeProductName, no ReviewService).
 * Em caso de falha do banco, a exceção sobe para quem chamou tratar.
 */

/**
 * Monta o HAVING/WHERE da lista de produtos avaliados (mesmos filtros na lista e na contagem).
 *
 * Recebe: $search (busca pelo nome; '' = sem busca) e $rating (1 a 5; 0 = todas).
 * A nota filtra pela média arredondada: 4 pega de 3,5 até antes de 4,5.
 * Retorna: [WHERE, HAVING, parâmetros nomeados].
 */
function buildProductFilter(string $search, int $rating): array
{
    $where = '1 = 1';
    $having = '1 = 1';
    $params = [];

    if ($search !== '') {
        // Escapa % e _ para a busca tratar como texto comum
        $where = 'p.name LIKE :search';
        $params[':search'] = '%' . addcslashes($search, '%_\\') . '%';
    }

    if ($rating >= 1 && $rating <= 5) {
        $having = 'ROUND(AVG(r.rating)) = :rating';
        $params[':rating'] = $rating;
    }

    return [$where, $having, $params];
}

/**
 * Liga os parâmetros nomeados ao statement (inteiros como inteiros).
 */
function bindReviewParams(PDOStatement $stmt, array $params): void
{
    foreach ($params as $name => $value) {
        $stmt->bindValue($name, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
    }
}

/**
 * Lista os produtos que têm pelo menos uma review, filtrados e paginados.
 *
 * Recebe: $pdo, $search, $rating (ver buildProductFilter), $limit e $offset.
 * Retorna: array de ['id', 'name', 'user_count', 'review_count', 'average' (float)],
 *          do produto com mais reviews para o com menos.
 * Lança: PDOException se o banco falhar.
 */
function listReviewedProducts(PDO $pdo, string $search, int $rating, int $limit, int $offset): array
{
    [$where, $having, $params] = buildProductFilter($search, $rating);

    $stmt = $pdo->prepare(
        'SELECT p.id, p.name, COUNT(DISTINCT r.user_id) AS user_count, COUNT(r.id) AS review_count,
                AVG(r.rating) AS average
         FROM products p
         JOIN reviews r ON r.product_id = p.id
         WHERE ' . $where . '
         GROUP BY p.id, p.name
         HAVING ' . $having . '
         ORDER BY review_count DESC, p.name ASC
         LIMIT :limit OFFSET :offset'
    );
    bindReviewParams($stmt, $params);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    return array_map(
        fn(array $row): array => [
            'id' => (int) $row['id'],
            'name' => $row['name'],
            'user_count' => (int) $row['user_count'],
            'review_count' => (int) $row['review_count'],
            'average' => (float) $row['average'],
        ],
        $stmt->fetchAll(PDO::FETCH_ASSOC)
    );
}

/**
 * Conta os produtos avaliados com os mesmos filtros da lista (para a paginação).
 * Lança: PDOException se o banco falhar.
 */
function countReviewedProducts(PDO $pdo, string $search, int $rating): int
{
    [$where, $having, $params] = buildProductFilter($search, $rating);

    $stmt = $pdo->prepare(
        'SELECT COUNT(*) FROM (
             SELECT p.id
             FROM products p
             JOIN reviews r ON r.product_id = p.id
             WHERE ' . $where . '
             GROUP BY p.id
             HAVING ' . $having . '
         ) AS reviewed'
    );
    bindReviewParams($stmt, $params);
    $stmt->execute();

    return (int) $stmt->fetchColumn();
}

/**
 * Busca um produto pelo id.
 *
 * Retorna: ['id', 'name'] ou null se não existir.
 * Lança: PDOException se o banco falhar.
 */
function findProductById(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare('SELECT id, name FROM products WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row === false ? null : ['id' => (int) $row['id'], 'name' => $row['name']];
}

/**
 * Devolve o id do produto com esse nome, criando o produto se ainda não existir.
 *
 * Recebe: $pdo e $name já normalizado (normalizeProductName).
 * O UNIQUE de products.name garante um produto só, mesmo com dois pedidos ao mesmo tempo:
 * no nome repetido, o LAST_INSERT_ID(id) devolve o id do produto que já existia.
 * Lança: PDOException se o banco falhar.
 */
function findOrCreateProduct(PDO $pdo, string $name): int
{
    $stmt = $pdo->prepare(
        'INSERT INTO products (name) VALUES (:name)
         ON DUPLICATE KEY UPDATE id = LAST_INSERT_ID(id)'
    );
    $stmt->execute([':name' => $name]);

    return (int) $pdo->lastInsertId();
}

/**
 * Confere se o usuário tem algum item com o nome do produto (só quem tem pode avaliar).
 *
 * Os espaços do nome do item são juntados no SQL, como o backend faz com o nome do produto.
 * Lança: PDOException se o banco falhar.
 */
function userHasProductItem(PDO $pdo, int $userId, string $productName): bool
{
    $stmt = $pdo->prepare(
        "SELECT 1 FROM items
         WHERE user_id = :user_id AND REGEXP_REPLACE(TRIM(name), '[[:space:]]+', ' ') = :name
         LIMIT 1"
    );
    $stmt->execute([':user_id' => $userId, ':name' => $productName]);

    return $stmt->fetchColumn() !== false;
}

/**
 * Confere se o usuário já avaliou o produto.
 * Lança: PDOException se o banco falhar.
 */
function userReviewedProduct(PDO $pdo, int $userId, int $productId): bool
{
    $stmt = $pdo->prepare('SELECT 1 FROM reviews WHERE user_id = :user_id AND product_id = :product_id LIMIT 1');
    $stmt->execute([':user_id' => $userId, ':product_id' => $productId]);

    return $stmt->fetchColumn() !== false;
}

/**
 * Resumo das notas de um produto, contando todas as reviews (sem paginação).
 *
 * Retorna: ['review_count' => int, 'average' => float, 'distribution' => [5 => n, ..., 1 => n]].
 * Lança: PDOException se o banco falhar.
 */
function productRatingSummary(PDO $pdo, int $productId): array
{
    $stmt = $pdo->prepare('SELECT rating, COUNT(*) AS total FROM reviews WHERE product_id = :id GROUP BY rating');
    $stmt->execute([':id' => $productId]);

    $distribution = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
    $count = 0;
    $sum = 0;
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $rating = (int) $row['rating'];
        $total = (int) $row['total'];
        $distribution[$rating] = $total;
        $count += $total;
        $sum += $rating * $total;
    }

    return [
        'review_count' => $count,
        'average' => $count > 0 ? $sum / $count : 0.0,
        'distribution' => $distribution,
    ];
}

/**
 * Lista as reviews de um produto, da mais recente para a mais antiga, paginadas.
 *
 * Retorna: array de ['user_name', 'user_photo' (nome do arquivo ou null), 'user_review_count'
 *          (quantas reviews a pessoa já fez no total), 'rating', 'text', 'created_at' (Y-m-d)].
 * Lança: PDOException se o banco falhar.
 */
function listProductReviews(PDO $pdo, int $productId, int $limit, int $offset): array
{
    $stmt = $pdo->prepare(
        'SELECT u.name AS user_name, u.photo AS user_photo,
                (SELECT COUNT(*) FROM reviews ur WHERE ur.user_id = r.user_id) AS user_review_count,
                r.rating, r.text, DATE(r.created_at) AS created_at
         FROM reviews r
         JOIN users u ON u.id = r.user_id
         WHERE r.product_id = :id
         ORDER BY r.created_at DESC, r.id DESC
         LIMIT :limit OFFSET :offset'
    );
    $stmt->bindValue(':id', $productId, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    return array_map(
        fn(array $row): array => array_merge($row, [
            'user_review_count' => (int) $row['user_review_count'],
            'rating' => (int) $row['rating'],
            'text' => (string) ($row['text'] ?? ''),
        ]),
        $stmt->fetchAll(PDO::FETCH_ASSOC)
    );
}

/**
 * Cria a review do usuário.
 *
 * Recebe: $pdo, $productId, $userId (da sessão), $rating (1 a 5) e $text (null quando vazia).
 * Lança: PDOException se o banco falhar. Review repetida também lança
 *        (UNIQUE de product_id + user_id, SQLSTATE 23000).
 */
function createReview(PDO $pdo, int $productId, int $userId, int $rating, ?string $text): void
{
    $stmt = $pdo->prepare(
        'INSERT INTO reviews (product_id, user_id, rating, text)
         VALUES (:product_id, :user_id, :rating, :text)'
    );
    $stmt->execute([
        ':product_id' => $productId,
        ':user_id'    => $userId,
        ':rating'     => $rating,
        ':text'       => $text,
    ]);
}
