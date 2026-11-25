<?php
/**
 * Acesso a dados da tabela categories (com a contagem de items).
 *
 * Aqui ficam só as queries, sem regra de negócio.
 * Validação e erros do mapa ficam com os controllers e o CategoryService.
 *
 * Toda função recebe a conexão $pdo (criada em backend/config/connection.php).
 * Toda query filtra por $userId: um usuário nunca enxerga nem altera dado de outro.
 * Em caso de falha do banco, a exceção sobe para o controller tratar.
 *
 * Obs.: categoryBelongsToUser e listCategoriesWithCount (usadas pela tela de Itens)
 * continuam no ItemModel.
 */

/**
 * Monta o WHERE da listagem e da contagem (mesma busca nas duas).
 *
 * Recebe: $userId e $search (busca pelo nome; '' = sem busca).
 * Retorna: [string do WHERE, array de parâmetros nomeados].
 */
function buildCategoryFilter(int $userId, string $search): array
{
    $where = ['c.user_id = :user_id'];
    $params = [':user_id' => $userId];

    if ($search !== '') {
        // Escapa % e _ para a busca tratar como texto comum
        $where[] = 'c.name LIKE :search';
        $params[':search'] = '%' . addcslashes($search, '%_\\') . '%';
    }

    return [implode(' AND ', $where), $params];
}

/**
 * Converte os números para int (o resto fica como o banco devolveu).
 */
function normalizeCategoryRow(array $row): array
{
    $row['id'] = (int) $row['id'];
    $row['item_count'] = (int) $row['item_count'];
    $row['description'] = (string) ($row['description'] ?? '');
    return $row;
}

/**
 * Lista as categorias do usuário com a quantidade de itens de cada uma, filtradas e paginadas.
 *
 * Recebe: $pdo, $userId, $search (ver buildCategoryFilter), $limit e $offset.
 * Retorna: array de categorias com id, name, description, icon, color e item_count
 *          (da mais recente para a mais antiga), ou array vazio.
 * Lança: PDOException se o banco falhar.
 */
function listCategories(PDO $pdo, int $userId, string $search, int $limit, int $offset): array
{
    [$where, $params] = buildCategoryFilter($userId, $search);

    $stmt = $pdo->prepare(
        'SELECT c.id, c.name, c.description, c.icon, c.color, COUNT(i.id) AS item_count
         FROM categories c
         LEFT JOIN items i ON i.category_id = c.id AND i.user_id = c.user_id
         WHERE ' . $where . '
         GROUP BY c.id, c.name, c.description, c.icon, c.color
         ORDER BY c.id DESC
         LIMIT :limit OFFSET :offset'
    );
    foreach ($params as $name => $value) {
        $stmt->bindValue($name, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    return array_map('normalizeCategoryRow', $stmt->fetchAll(PDO::FETCH_ASSOC));
}

/**
 * Conta as categorias do usuário com a mesma busca da listagem (para a paginação).
 *
 * Recebe: $pdo, $userId e $search.
 * Retorna: total de categorias.
 * Lança: PDOException se o banco falhar.
 */
function countCategories(PDO $pdo, int $userId, string $search): int
{
    [$where, $params] = buildCategoryFilter($userId, $search);

    $stmt = $pdo->prepare('SELECT COUNT(*) FROM categories c WHERE ' . $where);
    $stmt->execute($params);

    return (int) $stmt->fetchColumn();
}

/**
 * Números dos cards do topo, sempre com todas as categorias do usuário (sem a busca).
 *
 * Recebe: $pdo e $userId.
 * Retorna: ['category_count' => int, 'item_count' => int, 'most_used' => string].
 *          most_used é o nome da categoria com mais itens ('' quando não há nenhum item;
 *          no empate, vale a criada primeiro).
 * Lança: PDOException se o banco falhar.
 */
function categorySummary(PDO $pdo, int $userId): array
{
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM categories WHERE user_id = :user_id');
    $stmt->execute([':user_id' => $userId]);
    $categoryCount = (int) $stmt->fetchColumn();

    $stmt = $pdo->prepare('SELECT COUNT(*) FROM items WHERE user_id = :user_id');
    $stmt->execute([':user_id' => $userId]);
    $itemCount = (int) $stmt->fetchColumn();

    $stmt = $pdo->prepare(
        'SELECT c.name
         FROM categories c
         JOIN items i ON i.category_id = c.id AND i.user_id = c.user_id
         WHERE c.user_id = :user_id
         GROUP BY c.id, c.name
         ORDER BY COUNT(i.id) DESC, c.id ASC
         LIMIT 1'
    );
    $stmt->execute([':user_id' => $userId]);
    $mostUsed = $stmt->fetchColumn();

    return [
        'category_count' => $categoryCount,
        'item_count'     => $itemCount,
        'most_used'      => $mostUsed === false ? '' : (string) $mostUsed,
    ];
}

/**
 * Busca uma categoria do usuário pelo id, com a quantidade de itens.
 *
 * Recebe: $pdo, $id da categoria e $userId (dono).
 * Retorna: array no mesmo formato da listagem, ou null se não existir
 *          ou se for de outro usuário (os dois casos são iguais).
 * Lança: PDOException se o banco falhar.
 */
function findCategoryById(PDO $pdo, int $id, int $userId): ?array
{
    $stmt = $pdo->prepare(
        'SELECT c.id, c.name, c.description, c.icon, c.color, COUNT(i.id) AS item_count
         FROM categories c
         LEFT JOIN items i ON i.category_id = c.id AND i.user_id = c.user_id
         WHERE c.id = :id AND c.user_id = :user_id
         GROUP BY c.id, c.name, c.description, c.icon, c.color'
    );
    $stmt->execute([':id' => $id, ':user_id' => $userId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row === false ? null : normalizeCategoryRow($row);
}

/**
 * Cria uma categoria.
 *
 * Recebe: $pdo, $userId (da sessão), $name, $description (null quando vazia),
 *         $icon e $color (já validados contra backend/config/categories.php).
 * Retorna: id da categoria criada.
 * Lança: PDOException se o banco falhar.
 */
function createCategory(
    PDO $pdo,
    int $userId,
    string $name,
    ?string $description,
    string $icon,
    string $color
): int {
    $stmt = $pdo->prepare(
        'INSERT INTO categories (user_id, name, description, icon, color)
         VALUES (:user_id, :name, :description, :icon, :color)'
    );
    $stmt->execute([
        ':user_id'     => $userId,
        ':name'        => $name,
        ':description' => $description,
        ':icon'        => $icon,
        ':color'       => $color,
    ]);

    return (int) $pdo->lastInsertId();
}

/**
 * Atualiza uma categoria do usuário.
 *
 * Recebe: $pdo, $id, $userId (dono) e os mesmos campos do createCategory.
 * Retorna: nada. Se o id não for do usuário, nenhuma linha muda
 *          (o controller confere antes com findCategoryById).
 * Lança: PDOException se o banco falhar.
 */
function updateCategory(
    PDO $pdo,
    int $id,
    int $userId,
    string $name,
    ?string $description,
    string $icon,
    string $color
): void {
    $stmt = $pdo->prepare(
        'UPDATE categories
         SET name = :name, description = :description, icon = :icon, color = :color
         WHERE id = :id AND user_id = :user_id'
    );
    $stmt->execute([
        ':name'        => $name,
        ':description' => $description,
        ':icon'        => $icon,
        ':color'       => $color,
        ':id'          => $id,
        ':user_id'     => $userId,
    ]);
}

/**
 * Remove uma categoria do usuário.
 *
 * Recebe: $pdo, $id e $userId (dono).
 * Retorna: nada. Se o id não for do usuário, nada é removido.
 * Lança: PDOException se o banco falhar. Categoria com itens também lança
 *        (a chave estrangeira de items é ON DELETE RESTRICT, SQLSTATE 23000).
 */
function deleteCategory(PDO $pdo, int $id, int $userId): void
{
    $stmt = $pdo->prepare('DELETE FROM categories WHERE id = :id AND user_id = :user_id');
    $stmt->execute([':id' => $id, ':user_id' => $userId]);
}
