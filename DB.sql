-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         10.4.32-MariaDB - mariadb.org binary distribution
-- SO del servidor:              Win64
-- HeidiSQL Versión:             12.16.0.7229
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
  `imagen_url` varchar(255) NOT NULL DEFAULT 'public/deliciaFR.png',
  `titulo` varchar(255) NOT NULL DEFAULT 'AIMA AROMAS',
  `subtitulo` text NOT NULL,
  `orden` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla aima-db.banner_home: ~3 rows (aproximadamente)
DELETE FROM `banner_home`;
INSERT INTO `banner_home` (`id`, `imagen_url`, `titulo`, `subtitulo`, `orden`) VALUES
	(1, 'public/deliciaFR.png', 'AIMA AROMAS', 'Rituales para el bienestar y la pausa.', 1),
	(2, 'public/flores.png', 'JABONES ARTESANALES', 'Aromas suaves y materiales nobles, hechos a mano.', 2),
	(3, 'public/chocolateCream.png', 'Cookie', 'Conectá con tu momento de calma.', 3);

-- Volcando estructura para tabla aima-db.carrito
CREATE TABLE IF NOT EXISTS `carrito` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `cantidad` int(11) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla aima-db.carrito: ~1 rows (aproximadamente)
DELETE FROM `carrito`;
INSERT INTO `carrito` (`id`, `user_id`, `product_id`, `cantidad`) VALUES
	(10, 5, 1, 1);

-- Volcando estructura para tabla aima-db.comentarios
CREATE TABLE IF NOT EXISTS `comentarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) DEFAULT NULL,
  `mensaje` text DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla aima-db.comentarios: ~0 rows (aproximadamente)
DELETE FROM `comentarios`;
INSERT INTO `comentarios` (`id`, `nombre`, `mensaje`, `fecha`) VALUES
	(1, 'fefe', 'sdgsf', '2026-03-18 03:00:00');

-- Volcando estructura para tabla aima-db.consultas
CREATE TABLE IF NOT EXISTS `consultas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `mensaje` text DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla aima-db.consultas: ~0 rows (aproximadamente)
DELETE FROM `consultas`;
INSERT INTO `consultas` (`id`, `nombre`, `email`, `mensaje`, `fecha`) VALUES
	(1, 'ailen cury', 'cury.ailena@gmail.com', 'dfbvxcyjgtuikmxfgvb aserfgb', '2026-03-18 03:00:00');

-- Volcando estructura para tabla aima-db.insumos
CREATE TABLE IF NOT EXISTS `insumos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(200) NOT NULL,
  `tipo` enum('unidad','volumen','peso') NOT NULL DEFAULT 'unidad',
  `precio_total_compra` decimal(10,2) NOT NULL DEFAULT 0.00,
  `cantidad_lote` decimal(10,2) NOT NULL DEFAULT 1.00 COMMENT 'Cuántas unidades hay en el pack',
  `contenido_por_unidad` decimal(10,4) NOT NULL DEFAULT 1.0000 COMMENT 'Contenido de cada unidad (ml, g o 1 para unidad)',
  `proveedor` varchar(200) DEFAULT NULL,
  `fecha_compra` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla aima-db.insumos: ~22 rows (aproximadamente)
DELETE FROM `insumos`;
INSERT INTO `insumos` (`id`, `nombre`, `tipo`, `precio_total_compra`, `cantidad_lote`, `contenido_por_unidad`, `proveedor`, `fecha_compra`, `created_at`) VALUES
	(1, 'Cera de soja APF', 'peso', 8684.16, 3.00, 1.0000, 'Aupar', '2026-03-20', '2026-03-24 03:02:46'),
	(2, 'Vaso Tennessee 320ml', 'volumen', 32999.00, 24.00, 1.0000, 'IMPO BAZAR DECO', '2026-03-20', '2026-03-24 03:02:46'),
	(3, 'Batidor Eléctrico', 'unidad', 7990.00, 1.00, 1.0000, 'Jhonys wear', '2026-01-07', '2026-03-24 03:02:46'),
	(4, 'Centrador De Pabilo Largo X 3', 'unidad', 3499.00, 1.00, 1.0000, 'Issaka Home', '2026-01-07', '2026-03-24 03:02:46'),
	(5, 'Ojalillos Para Velas Cera De Soja Parafina X 100', 'unidad', 3999.00, 1.00, 1.0000, 'Issaka Home', '2026-01-07', '2026-03-24 03:02:46'),
	(6, 'Pegamento Ojalillos X 100', 'unidad', 3477.06, 1.00, 1.0000, 'Issaka Home', '2026-01-07', '2026-03-24 03:02:46'),
	(7, 'Vaso Whisky Bar Nadir Vidrio 265ml', 'unidad', 19958.00, 12.00, 1.0000, 'IMPO BAZAR DECO', '2025-12-30', '2026-03-24 03:02:46'),
	(8, 'Cera De Soja Comercial x 1kg', 'unidad', 5709.26, 7.00, 1.0000, 'Aupar', '2025-12-15', '2026-03-24 03:02:46'),
	(9, 'Papel De Oro Plata Cobre Ideal Para Velas De Soja', 'unidad', 6109.06, 1.00, 1.0000, 'Issaka Home', '2025-11-20', '2026-03-24 03:02:46'),
	(10, 'Mica perlada 20gs', 'unidad', 5999.00, 1.00, 1.0000, 'Issaka Home', '2025-11-20', '2026-03-24 03:02:46'),
	(11, 'Molde Silicona Flores Y Moños', 'unidad', 6109.06, 1.00, 1.0000, 'Issaka Home', '2025-11-20', '2026-03-24 03:02:46'),
	(12, 'Colorante ultra concentrado vela x 10gs', 'peso', 3477.06, 2.00, 10.0000, 'Issaka Home', '2025-11-20', '2026-03-24 03:02:46'),
	(13, 'Molde Silicona 15 Corazones', 'unidad', 6109.06, 1.00, 1.0000, 'Issaka Home', '2025-11-20', '2026-03-24 03:02:46'),
	(14, 'Molde Silicona 14 Huellas', 'unidad', 6109.06, 1.00, 1.0000, 'Issaka Home', '2025-11-20', '2026-03-24 03:02:46'),
	(15, 'Colorantes Liquido Concentrado Para Velas', 'volumen', 31019.00, 24.00, 10.0000, 'Bazar de Jua', '2025-11-05', '2026-03-24 03:06:38'),
	(16, 'Termómetro Digital', 'unidad', 4850.00, 1.00, 1.0000, 'Bazar de Jua', '2025-11-05', '2026-03-24 03:07:39'),
	(17, 'Molde de silicona Patitas', 'unidad', 4999.00, 1.00, 1.0000, 'Durama Shop', '2025-11-05', '2026-03-24 03:08:49'),
	(18, 'Molde Silicona Tableta Chocolate', 'unidad', 7770.00, 1.00, 1.0000, 'Daruma Shop', '2025-11-05', '2026-03-24 03:10:18'),
	(19, 'Molde Silicona Leon 3D', 'unidad', 7700.00, 1.00, 11.0000, 'Daruma Shop', '2025-11-05', '2026-03-24 03:11:09'),
	(20, 'Pipeta Plástica/gotero', 'volumen', 2792.00, 30.00, 3.0000, 'Aupar', '2025-11-26', '2026-03-24 03:12:12'),
	(21, 'Cera de soja APF', 'peso', 8684.00, 1.00, 1000.0000, 'Aupar', '2025-11-05', '2026-03-24 03:12:49'),
	(22, 'Fragancias', 'volumen', 22514.96, 5.00, 15.0000, 'Aupar', '2025-11-06', '2026-03-24 03:13:57');

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
	(1, 3, 1, 2, 18000.00),
	(2, 4, 1, 4, 18000.00),
	(3, 5, 1, 10, 18000.00),
	(4, 6, 2, 4, 16000.00),
	(5, 7, 2, 10, 16000.00),
	(6, 8, 1, 3, 16000.00),
	(7, 8, 2, 3, 16000.00),
	(8, 9, 2, 4, 16000.00),
	(9, 9, 1, 1, 16000.00),
	(10, 10, 2, 2, 16000.00),
	(11, 10, 1, 1, 16000.00),
	(12, 11, 1, 3, 16000.00),
	(13, 12, 2, 4, 16000.00),
	(14, 13, 2, 12, 16000.00),
	(15, 14, 2, 1, 16000.00),
	(16, 14, 3, 3, 10000.00);

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
	(6, NULL, 64000.00, '', 'retiro', NULL, NULL, NULL, 'pagado', 'finalizado', 'veee', NULL, '2026-03-18 17:22:49'),
	(7, NULL, 160000.00, '', 'retiro', NULL, NULL, NULL, 'pendiente', 'cancelado', 'aaaa', NULL, '2026-03-18 17:33:53'),
	(8, NULL, 96000.00, '', 'Envío a domicilio', '1168508686', 'cury.ailena@gmail.com', NULL, 'pagado', 'finalizado', NULL, NULL, '2026-03-18 13:37:42'),
	(9, NULL, 80000.00, '', 'Retiro en Local', '1168508686', 'ana@gmail.com', NULL, 'pagado', 'finalizado', NULL, NULL, '2026-03-18 13:57:37'),
	(10, 4, 48000.00, '', 'Envío a domicilio', '1168508686', 'cury.ailena@gmail.com', NULL, 'pendiente', 'finalizado', NULL, NULL, '2026-03-18 14:45:33'),
	(11, 4, 48000.00, '', 'Retiro en Local', '1168508686', 'lu@lu.go.com', NULL, 'pendiente', 'procesando', NULL, NULL, '2026-03-18 14:55:25'),
	(12, 5, 64000.00, '', 'Retiro en Local', '1168508686', '', NULL, 'pagado', 'finalizado', NULL, NULL, '2026-03-20 15:25:11'),
	(13, 6, 192000.00, 'transferencia', 'Envio', '1168508686', 'cury.ailena@gmail.com', NULL, 'pendiente', 'procesando', NULL, NULL, '2026-03-22 22:58:11'),
	(14, 7, 46000.00, 'transferencia', 'Envío a domicilio', '1168508686', 'ailu@ailu', NULL, 'pagado', 'finalizado', NULL, NULL, '2026-03-24 01:38:51');

-- Volcando estructura para tabla aima-db.pedidos
CREATE TABLE IF NOT EXISTS `pedidos` (
  `id_pedido` int(11) NOT NULL AUTO_INCREMENT,
  `id_producto` int(10) unsigned NOT NULL DEFAULT 0,
  `cantidad` int(11) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `estado` varchar(20) DEFAULT 'procesando',
  `fecha_alta` datetime DEFAULT NULL,
  PRIMARY KEY (`id_pedido`)
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
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla aima-db.products: ~2 rows (aproximadamente)
DELETE FROM `products`;
INSERT INTO `products` (`id`, `nombre`, `descripcion`, `precio`, `imagen`, `stock`, `status`, `created_at`, `updated_at`) VALUES
	(1, 'chocolate Cream', 'Vela de Soja Chocolate Intenso', 16000.00, '1773681768_chocolatecream.png', 17, 1, '2026-03-16 17:22:48', '2026-03-18 14:55:25'),
	(2, 'frutos rojos', 'Vela de Soja', 16000.00, '1773839577_frutos_rojos.png', 11, 1, '2026-03-18 13:12:57', '2026-03-24 01:38:51'),
	(3, 'Cookie', 'Recreá la calidez de una tarde de horneado en casa. Nuestra vela de soja Cookie inunda tu espacio con el aroma irresistible de galletas recién salidas del horno: vainilla dulce, mantequilla cremosa y un toque de azúcar tostado. Un abrazo reconfortante hecho aroma.', 10000.00, '1774128008_cookie.png', 3, 1, '2026-03-21 21:20:08', '2026-03-24 01:38:51');

-- Volcando estructura para tabla aima-db.suministros_old
CREATE TABLE IF NOT EXISTS `suministros_old` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(200) NOT NULL,
  `tipo_medida` enum('ml','gr','unidad') NOT NULL DEFAULT 'ml',
  `cantidad_total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `precio_total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `proveedor` varchar(200) DEFAULT NULL,
  `fecha_compra` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla aima-db.suministros_old: ~14 rows (aproximadamente)
DELETE FROM `suministros_old`;
INSERT INTO `suministros_old` (`id`, `nombre`, `tipo_medida`, `cantidad_total`, `precio_total`, `proveedor`, `fecha_compra`, `created_at`) VALUES
	(2, 'Cera de soja APF', 'gr', 3.00, 8684.16, 'Aupar', '2026-03-20', '2026-03-24 02:38:23'),
	(3, 'Vaso Tennessee 320ml', 'ml', 24.00, 32999.00, 'IMPO BAZAR DECO', '2026-03-20', '2026-03-24 02:40:25'),
	(4, 'Batidor Eléctrico', 'unidad', 1.00, 7990.00, 'Jhonys wear', '2026-01-07', '2026-03-24 02:44:25'),
	(6, 'Centrador De Pabilo Largo X 3', 'unidad', 1.00, 3499.00, 'Issaka Home', '2026-01-07', '2026-03-24 02:46:07'),
	(8, 'Ojalillos Para Velas Cera De Soja Parafina X 100', 'unidad', 1.00, 3999.00, 'Issaka Home', '2026-01-07', '2026-03-24 02:46:59'),
	(9, 'Pegamento Ojalillos X 100', 'unidad', 1.00, 3477.06, 'Issaka Home', '2026-01-07', '2026-03-24 02:48:19'),
	(10, 'Vaso Whisky Bar Nadir Vidrio 265ml', 'unidad', 12.00, 19958.00, 'IMPO BAZAR DECO', '2025-12-30', '2026-03-24 02:50:37'),
	(11, 'Cera De Soja Comercial x 1kg', 'unidad', 7.00, 5709.26, 'Aupar', '2025-12-15', '2026-03-24 02:51:57'),
	(12, 'Papel De Oro Plata Cobre Ideal Para Velas De Soja', 'unidad', 1.00, 6109.06, 'Issaka Home', '2025-11-20', '2026-03-24 02:53:53'),
	(13, 'Mica perlada 20gs', 'unidad', 1.00, 5999.00, 'Issaka Home', '2025-11-20', '2026-03-24 02:54:39'),
	(14, 'Molde Silicona Flores Y Moños', 'unidad', 1.00, 6109.06, 'Issaka Home', '2025-11-20', '2026-03-24 02:56:59'),
	(15, 'Colorante ultra concentrado vela x 10gs', 'unidad', 1.00, 3477.06, 'Issaka Home', '2025-11-20', '2026-03-24 02:57:55'),
	(16, 'Molde Silicona 15 Corazones', 'unidad', 1.00, 6109.06, 'Issaka Home', '2025-11-20', '2026-03-24 02:58:50'),
	(17, 'Molde Silicona 14 Huellas', 'unidad', 1.00, 6109.06, 'Issaka Home', '2025-11-20', '2026-03-24 02:59:10'),
	(18, 'Cera de soja APF', 'gr', 3.00, 8684.16, 'Aupar', '2025-11-14', '2026-03-24 03:00:53'),
	(19, 'Cera de soja APF', 'gr', 1000.00, 8684.00, 'Aupar', '2025-11-07', '2026-03-24 03:01:49');

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
	(3, 'Yannel', NULL, NULL, 'UTN', 'admin@admin.com', '0192023a7bbd73250516f069df18b500', 'admin', '2026-03-18 14:39:09'),
	(4, 'Yannel', NULL, NULL, 'UTN', 'cliente', 'cliente123', 'cliente', '2026-03-18 14:44:54'),
	(5, 'ailen', '1168508686', '', '', 'antakle@gmail.com', '$2y$10$R8TsDEAoOuNHo1wTv26fqurZXphYnXcdg507rNrinSWTu0/R7j1N6', 'cliente', '2026-03-20 15:01:45'),
	(6, 'ailen', '1168508686', '', '', 'cury.ailena@gmail.com', '$2y$10$YJdowKenH2sKpCd/c0HjquU/n1fo7R1eHAco2.Fkh7u/6nfP.PyiS', 'admin', '2026-03-20 16:41:34'),
	(7, 'a', '11111111', '', '', 'ailu@ailu', '$2y$10$14kAbe8v3gG/ZQSSojwOkul.TAZ/bQvaBn0putGnA9l4pMQJBfwIy', 'cliente', '2026-03-24 01:37:57');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
