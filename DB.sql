-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         10.4.32-MariaDB - mariadb.org binary distribution
-- SO del servidor:              Win64
-- HeidiSQL Versión:             12.10.0.7000
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Volcando estructura de base de datos para aima-db
CREATE DATABASE IF NOT EXISTS `aima-db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `aima-db`;

-- Volcando estructura para tabla aima-db.banner_home
CREATE TABLE IF NOT EXISTS `banner_home` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `imagen` varchar(255) NOT NULL,
  `titulo` varchar(100) DEFAULT NULL,
  `subtitulo` varchar(255) DEFAULT NULL,
  `orden` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla aima-db.banner_home: ~3 rows (aproximadamente)
DELETE FROM `banner_home`;
INSERT INTO `banner_home` (`id`, `imagen`, `titulo`, `subtitulo`, `orden`) VALUES
	(1, 'banner1.jpg', 'Bienvenidos a AIMA', 'Aromas que transforman tu hogar', 1),
	(2, 'banner2.jpg', 'Velas de Soja', '100% Naturales y Artesanales', 2),
	(3, 'banner3.jpg', 'Jabones Premium', 'Cuidado y suavidad para tu piel', 3);

-- Volcando estructura para tabla aima-db.carrito
CREATE TABLE IF NOT EXISTS `carrito` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `cantidad` int(11) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla aima-db.carrito: ~1 rows (aproximadamente)
DELETE FROM `carrito`;
INSERT INTO `carrito` (`id`, `user_id`, `product_id`, `cantidad`) VALUES
	(10, 5, 1, 1);

-- Volcando estructura para tabla aima-db.insumos
CREATE TABLE IF NOT EXISTS `insumos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `tipo_unidad` enum('gr','ml','unidades') NOT NULL,
  `precio_lote` decimal(10,2) NOT NULL,
  `cantidad_lote` decimal(10,2) NOT NULL,
  `costo_unitario` decimal(10,4) GENERATED ALWAYS AS (`precio_lote` / `cantidad_lote`) VIRTUAL,
  `proveedor` varchar(100) DEFAULT NULL,
  `fecha_compra` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla aima-db.insumos: ~0 rows (aproximadamente)
DELETE FROM `insumos`;

-- Volcando estructura para tabla aima-db.orders
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `total` decimal(10,2) NOT NULL,
  `metodo_pago` enum('mercadopago','efectivo_punto_retiro','transferencia') DEFAULT NULL,
  `logistica` varchar(50) DEFAULT 'retiro',
  `telefono` varchar(30) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `email_contacto` varchar(100) DEFAULT NULL,
  `estado_pago` varchar(50) DEFAULT 'pendiente',
  `estado` varchar(50) DEFAULT 'procesando',
  `descripcion` text DEFAULT NULL,
  `external_reference` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla aima-db.orders: ~14 rows (aproximadamente)
DELETE FROM `orders`;
INSERT INTO `orders` (`id`, `user_id`, `total`, `metodo_pago`, `logistica`, `telefono`, `email`, `email_contacto`, `estado_pago`, `estado`, `descripcion`, `external_reference`, `created_at`) VALUES
	(1, NULL, 36000.00, NULL, 'retiro', NULL, NULL, NULL, 'pendiente', 'procesando', 'vecina', NULL, '2026-03-17 17:16:34'),
	(2, NULL, 36000.00, NULL, 'retiro', NULL, NULL, NULL, 'pendiente', 'procesando', 'vecina', NULL, '2026-03-17 17:17:26'),
	(3, NULL, 36000.00, NULL, 'retiro', NULL, NULL, NULL, 'pagado', 'finalizado', 'vecina', NULL, '2026-03-17 21:29:29'),
	(4, NULL, 72000.00, NULL, 'retiro', NULL, NULL, NULL, 'pagado', 'finalizado', 'for ig', NULL, '2026-03-17 21:29:52'),
	(5, NULL, 180000.00, NULL, 'retiro', NULL, NULL, NULL, 'pendiente', 'finalizado', 'afds', NULL, '2026-03-17 21:33:15'),
	(6, NULL, 64000.00, '', 'retiro', NULL, NULL, NULL, 'pagado', 'procesando', 'veee', NULL, '2026-03-18 17:22:49'),
	(7, NULL, 160000.00, '', 'retiro', NULL, NULL, NULL, 'pendiente', 'cancelado', 'aaaa', NULL, '2026-03-18 17:33:53'),
	(8, NULL, 96000.00, '', 'Envío a domicilio', '1168508686', 'cury.ailena@gmail.com', NULL, 'pagado', 'finalizado', NULL, NULL, '2026-03-18 13:37:42'),
	(9, NULL, 80000.00, '', 'Retiro en Local', '1168508686', 'ana@gmail.com', NULL, 'pagado', 'finalizado', NULL, NULL, '2026-03-18 13:57:37'),
	(10, 4, 48000.00, '', 'Envío a domicilio', '1168508686', 'cury.ailena@gmail.com', NULL, 'pendiente', 'finalizado', NULL, NULL, '2026-03-18 14:45:33'),
	(11, 4, 48000.00, '', 'Retiro en Local', '1168508686', 'lu@lu.go.com', NULL, 'pendiente', 'procesando', NULL, NULL, '2026-03-18 14:55:25'),
	(12, 5, 64000.00, '', 'Retiro en Local', '1168508686', '', NULL, 'pendiente', 'cancelado', NULL, NULL, '2026-03-20 15:25:11'),
	(13, 6, 38000.00, '', 'Retiro por local', '1168508686', 'cury.ailena@gmail.com', NULL, 'pendiente', 'procesando', NULL, NULL, '2026-04-01 21:56:11'),
	(14, 6, 30000.00, 'transferencia', 'Envío', '1130920595', 'cury.ailena@gmail.com', NULL, 'pendiente', 'procesando', NULL, NULL, '2026-04-08 22:42:24');

-- Volcando estructura para tabla aima-db.order_items
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla aima-db.order_items: ~16 rows (aproximadamente)
DELETE FROM `order_items`;
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `cantidad`, `precio_unitario`) VALUES
	(1, 3, NULL, 2, 18000.00),
	(2, 4, NULL, 4, 18000.00),
	(3, 5, NULL, 10, 18000.00),
	(4, 6, NULL, 4, 16000.00),
	(5, 7, NULL, 10, 16000.00),
	(6, 8, NULL, 3, 16000.00),
	(7, 8, NULL, 3, 16000.00),
	(8, 9, NULL, 4, 16000.00),
	(9, 9, NULL, 1, 16000.00),
	(10, 10, NULL, 2, 16000.00),
	(11, 10, NULL, 1, 16000.00),
	(12, 11, NULL, 3, 16000.00),
	(13, 12, NULL, 4, 16000.00),
	(14, 13, NULL, 2, 16000.00),
	(15, 13, NULL, 4, 1500.00),
	(16, 14, 23, 6, 5000.00);

-- Volcando estructura para tabla aima-db.pedidos
CREATE TABLE IF NOT EXISTS `pedidos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `logistica` enum('Retiro por local','Envio') NOT NULL,
  `metodo_pago` varchar(50) DEFAULT NULL,
  `estado` enum('procesando','pagado','finalizado','cancelado') DEFAULT 'procesando',
  `fecha_pedido` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla aima-db.pedidos: ~0 rows (aproximadamente)
DELETE FROM `pedidos`;

-- Volcando estructura para tabla aima-db.products
CREATE TABLE IF NOT EXISTS `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(200) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `stock` int(11) DEFAULT 0,
  `tipo` varchar(50) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla aima-db.products: ~6 rows (aproximadamente)
DELETE FROM `products`;
INSERT INTO `products` (`id`, `nombre`, `descripcion`, `precio`, `imagen`, `stock`, `tipo`, `status`, `created_at`, `updated_at`) VALUES
	(19, 'Pitanga Gourmet | Energía y Frescura', 'Pitanga Gourmet\r\nExcelente para: Renovar energías y aportar frescura vibrante al ambiente.\r\n\r\nAcordes: Frutal, cítrico y verde.\r\nNotas: Salida de hojas de pitanga; Corazón de pulpa frutal; Fondo de sándalo suave.\r\nTemporada: Verano / Primavera – De día.', 18000.00, '1775668113_vela_pitanga.png', 5, NULL, 1, '2026-04-08 17:08:33', '2026-04-08 17:27:43'),
	(20, 'Dulces Frutos del Bosque | Intensidad y Dulzura', 'Excelente para: Crear un clima cálido, dulce y acogedor en el hogar.\r\n\r\nAcordes: Dulce, silvestre y frutal.\r\nNotas: Salida de frambuesas; Corazón de arándanos y moras; Fondo de vainilla.\r\nTemporada: Otoño / Invierno – De noche.', 18000.00, '1775668499_vela_frutos_rojos.png', 5, NULL, 1, '2026-04-08 17:14:59', '2026-04-08 17:28:02'),
	(21, 'Dulce Caramelo Gourmet', 'Excelente para: Endulzar el ambiente y generar una sensación de confort absoluto.\r\n\r\nAcordes principales: Dulce, láctico y tostado.\r\nNotas de Salida: Azúcar quemada y un toque de manteca.\r\nNotas de Corazón: Dulce de leche artesanal y esencia de praliné.\r\nNotas de Fondo: Vainilla cremosa y un fondo de caramelo intenso.\r\nTemporada: Otoño / Invierno — De tarde o noche.', 18000.00, '1775669499_vela_dulce_caramelo.png', 6, NULL, 1, '2026-04-08 17:31:39', '2026-04-08 17:31:39'),
	(22, 'Cookie Gourmet', 'Excelente para: Recrear la calidez de una cocina horneando y combatir el estrés con un aroma "refugio".\r\n\r\nAcordes principales: Dulce, horneado y chocolate.\r\nNotas de Salida: Masa de galletita mantecosa y una pizca de sal.\r\nNotas de Corazón: Chips de chocolate negro y esencia de avellanas.\r\nNotas de Fondo: Vainilla de madagascar y azúcar rubia tostada.\r\nTemporada: Otoño / Invierno — Tarde de lluvia o noche.', 18000.00, '1775669534_vela_cookie.png', 6, NULL, 1, '2026-04-08 17:32:14', '2026-04-08 17:32:14'),
	(23, 'Barra | Campo de flores', 'Excelente para: Aportar elegancia y frescura natural a living o dormitorios.\r\n\r\nAcordes principales: Florales blancos, frutales y especiados.\r\n\r\nNotas de Salida: Pera jugosa y un toque de rocío matinal.\r\n\r\nNotas de Corazón: Jazmín chino auténtico y pétalos de flores blancas.\r\n\r\nNotas de Fondo: Canela cálida y un sutil fondo amaderado.\r\n\r\nTemporada: Primavera / Verano — De día.', 5000.00, '1775669658_barras_aromaticas.png', 19, NULL, 1, '2026-04-08 17:34:18', '2026-04-08 17:42:24'),
	(24, 'Bombones aromaticos | Coco Vainilla', 'Excelente para: Crear una atmósfera de relax absoluto, calma y calidez tropical.\r\n\r\nAcordes principales: Dulce, cremoso y exótico.\r\nNotas de Salida: Coco rallado y leche de coco.\r\nNotas de Corazón: Vainilla en vaina y toques de flores blancas.\r\nNotas de Fondo: Caramelo suave y almizcle dulce.\r\nTemporada: Todo el año — Noche o tarde de descanso.', 4000.00, '1775670043_waxmelt1.png', 25, NULL, 1, '2026-04-08 17:40:43', '2026-04-08 17:40:43');

-- Volcando estructura para tabla aima-db.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `celular` varchar(20) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `apellido` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('cliente','admin') DEFAULT 'cliente',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla aima-db.users: ~5 rows (aproximadamente)
DELETE FROM `users`;
INSERT INTO `users` (`id`, `nombre`, `celular`, `direccion`, `apellido`, `email`, `password`, `rol`, `created_at`) VALUES
	(3, 'Yannel', NULL, NULL, 'UTN', 'admin@admin.com', '0192023a7bbd73250516f069df18b500', 'admin', '2026-03-18 17:39:09'),
	(4, 'Yannel', NULL, NULL, 'UTN', 'cliente', 'cliente123', 'cliente', '2026-03-18 17:44:54'),
	(5, 'ailen', '1168508686', '', '', 'antakle@gmail.com', '$2y$10$R8TsDEAoOuNHo1wTv26fqurZXphYnXcdg507rNrinSWTu0/R7j1N6', 'cliente', '2026-03-20 18:01:45'),
	(6, 'ailen', '1168508686', '', '', 'cury.ailena@gmail.com', '$2y$10$YJdowKenH2sKpCd/c0HjquU/n1fo7R1eHAco2.Fkh7u/6nfP.PyiS', 'admin', '2026-03-20 19:41:34'),
	(7, 'a', '11111111', '', '', 'ailu@ailu', '$2y$10$14kAbe8v3gG/ZQSSojwOkul.TAZ/bQvaBn0putGnA9l4pMQJBfwIy', 'cliente', '2026-03-24 04:37:57');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
