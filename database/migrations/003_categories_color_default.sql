-- ==========================================================
-- MIGRATION 003: cor padrão da categoria com tom (24/11/2026)
-- ==========================================================
-- As cores passaram a ser guardadas como família-tom (ex.: 'sky-200'), a mesma chave do
-- backend/config/categories.php. O padrão 'gray' vira 'gray-200'.

USE projeto_integrador;

ALTER TABLE categories
    ALTER COLUMN color SET DEFAULT 'gray-200';

UPDATE categories SET color = 'gray-200' WHERE color = 'gray';
