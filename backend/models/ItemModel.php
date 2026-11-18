<?php
/**
 * Acesso a dados das tabelas items e categories.
 *
 * Aqui ficam só as queries, sem regra de negócio.
 * Validação, upload de foto e erros do mapa ficam com os controllers e o ItemService.
 *
 * Toda função recebe a conexão $pdo (criada em backend/config/connection.php).
 * Toda query filtra por $userId: um usuário nunca enxerga nem altera dado de outro.
 * Em caso de falha do banco, a exceção sobe para o controller tratar.
 */

/**
 * Monta o WHERE da listagem e da contagem (mesmos filtros nas duas).
 *
 * Recebe: $userId e $filters com as chaves opcionais:
 *   search (string), category_id (int), start_date e end_date (Y-m-d ou null).
 * O período filtra pela data de inclusão (start_date do item):
 *   start_date do item >= início do filtro e start_date do item <= fim do filtro.
 * Retorna: [string do WHERE, array de parâmetros nomeados].
 */
function buildItemFilter(int $userId, array $filters): array
{
    $where = ['i.user_id = :user_id'];
    $params = [':user_id' => $userId];

    $search = (string) ($filters['search'] ?? '');
    if ($search !== '') {
        // Escapa % e _ para a busca tratar como texto comum
        $where[] = 'i.name LIKE :search';
        $params[':search'] = '%' . addcslashes($search, '%_\\') . '%';
    }

    $categoryId = (int) ($filters['category_id'] ?? 0);
    if ($categoryId > 0) {
        $where[] = 'i.category_id = :category_id';
        $params[':category_id'] = $categoryId;
    }

    if (!empty($filters['start_date'])) {
        $where[] = 'i.start_date >= :filter_start';
        $params[':filter_start'] = (string) $filters['start_date'];
    }

    if (!empty($filters['end_date'])) {
        $where[] = 'i.start_date <= :filter_end';
        $params[':filter_end'] = (string) $filters['end_date'];
    }

    return [implode(' AND ', $where), $params];
}

/**
 * Liga os parâmetros nomeados ao statement (inteiros como inteiros).
 */
function bindItemParams(PDOStatement $stmt, array $params): void
{
    foreach ($params as $name => $value) {
        $stmt->bindValue($name, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
    }
}

/**
 * Converte os ids para int (o resto fica como o banco devolveu).
 */
function normalizeItemRow(array $row): array
{
    $row['id'] = (int) $row['id'];
    $row['category_id'] = (int) $row['category_id'];
    return $row;
}

/**
 * Lista os itens do usuário, já com os dados da categoria (JOIN), filtrados e paginados.
 *
 * Recebe: $pdo, $userId, $filters (ver buildItemFilter), $limit e $offset.
 * Retorna: array de itens com id, name, image, price, link, start_date, end_date,
 *          status, category_id, category_name, category_icon e category_color
 *          (do mais recente para o mais antigo), ou array vazio.
 * Lança: PDOException se o banco falhar.
 */
function listItems(PDO $pdo, int $userId, array $filters, int $limit, int $offset): array
{
    [$where, $params] = buildItemFilter($userId, $filters);

    $stmt = $pdo->prepare(
        'SELECT i.id, i.name, i.image, i.price, i.link, i.start_date, i.end_date, i.status, i.category_id,
                c.name AS category_name, c.icon AS category_icon, c.color AS category_color
         FROM items i
         JOIN categories c ON c.id = i.category_id
         WHERE ' . $where . '
         ORDER BY i.start_date DESC, i.id DESC
         LIMIT :limit OFFSET :offset'
    );
    bindItemParams($stmt, $params);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    return array_map('normalizeItemRow', $stmt->fetchAll(PDO::FETCH_ASSOC));
}

/**
 * Conta os itens do usuário com os mesmos filtros da listagem (para a paginação).
 *
 * Recebe: $pdo, $userId e $filters (ver buildItemFilter).
 * Retorna: total de itens.
 * Lança: PDOException se o banco falhar.
 */
function countItems(PDO $pdo, int $userId, array $filters): int
{
    [$where, $params] = buildItemFilter($userId, $filters);

    $stmt = $pdo->prepare('SELECT COUNT(*) FROM items i WHERE ' . $where);
    bindItemParams($stmt, $params);
    $stmt->execute();

    return (int) $stmt->fetchColumn();
}

/**
 * Busca um item do usuário pelo id.
 *
 * Recebe: $pdo, $id do item e $userId (dono).
 * Retorna: array no mesmo formato da listagem, ou null se não existir
 *          ou se for de outro usuário (os dois casos são iguais).
 * Lança: PDOException se o banco falhar.
 */
function findItemById(PDO $pdo, int $id, int $userId): ?array
{
    $stmt = $pdo->prepare(
        'SELECT i.id, i.name, i.image, i.price, i.link, i.start_date, i.end_date, i.status, i.category_id,
                c.name AS category_name, c.icon AS category_icon, c.color AS category_color
         FROM items i
         JOIN categories c ON c.id = i.category_id
         WHERE i.id = :id AND i.user_id = :user_id
         LIMIT 1'
    );
    $stmt->execute([':id' => $id, ':user_id' => $userId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row === false ? null : normalizeItemRow($row);
}

/**
 * Lista as categorias do usuário com a quantidade de itens de cada uma.
 *
 * Recebe: $pdo e $userId.
 * Retorna: array de ['id' => int, 'name' => string, 'count' => int], por nome.
 *          A contagem é o total do usuário, sem considerar os filtros da tela.
 * Lança: PDOException se o banco falhar.
 */
function listCategoriesWithCount(PDO $pdo, int $userId): array
{
    $stmt = $pdo->prepare(
        'SELECT c.id, c.name, COUNT(i.id) AS count
         FROM categories c
         LEFT JOIN items i ON i.category_id = c.id AND i.user_id = c.user_id
         WHERE c.user_id = :user_id
         GROUP BY c.id, c.name
         ORDER BY c.name ASC'
    );
    $stmt->execute([':user_id' => $userId]);

    return array_map(
        fn(array $row): array => ['id' => (int) $row['id'], 'name' => $row['name'], 'count' => (int) $row['count']],
        $stmt->fetchAll(PDO::FETCH_ASSOC)
    );
}

/**
 * Confere se a categoria existe e é do usuário.
 *
 * Recebe: $pdo, $categoryId e $userId.
 * Retorna: true se for do usuário, false se não existir ou for de outro.
 * Lança: PDOException se o banco falhar.
 */
function categoryBelongsToUser(PDO $pdo, int $categoryId, int $userId): bool
{
    $stmt = $pdo->prepare('SELECT 1 FROM categories WHERE id = :id AND user_id = :user_id LIMIT 1');
    $stmt->execute([':id' => $categoryId, ':user_id' => $userId]);

    return $stmt->fetchColumn() !== false;
}

/**
 * Cria um item (o status começa como 'thinking', padrão do banco).
 *
 * Recebe: $pdo, $userId (da sessão), $categoryId, $name, $price (ex.: '4000.00'),
 *         $link e $image (nome do arquivo; null quando não há), $startDate e $endDate (Y-m-d).
 * Retorna: id do item criado.
 * Lança: PDOException se o banco falhar.
 */
function createItem(
    PDO $pdo,
    int $userId,
    int $categoryId,
    string $name,
    string $price,
    ?string $link,
    ?string $image,
    string $startDate,
    string $endDate
): int {
    $stmt = $pdo->prepare(
        'INSERT INTO items (user_id, category_id, name, price, link, image, start_date, end_date)
         VALUES (:user_id, :category_id, :name, :price, :link, :image, :start_date, :end_date)'
    );
    $stmt->execute([
        ':user_id'     => $userId,
        ':category_id' => $categoryId,
        ':name'        => $name,
        ':price'       => $price,
        ':link'        => $link,
        ':image'       => $image,
        ':start_date'  => $startDate,
        ':end_date'    => $endDate,
    ]);

    return (int) $pdo->lastInsertId();
}

/**
 * Atualiza um item do usuário (o status não muda aqui).
 *
 * Recebe: $pdo, $id, $userId (dono) e os mesmos campos do createItem.
 * Retorna: nada. Se o id não for do usuário, nenhuma linha muda
 *          (o controller confere antes com findItemById).
 * Lança: PDOException se o banco falhar.
 */
function updateItem(
    PDO $pdo,
    int $id,
    int $userId,
    int $categoryId,
    string $name,
    string $price,
    ?string $link,
    ?string $image,
    string $startDate,
    string $endDate
): void {
    $stmt = $pdo->prepare(
        'UPDATE items
         SET category_id = :category_id, name = :name, price = :price, link = :link,
             image = :image, start_date = :start_date, end_date = :end_date
         WHERE id = :id AND user_id = :user_id'
    );
    $stmt->execute([
        ':category_id' => $categoryId,
        ':name'        => $name,
        ':price'       => $price,
        ':link'        => $link,
        ':image'       => $image,
        ':start_date'  => $startDate,
        ':end_date'    => $endDate,
        ':id'          => $id,
        ':user_id'     => $userId,
    ]);
}

/**
 * Remove um item do usuário.
 *
 * Recebe: $pdo, $id e $userId (dono).
 * Retorna: nada. Se o id não for do usuário, nada é removido.
 * Lança: PDOException se o banco falhar.
 */
function deleteItem(PDO $pdo, int $id, int $userId): void
{
    $stmt = $pdo->prepare('DELETE FROM items WHERE id = :id AND user_id = :user_id');
    $stmt->execute([':id' => $id, ':user_id' => $userId]);
}