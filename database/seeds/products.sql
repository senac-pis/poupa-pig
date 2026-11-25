-- ==========================================================
-- CATÁLOGO DE PRODUTOS (autocompletar do nome do item)
-- ==========================================================
-- Rodar depois do database.sql. Pode rodar de novo sem problema: nome que já existe
-- só passa a ser público (is_public = true).
-- Esses produtos aparecem para todos os usuários no autocompletar do criar e editar item.
-- O nome é único sem diferenciar maiúsculas nem acentos (collation utf8mb4_unicode_ci).

USE projeto_integrador;

-- Celulares e tablets
INSERT INTO products (name, is_public) VALUES
    ('iPhone 16', true),
    ('iPhone 16 Pro', true),
    ('iPhone 15', true),
    ('iPhone 14', true),
    ('iPhone 13', true),
    ('Samsung Galaxy S24', true),
    ('Samsung Galaxy S24 Ultra', true),
    ('Samsung Galaxy S23', true),
    ('Samsung Galaxy A55', true),
    ('Samsung Galaxy A35', true),
    ('Samsung Galaxy A15', true),
    ('Samsung Galaxy Z Flip 6', true),
    ('Motorola Edge 50', true),
    ('Motorola Moto G84', true),
    ('Motorola Moto G54', true),
    ('Motorola Moto G34', true),
    ('Xiaomi Redmi Note 13', true),
    ('Xiaomi Redmi Note 13 Pro', true),
    ('Xiaomi Poco X6', true),
    ('Google Pixel 9', true),
    ('iPad', true),
    ('iPad Air', true),
    ('iPad Pro', true),
    ('iPad Mini', true),
    ('Samsung Galaxy Tab S9', true),
    ('Samsung Galaxy Tab A9', true),
    ('Xiaomi Redmi Pad', true),
    ('Kindle', true),
    ('Kindle Paperwhite', true),
    ('Capa de celular', true),
    ('Película de vidro', true),
    ('Carregador turbo', true),
    ('Power bank', true),
    ('Cabo USB-C', true)
ON DUPLICATE KEY UPDATE is_public = true;

-- Computadores e acessórios
INSERT INTO products (name, is_public) VALUES
    ('MacBook Air', true),
    ('MacBook Pro', true),
    ('Notebook Dell Inspiron', true),
    ('Notebook Lenovo IdeaPad', true),
    ('Notebook Acer Aspire', true),
    ('Notebook Asus Vivobook', true),
    ('Notebook Samsung Galaxy Book', true),
    ('Notebook gamer', true),
    ('Chromebook', true),
    ('PC gamer', true),
    ('Monitor 24 polegadas', true),
    ('Monitor 27 polegadas', true),
    ('Monitor gamer 144Hz', true),
    ('Monitor ultrawide', true),
    ('Teclado mecânico', true),
    ('Teclado sem fio', true),
    ('Mouse sem fio', true),
    ('Mouse gamer', true),
    ('Mousepad grande', true),
    ('Webcam Full HD', true),
    ('Headset gamer', true),
    ('Microfone USB', true),
    ('Impressora multifuncional', true),
    ('Roteador Wi-Fi 6', true),
    ('SSD 1TB', true),
    ('SSD externo', true),
    ('HD externo 2TB', true),
    ('Pen drive 64GB', true),
    ('Hub USB-C', true),
    ('Suporte para notebook', true),
    ('Cadeira de escritório', true),
    ('Mesa de escritório', true)
ON DUPLICATE KEY UPDATE is_public = true;

-- Áudio e vídeo
INSERT INTO products (name, is_public) VALUES
    ('AirPods', true),
    ('AirPods Pro', true),
    ('Fone JBL Tune', true),
    ('Fone Bluetooth', true),
    ('Fone com cancelamento de ruído', true),
    ('Galaxy Buds', true),
    ('Caixa de som JBL', true),
    ('JBL Flip', true),
    ('JBL Charge', true),
    ('JBL Go', true),
    ('Caixa de som bluetooth', true),
    ('Soundbar', true),
    ('Echo Dot', true),
    ('Echo Show', true),
    ('Google Nest Mini', true),
    ('Smart TV 43 polegadas', true),
    ('Smart TV 50 polegadas', true),
    ('Smart TV 55 polegadas', true),
    ('Smart TV 65 polegadas', true),
    ('Fire TV Stick', true),
    ('Chromecast', true),
    ('Projetor', true),
    ('Vitrola', true),
    ('Toca-discos', true),
    ('Câmera digital', true),
    ('Câmera instantânea', true),
    ('GoPro', true),
    ('Drone', true),
    ('Ring light', true),
    ('Tripé', true)
ON DUPLICATE KEY UPDATE is_public = true;

-- Games
INSERT INTO products (name, is_public) VALUES
    ('PlayStation 5', true),
    ('PlayStation 5 Slim', true),
    ('PlayStation 4', true),
    ('Xbox Series X', true),
    ('Xbox Series S', true),
    ('Nintendo Switch', true),
    ('Nintendo Switch OLED', true),
    ('Steam Deck', true),
    ('Controle DualSense', true),
    ('Controle Xbox', true),
    ('Controle Pro Switch', true),
    ('PlayStation VR2', true),
    ('Meta Quest 3', true),
    ('Volante gamer', true),
    ('Cadeira gamer', true),
    ('Jogo de videogame', true),
    ('Assinatura Game Pass', true),
    ('Assinatura PlayStation Plus', true),
    ('Gift card Steam', true),
    ('Gift card PlayStation', true)
ON DUPLICATE KEY UPDATE is_public = true;

-- Relógios e acessórios
INSERT INTO products (name, is_public) VALUES
    ('Apple Watch', true),
    ('Apple Watch SE', true),
    ('Galaxy Watch', true),
    ('Smartwatch Amazfit', true),
    ('Smartband Xiaomi', true),
    ('Relógio Casio', true),
    ('Relógio analógico', true),
    ('Óculos de sol', true),
    ('Óculos de grau', true),
    ('Carteira de couro', true),
    ('Cinto de couro', true),
    ('Mochila', true),
    ('Mochila para notebook', true),
    ('Bolsa', true),
    ('Bolsa transversal', true),
    ('Mala de viagem', true),
    ('Mala de bordo', true),
    ('Pochete', true),
    ('Boné', true),
    ('Chapéu', true),
    ('Anel de prata', true),
    ('Colar', true),
    ('Brinco', true),
    ('Pulseira', true),
    ('Perfume importado', true),
    ('Perfume nacional', true)
ON DUPLICATE KEY UPDATE is_public = true;

-- Roupas e calçados
INSERT INTO products (name, is_public) VALUES
    ('Tênis Nike Air Force 1', true),
    ('Tênis Nike Air Max', true),
    ('Tênis Adidas Ultraboost', true),
    ('Tênis Adidas Samba', true),
    ('Tênis New Balance', true),
    ('Tênis Vans', true),
    ('Tênis Converse All Star', true),
    ('Tênis Puma', true),
    ('Tênis de corrida', true),
    ('Chinelo Havaianas', true),
    ('Sandália', true),
    ('Bota', true),
    ('Sapato social', true),
    ('Jaqueta jeans', true),
    ('Jaqueta de couro', true),
    ('Jaqueta corta-vento', true),
    ('Moletom', true),
    ('Moletom canguru', true),
    ('Camiseta básica', true),
    ('Camiseta de time', true),
    ('Camisa social', true),
    ('Camisa polo', true),
    ('Calça jeans', true),
    ('Calça cargo', true),
    ('Calça de moletom', true),
    ('Bermuda', true),
    ('Short', true),
    ('Vestido', true),
    ('Saia', true),
    ('Blazer', true),
    ('Casaco', true),
    ('Pijama', true),
    ('Roupa de academia', true),
    ('Biquíni', true),
    ('Sunga', true)
ON DUPLICATE KEY UPDATE is_public = true;

-- Casa e móveis
INSERT INTO products (name, is_public) VALUES
    ('Sofá', true),
    ('Sofá retrátil', true),
    ('Poltrona', true),
    ('Puff', true),
    ('Cama box', true),
    ('Colchão', true),
    ('Travesseiro', true),
    ('Jogo de cama', true),
    ('Edredom', true),
    ('Cortina', true),
    ('Tapete', true),
    ('Guarda-roupa', true),
    ('Cômoda', true),
    ('Rack para TV', true),
    ('Estante', true),
    ('Mesa de jantar', true),
    ('Cadeira', true),
    ('Escrivaninha', true),
    ('Luminária', true),
    ('Abajur', true),
    ('Espelho', true),
    ('Quadro decorativo', true),
    ('Vaso de planta', true),
    ('Organizador', true),
    ('Ventilador', true),
    ('Ventilador de teto', true),
    ('Ar-condicionado', true),
    ('Aquecedor', true),
    ('Umidificador', true),
    ('Purificador de ar', true)
ON DUPLICATE KEY UPDATE is_public = true;

-- Eletrodomésticos
INSERT INTO products (name, is_public) VALUES
    ('Geladeira', true),
    ('Geladeira frost free', true),
    ('Fogão', true),
    ('Cooktop', true),
    ('Forno elétrico', true),
    ('Micro-ondas', true),
    ('Air fryer', true),
    ('Lava-louças', true),
    ('Máquina de lavar', true),
    ('Lava e seca', true),
    ('Secadora de roupas', true),
    ('Aspirador de pó', true),
    ('Aspirador robô', true),
    ('Ferro de passar', true),
    ('Vaporizador de roupas', true),
    ('Liquidificador', true),
    ('Batedeira', true),
    ('Mixer', true),
    ('Processador de alimentos', true),
    ('Cafeteira', true),
    ('Cafeteira Nespresso', true),
    ('Cafeteira Dolce Gusto', true),
    ('Chaleira elétrica', true),
    ('Torradeira', true),
    ('Sanduicheira', true),
    ('Grill', true),
    ('Panela elétrica de arroz', true),
    ('Panela de pressão elétrica', true),
    ('Purificador de água', true),
    ('Frigobar', true)
ON DUPLICATE KEY UPDATE is_public = true;

-- Cozinha
INSERT INTO products (name, is_public) VALUES
    ('Jogo de panelas', true),
    ('Panela antiaderente', true),
    ('Frigideira', true),
    ('Jogo de facas', true),
    ('Tábua de corte', true),
    ('Jogo de pratos', true),
    ('Jogo de copos', true),
    ('Jogo de talheres', true),
    ('Garrafa térmica', true),
    ('Copo térmico Stanley', true),
    ('Marmita', true),
    ('Potes herméticos', true),
    ('Escorredor de louça', true),
    ('Lixeira', true)
ON DUPLICATE KEY UPDATE is_public = true;

-- Alimentação e mercado
INSERT INTO products (name, is_public) VALUES
    ('Compras do mês', true),
    ('Pizza', true),
    ('Hambúrguer', true),
    ('Delivery', true),
    ('Sushi', true),
    ('Açaí', true),
    ('Sorvete', true),
    ('Chocolate', true),
    ('Café em cápsula', true),
    ('Café especial', true),
    ('Vinho', true),
    ('Cerveja artesanal', true),
    ('Lanche', true),
    ('Restaurante', true),
    ('Padaria', true),
    ('Doces', true),
    ('Suplemento whey', true),
    ('Creatina', true)
ON DUPLICATE KEY UPDATE is_public = true;

-- Beleza e cuidados
INSERT INTO products (name, is_public) VALUES
    ('Secador de cabelo', true),
    ('Chapinha', true),
    ('Modelador de cachos', true),
    ('Escova alisadora', true),
    ('Barbeador elétrico', true),
    ('Aparador de pelos', true),
    ('Escova de dentes elétrica', true),
    ('Kit de maquiagem', true),
    ('Base', true),
    ('Batom', true),
    ('Paleta de sombras', true),
    ('Skincare', true),
    ('Protetor solar', true),
    ('Hidratante', true),
    ('Shampoo', true),
    ('Perfume feminino', true),
    ('Perfume masculino', true)
ON DUPLICATE KEY UPDATE is_public = true;

-- Esporte e lazer
INSERT INTO products (name, is_public) VALUES
    ('Bicicleta', true),
    ('Bicicleta elétrica', true),
    ('Patinete elétrico', true),
    ('Skate', true),
    ('Patins', true),
    ('Esteira', true),
    ('Bicicleta ergométrica', true),
    ('Kit de halteres', true),
    ('Tapete de yoga', true),
    ('Bola de futebol', true),
    ('Chuteira', true),
    ('Raquete de tênis', true),
    ('Raquete de beach tennis', true),
    ('Barraca de camping', true),
    ('Garrafa de água', true),
    ('Mochila de trilha', true),
    ('Prancha de surfe', true),
    ('Mensalidade da academia', true)
ON DUPLICATE KEY UPDATE is_public = true;

-- Livros e papelaria
INSERT INTO products (name, is_public) VALUES
    ('Livro', true),
    ('Box de livros', true),
    ('Mangá', true),
    ('HQ', true),
    ('Agenda', true),
    ('Caderno', true),
    ('Planner', true),
    ('Caneta', true),
    ('Kit de canetas', true),
    ('Mochila escolar', true),
    ('Calculadora científica', true),
    ('Curso online', true)
ON DUPLICATE KEY UPDATE is_public = true;

-- Bebê e infantil
INSERT INTO products (name, is_public) VALUES
    ('Carrinho de bebê', true),
    ('Cadeirinha para carro', true),
    ('Berço', true),
    ('Brinquedo', true),
    ('LEGO', true),
    ('Boneca', true),
    ('Carrinho de controle remoto', true),
    ('Quebra-cabeça', true),
    ('Jogo de tabuleiro', true)
ON DUPLICATE KEY UPDATE is_public = true;

-- Pet
INSERT INTO products (name, is_public) VALUES
    ('Ração', true),
    ('Caminha de pet', true),
    ('Arranhador', true),
    ('Brinquedo para pet', true),
    ('Coleira', true),
    ('Caixa de transporte', true),
    ('Fonte de água para pet', true)
ON DUPLICATE KEY UPDATE is_public = true;

-- Carro e moto
INSERT INTO products (name, is_public) VALUES
    ('Pneu', true),
    ('Som automotivo', true),
    ('Câmera de ré', true),
    ('Suporte de celular para carro', true),
    ('Capacete', true),
    ('Jaqueta de moto', true),
    ('Luva de moto', true),
    ('Lavagem do carro', true)
ON DUPLICATE KEY UPDATE is_public = true;

-- Ferramentas
INSERT INTO products (name, is_public) VALUES
    ('Furadeira', true),
    ('Parafusadeira', true),
    ('Kit de ferramentas', true),
    ('Lavadora de alta pressão', true),
    ('Escada', true),
    ('Trena a laser', true)
ON DUPLICATE KEY UPDATE is_public = true;

-- Serviços e experiências
INSERT INTO products (name, is_public) VALUES
    ('Viagem', true),
    ('Passagem aérea', true),
    ('Hospedagem', true),
    ('Show', true),
    ('Ingresso de cinema', true),
    ('Ingresso de futebol', true),
    ('Assinatura Netflix', true),
    ('Assinatura Spotify', true),
    ('Assinatura Disney+', true),
    ('Assinatura Amazon Prime', true),
    ('Corte de cabelo', true),
    ('Tatuagem', true),
    ('Massagem', true)
ON DUPLICATE KEY UPDATE is_public = true;
