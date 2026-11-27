-- ==========================================================
-- MIGRATION 001: estrutura inicial (28/08/2026)
-- ==========================================================
-- Cria o banco e as primeiras tabelas: users (do template da disciplina), category e items.
-- Nessa versão a categoria ainda não era por usuário e o item tinha um prazo só
-- (thinking_deadline). As migrations seguintes levam até o database.sql atual.

CREATE DATABASE IF NOT EXISTS projeto_integrador
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE projeto_integrador;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(180) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL DEFAULT 'user',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS category (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    category_id INT UNSIGNED NOT NULL,
    name VARCHAR(180) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    link TEXT,
    image TEXT,
    reason TEXT,
    thinking_deadline DATE NOT NULL,
    status ENUM('thinking', 'bought', 'gave_up') NOT NULL DEFAULT 'thinking',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    decided_at TIMESTAMP NULL DEFAULT NULL,
    CONSTRAINT fk_items_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_items_category
        FOREIGN KEY (category_id) REFERENCES category(id)
        ON DELETE RESTRICT
);
