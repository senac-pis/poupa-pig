-- ==========================================================
-- DQL: CONSULTAS DO SISTEMA
-- ==========================================================
-- As consultas (SELECT) que os models de backend/models/ fazem, reescritas como SQL puro.
-- No PHP elas usam prepared statements do PDO com parâmetros nomeados (:user_id, :search...);
-- aqui os parâmetros viram variáveis do MySQL (@user_id, @search...) para dar para rodar
-- direto no phpMyAdmin. Cada consulta indica a função de origem.
--
-- Rodar depois do database.sql, do seeds/products.sql e do seeds/demo.sql
-- (sem o demo, as consultas rodam mas devolvem pouco ou nada).
-- Só lê dados: não altera nada no banco.

USE projeto_integrador;

-- Parâmetros de exemplo (no sistema, @user_id é sempre o id do usuário da sessão)
SET @user_id = (SELECT id FROM users WHERE email = 'demo@poupapig.test');
SET @email = 'demo@poupapig.test';
SET @search = '%tênis%';  -- o PHP põe os % em volta e escapa % e _ digitados
SET @category_id = (SELECT id FROM categories WHERE user_id = @user_id AND name = 'Roupas');
SET @filter_start = CURRENT_DATE - INTERVAL 30 DAY;
SET @filter_end = CURRENT_DATE;
SET @item_id = (SELECT MIN(id) FROM items WHERE user_id = @user_id);
SET @product_id = (SELECT id FROM products WHERE name = 'Kindle Paperwhite');
SET @product_name = 'Kindle Paperwhite';
SET @rating = 5;

-- ==========================================================
-- USUÁRIOS (UserModel.php)
-- ==========================================================

-- findUserByEmail: login. O PHP confere a senha com password_verify() no password_hash.
SELECT id, name, email, password_hash, role, photo, accent_color
FROM users
WHERE email = @email
LIMIT 1;

-- findUserById: tela de Configurações. Os dias desde a troca de senha são contados no MySQL,
-- para não depender do fuso do PHP.
SELECT id, name, password_hash, photo, accent_color,
       DATEDIFF(CURRENT_DATE, DATE(password_changed_at)) AS password_changed_days
FROM users
WHERE id = @user_id
LIMIT 1;

-- deleteUserAccount (parte de leitura): fotos a apagar do disco depois da exclusão
SELECT image FROM items WHERE user_id = @user_id AND image IS NOT NULL;
SELECT photo FROM users WHERE id = @user_id;

-- ==========================================================
-- ITENS (ItemModel.php)
-- ==========================================================

-- listItems: tela de Itens, com os dados da categoria (JOIN), filtros e paginação.
-- Os filtros são opcionais: o PHP só acrescenta ao WHERE os que a pessoa preencheu
-- (busca no nome, categoria e período pela data de inclusão). Aqui aparecem todos juntos.
SELECT i.id, i.name, i.image, i.price, i.link, i.start_date, i.end_date, i.status, i.category_id,
       c.name AS category_name, c.icon AS category_icon, c.color AS category_color
FROM items i
JOIN categories c ON c.id = i.category_id
WHERE i.user_id = @user_id
  AND i.name LIKE @search
  AND i.category_id = @category_id
  AND i.start_date >= @filter_start
  AND i.start_date <= @filter_end
ORDER BY i.start_date DESC, i.id DESC
LIMIT 10 OFFSET 0;

-- countItems: total com os mesmos filtros, para a paginação (aqui só com o filtro de dono)
SELECT COUNT(*) FROM items i WHERE i.user_id = @user_id;

-- findItemById: um item do usuário. Item de outra pessoa não volta (mesmo resultado de não existir).
SELECT i.id, i.name, i.image, i.price, i.link, i.start_date, i.end_date, i.status, i.category_id,
       c.name AS category_name, c.icon AS category_icon, c.color AS category_color
FROM items i
JOIN categories c ON c.id = i.category_id
WHERE i.id = @item_id AND i.user_id = @user_id
LIMIT 1;

-- listCategoriesWithCount: filtro de categorias da tela de Itens, com quantos itens cada uma tem.
-- LEFT JOIN para a categoria sem item aparecer com 0.
SELECT c.id, c.name, COUNT(i.id) AS count
FROM categories c
LEFT JOIN items i ON i.category_id = c.id AND i.user_id = c.user_id
WHERE c.user_id = @user_id
GROUP BY c.id, c.name
ORDER BY c.name ASC;

-- categoryBelongsToUser: confere o dono da categoria antes de criar ou editar um item
SELECT 1 FROM categories WHERE id = @category_id AND user_id = @user_id LIMIT 1;

-- listDashboardItems: todos os itens do usuário. O Dashboard calcula os números de cada mês
-- (gasto, economizado, % do porquinho) a partir desta lista.
SELECT i.id, i.name, i.image, i.price, i.link, i.start_date, i.end_date, i.status, i.category_id,
       c.name AS category_name, c.icon AS category_icon, c.color AS category_color,
       i.start_date AS created_at, DATE(i.decided_at) AS decided_at
FROM items i
JOIN categories c ON c.id = i.category_id
WHERE i.user_id = @user_id
ORDER BY i.start_date DESC, i.id DESC;

-- databaseToday: o "hoje" do Dashboard vem do MySQL, que é quem grava as datas de decisão
SELECT CURRENT_DATE;

-- ==========================================================
-- CATEGORIAS (CategoryModel.php)
-- ==========================================================

-- listCategories: tela de Categorias, com a quantidade de itens, busca e paginação
-- (a busca no nome é opcional; aqui sem ela)
SELECT c.id, c.name, c.description, c.icon, c.color, COUNT(i.id) AS item_count
FROM categories c
LEFT JOIN items i ON i.category_id = c.id AND i.user_id = c.user_id
WHERE c.user_id = @user_id
GROUP BY c.id, c.name, c.description, c.icon, c.color
ORDER BY c.id DESC
LIMIT 10 OFFSET 0;

-- countCategories: total para a paginação
SELECT COUNT(*) FROM categories c WHERE c.user_id = @user_id;

-- categorySummary: os três cards do topo da tela de Categorias
SELECT COUNT(*) FROM categories WHERE user_id = @user_id;
SELECT COUNT(*) FROM items WHERE user_id = @user_id;

-- categorySummary: categoria mais usada (no empate, a criada primeiro)
SELECT c.name
FROM categories c
JOIN items i ON i.category_id = c.id AND i.user_id = c.user_id
WHERE c.user_id = @user_id
GROUP BY c.id, c.name
ORDER BY COUNT(i.id) DESC, c.id ASC
LIMIT 1;

-- findCategoryById: uma categoria do usuário, com a quantidade de itens
SELECT c.id, c.name, c.description, c.icon, c.color, COUNT(i.id) AS item_count
FROM categories c
LEFT JOIN items i ON i.category_id = c.id AND i.user_id = c.user_id
WHERE c.id = @category_id AND c.user_id = @user_id
GROUP BY c.id, c.name, c.description, c.icon, c.color;

-- ==========================================================
-- PRODUTOS E REVIEWS (ReviewModel.php)
-- ==========================================================
-- O produto é o nome do item com os espaços juntados (REGEXP_REPLACE), comparado pela
-- collation do banco, que não diferencia maiúsculas nem acentos.

-- listUserProducts: lista de Reviews. Mostra os produtos que estão nos itens do usuário,
-- com as reviews de todos os usuários (quantas pessoas, quantas reviews e a média).
-- Filtros opcionais: busca no nome (no WHERE) e nota pela média arredondada (no HAVING).
SELECT p.id, p.name, COUNT(DISTINCT r.user_id) AS user_count, COUNT(r.id) AS review_count,
       COALESCE(AVG(r.rating), 0) AS average
FROM products p
LEFT JOIN reviews r ON r.product_id = p.id
WHERE EXISTS (
    SELECT 1 FROM items i
    WHERE i.user_id = @user_id
      AND REGEXP_REPLACE(TRIM(i.name), '[[:space:]]+', ' ') = p.name
)
GROUP BY p.id, p.name
HAVING ROUND(AVG(r.rating)) = @rating
ORDER BY review_count DESC, p.name ASC
LIMIT 10 OFFSET 0;

-- countUserProducts: total da lista com os mesmos filtros (subconsulta por causa do GROUP BY)
SELECT COUNT(*) FROM (
    SELECT p.id
    FROM products p
    LEFT JOIN reviews r ON r.product_id = p.id
    WHERE EXISTS (
        SELECT 1 FROM items i
        WHERE i.user_id = @user_id
          AND REGEXP_REPLACE(TRIM(i.name), '[[:space:]]+', ' ') = p.name
    )
    GROUP BY p.id
) AS user_products;

-- findProductById: página do produto
SELECT id, name FROM products WHERE id = @product_id LIMIT 1;

-- userHasProductItem: só quem tem um item com o nome do produto pode avaliar
SELECT 1 FROM items
WHERE user_id = @user_id AND REGEXP_REPLACE(TRIM(name), '[[:space:]]+', ' ') = @product_name
LIMIT 1;

-- productIsPublic: quem não tem o item só vê o produto se ele for do catálogo ou já tiver review
-- (assim trocar o ?id= na URL não mostra o nome do item de outra pessoa)
SELECT 1 FROM products p
WHERE p.id = @product_id
  AND (p.is_public OR EXISTS (SELECT 1 FROM reviews r WHERE r.product_id = p.id))
LIMIT 1;

-- listProductSuggestions: autocompletar do nome do item. O catálogo, os produtos com review e
-- os nomes dos itens do próprio usuário. O UNION já tira os repetidos.
SELECT p.name AS name
FROM products p
WHERE p.is_public OR EXISTS (SELECT 1 FROM reviews r WHERE r.product_id = p.id)
UNION
SELECT REGEXP_REPLACE(TRIM(i.name), '[[:space:]]+', ' ') AS name
FROM items i
WHERE i.user_id = @user_id
ORDER BY name;

-- userReviewedProduct: uma review por usuário em cada produto
SELECT 1 FROM reviews WHERE user_id = @user_id AND product_id = @product_id LIMIT 1;

-- productRatingSummary: quantas reviews de cada nota (o PHP calcula a média e as barras)
SELECT rating, COUNT(*) AS total FROM reviews WHERE product_id = @product_id GROUP BY rating;

-- listProductReviews: reviews do produto, da mais recente para a mais antiga, com o autor
-- e quantas reviews ele já fez no total (subconsulta correlacionada)
SELECT u.name AS user_name, u.photo AS user_photo,
       (SELECT COUNT(*) FROM reviews ur WHERE ur.user_id = r.user_id) AS user_review_count,
       r.rating, r.text, DATE(r.created_at) AS created_at
FROM reviews r
JOIN users u ON u.id = r.user_id
WHERE r.product_id = @product_id
ORDER BY r.created_at DESC, r.id DESC
LIMIT 10 OFFSET 0;
