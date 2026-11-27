-- ==========================================================
-- MIGRATION 005: foto e cor principal do usuário (25/11/2026)
-- ==========================================================
-- photo guarda só o nome do arquivo gerado pelo servidor; accent_color, a chave da cor
-- (ex.: 'green-400'). As duas ficam NULL até a pessoa escolher.

USE projeto_integrador;

ALTER TABLE users
    ADD COLUMN photo VARCHAR(255) NULL AFTER password_changed_at,
    ADD COLUMN accent_color VARCHAR(20) NULL AFTER photo;
