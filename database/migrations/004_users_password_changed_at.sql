-- ==========================================================
-- MIGRATION 004: data da última troca de senha (24/11/2026)
-- ==========================================================
-- A tela de Configurações mostra há quantos dias a senha foi trocada.
-- Usuários que já existiam ficam com a data em que a migration rodou.

USE projeto_integrador;

ALTER TABLE users
    ADD COLUMN password_changed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER role;
