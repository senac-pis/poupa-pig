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
    password_changed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    photo VARCHAR(255) NULL,
    accent_color VARCHAR(20) NULL,
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


create table if not exists categories (
    id int unsigned auto_increment primary key,
    user_id int unsigned not null,
    name varchar(120) not null,
    icon varchar(50) not null default 'box-seam',
    color varchar(50) not null default 'gray-200',
    description text null,
    created_at timestamp not null default current_timestamp,
    constraint fk_categories_user
        foreign key (user_id) references users(id)
        on delete cascade
);

create table if not exists items (
    id int unsigned auto_increment primary key,
    user_id int unsigned not null,
    category_id int unsigned not null,
    name varchar(180) not null,
    price decimal(10,2) not null,
    link text,
    image text,
    reason text,
    start_date date not null,
    end_date date not null,
    status enum('thinking', 'bought', 'gave_up') not null default 'thinking',
    created_at timestamp not null default current_timestamp,
    decided_at timestamp null default null,
    constraint fk_items_user
        foreign key (user_id) references users(id)
        on delete cascade,
    constraint fk_items_category
        foreign key (category_id) references categories(id)
        on delete restrict
);



-- Produtos de Reviews: compartilhados entre os usuários e identificados pelo nome do item.
-- O UNIQUE usa a collation do banco (utf8mb4_unicode_ci), que não diferencia maiúsculas nem acentos:
-- "Jaqueta Adidas" e "jaqueta adidas" são o mesmo produto. O backend junta os espaços antes de salvar.
-- is_public marca o catálogo (database/seeds/products.sql): esses aparecem para todos no
-- autocompletar do item. Produto criado a partir do item de alguém só fica visível para os outros
-- depois da primeira review.
create table if not exists products (
    id int unsigned auto_increment primary key,
    name varchar(180) not null,
    is_public boolean not null default false,
    created_at timestamp not null default current_timestamp,
    constraint uq_products_name unique (name)
);

-- Uma review por usuário em cada produto; a resenha é opcional, a nota vai de 1 a 5
create table if not exists reviews (
    id int unsigned auto_increment primary key,
    product_id int unsigned not null,
    user_id int unsigned not null,
    rating tinyint unsigned not null,
    text text null,
    created_at timestamp not null default current_timestamp,
    constraint uq_reviews_product_user unique (product_id, user_id),
    constraint chk_reviews_rating check (rating between 1 and 5),
    constraint fk_reviews_product
        foreign key (product_id) references products(id)
        on delete cascade,
    constraint fk_reviews_user
        foreign key (user_id) references users(id)
        on delete cascade
);
