<?php
/**
 * Acesso a dados das tabelas products e reviews.
 *
 * Aqui ficam só as queries, sem regra de negócio.
 * Validação e erros do mapa ficam com o controller e o ReviewService.
 *
 * Os produtos são compartilhados: as reviews de um produto são de todos os usuários.
 * A lista de Reviews mostra os produtos dos itens de cada um, e só o dono do item avalia,
 * uma vez por produto.
 * O produto é o nome do item, comparado pela collation do banco (sem diferenciar maiúsculas
 * nem acentos) e com os espaços juntados (REGEXP_REPLACE nas queries).
 * Em caso de falha do banco, a exceção sobe para quem chamou tratar.
 */

/**
 * Garante que cada item do usuário tenha o seu produto (o nome do item com os espaços juntados).
 *
 * Roda antes da lista de Reviews: assim os itens que já existiam, ou que foram renomeados,
 * aparecem na lista.
 * Só tenta inserir os nomes que ainda não são produto: o InnoDB gasta um número do
 * AUTO_INCREMENT a cada tentativa, mesmo quando o UNIQUE recusa, e sem esse filtro os ids
 * pulariam a cada vez que a lista abre. O ON DUPLICATE KEY fica para dois pedidos ao mesmo tempo.
 * Lança: PDOException se o banco falhar.
 */
function syncUserProducts(PDO $pdo, int $userId): void
{
    $stmt = $pdo->prepare(
        "INSERT INTO products (name)
         SELECT DISTINCT REGEXP_REPLACE(TRIM(i.name), '[[:space:]]+', ' ') AS product_name
         FROM items i
         WHERE i.user_id = :user_id
           AND NOT EXISTS (
               SELECT 1 FROM products p
               WHERE p.name = REGEXP_REPLACE(TRIM(i.name), '[[:space:]]+', ' ')
           )
         ON DUPLICATE KEY UPDATE name = products.name"
    );
    $stmt->execute([':user_id' => $userId]);
}

/**
 * Monta o WHERE/HAVING da lista de Reviews (mesmos filtros na lista e na contagem).
 *
 * A lista mostra os produtos que estão nos itens do usuário, com ou sem review:
 * é por ela que a pessoa chega na página do produto para avaliar.
 * Recebe: $userId, $search (busca pelo nome; '' = sem busca) e $rating (1 a 5; 0 = todas).
 * A nota filtra pela média arredondada: 4 pega de 3,5 até antes de 4,5 (produto sem review não entra).
 * Retorna: [WHERE, HAVING, parâmetros nomeados].
 */
function buildProductFilter(int $userId, string $search, int $rating): array
{
    $where = "EXISTS (
        SELECT 1 FROM items i
        WHERE i.user_id = :user_id AND REGEXP_REPLACE(TRIM(i.name), '[[:space:]]+', ' ') = p.name
    )";
    $having = '1 = 1';
    $params = [':user_id' => $userId];

    if ($search !== '') {
        // Escapa % e _ para a busca tratar como texto comum
        $where .= ' AND p.name LIKE :search';
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
 * Lista os produtos dos itens do usuário, com as reviews de todos os usuários, filtrados e paginados.
 *
 * Recebe: $pdo, $userId, $search, $rating (ver buildProductFilter), $limit e $offset.
 * Retorna: array de ['id', 'name', 'user_count', 'review_count', 'average' (float, 0 sem review)],
 *          do produto com mais reviews para o com menos, e por nome no empate.
 * Lança: PDOException se o banco falhar.
 */
function listUserProducts(PDO $pdo, int $userId, string $search, int $rating, int $limit, int $offset): array
{
    [$where, $having, $params] = buildProductFilter($userId, $search, $rating);

    $stmt = $pdo->prepare(
        'SELECT p.id, p.name, COUNT(DISTINCT r.user_id) AS user_count, COUNT(r.id) AS review_count,
                COALESCE(AVG(r.rating), 0) AS average
         FROM products p
         LEFT JOIN reviews r ON r.product_id = p.id
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
 * Conta os produtos da lista com os mesmos filtros (para a paginação).
 * Lança: PDOException se o banco falhar.
 */
function countUserProducts(PDO $pdo, int $userId, string $search, int $rating): int
{
    [$where, $having, $params] = buildProductFilter($userId, $search, $rating);

    $stmt = $pdo->prepare(
        'SELECT COUNT(*) FROM (
             SELECT p.id
             FROM products p
             LEFT JOIN reviews r ON r.product_id = p.id
             WHERE ' . $where . '
             GROUP BY p.id
             HAVING ' . $having . '
         ) AS user_products'
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
 * Confere se o produto pode ser visto por quem não tem o item: é do catálogo (is_public)
 * ou já tem pelo menos uma review (alguém publicou de propósito).
 *
 * Produto que só existe porque está nos itens de alguém não aparece para os outros:
 * sem isso, trocar o ?id= na URL mostraria os nomes dos itens de outras pessoas.
 * Lança: PDOException se o banco falhar.
 */
function productIsPublic(PDO $pdo, int $productId): bool
{
    $stmt = $pdo->prepare(
        'SELECT 1 FROM products p
         WHERE p.id = :id
           AND (p.is_public OR EXISTS (SELECT 1 FROM reviews r WHERE r.product_id = p.id))
         LIMIT 1'
    );
    $stmt->execute([':id' => $productId]);

    return $stmt->fetchColumn() !== false;
}

/**
 * Nomes sugeridos no autocompletar do nome do item (criar e editar).
 *
 * Só o que o usuário pode ver: o catálogo, os produtos que já têm review e os nomes dos
 * itens dele. Item de outra pessoa sem review nunca aparece.
 * Retorna: lista de nomes em ordem alfabética, sem repetir (a collation junta maiúsculas e acentos).
 * Lança: PDOException se o banco falhar.
 */
function listProductSuggestions(PDO $pdo, int $userId): array
{
    $stmt = $pdo->prepare(
        "SELECT p.name AS name
         FROM products p
         WHERE p.is_public OR EXISTS (SELECT 1 FROM reviews r WHERE r.product_id = p.id)
         UNION
         SELECT REGEXP_REPLACE(TRIM(i.name), '[[:space:]]+', ' ') AS name
         FROM items i
         WHERE i.user_id = :user_id
         ORDER BY name"
    );
    $stmt->execute([':user_id' => $userId]);

    return $stmt->fetchAll(PDO::FETCH_COLUMN);
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
