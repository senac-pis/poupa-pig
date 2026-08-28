-- ==========================================================
-- PROJETO INTEGRADOR — TEMPLATE DO BANCO DE DADOS
-- MySQL / phpMyAdmin / XAMPP
-- ==========================================================
-- IMPORTANTE:
-- Este arquivo é apenas um ponto de partida.
-- A equipe deverá substituir nomes, tabelas, campos e relacionamentos
-- de acordo com o projeto definido na Atividade 01.
-- ==========================================================

CREATE DATABASE IF NOT EXISTS projeto_integrador
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE projeto_integrador;

-- ==========================================================
-- EXEMPLO DE ENTIDADE PARA AUTENTICAÇÃO
-- A equipe poderá adaptar os campos conforme a necessidade.
-- Nunca armazene senha em texto puro.
-- Use password_hash() no PHP para gerar o valor de password_hash.
-- ==========================================================

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(180) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL DEFAULT 'user',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ==========================================================
-- ATIVIDADE DA EQUIPE
-- Crie abaixo pelo menos duas entidades relacionadas.
-- Exemplo conceitual:
-- clientes (1) -------- (N) agendamentos
-- ==========================================================

-- CREATE TABLE ...
-- CREATE TABLE ...
-- ALTER TABLE ... ADD CONSTRAINT ... FOREIGN KEY ...

-- poupa pig -- 


create table if not exists category (
    id int unsigned auto_increment primary key,
    name varchar(120) not null,
    created_at timestamp not null default current_timestamp
);

create table if not exists purchase_items (
    id int unsigned auto_increment primary key,
    users_id int unsigned not null,
    category_id int unsigned not null,
    price decimal(10,2) not null,
    created_at timestamp not null default current_timestamp,
    foreign key (users_id) references users(id),
    foreign key (category_id) references category(id)
);

create table if not exists transactions (
    id int unsigned auto_increment primary key,
    purchase_item_id int unsigned not null,
    type enum ('purchase', 'abandonment') not null,
    amount decimal(10,2) not null,
    transaction_date timestamp not null default current_timestamp,
    created_at timestamp not null default current_timestamp,
    foreign key (purchase_item_id) references purchase_items(id)
);