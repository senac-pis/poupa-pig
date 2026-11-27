-- ==========================================================
-- DML: ESCRITAS DO SISTEMA
-- ==========================================================
-- Os INSERT, UPDATE e DELETE que os models de backend/models/ fazem, reescritos como SQL puro.
-- No PHP eles usam prepared statements do PDO com parâmetros nomeados (:user_id, :name...);
-- aqui os parâmetros viram variáveis do MySQL (@user_id, @name...). Cada comando indica a
-- função de origem.
--
-- O script inteiro roda dentro de uma transação que termina em ROLLBACK: dá para executar
-- no phpMyAdmin para ver os comandos funcionando sem mudar nada no banco.
-- Para os dados de verdade, veja seeds/products.sql e seeds/demo.sql (também DML).
--
-- Regra que vale para todos: toda escrita em items e categories filtra por user_id
-- (o da sessão), então um usuário nunca altera dado de outro.

USE projeto_integrador;

START TRANSACTION;

-- ==========================================================
-- USUÁRIOS (UserModel.php)
-- ==========================================================

-- createUser: cadastro. O hash vem do password_hash() do PHP, nunca a senha pura.
-- E-mail repetido é recusado pelo UNIQUE de users.email.
-- (Hash de exemplo: senha "poupapig123", a mesma do seeds/demo.sql.)
INSERT INTO users (name, email, password_hash)
VALUES ('Pessoa Exemplo', 'exemplo@poupapig.test',
        '$2y$12$.TCvUiPWNP1SLkwR88lxPeUG9bvYvPWGDDWhMyWJloItrVFv05ZgS');
SET @user_id = LAST_INSERT_ID();

-- updateUserName
UPDATE users SET name = 'Pessoa Exemplo da Silva' WHERE id = @user_id;

-- updateUserPassword: grava o novo hash e a data da troca
UPDATE users
SET password_hash = '$2y$12$.TCvUiPWNP1SLkwR88lxPeUG9bvYvPWGDDWhMyWJloItrVFv05ZgS',
    password_changed_at = CURRENT_TIMESTAMP
WHERE id = @user_id;

-- updateUserPhoto: só o nome do arquivo, gerado pelo servidor (32 caracteres hex + extensão)
UPDATE users SET photo = '0123456789abcdef0123456789abcdef.jpg' WHERE id = @user_id;

-- updateUserAccentColor: chave da cor, já validada pelo backend
UPDATE users SET accent_color = 'purple-400' WHERE id = @user_id;

-- ==========================================================
-- CATEGORIAS (CategoryModel.php)
-- ==========================================================

-- createCategory: ícone e cor validados contra backend/config/categories.php
INSERT INTO categories (user_id, name, description, icon, color)
VALUES (@user_id, 'Eletrônicos', 'Celular, fone e afins', 'smartphone', 'sky-200');
SET @category_id = LAST_INSERT_ID();

-- updateCategory
UPDATE categories
SET name = 'Tecnologia', description = NULL, icon = 'laptop-windows', color = 'sky-300'
WHERE id = @category_id AND user_id = @user_id;

-- ==========================================================
-- ITENS (ItemModel.php)
-- ==========================================================

-- createItem: o status começa como 'thinking' (padrão da coluna)
INSERT INTO items (user_id, category_id, name, price, link, image, start_date, end_date)
VALUES (@user_id, @category_id, 'iPhone 16', 5999.00, NULL, NULL,
        CURRENT_DATE, CURRENT_DATE + INTERVAL 7 DAY);
SET @item_id = LAST_INSERT_ID();

-- updateItem: edita os dados, mas não o status
UPDATE items
SET category_id = @category_id, name = 'iPhone 16 Pro', price = 7999.00, link = NULL,
    image = NULL, start_date = CURRENT_DATE, end_date = CURRENT_DATE + INTERVAL 14 DAY
WHERE id = @item_id AND user_id = @user_id;

-- decideItem: Comprei ('bought') ou Desisti ('gave_up') no Dashboard.
-- O status = 'thinking' no WHERE impede decidir duas vezes, mesmo com dois cliques:
-- a segunda vez não altera nenhuma linha.
UPDATE items
SET status = 'gave_up', decided_at = CURRENT_TIMESTAMP
WHERE id = @item_id AND user_id = @user_id AND status = 'thinking';

-- ==========================================================
-- PRODUTOS E REVIEWS (ReviewModel.php)
-- ==========================================================

-- syncUserProducts: cria o produto de cada item do usuário que ainda não tem um
-- (nome com os espaços juntados). Roda antes da lista de Reviews.
-- O NOT EXISTS evita gastar números do AUTO_INCREMENT à toa; o ON DUPLICATE KEY cobre dois
-- pedidos ao mesmo tempo.
INSERT INTO products (name)
SELECT DISTINCT REGEXP_REPLACE(TRIM(i.name), '[[:space:]]+', ' ') AS product_name
FROM items i
WHERE i.user_id = @user_id
  AND NOT EXISTS (
      SELECT 1 FROM products p
      WHERE p.name = REGEXP_REPLACE(TRIM(i.name), '[[:space:]]+', ' ')
  )
ON DUPLICATE KEY UPDATE name = products.name;

-- createReview: nota de 1 a 5 (CHECK) e uma por usuário em cada produto (UNIQUE)
SET @product_id = (SELECT id FROM products WHERE name = 'iPhone 16 Pro');
INSERT INTO reviews (product_id, user_id, rating, text)
VALUES (@product_id, @user_id, 4, 'Ótima câmera, mas a bateria podia durar mais.');

-- ==========================================================
-- EXCLUSÕES
-- ==========================================================

-- deleteItem
DELETE FROM items WHERE id = @item_id AND user_id = @user_id;

-- deleteCategory: categoria com itens é recusada (items.category_id é ON DELETE RESTRICT)
DELETE FROM categories WHERE id = @category_id AND user_id = @user_id;

-- deleteUserAccount: no PHP é uma transação própria (tudo ou nada).
-- Os itens saem antes das categorias por causa do RESTRICT; as reviews saem em cascata.
DELETE FROM items WHERE user_id = @user_id;
DELETE FROM categories WHERE user_id = @user_id;
DELETE FROM users WHERE id = @user_id;

-- Desfaz tudo o que foi feito acima
ROLLBACK;
