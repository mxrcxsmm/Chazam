-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 23-05-2025 a las 16:09:33
-- Versión del servidor: 9.1.0
-- Versión de PHP: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `chazam`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache`
--

DROP TABLE IF EXISTS `cache`;
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `chats`
--

DROP TABLE IF EXISTS `chats`;
CREATE TABLE IF NOT EXISTS `chats` (
  `id_chat` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `creator` bigint UNSIGNED DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL,
  `img` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipocomunidad` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `codigo` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `id_reto` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_chat`),
  UNIQUE KEY `chats_codigo_unique` (`codigo`),
  KEY `chats_id_reto_foreign` (`id_reto`),
  KEY `chats_creator_foreign` (`creator`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `chats`
--

INSERT INTO `chats` (`id_chat`, `creator`, `fecha_creacion`, `img`, `nombre`, `tipocomunidad`, `codigo`, `descripcion`, `id_reto`, `created_at`, `updated_at`) VALUES
(1, NULL, '2025-05-23 18:08:48', NULL, 'Chat de Amistad', NULL, NULL, 'Chat entre Carlos y Ana', NULL, '2025-05-23 16:08:48', '2025-05-23 16:08:48'),
(2, 1, '2025-05-23 18:08:48', 'php.jpg', 'Programadores PHP', 'publica', NULL, 'Comunidad para discutir sobre desarrollo en PHP, Laravel y frameworks relacionados.', NULL, '2025-05-23 16:08:48', '2025-05-23 16:08:48'),
(3, 1, '2025-05-23 18:08:48', 'gamers.jpg', 'Gamers Chazam', 'publica', NULL, 'Comunidad para gamers que quieren compartir sus experiencias y organizar partidas.', NULL, '2025-05-23 16:08:48', '2025-05-23 16:08:48'),
(4, 1, '2025-05-23 18:08:48', 'music_art.jpg', 'Música y Arte', 'publica', NULL, 'Espacio para compartir y discutir sobre música, arte y cultura en general.', NULL, '2025-05-23 16:08:48', '2025-05-23 16:08:48'),
(5, 1, '2025-05-23 18:08:48', 'secret.jpg', 'Comunidad Cerrada', 'privada', 'CHAZAM2024', 'Comunidad privada para miembros selectos.', NULL, '2025-05-23 16:08:48', '2025-05-23 16:08:48');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `chat_usuario`
--

DROP TABLE IF EXISTS `chat_usuario`;
CREATE TABLE IF NOT EXISTS `chat_usuario` (
  `id_chat_usuario` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_chat` bigint UNSIGNED NOT NULL,
  `id_usuario` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_chat_usuario`),
  KEY `chat_usuario_id_chat_foreign` (`id_chat`),
  KEY `chat_usuario_id_usuario_foreign` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `chat_usuario`
--

INSERT INTO `chat_usuario` (`id_chat_usuario`, `id_chat`, `id_usuario`, `created_at`, `updated_at`) VALUES
(1, 1, 4, '2025-05-23 16:08:48', '2025-05-23 16:08:48'),
(2, 1, 5, '2025-05-23 16:08:48', '2025-05-23 16:08:48');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estados`
--

DROP TABLE IF EXISTS `estados`;
CREATE TABLE IF NOT EXISTS `estados` (
  `id_estado` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nom_estado` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_estado`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `estados`
--

INSERT INTO `estados` (`id_estado`, `nom_estado`, `created_at`, `updated_at`) VALUES
(1, 'Activo', NULL, NULL),
(2, 'Inactivo', NULL, NULL),
(3, 'Ban', NULL, NULL),
(4, 'PermaBan', NULL, NULL),
(5, 'Disponible', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historias`
--

DROP TABLE IF EXISTS `historias`;
CREATE TABLE IF NOT EXISTS `historias` (
  `id_historia` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_usuario` bigint UNSIGNED NOT NULL,
  `fecha_inicio` datetime NOT NULL,
  `fecha_fin` datetime NOT NULL,
  `img` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_historia`),
  KEY `historias_id_usuario_foreign` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jobs`
--

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE IF NOT EXISTS `job_batches` (
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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensajes`
--

DROP TABLE IF EXISTS `mensajes`;
CREATE TABLE IF NOT EXISTS `mensajes` (
  `id_mensaje` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_chat_usuario` bigint UNSIGNED NOT NULL,
  `contenido` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_envio` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_mensaje`),
  KEY `mensajes_id_chat_usuario_foreign` (`id_chat_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `mensajes`
--

INSERT INTO `mensajes` (`id_mensaje`, `id_chat_usuario`, `contenido`, `fecha_envio`, `created_at`, `updated_at`) VALUES
(1, 1, '¡Hola Ana! ¿Cómo estás?', '2025-05-23 18:08:48', '2025-05-23 16:08:48', '2025-05-23 16:08:48'),
(2, 2, '¡Hola Carlos! Muy bien, ¿y tú?', '2025-05-23 18:08:48', '2025-05-23 16:08:48', '2025-05-23 16:08:48');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000001_create_cache_table', 1),
(2, '0001_01_01_000002_create_jobs_table', 1),
(3, '2025_04_08_133542_roles', 1),
(4, '2025_04_08_133548_estados', 1),
(5, '2025_04_08_133700_retos', 1),
(6, '2025_04_08_133720_chats', 1),
(7, '2025_04_08_135706_nacionalidad', 1),
(8, '2025_04_08_140830_tipo_producto', 1),
(9, '2025_04_08_150000_productos', 1),
(10, '2025_04_08_150001_create_users_table', 1),
(11, '2025_04_08_150003_add_creator_to_chats', 1),
(12, '2025_04_08_160003_historias', 1),
(13, '2025_04_08_160004_solicitudes', 1),
(14, '2025_04_08_160005_reportes', 1),
(15, '2025_04_08_160006_pagos', 1),
(16, '2025_04_08_160007_chat_usuario', 1),
(17, '2025_04_08_160008_mensajes', 1),
(18, '2025_04_08_160009_sugerencias', 1),
(19, '2025_05_21_193607_create_personalizacion_table', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nacionalidad`
--

DROP TABLE IF EXISTS `nacionalidad`;
CREATE TABLE IF NOT EXISTS `nacionalidad` (
  `id_nacionalidad` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bandera` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_nacionalidad`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `nacionalidad`
--

INSERT INTO `nacionalidad` (`id_nacionalidad`, `nombre`, `bandera`, `created_at`, `updated_at`) VALUES
(1, 'España', 'es.png', NULL, NULL),
(2, 'Mexico', 'mx.png', NULL, NULL),
(3, 'Colombia', 'co.png', NULL, NULL),
(4, 'Argentina', 'ar.png', NULL, NULL),
(5, 'Chile', 'cl.png', NULL, NULL),
(6, 'Perú', 'pe.png', NULL, NULL),
(7, 'Venezuela', 've.png', NULL, NULL),
(8, 'Ecuador', 'ec.png', NULL, NULL),
(9, 'Brasil', 'br.png', NULL, NULL),
(10, 'Chile', 'cl.png', NULL, NULL),
(11, 'China', 'cn.png', NULL, NULL),
(12, 'Japón', 'jp.png', NULL, NULL),
(13, 'Corea del Sur', 'kr.png', NULL, NULL),
(14, 'India', 'in.png', NULL, NULL),
(15, 'Australia', 'au.png', NULL, NULL),
(16, 'Nueva Zelanda', 'nz.png', NULL, NULL),
(17, 'Estados Unidos', 'us.png', NULL, NULL),
(18, 'Canada', 'ca.png', NULL, NULL),
(19, 'Francia', 'fr.png', NULL, NULL),
(20, 'Alemania', 'de.png', NULL, NULL),
(21, 'Italia', 'it.png', NULL, NULL),
(22, 'Portugal', 'pt.png', NULL, NULL),
(23, 'Grecia', 'gr.png', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

DROP TABLE IF EXISTS `pagos`;
CREATE TABLE IF NOT EXISTS `pagos` (
  `id_pago` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_comprador` bigint UNSIGNED DEFAULT NULL,
  `fecha_pago` datetime NOT NULL,
  `id_producto` bigint UNSIGNED DEFAULT NULL,
  `cantidad` decimal(8,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_pago`),
  KEY `pagos_id_comprador_foreign` (`id_comprador`),
  KEY `pagos_id_producto_foreign` (`id_producto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personalizacion`
--

DROP TABLE IF EXISTS `personalizacion`;
CREATE TABLE IF NOT EXISTS `personalizacion` (
  `id_personalizacion` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_usuario` bigint UNSIGNED NOT NULL,
  `marco` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rotacion` tinyint(1) NOT NULL DEFAULT '0',
  `brillo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sidebar` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_personalizacion`),
  KEY `personalizacion_id_usuario_foreign` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

DROP TABLE IF EXISTS `productos`;
CREATE TABLE IF NOT EXISTS `productos` (
  `id_producto` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `titulo` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `tipo_valor` enum('euros','puntos') COLLATE utf8mb4_unicode_ci NOT NULL,
  `puntos` int DEFAULT NULL,
  `id_tipo_producto` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_producto`),
  KEY `productos_id_tipo_producto_foreign` (`id_tipo_producto`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id_producto`, `titulo`, `descripcion`, `precio`, `tipo_valor`, `puntos`, `id_tipo_producto`, `created_at`, `updated_at`) VALUES
(1, 'Suscripción Premium', 'Acceso completo a todas las funciones premium durante un mes.', 9.99, 'euros', NULL, 1, NULL, NULL),
(2, 'Mejorar skips', 'Reduce el tiempo de espera para realizar skips.', 5.99, 'euros', NULL, 2, NULL, NULL),
(3, 'Combo 1', 'Incluye 1 mes de suscripción premium y 750 puntos.', 14.99, 'euros', 750, 2, NULL, NULL),
(4, 'Combo 2', 'Incluye 1 mes de suscripción premium y 1250 puntos.', 19.99, 'euros', 1250, 2, NULL, NULL),
(5, 'Combo 3', 'Incluye 1 mes de suscripción premium y 2500 puntos.', 29.99, 'euros', 2500, 2, NULL, NULL),
(6, 'Pack de 1000 puntos', 'Compra de 1000 puntos en Chazam.', 1.99, 'euros', 1000, 3, NULL, NULL),
(7, 'Pack de 2000 puntos', 'Compra de 2000 puntos en Chazam.', 4.99, 'euros', 2000, 3, NULL, NULL),
(8, 'Pack de 3500 puntos', 'Compra de 3500 puntos en Chazam.', 7.99, 'euros', 3500, 3, NULL, NULL),
(9, 'Pack de 5000 puntos', 'Compra de 5000 puntos en Chazam.', 9.99, 'euros', 5000, 3, NULL, NULL),
(10, 'Pack de 10000 puntos', 'Compra de 10000 puntos en Chazam.', 19.99, 'euros', 10000, 3, NULL, NULL),
(11, 'Suscripción de miembro con puntos', 'Suscripción de miembro con 15000 puntos.', 15000.00, 'puntos', 15000, 1, NULL, NULL),
(12, 'Mejorar skips con puntos', 'Mejorar skips con 60000 puntos.', 60000.00, 'puntos', 60000, 2, NULL, NULL),
(13, 'Comunidad', 'Creación de una comunidad.', 75000.00, 'puntos', NULL, 5, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reportes`
--

DROP TABLE IF EXISTS `reportes`;
CREATE TABLE IF NOT EXISTS `reportes` (
  `id_reporte` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `titulo` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_reportador` bigint UNSIGNED NOT NULL,
  `id_reportado` bigint UNSIGNED NOT NULL,
  `leido` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_reporte`),
  KEY `reportes_id_reportador_foreign` (`id_reportador`),
  KEY `reportes_id_reportado_foreign` (`id_reportado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `retos`
--

DROP TABLE IF EXISTS `retos`;
CREATE TABLE IF NOT EXISTS `retos` (
  `id_reto` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nom_reto` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `desc_reto` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_reto`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `retos`
--

INSERT INTO `retos` (`id_reto`, `nom_reto`, `desc_reto`, `created_at`, `updated_at`) VALUES
(1, 'Hoy toca hablar con emojis', 'Usa SOLO emojis para comunicarte. ¿Podrán entenderte? ¡Sé creativo con tus combinaciones!', '2025-05-23 16:08:48', '2025-05-23 16:08:48'),
(2, 'Mensaje encriptado', '¡Algunos carácteres están cifrados! ¿Podrás conseguir comunicarte con tu pareja?', '2025-05-23 16:08:48', '2025-05-23 16:08:48'),
(3, 'Desorden absoluto', 'Vuestras frases se enviarán desordenadas, intentad descifrar el mensaje original', '2025-05-23 16:08:48', '2025-05-23 16:08:48'),
(4, 'Boca abajo', 'Vuestro texto estará boca abajo ¡no forzéis mucho el cuello!', '2025-05-23 16:08:48', '2025-05-23 16:08:48');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id_rol` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nom_rol` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_rol`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id_rol`, `nom_rol`, `created_at`, `updated_at`) VALUES
(1, 'Administrador', NULL, NULL),
(2, 'Usuario', NULL, NULL),
(3, 'Premium', NULL, NULL),
(4, 'Miembro', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sessions`
--

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudes`
--

DROP TABLE IF EXISTS `solicitudes`;
CREATE TABLE IF NOT EXISTS `solicitudes` (
  `id_solicitud` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `estado` enum('pendiente','aceptada','rechazada','blockeada') COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_emisor` bigint UNSIGNED NOT NULL,
  `id_receptor` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_solicitud`),
  KEY `solicitudes_id_emisor_foreign` (`id_emisor`),
  KEY `solicitudes_id_receptor_foreign` (`id_receptor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sugerencia`
--

DROP TABLE IF EXISTS `sugerencia`;
CREATE TABLE IF NOT EXISTS `sugerencia` (
  `id_sugerencia` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `titulo` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_sugerente` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_sugerencia`),
  KEY `sugerencia_id_sugerente_foreign` (`id_sugerente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_producto`
--

DROP TABLE IF EXISTS `tipo_producto`;
CREATE TABLE IF NOT EXISTS `tipo_producto` (
  `id_tipo_producto` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tipo_producto` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_tipo_producto`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tipo_producto`
--

INSERT INTO `tipo_producto` (`id_tipo_producto`, `tipo_producto`, `created_at`, `updated_at`) VALUES
(1, 'Suscripciones', NULL, NULL),
(2, 'Compras únicas', NULL, NULL),
(3, 'Packs de puntos', NULL, NULL),
(4, 'Donaciones', NULL, NULL),
(5, 'Comunidad', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id_usuario` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
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
  `id_nacionalidad` bigint UNSIGNED NOT NULL,
  `id_rol` bigint UNSIGNED NOT NULL,
  `id_estado` bigint UNSIGNED NOT NULL,
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
  KEY `users_id_nacionalidad_foreign` (`id_nacionalidad`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id_usuario`, `username`, `nombre`, `apellido`, `fecha_nacimiento`, `genero`, `email`, `email_verified_at`, `password`, `puntos`, `racha`, `id_nacionalidad`, `id_rol`, `id_estado`, `img`, `descripcion`, `strikes`, `inicio_ban`, `fin_ban`, `ultimo_login`, `puntos_diarios`, `skip_time`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'Administrador', 'Sistema', '1990-01-01', 'hombre', 'admin@example.com', NULL, '$2y$12$uc7qMb5nt4p9mhMD7efox.nhwERc43tBvCYkqM70YK49ut5hNSsK2', 1000, 0, 1, 1, 2, NULL, 'Administrador del sistema', 0, NULL, NULL, NULL, 0, '00:00:00', NULL, NULL, NULL),
(2, 'moderador', 'Moderador', 'Sistema', '1990-01-01', 'hombre', 'moderador@example.com', NULL, '$2y$12$xtqunlE6HKSA0FpfkHGcPe9gy2XUx6zX1ucVHMMsZEkY1iAsDOVze', 800, 0, 1, 2, 2, NULL, 'Moderador del sistema', 0, NULL, NULL, NULL, 0, '00:00:00', NULL, NULL, NULL),
(3, 'usuario1', 'Usuario', 'Ejemplo', '1995-05-15', 'hombre', 'usuario1@example.com', NULL, '$2y$12$VTWl5WsvzjFl/fV6W1stp.P/3U7Er29QOFQWdKA.1qFQt0CAhdIoy', 500, 0, 2, 3, 2, NULL, 'Usuario de ejemplo', 0, NULL, NULL, NULL, 0, '00:00:00', NULL, NULL, NULL),
(4, 'usuario2', 'Carlos', 'Cliente', '1992-07-20', 'hombre', 'carlos@example.com', NULL, '$2y$12$NUsNHPqDaSKB/XQ4.EOvgOz.U7ir3EL3sglXVOcPFW34t/tkZ70ie', 450, 0, 3, 3, 2, NULL, 'Usuario de ejemplo', 0, NULL, NULL, NULL, 0, '00:00:00', NULL, NULL, NULL),
(5, 'usuario3', 'Ana', 'Cliente', '1993-08-15', 'mujer', 'ana@example.com', NULL, '$2y$12$tk3QgwxWTtzZcsS8.i5Dqe2sZhXR6UWuuIwjH7hinsFTjb0x6kJtS', 470, 0, 4, 3, 2, NULL, 'Usuario de ejemplo', 0, NULL, NULL, NULL, 0, '00:00:00', NULL, NULL, NULL),
(6, 'usuario4', 'Luis', 'Fernández', '1994-09-10', 'hombre', 'luis@example.com', NULL, '$2y$12$DXqM8R1R2YzOBUQ1y1/D2u9adhxcTLwZ2O18qq5k6mpuJdV9x.HQm', 480, 0, 5, 3, 2, NULL, 'Usuario de ejemplo', 0, NULL, NULL, NULL, 0, '00:00:00', NULL, NULL, NULL),
(7, 'usuario5', 'María', 'López', '1995-10-05', 'mujer', 'maria@example.com', NULL, '$2y$12$35nP1WvDldcF3n.xfo2qcuTNLmgZkuvLgRXqA1qzDillpRGbMCYDq', 490, 0, 6, 2, 2, NULL, 'Usuario de ejemplo', 0, NULL, NULL, NULL, 0, '00:00:00', NULL, NULL, NULL),
(8, 'usuario6', 'Jorge', 'Pérez', '1996-11-25', 'hombre', 'jorge@example.com', NULL, '$2y$12$gNX2DlCSjnI/GZBu1/7ZaOBkjVAKKq3cdgTL.MuYmlEs9mFcgcCPC', 500, 0, 7, 3, 2, NULL, 'Usuario de ejemplo', 0, NULL, NULL, NULL, 0, '00:00:00', NULL, NULL, NULL);

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `chats`
--
ALTER TABLE `chats`
  ADD CONSTRAINT `chats_creator_foreign` FOREIGN KEY (`creator`) REFERENCES `users` (`id_usuario`),
  ADD CONSTRAINT `chats_id_reto_foreign` FOREIGN KEY (`id_reto`) REFERENCES `retos` (`id_reto`);

--
-- Filtros para la tabla `chat_usuario`
--
ALTER TABLE `chat_usuario`
  ADD CONSTRAINT `chat_usuario_id_chat_foreign` FOREIGN KEY (`id_chat`) REFERENCES `chats` (`id_chat`),
  ADD CONSTRAINT `chat_usuario_id_usuario_foreign` FOREIGN KEY (`id_usuario`) REFERENCES `users` (`id_usuario`);

--
-- Filtros para la tabla `historias`
--
ALTER TABLE `historias`
  ADD CONSTRAINT `historias_id_usuario_foreign` FOREIGN KEY (`id_usuario`) REFERENCES `users` (`id_usuario`);

--
-- Filtros para la tabla `mensajes`
--
ALTER TABLE `mensajes`
  ADD CONSTRAINT `mensajes_id_chat_usuario_foreign` FOREIGN KEY (`id_chat_usuario`) REFERENCES `chat_usuario` (`id_chat_usuario`);

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `pagos_id_comprador_foreign` FOREIGN KEY (`id_comprador`) REFERENCES `users` (`id_usuario`) ON DELETE SET NULL,
  ADD CONSTRAINT `pagos_id_producto_foreign` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE SET NULL;

--
-- Filtros para la tabla `personalizacion`
--
ALTER TABLE `personalizacion`
  ADD CONSTRAINT `personalizacion_id_usuario_foreign` FOREIGN KEY (`id_usuario`) REFERENCES `users` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `productos_id_tipo_producto_foreign` FOREIGN KEY (`id_tipo_producto`) REFERENCES `tipo_producto` (`id_tipo_producto`);

--
-- Filtros para la tabla `reportes`
--
ALTER TABLE `reportes`
  ADD CONSTRAINT `reportes_id_reportado_foreign` FOREIGN KEY (`id_reportado`) REFERENCES `users` (`id_usuario`),
  ADD CONSTRAINT `reportes_id_reportador_foreign` FOREIGN KEY (`id_reportador`) REFERENCES `users` (`id_usuario`);

--
-- Filtros para la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  ADD CONSTRAINT `solicitudes_id_emisor_foreign` FOREIGN KEY (`id_emisor`) REFERENCES `users` (`id_usuario`),
  ADD CONSTRAINT `solicitudes_id_receptor_foreign` FOREIGN KEY (`id_receptor`) REFERENCES `users` (`id_usuario`);

--
-- Filtros para la tabla `sugerencia`
--
ALTER TABLE `sugerencia`
  ADD CONSTRAINT `sugerencia_id_sugerente_foreign` FOREIGN KEY (`id_sugerente`) REFERENCES `users` (`id_usuario`);

--
-- Filtros para la tabla `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_id_estado_foreign` FOREIGN KEY (`id_estado`) REFERENCES `estados` (`id_estado`),
  ADD CONSTRAINT `users_id_nacionalidad_foreign` FOREIGN KEY (`id_nacionalidad`) REFERENCES `nacionalidad` (`id_nacionalidad`),
  ADD CONSTRAINT `users_id_rol_foreign` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
