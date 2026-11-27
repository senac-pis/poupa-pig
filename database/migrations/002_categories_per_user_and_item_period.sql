-- ==========================================================
-- MIGRATION 002: categorias por usuário e período do item (11/11 a 18/11/2026)
-- ==========================================================
-- Para as telas de Itens e Categorias:
-- - category vira categories, ganha dono (user_id), ícone, cor e descrição;
-- - o prazo único do item (thinking_deadline) vira um período: start_date (data de inclusão)
--   e end_date (fim da quarentena).
-- Obs.: na época a tabela category estava vazia (não havia tela). Com linhas antigas, o
-- user_id NOT NULL ficaria 0 e a chave estrangeira falharia: seria preciso preencher antes.

USE projeto_integrador;

-- A chave estrangeira fk_items_category acompanha a tabela renomeada
RENAME TABLE category TO categories;

ALTER TABLE categories
    ADD COLUMN user_id INT UNSIGNED NOT NULL AFTER id,
    ADD COLUMN icon VARCHAR(50) NOT NULL DEFAULT 'box-seam' AFTER name,
    ADD COLUMN color VARCHAR(50) NOT NULL DEFAULT 'gray' AFTER icon,
    ADD COLUMN description TEXT NULL AFTER color,
    ADD CONSTRAINT fk_categories_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE;

ALTER TABLE items
    ADD COLUMN start_date DATE NOT NULL AFTER reason,
    CHANGE COLUMN thinking_deadline end_date DATE NOT NULL;
