-- ==========================================================
-- DUMP DO BANCO POUPA PIG
-- ==========================================================
-- Gerado com mysqldump a partir de: database.sql + seeds/products.sql + seeds/demo.sql.
-- É uma alternativa aos três scripts: importa estrutura, catálogo e dados de demonstração
-- de uma vez. Login de demonstração: demo@poupapig.test ou ana@poupapig.test, senha "poupapig123".
-- Atenção: apaga e recria as tabelas do banco projeto_integrador (DROP TABLE IF EXISTS).
-- Gerado no MySQL; a importação no MariaDB do XAMPP não foi testada.
-- As datas dos itens de demonstração ficam fixas no dia em que o dump foi gerado.

CREATE DATABASE IF NOT EXISTS projeto_integrador
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE projeto_integrador;

-- MySQL dump 10.13  Distrib 26.7.0, for macos26.6 (arm64)
--
-- Host: localhost    Database: projeto_integrador
-- ------------------------------------------------------
-- Server version	26.7.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `password_changed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `accent_color` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'UsuÃ¡rio Demo','demo@poupapig.test','$2y$12$.TCvUiPWNP1SLkwR88lxPeUG9bvYvPWGDDWhMyWJloItrVFv05ZgS','user','2026-11-27 23:11:06',NULL,'green-400','2026-11-27 23:11:06','2026-11-27 23:11:06'),(2,'Ana Teste','ana@poupapig.test','$2y$12$.TCvUiPWNP1SLkwR88lxPeUG9bvYvPWGDDWhMyWJloItrVFv05ZgS','user','2026-11-27 23:11:06',NULL,NULL,'2026-11-27 23:11:06','2026-11-27 23:11:06');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'box-seam',
  `color` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'gray-200',
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_categories_user` (`user_id`),
  CONSTRAINT `fk_categories_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,1,'EletrÃ´nicos','smartphone','sky-200','Celular, fone e afins','2026-11-27 23:11:06'),(2,1,'Roupas','apparel','pink-200',NULL,'2026-11-27 23:11:06'),(3,1,'Casa','weekend','green-200','MÃ³veis e decoraÃ§Ã£o','2026-11-27 23:11:06'),(4,2,'Lazer','sports-esports','purple-300',NULL,'2026-11-27 23:11:06');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `items`
--

DROP TABLE IF EXISTS `items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `items` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `category_id` int unsigned NOT NULL,
  `name` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `link` text COLLATE utf8mb4_unicode_ci,
  `image` text COLLATE utf8mb4_unicode_ci,
  `reason` text COLLATE utf8mb4_unicode_ci,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('thinking','bought','gave_up') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'thinking',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `decided_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_items_user` (`user_id`),
  KEY `fk_items_category` (`category_id`),
  CONSTRAINT `fk_items_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_items_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `items`
--

LOCK TABLES `items` WRITE;
/*!40000 ALTER TABLE `items` DISABLE KEYS */;
INSERT INTO `items` VALUES (1,1,1,'AirPods Pro',1899.00,'https://www.apple.com/br/airpods-pro/',NULL,NULL,'2026-11-25','2026-12-02','thinking','2026-11-27 23:11:06',NULL),(2,1,3,'Cadeira gamer',1200.00,NULL,NULL,NULL,'2026-11-26','2026-12-10','thinking','2026-11-27 23:11:06',NULL),(3,1,2,'TÃªnis Adidas Samba',699.90,NULL,NULL,NULL,'2026-11-27','2026-12-04','thinking','2026-11-27 23:11:06',NULL),(4,1,1,'Kindle Paperwhite',899.00,NULL,NULL,NULL,'2026-11-17','2026-11-24','bought','2026-11-27 23:11:06','2026-11-24 23:11:06'),(5,1,2,'TÃªnis Nike Air Max',999.99,NULL,NULL,NULL,'2026-11-18','2026-11-25','gave_up','2026-11-27 23:11:06','2026-11-25 23:11:06'),(6,1,1,'JBL Flip',749.00,NULL,NULL,NULL,'2026-11-19','2026-11-26','gave_up','2026-11-27 23:11:06','2026-11-26 23:11:06'),(7,2,4,'PlayStation 5',3799.00,NULL,NULL,NULL,'2026-11-07','2026-11-21','bought','2026-11-27 23:11:06','2026-11-21 23:11:06'),(8,2,4,'Kindle Paperwhite',899.00,NULL,NULL,NULL,'2026-11-12','2026-11-22','bought','2026-11-27 23:11:06','2026-11-22 23:11:06');
/*!40000 ALTER TABLE `items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_products_name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=361 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,'iPhone 16',1,'2026-11-27 23:11:06'),(2,'iPhone 16 Pro',1,'2026-11-27 23:11:06'),(3,'iPhone 15',1,'2026-11-27 23:11:06'),(4,'iPhone 14',1,'2026-11-27 23:11:06'),(5,'iPhone 13',1,'2026-11-27 23:11:06'),(6,'Samsung Galaxy S24',1,'2026-11-27 23:11:06'),(7,'Samsung Galaxy S24 Ultra',1,'2026-11-27 23:11:06'),(8,'Samsung Galaxy S23',1,'2026-11-27 23:11:06'),(9,'Samsung Galaxy A55',1,'2026-11-27 23:11:06'),(10,'Samsung Galaxy A35',1,'2026-11-27 23:11:06'),(11,'Samsung Galaxy A15',1,'2026-11-27 23:11:06'),(12,'Samsung Galaxy Z Flip 6',1,'2026-11-27 23:11:06'),(13,'Motorola Edge 50',1,'2026-11-27 23:11:06'),(14,'Motorola Moto G84',1,'2026-11-27 23:11:06'),(15,'Motorola Moto G54',1,'2026-11-27 23:11:06'),(16,'Motorola Moto G34',1,'2026-11-27 23:11:06'),(17,'Xiaomi Redmi Note 13',1,'2026-11-27 23:11:06'),(18,'Xiaomi Redmi Note 13 Pro',1,'2026-11-27 23:11:06'),(19,'Xiaomi Poco X6',1,'2026-11-27 23:11:06'),(20,'Google Pixel 9',1,'2026-11-27 23:11:06'),(21,'iPad',1,'2026-11-27 23:11:06'),(22,'iPad Air',1,'2026-11-27 23:11:06'),(23,'iPad Pro',1,'2026-11-27 23:11:06'),(24,'iPad Mini',1,'2026-11-27 23:11:06'),(25,'Samsung Galaxy Tab S9',1,'2026-11-27 23:11:06'),(26,'Samsung Galaxy Tab A9',1,'2026-11-27 23:11:06'),(27,'Xiaomi Redmi Pad',1,'2026-11-27 23:11:06'),(28,'Kindle',1,'2026-11-27 23:11:06'),(29,'Kindle Paperwhite',1,'2026-11-27 23:11:06'),(30,'Capa de celular',1,'2026-11-27 23:11:06'),(31,'PelÃ­cula de vidro',1,'2026-11-27 23:11:06'),(32,'Carregador turbo',1,'2026-11-27 23:11:06'),(33,'Power bank',1,'2026-11-27 23:11:06'),(34,'Cabo USB-C',1,'2026-11-27 23:11:06'),(35,'MacBook Air',1,'2026-11-27 23:11:06'),(36,'MacBook Pro',1,'2026-11-27 23:11:06'),(37,'Notebook Dell Inspiron',1,'2026-11-27 23:11:06'),(38,'Notebook Lenovo IdeaPad',1,'2026-11-27 23:11:06'),(39,'Notebook Acer Aspire',1,'2026-11-27 23:11:06'),(40,'Notebook Asus Vivobook',1,'2026-11-27 23:11:06'),(41,'Notebook Samsung Galaxy Book',1,'2026-11-27 23:11:06'),(42,'Notebook gamer',1,'2026-11-27 23:11:06'),(43,'Chromebook',1,'2026-11-27 23:11:06'),(44,'PC gamer',1,'2026-11-27 23:11:06'),(45,'Monitor 24 polegadas',1,'2026-11-27 23:11:06'),(46,'Monitor 27 polegadas',1,'2026-11-27 23:11:06'),(47,'Monitor gamer 144Hz',1,'2026-11-27 23:11:06'),(48,'Monitor ultrawide',1,'2026-11-27 23:11:06'),(49,'Teclado mecÃ¢nico',1,'2026-11-27 23:11:06'),(50,'Teclado sem fio',1,'2026-11-27 23:11:06'),(51,'Mouse sem fio',1,'2026-11-27 23:11:06'),(52,'Mouse gamer',1,'2026-11-27 23:11:06'),(53,'Mousepad grande',1,'2026-11-27 23:11:06'),(54,'Webcam Full HD',1,'2026-11-27 23:11:06'),(55,'Headset gamer',1,'2026-11-27 23:11:06'),(56,'Microfone USB',1,'2026-11-27 23:11:06'),(57,'Impressora multifuncional',1,'2026-11-27 23:11:06'),(58,'Roteador Wi-Fi 6',1,'2026-11-27 23:11:06'),(59,'SSD 1TB',1,'2026-11-27 23:11:06'),(60,'SSD externo',1,'2026-11-27 23:11:06'),(61,'HD externo 2TB',1,'2026-11-27 23:11:06'),(62,'Pen drive 64GB',1,'2026-11-27 23:11:06'),(63,'Hub USB-C',1,'2026-11-27 23:11:06'),(64,'Suporte para notebook',1,'2026-11-27 23:11:06'),(65,'Cadeira de escritÃ³rio',1,'2026-11-27 23:11:06'),(66,'Mesa de escritÃ³rio',1,'2026-11-27 23:11:06'),(67,'AirPods',1,'2026-11-27 23:11:06'),(68,'AirPods Pro',1,'2026-11-27 23:11:06'),(69,'Fone JBL Tune',1,'2026-11-27 23:11:06'),(70,'Fone Bluetooth',1,'2026-11-27 23:11:06'),(71,'Fone com cancelamento de ruÃ­do',1,'2026-11-27 23:11:06'),(72,'Galaxy Buds',1,'2026-11-27 23:11:06'),(73,'Caixa de som JBL',1,'2026-11-27 23:11:06'),(74,'JBL Flip',1,'2026-11-27 23:11:06'),(75,'JBL Charge',1,'2026-11-27 23:11:06'),(76,'JBL Go',1,'2026-11-27 23:11:06'),(77,'Caixa de som bluetooth',1,'2026-11-27 23:11:06'),(78,'Soundbar',1,'2026-11-27 23:11:06'),(79,'Echo Dot',1,'2026-11-27 23:11:06'),(80,'Echo Show',1,'2026-11-27 23:11:06'),(81,'Google Nest Mini',1,'2026-11-27 23:11:06'),(82,'Smart TV 43 polegadas',1,'2026-11-27 23:11:06'),(83,'Smart TV 50 polegadas',1,'2026-11-27 23:11:06'),(84,'Smart TV 55 polegadas',1,'2026-11-27 23:11:06'),(85,'Smart TV 65 polegadas',1,'2026-11-27 23:11:06'),(86,'Fire TV Stick',1,'2026-11-27 23:11:06'),(87,'Chromecast',1,'2026-11-27 23:11:06'),(88,'Projetor',1,'2026-11-27 23:11:06'),(89,'Vitrola',1,'2026-11-27 23:11:06'),(90,'Toca-discos',1,'2026-11-27 23:11:06'),(91,'CÃ¢mera digital',1,'2026-11-27 23:11:06'),(92,'CÃ¢mera instantÃ¢nea',1,'2026-11-27 23:11:06'),(93,'GoPro',1,'2026-11-27 23:11:06'),(94,'Drone',1,'2026-11-27 23:11:06'),(95,'Ring light',1,'2026-11-27 23:11:06'),(96,'TripÃ©',1,'2026-11-27 23:11:06'),(97,'PlayStation 5',1,'2026-11-27 23:11:06'),(98,'PlayStation 5 Slim',1,'2026-11-27 23:11:06'),(99,'PlayStation 4',1,'2026-11-27 23:11:06'),(100,'Xbox Series X',1,'2026-11-27 23:11:06'),(101,'Xbox Series S',1,'2026-11-27 23:11:06'),(102,'Nintendo Switch',1,'2026-11-27 23:11:06'),(103,'Nintendo Switch OLED',1,'2026-11-27 23:11:06'),(104,'Steam Deck',1,'2026-11-27 23:11:06'),(105,'Controle DualSense',1,'2026-11-27 23:11:06'),(106,'Controle Xbox',1,'2026-11-27 23:11:06'),(107,'Controle Pro Switch',1,'2026-11-27 23:11:06'),(108,'PlayStation VR2',1,'2026-11-27 23:11:06'),(109,'Meta Quest 3',1,'2026-11-27 23:11:06'),(110,'Volante gamer',1,'2026-11-27 23:11:06'),(111,'Cadeira gamer',1,'2026-11-27 23:11:06'),(112,'Jogo de videogame',1,'2026-11-27 23:11:06'),(113,'Assinatura Game Pass',1,'2026-11-27 23:11:06'),(114,'Assinatura PlayStation Plus',1,'2026-11-27 23:11:06'),(115,'Gift card Steam',1,'2026-11-27 23:11:06'),(116,'Gift card PlayStation',1,'2026-11-27 23:11:06'),(117,'Apple Watch',1,'2026-11-27 23:11:06'),(118,'Apple Watch SE',1,'2026-11-27 23:11:06'),(119,'Galaxy Watch',1,'2026-11-27 23:11:06'),(120,'Smartwatch Amazfit',1,'2026-11-27 23:11:06'),(121,'Smartband Xiaomi',1,'2026-11-27 23:11:06'),(122,'RelÃ³gio Casio',1,'2026-11-27 23:11:06'),(123,'RelÃ³gio analÃ³gico',1,'2026-11-27 23:11:06'),(124,'Ã“culos de sol',1,'2026-11-27 23:11:06'),(125,'Ã“culos de grau',1,'2026-11-27 23:11:06'),(126,'Carteira de couro',1,'2026-11-27 23:11:06'),(127,'Cinto de couro',1,'2026-11-27 23:11:06'),(128,'Mochila',1,'2026-11-27 23:11:06'),(129,'Mochila para notebook',1,'2026-11-27 23:11:06'),(130,'Bolsa',1,'2026-11-27 23:11:06'),(131,'Bolsa transversal',1,'2026-11-27 23:11:06'),(132,'Mala de viagem',1,'2026-11-27 23:11:06'),(133,'Mala de bordo',1,'2026-11-27 23:11:06'),(134,'Pochete',1,'2026-11-27 23:11:06'),(135,'BonÃ©',1,'2026-11-27 23:11:06'),(136,'ChapÃ©u',1,'2026-11-27 23:11:06'),(137,'Anel de prata',1,'2026-11-27 23:11:06'),(138,'Colar',1,'2026-11-27 23:11:06'),(139,'Brinco',1,'2026-11-27 23:11:06'),(140,'Pulseira',1,'2026-11-27 23:11:06'),(141,'Perfume importado',1,'2026-11-27 23:11:06'),(142,'Perfume nacional',1,'2026-11-27 23:11:06'),(143,'TÃªnis Nike Air Force 1',1,'2026-11-27 23:11:06'),(144,'TÃªnis Nike Air Max',1,'2026-11-27 23:11:06'),(145,'TÃªnis Adidas Ultraboost',1,'2026-11-27 23:11:06'),(146,'TÃªnis Adidas Samba',1,'2026-11-27 23:11:06'),(147,'TÃªnis New Balance',1,'2026-11-27 23:11:06'),(148,'TÃªnis Vans',1,'2026-11-27 23:11:06'),(149,'TÃªnis Converse All Star',1,'2026-11-27 23:11:06'),(150,'TÃªnis Puma',1,'2026-11-27 23:11:06'),(151,'TÃªnis de corrida',1,'2026-11-27 23:11:06'),(152,'Chinelo Havaianas',1,'2026-11-27 23:11:06'),(153,'SandÃ¡lia',1,'2026-11-27 23:11:06'),(154,'Bota',1,'2026-11-27 23:11:06'),(155,'Sapato social',1,'2026-11-27 23:11:06'),(156,'Jaqueta jeans',1,'2026-11-27 23:11:06'),(157,'Jaqueta de couro',1,'2026-11-27 23:11:06'),(158,'Jaqueta corta-vento',1,'2026-11-27 23:11:06'),(159,'Moletom',1,'2026-11-27 23:11:06'),(160,'Moletom canguru',1,'2026-11-27 23:11:06'),(161,'Camiseta bÃ¡sica',1,'2026-11-27 23:11:06'),(162,'Camiseta de time',1,'2026-11-27 23:11:06'),(163,'Camisa social',1,'2026-11-27 23:11:06'),(164,'Camisa polo',1,'2026-11-27 23:11:06'),(165,'CalÃ§a jeans',1,'2026-11-27 23:11:06'),(166,'CalÃ§a cargo',1,'2026-11-27 23:11:06'),(167,'CalÃ§a de moletom',1,'2026-11-27 23:11:06'),(168,'Bermuda',1,'2026-11-27 23:11:06'),(169,'Short',1,'2026-11-27 23:11:06'),(170,'Vestido',1,'2026-11-27 23:11:06'),(171,'Saia',1,'2026-11-27 23:11:06'),(172,'Blazer',1,'2026-11-27 23:11:06'),(173,'Casaco',1,'2026-11-27 23:11:06'),(174,'Pijama',1,'2026-11-27 23:11:06'),(175,'Roupa de academia',1,'2026-11-27 23:11:06'),(176,'BiquÃ­ni',1,'2026-11-27 23:11:06'),(177,'Sunga',1,'2026-11-27 23:11:06'),(178,'SofÃ¡',1,'2026-11-27 23:11:06'),(179,'SofÃ¡ retrÃ¡til',1,'2026-11-27 23:11:06'),(180,'Poltrona',1,'2026-11-27 23:11:06'),(181,'Puff',1,'2026-11-27 23:11:06'),(182,'Cama box',1,'2026-11-27 23:11:06'),(183,'ColchÃ£o',1,'2026-11-27 23:11:06'),(184,'Travesseiro',1,'2026-11-27 23:11:06'),(185,'Jogo de cama',1,'2026-11-27 23:11:06'),(186,'Edredom',1,'2026-11-27 23:11:06'),(187,'Cortina',1,'2026-11-27 23:11:06'),(188,'Tapete',1,'2026-11-27 23:11:06'),(189,'Guarda-roupa',1,'2026-11-27 23:11:06'),(190,'CÃ´moda',1,'2026-11-27 23:11:06'),(191,'Rack para TV',1,'2026-11-27 23:11:06'),(192,'Estante',1,'2026-11-27 23:11:06'),(193,'Mesa de jantar',1,'2026-11-27 23:11:06'),(194,'Cadeira',1,'2026-11-27 23:11:06'),(195,'Escrivaninha',1,'2026-11-27 23:11:06'),(196,'LuminÃ¡ria',1,'2026-11-27 23:11:06'),(197,'Abajur',1,'2026-11-27 23:11:06'),(198,'Espelho',1,'2026-11-27 23:11:06'),(199,'Quadro decorativo',1,'2026-11-27 23:11:06'),(200,'Vaso de planta',1,'2026-11-27 23:11:06'),(201,'Organizador',1,'2026-11-27 23:11:06'),(202,'Ventilador',1,'2026-11-27 23:11:06'),(203,'Ventilador de teto',1,'2026-11-27 23:11:06'),(204,'Ar-condicionado',1,'2026-11-27 23:11:06'),(205,'Aquecedor',1,'2026-11-27 23:11:06'),(206,'Umidificador',1,'2026-11-27 23:11:06'),(207,'Purificador de ar',1,'2026-11-27 23:11:06'),(208,'Geladeira',1,'2026-11-27 23:11:06'),(209,'Geladeira frost free',1,'2026-11-27 23:11:06'),(210,'FogÃ£o',1,'2026-11-27 23:11:06'),(211,'Cooktop',1,'2026-11-27 23:11:06'),(212,'Forno elÃ©trico',1,'2026-11-27 23:11:06'),(213,'Micro-ondas',1,'2026-11-27 23:11:06'),(214,'Air fryer',1,'2026-11-27 23:11:06'),(215,'Lava-louÃ§as',1,'2026-11-27 23:11:06'),(216,'MÃ¡quina de lavar',1,'2026-11-27 23:11:06'),(217,'Lava e seca',1,'2026-11-27 23:11:06'),(218,'Secadora de roupas',1,'2026-11-27 23:11:06'),(219,'Aspirador de pÃ³',1,'2026-11-27 23:11:06'),(220,'Aspirador robÃ´',1,'2026-11-27 23:11:06'),(221,'Ferro de passar',1,'2026-11-27 23:11:06'),(222,'Vaporizador de roupas',1,'2026-11-27 23:11:06'),(223,'Liquidificador',1,'2026-11-27 23:11:06'),(224,'Batedeira',1,'2026-11-27 23:11:06'),(225,'Mixer',1,'2026-11-27 23:11:06'),(226,'Processador de alimentos',1,'2026-11-27 23:11:06'),(227,'Cafeteira',1,'2026-11-27 23:11:06'),(228,'Cafeteira Nespresso',1,'2026-11-27 23:11:06'),(229,'Cafeteira Dolce Gusto',1,'2026-11-27 23:11:06'),(230,'Chaleira elÃ©trica',1,'2026-11-27 23:11:06'),(231,'Torradeira',1,'2026-11-27 23:11:06'),(232,'Sanduicheira',1,'2026-11-27 23:11:06'),(233,'Grill',1,'2026-11-27 23:11:06'),(234,'Panela elÃ©trica de arroz',1,'2026-11-27 23:11:06'),(235,'Panela de pressÃ£o elÃ©trica',1,'2026-11-27 23:11:06'),(236,'Purificador de Ã¡gua',1,'2026-11-27 23:11:06'),(237,'Frigobar',1,'2026-11-27 23:11:06'),(238,'Jogo de panelas',1,'2026-11-27 23:11:06'),(239,'Panela antiaderente',1,'2026-11-27 23:11:06'),(240,'Frigideira',1,'2026-11-27 23:11:06'),(241,'Jogo de facas',1,'2026-11-27 23:11:06'),(242,'TÃ¡bua de corte',1,'2026-11-27 23:11:06'),(243,'Jogo de pratos',1,'2026-11-27 23:11:06'),(244,'Jogo de copos',1,'2026-11-27 23:11:06'),(245,'Jogo de talheres',1,'2026-11-27 23:11:06'),(246,'Garrafa tÃ©rmica',1,'2026-11-27 23:11:06'),(247,'Copo tÃ©rmico Stanley',1,'2026-11-27 23:11:06'),(248,'Marmita',1,'2026-11-27 23:11:06'),(249,'Potes hermÃ©ticos',1,'2026-11-27 23:11:06'),(250,'Escorredor de louÃ§a',1,'2026-11-27 23:11:06'),(251,'Lixeira',1,'2026-11-27 23:11:06'),(252,'Compras do mÃªs',1,'2026-11-27 23:11:06'),(253,'Pizza',1,'2026-11-27 23:11:06'),(254,'HambÃºrguer',1,'2026-11-27 23:11:06'),(255,'Delivery',1,'2026-11-27 23:11:06'),(256,'Sushi',1,'2026-11-27 23:11:06'),(257,'AÃ§aÃ­',1,'2026-11-27 23:11:06'),(258,'Sorvete',1,'2026-11-27 23:11:06'),(259,'Chocolate',1,'2026-11-27 23:11:06'),(260,'CafÃ© em cÃ¡psula',1,'2026-11-27 23:11:06'),(261,'CafÃ© especial',1,'2026-11-27 23:11:06'),(262,'Vinho',1,'2026-11-27 23:11:06'),(263,'Cerveja artesanal',1,'2026-11-27 23:11:06'),(264,'Lanche',1,'2026-11-27 23:11:06'),(265,'Restaurante',1,'2026-11-27 23:11:06'),(266,'Padaria',1,'2026-11-27 23:11:06'),(267,'Doces',1,'2026-11-27 23:11:06'),(268,'Suplemento whey',1,'2026-11-27 23:11:06'),(269,'Creatina',1,'2026-11-27 23:11:06'),(270,'Secador de cabelo',1,'2026-11-27 23:11:06'),(271,'Chapinha',1,'2026-11-27 23:11:06'),(272,'Modelador de cachos',1,'2026-11-27 23:11:06'),(273,'Escova alisadora',1,'2026-11-27 23:11:06'),(274,'Barbeador elÃ©trico',1,'2026-11-27 23:11:06'),(275,'Aparador de pelos',1,'2026-11-27 23:11:06'),(276,'Escova de dentes elÃ©trica',1,'2026-11-27 23:11:06'),(277,'Kit de maquiagem',1,'2026-11-27 23:11:06'),(278,'Base',1,'2026-11-27 23:11:06'),(279,'Batom',1,'2026-11-27 23:11:06'),(280,'Paleta de sombras',1,'2026-11-27 23:11:06'),(281,'Skincare',1,'2026-11-27 23:11:06'),(282,'Protetor solar',1,'2026-11-27 23:11:06'),(283,'Hidratante',1,'2026-11-27 23:11:06'),(284,'Shampoo',1,'2026-11-27 23:11:06'),(285,'Perfume feminino',1,'2026-11-27 23:11:06'),(286,'Perfume masculino',1,'2026-11-27 23:11:06'),(287,'Bicicleta',1,'2026-11-27 23:11:06'),(288,'Bicicleta elÃ©trica',1,'2026-11-27 23:11:06'),(289,'Patinete elÃ©trico',1,'2026-11-27 23:11:06'),(290,'Skate',1,'2026-11-27 23:11:06'),(291,'Patins',1,'2026-11-27 23:11:06'),(292,'Esteira',1,'2026-11-27 23:11:06'),(293,'Bicicleta ergomÃ©trica',1,'2026-11-27 23:11:06'),(294,'Kit de halteres',1,'2026-11-27 23:11:06'),(295,'Tapete de yoga',1,'2026-11-27 23:11:06'),(296,'Bola de futebol',1,'2026-11-27 23:11:06'),(297,'Chuteira',1,'2026-11-27 23:11:06'),(298,'Raquete de tÃªnis',1,'2026-11-27 23:11:06'),(299,'Raquete de beach tennis',1,'2026-11-27 23:11:06'),(300,'Barraca de camping',1,'2026-11-27 23:11:06'),(301,'Garrafa de Ã¡gua',1,'2026-11-27 23:11:06'),(302,'Mochila de trilha',1,'2026-11-27 23:11:06'),(303,'Prancha de surfe',1,'2026-11-27 23:11:06'),(304,'Mensalidade da academia',1,'2026-11-27 23:11:06'),(305,'Livro',1,'2026-11-27 23:11:06'),(306,'Box de livros',1,'2026-11-27 23:11:06'),(307,'MangÃ¡',1,'2026-11-27 23:11:06'),(308,'HQ',1,'2026-11-27 23:11:06'),(309,'Agenda',1,'2026-11-27 23:11:06'),(310,'Caderno',1,'2026-11-27 23:11:06'),(311,'Planner',1,'2026-11-27 23:11:06'),(312,'Caneta',1,'2026-11-27 23:11:06'),(313,'Kit de canetas',1,'2026-11-27 23:11:06'),(314,'Mochila escolar',1,'2026-11-27 23:11:06'),(315,'Calculadora cientÃ­fica',1,'2026-11-27 23:11:06'),(316,'Curso online',1,'2026-11-27 23:11:06'),(317,'Carrinho de bebÃª',1,'2026-11-27 23:11:06'),(318,'Cadeirinha para carro',1,'2026-11-27 23:11:06'),(319,'BerÃ§o',1,'2026-11-27 23:11:06'),(320,'Brinquedo',1,'2026-11-27 23:11:06'),(321,'LEGO',1,'2026-11-27 23:11:06'),(322,'Boneca',1,'2026-11-27 23:11:06'),(323,'Carrinho de controle remoto',1,'2026-11-27 23:11:06'),(324,'Quebra-cabeÃ§a',1,'2026-11-27 23:11:06'),(325,'Jogo de tabuleiro',1,'2026-11-27 23:11:06'),(326,'RaÃ§Ã£o',1,'2026-11-27 23:11:06'),(327,'Caminha de pet',1,'2026-11-27 23:11:06'),(328,'Arranhador',1,'2026-11-27 23:11:06'),(329,'Brinquedo para pet',1,'2026-11-27 23:11:06'),(330,'Coleira',1,'2026-11-27 23:11:06'),(331,'Caixa de transporte',1,'2026-11-27 23:11:06'),(332,'Fonte de Ã¡gua para pet',1,'2026-11-27 23:11:06'),(333,'Pneu',1,'2026-11-27 23:11:06'),(334,'Som automotivo',1,'2026-11-27 23:11:06'),(335,'CÃ¢mera de rÃ©',1,'2026-11-27 23:11:06'),(336,'Suporte de celular para carro',1,'2026-11-27 23:11:06'),(337,'Capacete',1,'2026-11-27 23:11:06'),(338,'Jaqueta de moto',1,'2026-11-27 23:11:06'),(339,'Luva de moto',1,'2026-11-27 23:11:06'),(340,'Lavagem do carro',1,'2026-11-27 23:11:06'),(341,'Furadeira',1,'2026-11-27 23:11:06'),(342,'Parafusadeira',1,'2026-11-27 23:11:06'),(343,'Kit de ferramentas',1,'2026-11-27 23:11:06'),(344,'Lavadora de alta pressÃ£o',1,'2026-11-27 23:11:06'),(345,'Escada',1,'2026-11-27 23:11:06'),(346,'Trena a laser',1,'2026-11-27 23:11:06'),(347,'Viagem',1,'2026-11-27 23:11:06'),(348,'Passagem aÃ©rea',1,'2026-11-27 23:11:06'),(349,'Hospedagem',1,'2026-11-27 23:11:06'),(350,'Show',1,'2026-11-27 23:11:06'),(351,'Ingresso de cinema',1,'2026-11-27 23:11:06'),(352,'Ingresso de futebol',1,'2026-11-27 23:11:06'),(353,'Assinatura Netflix',1,'2026-11-27 23:11:06'),(354,'Assinatura Spotify',1,'2026-11-27 23:11:06'),(355,'Assinatura Disney+',1,'2026-11-27 23:11:06'),(356,'Assinatura Amazon Prime',1,'2026-11-27 23:11:06'),(357,'Corte de cabelo',1,'2026-11-27 23:11:06'),(358,'Tatuagem',1,'2026-11-27 23:11:06'),(359,'Massagem',1,'2026-11-27 23:11:06');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reviews` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int unsigned NOT NULL,
  `user_id` int unsigned NOT NULL,
  `rating` tinyint unsigned NOT NULL,
  `text` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_reviews_product_user` (`product_id`,`user_id`),
  KEY `fk_reviews_user` (`user_id`),
  CONSTRAINT `fk_reviews_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_reviews_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `chk_reviews_rating` CHECK ((`rating` between 1 and 5))
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reviews`
--

LOCK TABLES `reviews` WRITE;
/*!40000 ALTER TABLE `reviews` DISABLE KEYS */;
INSERT INTO `reviews` VALUES (1,29,1,5,'Bateria dura muito e a tela nÃ£o cansa a vista.','2026-11-27 23:11:06'),(2,29,2,4,NULL,'2026-11-27 23:11:06'),(3,97,2,5,'Valeu cada dia de espera.','2026-11-27 23:11:06');
/*!40000 ALTER TABLE `reviews` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed
