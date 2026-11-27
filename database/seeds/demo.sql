-- ==========================================================
-- DADOS DE DEMONSTRAÇÃO (opcional)
-- ==========================================================
-- Rodar depois do database.sql e do seeds/products.sql. Serve para apresentar o sistema e
-- para as consultas de database/queries/dql.sql devolverem alguma coisa.
-- Todos os dados são fictícios. Pode rodar de novo: apaga os dois usuários de demonstração
-- (e tudo o que é deles) e cria de novo.
--
-- Login dos dois usuários: senha "poupapig123".
-- No banco fica só o hash gerado pelo password_hash() do PHP, nunca a senha pura.
-- As datas são relativas ao dia em que o script roda, para o Dashboard mostrar o mês atual.

USE projeto_integrador;

-- ----------------------------------------------------------
-- Limpa a demonstração anterior
-- ----------------------------------------------------------
-- Os itens saem antes, porque items.category_id é ON DELETE RESTRICT
-- (mesma ordem do deleteUserAccount do UserModel). Reviews e categorias saem em cascata.
DELETE i FROM items i
JOIN users u ON u.id = i.user_id
WHERE u.email IN ('demo@poupapig.test', 'ana@poupapig.test');

DELETE FROM users WHERE email IN ('demo@poupapig.test', 'ana@poupapig.test');

-- ----------------------------------------------------------
-- Usuários
-- ----------------------------------------------------------
INSERT INTO users (name, email, password_hash, accent_color) VALUES
    ('Usuário Demo', 'demo@poupapig.test',
     '$2y$12$.TCvUiPWNP1SLkwR88lxPeUG9bvYvPWGDDWhMyWJloItrVFv05ZgS', 'green-400');
SET @demo = LAST_INSERT_ID();

INSERT INTO users (name, email, password_hash) VALUES
    ('Ana Teste', 'ana@poupapig.test',
     '$2y$12$.TCvUiPWNP1SLkwR88lxPeUG9bvYvPWGDDWhMyWJloItrVFv05ZgS');
SET @ana = LAST_INSERT_ID();

-- ----------------------------------------------------------
-- Categorias (ícone e cor são chaves de backend/config/categories.php)
-- ----------------------------------------------------------
INSERT INTO categories (user_id, name, icon, color, description) VALUES
    (@demo, 'Eletrônicos', 'smartphone', 'sky-200', 'Celular, fone e afins');
SET @demo_eletronicos = LAST_INSERT_ID();

INSERT INTO categories (user_id, name, icon, color, description) VALUES
    (@demo, 'Roupas', 'apparel', 'pink-200', NULL);
SET @demo_roupas = LAST_INSERT_ID();

INSERT INTO categories (user_id, name, icon, color, description) VALUES
    (@demo, 'Casa', 'weekend', 'green-200', 'Móveis e decoração');
SET @demo_casa = LAST_INSERT_ID();

INSERT INTO categories (user_id, name, icon, color) VALUES
    (@ana, 'Lazer', 'sports-esports', 'purple-300');
SET @ana_lazer = LAST_INSERT_ID();

-- ----------------------------------------------------------
-- Itens nos três status
-- ----------------------------------------------------------
-- thinking: ainda na quarentena (end_date no futuro ou hoje)
INSERT INTO items (user_id, category_id, name, price, link, start_date, end_date) VALUES
    (@demo, @demo_eletronicos, 'AirPods Pro', 1899.00, 'https://www.apple.com/br/airpods-pro/',
     CURRENT_DATE - INTERVAL 2 DAY, CURRENT_DATE + INTERVAL 5 DAY),
    (@demo, @demo_casa, 'Cadeira gamer', 1200.00, NULL,
     CURRENT_DATE - INTERVAL 1 DAY, CURRENT_DATE + INTERVAL 13 DAY),
    (@demo, @demo_roupas, 'Tênis Adidas Samba', 699.90, NULL,
     CURRENT_DATE, CURRENT_DATE + INTERVAL 7 DAY);

-- bought e gave_up: decididos (decided_at preenchido)
INSERT INTO items (user_id, category_id, name, price, start_date, end_date, status, decided_at) VALUES
    (@demo, @demo_eletronicos, 'Kindle Paperwhite', 899.00,
     CURRENT_DATE - INTERVAL 10 DAY, CURRENT_DATE - INTERVAL 3 DAY,
     'bought', CURRENT_TIMESTAMP - INTERVAL 3 DAY),
    (@demo, @demo_roupas, 'Tênis Nike Air Max', 999.99,
     CURRENT_DATE - INTERVAL 9 DAY, CURRENT_DATE - INTERVAL 2 DAY,
     'gave_up', CURRENT_TIMESTAMP - INTERVAL 2 DAY),
    (@demo, @demo_eletronicos, 'JBL Flip', 749.00,
     CURRENT_DATE - INTERVAL 8 DAY, CURRENT_DATE - INTERVAL 1 DAY,
     'gave_up', CURRENT_TIMESTAMP - INTERVAL 1 DAY),
    (@ana, @ana_lazer, 'PlayStation 5', 3799.00,
     CURRENT_DATE - INTERVAL 20 DAY, CURRENT_DATE - INTERVAL 6 DAY,
     'bought', CURRENT_TIMESTAMP - INTERVAL 6 DAY),
    (@ana, @ana_lazer, 'Kindle Paperwhite', 899.00,
     CURRENT_DATE - INTERVAL 15 DAY, CURRENT_DATE - INTERVAL 5 DAY,
     'bought', CURRENT_TIMESTAMP - INTERVAL 5 DAY);

-- ----------------------------------------------------------
-- Produtos e reviews
-- ----------------------------------------------------------
-- Garante o produto de cada item (o backend faz isso no syncUserProducts ao abrir Reviews).
-- Os nomes do catálogo já existem e não mudam.
INSERT INTO products (name)
SELECT DISTINCT i.name FROM items i
WHERE i.user_id IN (@demo, @ana)
ON DUPLICATE KEY UPDATE name = products.name;

-- Os dois avaliaram o Kindle Paperwhite: a média do produto fica 4,5
INSERT INTO reviews (product_id, user_id, rating, text)
SELECT p.id, @demo, 5, 'Bateria dura muito e a tela não cansa a vista.'
FROM products p WHERE p.name = 'Kindle Paperwhite';

INSERT INTO reviews (product_id, user_id, rating, text)
SELECT p.id, @ana, 4, NULL
FROM products p WHERE p.name = 'Kindle Paperwhite';

INSERT INTO reviews (product_id, user_id, rating, text)
SELECT p.id, @ana, 5, 'Valeu cada dia de espera.'
FROM products p WHERE p.name = 'PlayStation 5';
