-- MySQL dump 10.13  Distrib 8.0.38, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: chazam
-- ------------------------------------------------------
-- Server version	8.3.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chat_usuario`
--

DROP TABLE IF EXISTS `chat_usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chat_usuario` (
  `id_chat_usuario` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_chat` bigint unsigned NOT NULL,
  `id_usuario` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_chat_usuario`),
  KEY `chat_usuario_id_chat_foreign` (`id_chat`),
  KEY `chat_usuario_id_usuario_foreign` (`id_usuario`),
  CONSTRAINT `chat_usuario_id_chat_foreign` FOREIGN KEY (`id_chat`) REFERENCES `chats` (`id_chat`),
  CONSTRAINT `chat_usuario_id_usuario_foreign` FOREIGN KEY (`id_usuario`) REFERENCES `users` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chat_usuario`
--

LOCK TABLES `chat_usuario` WRITE;
/*!40000 ALTER TABLE `chat_usuario` DISABLE KEYS */;
/*!40000 ALTER TABLE `chat_usuario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chats`
--

DROP TABLE IF EXISTS `chats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chats` (
  `id_chat` bigint unsigned NOT NULL AUTO_INCREMENT,
  `creator` bigint unsigned DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL,
  `img` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipocomunidad` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `codigo` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `id_reto` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_chat`),
  UNIQUE KEY `chats_codigo_unique` (`codigo`),
  KEY `chats_id_reto_foreign` (`id_reto`),
  KEY `chats_creator_foreign` (`creator`),
  CONSTRAINT `chats_creator_foreign` FOREIGN KEY (`creator`) REFERENCES `users` (`id_usuario`),
  CONSTRAINT `chats_id_reto_foreign` FOREIGN KEY (`id_reto`) REFERENCES `retos` (`id_reto`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chats`
--

LOCK TABLES `chats` WRITE;
/*!40000 ALTER TABLE `chats` DISABLE KEYS */;
INSERT INTO `chats` VALUES (1,1,'2025-05-24 20:33:36','php.jpg','Programadores PHP','publica',NULL,'Comunidad para discutir sobre desarrollo en PHP, Laravel y frameworks relacionados.',NULL,'2025-05-24 18:33:36','2025-05-24 18:33:36'),(2,1,'2025-05-24 20:33:36','gamers.jpg','Gamers Chazam','publica',NULL,'Comunidad para gamers que quieren compartir sus experiencias y organizar partidas.',NULL,'2025-05-24 18:33:36','2025-05-24 18:33:36'),(3,1,'2025-05-24 20:33:36','music_art.jpg','Música y Arte','publica',NULL,'Espacio para compartir y discutir sobre música, arte y cultura en general.',NULL,'2025-05-24 18:33:36','2025-05-24 18:33:36'),(4,1,'2025-05-24 20:33:36','secret.jpg','Comunidad Cerrada','privada','CHAZAM2024','Comunidad privada para miembros selectos.',NULL,'2025-05-24 18:33:36','2025-05-24 18:33:36');
/*!40000 ALTER TABLE `chats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `estados`
--

DROP TABLE IF EXISTS `estados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `estados` (
  `id_estado` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nom_estado` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_estado`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estados`
--

LOCK TABLES `estados` WRITE;
/*!40000 ALTER TABLE `estados` DISABLE KEYS */;
INSERT INTO `estados` VALUES (1,'Activo',NULL,NULL),(2,'Inactivo',NULL,NULL),(3,'Ban',NULL,NULL),(4,'PermaBan',NULL,NULL),(5,'Disponible',NULL,NULL);
/*!40000 ALTER TABLE `estados` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `historias`
--

DROP TABLE IF EXISTS `historias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `historias` (
  `id_historia` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_usuario` bigint unsigned NOT NULL,
  `fecha_inicio` datetime NOT NULL,
  `fecha_fin` datetime NOT NULL,
  `img` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_historia`),
  KEY `historias_id_usuario_foreign` (`id_usuario`),
  CONSTRAINT `historias_id_usuario_foreign` FOREIGN KEY (`id_usuario`) REFERENCES `users` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `historias`
--

LOCK TABLES `historias` WRITE;
/*!40000 ALTER TABLE `historias` DISABLE KEYS */;
/*!40000 ALTER TABLE `historias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mensajes`
--

DROP TABLE IF EXISTS `mensajes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mensajes` (
  `id_mensaje` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_chat_usuario` bigint unsigned NOT NULL,
  `contenido` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_envio` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_mensaje`),
  KEY `mensajes_id_chat_usuario_foreign` (`id_chat_usuario`),
  CONSTRAINT `mensajes_id_chat_usuario_foreign` FOREIGN KEY (`id_chat_usuario`) REFERENCES `chat_usuario` (`id_chat_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mensajes`
--

LOCK TABLES `mensajes` WRITE;
/*!40000 ALTER TABLE `mensajes` DISABLE KEYS */;
/*!40000 ALTER TABLE `mensajes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000001_create_cache_table',1),(2,'0001_01_01_000002_create_jobs_table',1),(3,'2025_04_08_133542_roles',1),(4,'2025_04_08_133548_estados',1),(5,'2025_04_08_133700_retos',1),(6,'2025_04_08_133720_chats',1),(7,'2025_04_08_135706_nacionalidad',1),(8,'2025_04_08_140830_tipo_producto',1),(9,'2025_04_08_150000_productos',1),(10,'2025_04_08_150001_create_users_table',1),(11,'2025_04_08_150003_add_creator_to_chats',1),(12,'2025_04_08_160003_historias',1),(13,'2025_04_08_160004_solicitudes',1),(14,'2025_04_08_160005_reportes',1),(15,'2025_04_08_160006_pagos',1),(16,'2025_04_08_160007_chat_usuario',1),(17,'2025_04_08_160008_mensajes',1),(18,'2025_04_08_160009_sugerencias',1),(19,'2025_05_21_193607_create_personalizacion_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nacionalidad`
--

DROP TABLE IF EXISTS `nacionalidad`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nacionalidad` (
  `id_nacionalidad` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bandera` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_nacionalidad`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nacionalidad`
--

LOCK TABLES `nacionalidad` WRITE;
/*!40000 ALTER TABLE `nacionalidad` DISABLE KEYS */;
INSERT INTO `nacionalidad` VALUES (1,'España','es.png',NULL,NULL),(2,'Mexico','mx.png',NULL,NULL),(3,'Colombia','co.png',NULL,NULL),(4,'Argentina','ar.png',NULL,NULL),(5,'Chile','cl.png',NULL,NULL),(6,'Perú','pe.png',NULL,NULL),(7,'Venezuela','ve.png',NULL,NULL),(8,'Ecuador','ec.png',NULL,NULL),(9,'Brasil','br.png',NULL,NULL),(10,'Chile','cl.png',NULL,NULL),(11,'China','cn.png',NULL,NULL),(12,'Japón','jp.png',NULL,NULL),(13,'Corea del Sur','kr.png',NULL,NULL),(14,'India','in.png',NULL,NULL),(15,'Australia','au.png',NULL,NULL),(16,'Nueva Zelanda','nz.png',NULL,NULL),(17,'Estados Unidos','us.png',NULL,NULL),(18,'Canada','ca.png',NULL,NULL),(19,'Francia','fr.png',NULL,NULL),(20,'Alemania','de.png',NULL,NULL),(21,'Italia','it.png',NULL,NULL),(22,'Portugal','pt.png',NULL,NULL),(23,'Grecia','gr.png',NULL,NULL);
/*!40000 ALTER TABLE `nacionalidad` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pagos`
--

DROP TABLE IF EXISTS `pagos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pagos` (
  `id_pago` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_comprador` bigint unsigned DEFAULT NULL,
  `fecha_pago` datetime NOT NULL,
  `id_producto` bigint unsigned DEFAULT NULL,
  `cantidad` decimal(8,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_pago`),
  KEY `pagos_id_comprador_foreign` (`id_comprador`),
  KEY `pagos_id_producto_foreign` (`id_producto`),
  CONSTRAINT `pagos_id_comprador_foreign` FOREIGN KEY (`id_comprador`) REFERENCES `users` (`id_usuario`) ON DELETE SET NULL,
  CONSTRAINT `pagos_id_producto_foreign` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pagos`
--

LOCK TABLES `pagos` WRITE;
/*!40000 ALTER TABLE `pagos` DISABLE KEYS */;
/*!40000 ALTER TABLE `pagos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personalizacion`
--

DROP TABLE IF EXISTS `personalizacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personalizacion` (
  `id_personalizacion` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_usuario` bigint unsigned NOT NULL,
  `marco` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rotacion` tinyint(1) NOT NULL DEFAULT '0',
  `brillo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sidebar` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_personalizacion`),
  KEY `personalizacion_id_usuario_foreign` (`id_usuario`),
  CONSTRAINT `personalizacion_id_usuario_foreign` FOREIGN KEY (`id_usuario`) REFERENCES `users` (`id_usuario`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personalizacion`
--

LOCK TABLES `personalizacion` WRITE;
/*!40000 ALTER TABLE `personalizacion` DISABLE KEYS */;
/*!40000 ALTER TABLE `personalizacion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `productos`
--

DROP TABLE IF EXISTS `productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `productos` (
  `id_producto` bigint unsigned NOT NULL AUTO_INCREMENT,
  `titulo` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `tipo_valor` enum('euros','puntos') COLLATE utf8mb4_unicode_ci NOT NULL,
  `puntos` int DEFAULT NULL,
  `id_tipo_producto` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_producto`),
  KEY `productos_id_tipo_producto_foreign` (`id_tipo_producto`),
  CONSTRAINT `productos_id_tipo_producto_foreign` FOREIGN KEY (`id_tipo_producto`) REFERENCES `tipo_producto` (`id_tipo_producto`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `productos`
--

LOCK TABLES `productos` WRITE;
/*!40000 ALTER TABLE `productos` DISABLE KEYS */;
INSERT INTO `productos` VALUES (1,'Suscripción Premium','Acceso completo a todas las funciones premium durante un mes.',9.99,'euros',NULL,1,NULL,NULL),(2,'Mejorar skips','Reduce el tiempo de espera para realizar skips.',5.99,'euros',NULL,2,NULL,NULL),(3,'Combo 1','Incluye 1 mes de suscripción premium y 750 puntos.',14.99,'euros',750,2,NULL,NULL),(4,'Combo 2','Incluye 1 mes de suscripción premium y 1250 puntos.',19.99,'euros',1250,2,NULL,NULL),(5,'Combo 3','Incluye 1 mes de suscripción premium y 2500 puntos.',29.99,'euros',2500,2,NULL,NULL),(6,'Pack de 1000 puntos','Compra de 1000 puntos en Chazam.',1.99,'euros',1000,3,NULL,NULL),(7,'Pack de 2000 puntos','Compra de 2000 puntos en Chazam.',4.99,'euros',2000,3,NULL,NULL),(8,'Pack de 3500 puntos','Compra de 3500 puntos en Chazam.',7.99,'euros',3500,3,NULL,NULL),(9,'Pack de 5000 puntos','Compra de 5000 puntos en Chazam.',9.99,'euros',5000,3,NULL,NULL),(10,'Pack de 10000 puntos','Compra de 10000 puntos en Chazam.',19.99,'euros',10000,3,NULL,NULL),(11,'Suscripción de miembro con puntos','Suscripción de miembro con 15000 puntos.',15000.00,'puntos',15000,1,NULL,NULL),(12,'Mejorar skips con puntos','Mejorar skips con 60000 puntos.',60000.00,'puntos',60000,2,NULL,NULL),(13,'Comunidad','Creación de una comunidad.',75000.00,'puntos',NULL,5,NULL,NULL);
/*!40000 ALTER TABLE `productos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reportes`
--

DROP TABLE IF EXISTS `reportes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reportes` (
  `id_reporte` bigint unsigned NOT NULL AUTO_INCREMENT,
  `titulo` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_reportador` bigint unsigned NOT NULL,
  `id_reportado` bigint unsigned NOT NULL,
  `leido` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_reporte`),
  KEY `reportes_id_reportador_foreign` (`id_reportador`),
  KEY `reportes_id_reportado_foreign` (`id_reportado`),
  CONSTRAINT `reportes_id_reportado_foreign` FOREIGN KEY (`id_reportado`) REFERENCES `users` (`id_usuario`),
  CONSTRAINT `reportes_id_reportador_foreign` FOREIGN KEY (`id_reportador`) REFERENCES `users` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reportes`
--

LOCK TABLES `reportes` WRITE;
/*!40000 ALTER TABLE `reportes` DISABLE KEYS */;
/*!40000 ALTER TABLE `reportes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `retos`
--

DROP TABLE IF EXISTS `retos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `retos` (
  `id_reto` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nom_reto` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `desc_reto` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_reto`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `retos`
--

LOCK TABLES `retos` WRITE;
/*!40000 ALTER TABLE `retos` DISABLE KEYS */;
INSERT INTO `retos` VALUES (1,'Hoy toca hablar con emojis','Usa SOLO emojis para comunicarte. ¿Podrán entenderte? ¡Sé creativo con tus combinaciones!','2025-05-24 18:33:36','2025-05-24 18:33:36'),(2,'Mensaje encriptado','¡Algunos carácteres están cifrados! ¿Podrás conseguir comunicarte con tu pareja?','2025-05-24 18:33:36','2025-05-24 18:33:36'),(3,'Desorden absoluto','Vuestras frases se enviarán desordenadas, intentad descifrar el mensaje original','2025-05-24 18:33:36','2025-05-24 18:33:36'),(4,'Boca abajo','Vuestro texto estará boca abajo ¡no forzéis mucho el cuello!','2025-05-24 18:33:36','2025-05-24 18:33:36');
/*!40000 ALTER TABLE `retos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id_rol` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nom_rol` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_rol`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Administrador',NULL,NULL),(2,'Usuario',NULL,NULL),(3,'Premium',NULL,NULL),(4,'Miembro',NULL,NULL);
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('WRGGAaLQ2ltQkbGYvEQdeyhMOjnkDINoi0Ty24Y1',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoicDBjbTVOWU9xUlB5c0NnUTM4aXFWNkJsT0czVGRkU3cxc0dZOEtFZCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0NjoiaHR0cDovL2xvY2FsaG9zdDo4MDAwL2VzdGFkby91c3Vhcmlvcy1lbi1saW5lYSI7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjIxOiJodHRwOi8vbG9jYWxob3N0OjgwMDAiO319',1748111642);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `solicitudes`
--

DROP TABLE IF EXISTS `solicitudes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `solicitudes` (
  `id_solicitud` bigint unsigned NOT NULL AUTO_INCREMENT,
  `estado` enum('pendiente','aceptada','rechazada','blockeada') COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_emisor` bigint unsigned NOT NULL,
  `id_receptor` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_solicitud`),
  KEY `solicitudes_id_emisor_foreign` (`id_emisor`),
  KEY `solicitudes_id_receptor_foreign` (`id_receptor`),
  CONSTRAINT `solicitudes_id_emisor_foreign` FOREIGN KEY (`id_emisor`) REFERENCES `users` (`id_usuario`),
  CONSTRAINT `solicitudes_id_receptor_foreign` FOREIGN KEY (`id_receptor`) REFERENCES `users` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `solicitudes`
--

LOCK TABLES `solicitudes` WRITE;
/*!40000 ALTER TABLE `solicitudes` DISABLE KEYS */;
/*!40000 ALTER TABLE `solicitudes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sugerencia`
--

DROP TABLE IF EXISTS `sugerencia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sugerencia` (
  `id_sugerencia` bigint unsigned NOT NULL AUTO_INCREMENT,
  `titulo` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_sugerente` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_sugerencia`),
  KEY `sugerencia_id_sugerente_foreign` (`id_sugerente`),
  CONSTRAINT `sugerencia_id_sugerente_foreign` FOREIGN KEY (`id_sugerente`) REFERENCES `users` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sugerencia`
--

LOCK TABLES `sugerencia` WRITE;
/*!40000 ALTER TABLE `sugerencia` DISABLE KEYS */;
/*!40000 ALTER TABLE `sugerencia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipo_producto`
--

DROP TABLE IF EXISTS `tipo_producto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipo_producto` (
  `id_tipo_producto` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tipo_producto` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_tipo_producto`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipo_producto`
--

LOCK TABLES `tipo_producto` WRITE;
/*!40000 ALTER TABLE `tipo_producto` DISABLE KEYS */;
INSERT INTO `tipo_producto` VALUES (1,'Suscripciones',NULL,NULL),(2,'Compras únicas',NULL,NULL),(3,'Packs de puntos',NULL,NULL),(4,'Donaciones',NULL,NULL),(5,'Comunidad',NULL,NULL);
/*!40000 ALTER TABLE `tipo_producto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id_usuario` bigint unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `genero` enum('hombre','mujer') COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `puntos` int NOT NULL DEFAULT '500',
  `racha` int NOT NULL DEFAULT '0',
  `id_nacionalidad` bigint unsigned NOT NULL,
  `id_rol` bigint unsigned NOT NULL,
  `id_estado` bigint unsigned NOT NULL,
  `img` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `strikes` int DEFAULT '0',
  `inicio_ban` datetime DEFAULT NULL,
  `fin_ban` datetime DEFAULT NULL,
  `ultimo_login` datetime DEFAULT NULL,
  `puntos_diarios` int NOT NULL DEFAULT '0',
  `skip_time` time NOT NULL DEFAULT '00:00:00',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_id_rol_foreign` (`id_rol`),
  KEY `users_id_estado_foreign` (`id_estado`),
  KEY `users_id_nacionalidad_foreign` (`id_nacionalidad`),
  CONSTRAINT `users_id_estado_foreign` FOREIGN KEY (`id_estado`) REFERENCES `estados` (`id_estado`),
  CONSTRAINT `users_id_nacionalidad_foreign` FOREIGN KEY (`id_nacionalidad`) REFERENCES `nacionalidad` (`id_nacionalidad`),
  CONSTRAINT `users_id_rol_foreign` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin','Administrador','Sistema','1990-01-01','hombre','admin@example.com',NULL,'$2y$12$FOyLQfrQ8uBEFWvmrqyR4.h3vjIWd74mhkFWb/OJ.nv4YEwqoHCkm',1000,0,1,1,2,NULL,'Administrador del sistema',0,NULL,NULL,NULL,0,'00:00:00',NULL,NULL,NULL),(2,'moderador','Moderador','Sistema','1990-01-01','hombre','moderador@example.com',NULL,'$2y$12$x3X.LlaPM7jI0VAEpuBFFu5Ctdv198zKScRtRax8dFp.i4qqfAxka',800,0,1,2,2,NULL,'Moderador del sistema',0,NULL,NULL,NULL,0,'00:00:00',NULL,NULL,NULL),(3,'usuario1','Usuario','Ejemplo','1995-05-15','hombre','usuario1@example.com',NULL,'$2y$12$uixc2/T6ISy.DhSS7kQucum7CtSuAGV/jo293cflFxuDemHjHEZEK',500,0,2,3,2,NULL,'Usuario de ejemplo',0,NULL,NULL,NULL,0,'00:00:00',NULL,NULL,NULL),(4,'usuario2','Carlos','Gómez','1992-07-20','hombre','carlos@example.com',NULL,'$2y$12$870.gNcZVnJDtr54PCFLNeRiO4EOPlqmxvStGBJOerrA0JIgKyLyq',450,0,3,3,2,NULL,'Usuario de ejemplo',0,NULL,NULL,NULL,0,'00:00:00',NULL,NULL,NULL),(5,'usuario3','Ana','Martínez','1993-08-15','mujer','ana@example.com',NULL,'$2y$12$pWJBNT145hsqFXzGfXnSou8a7uzoYMEJOtC7kHXxAyXHskOH1SZqa',470,0,4,3,2,NULL,'Usuario de ejemplo',0,NULL,NULL,NULL,0,'00:00:00',NULL,NULL,NULL),(6,'usuario4','Luis','Fernández','1994-09-10','hombre','luis@example.com',NULL,'$2y$12$5trfqQ/6pT6inLUkEoLAMePVYwveXkWaH5/XW6J4RM6XPVxQUiscS',480,0,5,3,2,NULL,'Usuario de ejemplo',0,NULL,NULL,NULL,0,'00:00:00',NULL,NULL,NULL),(7,'usuario5','María','López','1995-10-05','mujer','maria@example.com',NULL,'$2y$12$gRgeeI1UBZnGMJr1QSRi2OYU/gBx/AZ/XVox4GaTDW9LFdY9y6DYW',490,0,6,2,2,NULL,'Usuario de ejemplo',0,NULL,NULL,NULL,0,'00:00:00',NULL,NULL,NULL),(8,'usuario6','Jorge','Pérez','1996-11-25','hombre','jorge@example.com',NULL,'$2y$12$ZnaCk9OUKJPnKXg18j/Ihuyy2vaQ3p1xScP/K4lYi7nu4Wcta4Oe.',500,0,7,3,2,NULL,'Usuario de ejemplo',0,NULL,NULL,NULL,0,'00:00:00',NULL,NULL,NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-05-24 20:34:47
