-- Backup Base de Datos Dulce Gracia
-- Generado el: 2026-10-01 10:32:44 (America/Bogota)

SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `banners`;
CREATE TABLE `banners` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(150) DEFAULT NULL,
  `image_path` varchar(255) NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `position` varchar(50) NOT NULL DEFAULT 'home',
  `priority` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `banners` (`id`, `title`, `image_path`, `link`, `position`, `priority`, `status`, `created_at`, `updated_at`) VALUES ('1', 'Oferta amor y amistad', 'http://localhost/DGracia/public/uploads/banners/1788183481_a7e945a37f68d74891a8.jpg', 'productos', 'home', '1', '1', '2026-08-31 13:38:01', '2026-08-31 13:38:01');
INSERT INTO `banners` (`id`, `title`, `image_path`, `link`, `position`, `priority`, `status`, `created_at`, `updated_at`) VALUES ('2', 'Oferta navideña', 'http://localhost/DGracia/public/uploads/banners/1788190004_3e89366e832109b900fe.jpg', '/productos/categoria/postres', 'home', '1', '1', '2026-08-31 15:26:44', '2026-08-31 15:26:44');
INSERT INTO `banners` (`id`, `title`, `image_path`, `link`, `position`, `priority`, `status`, `created_at`, `updated_at`) VALUES ('5', 'Oferta de lanzamiento', 'http://localhost/DGracia/public/uploads/banners/1788295641_e4c64b67a20687010bf6.webp', 'http://localhost/DGracia/public/producto/merengon-663', 'home', '1', '1', '2026-09-01 20:47:21', '2026-09-01 20:47:21');

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `categories` (`id`, `name`, `slug`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES ('1', 'Tortas', 'tortas', '1', '2026-08-29 16:12:44', '2026-08-29 16:12:44', NULL);
INSERT INTO `categories` (`id`, `name`, `slug`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES ('2', 'Galletas', 'galletas', '1', '2026-08-29 16:12:44', '2026-08-29 16:12:44', NULL);
INSERT INTO `categories` (`id`, `name`, `slug`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES ('3', 'Postres', 'postres', '1', '2026-08-29 16:12:44', '2026-08-29 16:12:44', NULL);
INSERT INTO `categories` (`id`, `name`, `slug`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES ('4', 'Mecato', 'mecato', '1', '2026-09-11 18:07:56', '2026-09-11 18:07:56', NULL);

DROP TABLE IF EXISTS `coupon_logs`;
CREATE TABLE `coupon_logs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `coupon_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned DEFAULT NULL,
  `order_id` int(11) unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `coupon_logs_coupon_id_foreign` (`coupon_id`),
  CONSTRAINT `coupon_logs_coupon_id_foreign` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `coupons`;
CREATE TABLE `coupons` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `code` varchar(50) NOT NULL,
  `discount_type` enum('percentage','fixed') NOT NULL DEFAULT 'percentage',
  `value` decimal(10,2) NOT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `max_uses` int(11) NOT NULL DEFAULT 0,
  `used_count` int(11) NOT NULL DEFAULT 0,
  `max_uses_per_user` int(11) NOT NULL DEFAULT 1,
  `category_id` int(11) unsigned DEFAULT NULL,
  `product_id` int(11) unsigned DEFAULT NULL,
  `loyal_only` tinyint(1) NOT NULL DEFAULT 0,
  `is_stackable` tinyint(1) NOT NULL DEFAULT 1,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `coupons` (`id`, `name`, `description`, `code`, `discount_type`, `value`, `start_date`, `end_date`, `max_uses`, `used_count`, `max_uses_per_user`, `category_id`, `product_id`, `loyal_only`, `is_stackable`, `status`, `created_at`, `updated_at`) VALUES ('1', 'Amor y amistad', '10% en la categoria postres', 'AMOR', 'percentage', '10.00', '2026-09-03 08:51:00', '2026-09-06 08:51:00', '1', '0', '1', NULL, NULL, '0', '1', '1', '2026-09-03 13:52:07', '2026-09-03 13:52:07');
INSERT INTO `coupons` (`id`, `name`, `description`, `code`, `discount_type`, `value`, `start_date`, `end_date`, `max_uses`, `used_count`, `max_uses_per_user`, `category_id`, `product_id`, `loyal_only`, `is_stackable`, `status`, `created_at`, `updated_at`) VALUES ('2', 'amor y amistad', '10% en la categoria postres', 'AMISTAD', 'percentage', '10.00', '2026-09-11 10:26:00', '2026-09-13 10:26:00', '1', '0', '1', NULL, NULL, '0', '1', '1', '2026-09-11 15:27:32', '2026-09-11 15:27:32');
INSERT INTO `coupons` (`id`, `name`, `description`, `code`, `discount_type`, `value`, `start_date`, `end_date`, `max_uses`, `used_count`, `max_uses_per_user`, `category_id`, `product_id`, `loyal_only`, `is_stackable`, `status`, `created_at`, `updated_at`) VALUES ('3', 'Cliente fiel', '', 'JAVIER', 'percentage', '14999.99', '2026-09-21 11:06:00', '2026-09-24 11:06:00', '1', '0', '1', NULL, NULL, '0', '1', '1', '2026-09-21 16:06:51', '2026-09-21 16:08:52');

DROP TABLE IF EXISTS `delivery_zones`;
CREATE TABLE `delivery_zones` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `delivery_zones` (`id`, `name`, `cost`, `status`, `created_at`, `updated_at`) VALUES ('1', 'Zona 1 (Cercana)', '5000.00', '1', '2026-08-29 16:12:44', '2026-08-29 16:12:44');
INSERT INTO `delivery_zones` (`id`, `name`, `cost`, `status`, `created_at`, `updated_at`) VALUES ('2', 'Zona 2 (Intermedia)', '7000.00', '1', '2026-08-29 16:12:44', '2026-08-29 16:12:44');
INSERT INTO `delivery_zones` (`id`, `name`, `cost`, `status`, `created_at`, `updated_at`) VALUES ('3', 'Zona 3 (Lejana)', '10000.00', '1', '2026-08-29 16:12:44', '2026-08-29 16:12:44');

DROP TABLE IF EXISTS `discounts`;
CREATE TABLE `discounts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL,
  `type` enum('product','category','loyalty','general') NOT NULL DEFAULT 'general',
  `discount_type` enum('percentage','fixed') NOT NULL DEFAULT 'percentage',
  `value` decimal(10,2) NOT NULL DEFAULT 0.00,
  `product_id` int(11) unsigned DEFAULT NULL,
  `category_id` int(11) unsigned DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `discounts_product_id_foreign` (`product_id`),
  KEY `discounts_category_id_foreign` (`category_id`),
  CONSTRAINT `discounts_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `discounts_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `discounts` (`id`, `title`, `type`, `discount_type`, `value`, `product_id`, `category_id`, `start_date`, `end_date`, `status`, `created_at`, `updated_at`) VALUES ('1', 'Descuento por lanzamiento', 'product', 'percentage', '5.00', '10', NULL, '2026-09-21 15:48:00', '2026-09-24 15:48:00', '1', '2026-09-01 20:48:47', '2026-09-21 15:50:36');
INSERT INTO `discounts` (`id`, `title`, `type`, `discount_type`, `value`, `product_id`, `category_id`, `start_date`, `end_date`, `status`, `created_at`, `updated_at`) VALUES ('2', 'SEP', 'product', 'percentage', '5.00', '2', NULL, '2026-09-21 10:49:00', '2026-09-24 10:49:00', '1', '2026-09-21 15:49:17', '2026-09-21 15:49:17');
INSERT INTO `discounts` (`id`, `title`, `type`, `discount_type`, `value`, `product_id`, `category_id`, `start_date`, `end_date`, `status`, `created_at`, `updated_at`) VALUES ('3', 'Flores Amarillas', 'category', 'percentage', '10.00', NULL, '3', '2026-09-25 10:33:00', '2026-09-26 10:33:00', '1', '2026-09-25 10:33:27', '2026-09-25 10:33:27');

DROP TABLE IF EXISTS `favorites`;
CREATE TABLE `favorites` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `product_id` int(11) unsigned NOT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `favorites_user_id_foreign` (`user_id`),
  KEY `favorites_product_id_foreign` (`product_id`),
  CONSTRAINT `favorites_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `favorites_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `favorites` (`id`, `user_id`, `product_id`, `created_at`) VALUES ('1', '3', '6', '2026-09-25 10:31:37');

DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `type` enum('Sugerencia','Felicitación','Queja','Reclamo') NOT NULL DEFAULT 'Sugerencia',
  `message` text NOT NULL,
  `status` enum('unread','read') NOT NULL DEFAULT 'unread',
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `messages` (`id`, `name`, `phone`, `email`, `type`, `message`, `status`, `created_at`) VALUES ('1', 'Daniel Restrepo', '3117540084', 'jrpoalvarez@hotmail.com', 'Felicitación', 'Buena torta', 'read', '2026-09-25 12:23:36');

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES ('1', '2026-08-26-100000', 'App\\Database\\Migrations\\CreateDulceGraciaTables', 'default', 'App', '1788019952', '1');
INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES ('2', '2026-08-28-120000', 'App\\Database\\Migrations\\FixEnumColumns', 'default', 'App', '1788019952', '1');
INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES ('3', '2026-09-01-100000', 'App\\Database\\Migrations\\CreateDiscountsAndEnhancements', 'default', 'App', '1788277339', '2');
INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES ('4', '2026-09-11-120000', 'App\\Database\\Migrations\\AddCustomerFieldsAndPointsTables', 'default', 'App', '1789148870', '3');
INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES ('5', '2026-09-22-100000', 'App\\Database\\Migrations\\AuditAndNewFeaturesUpdates', 'default', 'App', '1790035909', '4');
INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES ('6', '2026-09-29-140000', 'App\\Database\\Migrations\\CreateRatingsAndNotificationsTables', 'default', 'App', '1790707698', '5');

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(50) NOT NULL DEFAULT 'system',
  `title` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `target_url` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `is_read` (`is_read`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `notifications` (`id`, `type`, `title`, `message`, `target_url`, `is_read`, `created_at`, `updated_at`) VALUES ('1', 'order', '🛍️ Nuevo Pedido #DG-260929-BC304', 'Pedido por $95.000 registrado por Daniel Restrepo', 'http://localhost/DGracia/public/admin/pedidos/ver/12', '0', '2026-09-29 14:22:01', '2026-09-29 14:22:01');

DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(11) unsigned NOT NULL,
  `product_id` int(11) unsigned DEFAULT NULL,
  `product_name` varchar(150) NOT NULL,
  `sku` varchar(50) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `original_price` decimal(10,2) DEFAULT NULL,
  `offer_price` decimal(10,2) DEFAULT NULL,
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `quantity` int(11) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_items_order_id_foreign` (`order_id`),
  KEY `order_items_product_id_foreign` (`product_id`),
  CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `sku`, `price`, `original_price`, `offer_price`, `discount_amount`, `quantity`, `subtotal`) VALUES ('1', '1', '3', 'Galletas Choco-Chips (Caja x6)', NULL, '25000.00', NULL, NULL, '0.00', '1', '25000.00');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `sku`, `price`, `original_price`, `offer_price`, `discount_amount`, `quantity`, `subtotal`) VALUES ('2', '2', '3', 'Galletas Choco-Chips (Caja x6)', NULL, '25000.00', NULL, NULL, '0.00', '1', '25000.00');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `sku`, `price`, `original_price`, `offer_price`, `discount_amount`, `quantity`, `subtotal`) VALUES ('3', '2', '2', 'Torta Red Velvet', NULL, '90000.00', NULL, NULL, '0.00', '1', '90000.00');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `sku`, `price`, `original_price`, `offer_price`, `discount_amount`, `quantity`, `subtotal`) VALUES ('4', '2', '1', 'Torta de Chocolate Supremo', NULL, '95000.00', NULL, NULL, '0.00', '1', '95000.00');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `sku`, `price`, `original_price`, `offer_price`, `discount_amount`, `quantity`, `subtotal`) VALUES ('5', '3', '3', 'Galletas Choco-Chips (Caja x6)', NULL, '25000.00', NULL, NULL, '0.00', '1', '25000.00');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `sku`, `price`, `original_price`, `offer_price`, `discount_amount`, `quantity`, `subtotal`) VALUES ('6', '4', '2', 'Torta Red Velvet', NULL, '90000.00', NULL, NULL, '0.00', '1', '90000.00');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `sku`, `price`, `original_price`, `offer_price`, `discount_amount`, `quantity`, `subtotal`) VALUES ('7', '5', '7', 'Galletas de Avena y Pasas', NULL, '22000.00', NULL, NULL, '0.00', '1', '22000.00');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `sku`, `price`, `original_price`, `offer_price`, `discount_amount`, `quantity`, `subtotal`) VALUES ('8', '6', '4', 'Cheesecake de Frutos Rojos', NULL, '15000.00', NULL, NULL, '0.00', '1', '15000.00');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `sku`, `price`, `original_price`, `offer_price`, `discount_amount`, `quantity`, `subtotal`) VALUES ('9', '7', '4', 'Cheesecake de Frutos Rojos', 'cheesecake-de-frutos-rojos', '15000.00', '15000.00', NULL, '0.00', '1', '15000.00');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `sku`, `price`, `original_price`, `offer_price`, `discount_amount`, `quantity`, `subtotal`) VALUES ('12', '10', '4', 'Cheesecake de Frutos Rojos', 'cheesecake-de-frutos-rojos', '13500.00', '15000.00', '13500.00', '1500.00', '2', '27000.00');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `sku`, `price`, `original_price`, `offer_price`, `discount_amount`, `quantity`, `subtotal`) VALUES ('13', '11', '2', 'Torta Red Velvet', 'torta-red-velvet', '90000.00', '90000.00', NULL, '0.00', '1', '90000.00');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `sku`, `price`, `original_price`, `offer_price`, `discount_amount`, `quantity`, `subtotal`) VALUES ('14', '12', '2', 'Torta Red Velvet', 'torta-red-velvet', '90000.00', '90000.00', NULL, '0.00', '1', '90000.00');

DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_number` varchar(50) NOT NULL,
  `user_id` int(11) unsigned DEFAULT NULL,
  `customer_name` varchar(150) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `customer_email` varchar(150) DEFAULT NULL,
  `whatsapp` varchar(50) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `neighborhood` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `delivery_instructions` text DEFAULT NULL,
  `delivery_date` date DEFAULT NULL,
  `delivery_time` varchar(50) DEFAULT NULL,
  `delivery_type` enum('recoger','domicilio') NOT NULL DEFAULT 'domicilio',
  `delivery_zone_id` int(11) unsigned DEFAULT NULL,
  `delivery_cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `coupon_code` varchar(50) DEFAULT NULL,
  `coupon_discount` decimal(10,2) DEFAULT 0.00,
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_method` varchar(50) NOT NULL,
  `payment_status` enum('pendiente','pagado','rechazado') DEFAULT 'pendiente',
  `status` enum('pendiente','en preparacion','enviado','entregado','cancelado') NOT NULL DEFAULT 'pendiente',
  `notes` text DEFAULT NULL,
  `source` enum('web','pos') DEFAULT 'web',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_number` (`order_number`),
  KEY `orders_user_id_foreign` (`user_id`),
  KEY `orders_delivery_zone_id_foreign` (`delivery_zone_id`),
  CONSTRAINT `orders_delivery_zone_id_foreign` FOREIGN KEY (`delivery_zone_id`) REFERENCES `delivery_zones` (`id`) ON DELETE CASCADE ON UPDATE SET NULL,
  CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `orders` (`id`, `order_number`, `user_id`, `customer_name`, `phone`, `customer_email`, `whatsapp`, `address`, `neighborhood`, `city`, `delivery_instructions`, `delivery_date`, `delivery_time`, `delivery_type`, `delivery_zone_id`, `delivery_cost`, `coupon_code`, `coupon_discount`, `discount_amount`, `subtotal`, `total`, `payment_method`, `payment_status`, `status`, `notes`, `source`, `created_at`, `updated_at`) VALUES ('1', 'DG-260914-C3582', NULL, 'luis', '3108690810', NULL, '', 'Calle 49ae #99', '', 'Bogotá', '', '2026-09-14', '', 'domicilio', '1', '5000.00', NULL, '0.00', '0.00', '25000.00', '30000.00', 'Efectivo', 'pendiente', 'pendiente', '', 'web', '2026-09-14 15:19:03', '2026-09-14 15:19:03');
INSERT INTO `orders` (`id`, `order_number`, `user_id`, `customer_name`, `phone`, `customer_email`, `whatsapp`, `address`, `neighborhood`, `city`, `delivery_instructions`, `delivery_date`, `delivery_time`, `delivery_type`, `delivery_zone_id`, `delivery_cost`, `coupon_code`, `coupon_discount`, `discount_amount`, `subtotal`, `total`, `payment_method`, `payment_status`, `status`, `notes`, `source`, `created_at`, `updated_at`) VALUES ('2', 'POS-260914-9414', NULL, 'Cliente Mostrador / Caja', 'N/A', NULL, '', 'Venta Directa en Tienda (POS)', NULL, NULL, NULL, NULL, NULL, 'recoger', NULL, '0.00', NULL, '0.00', '0.00', '210000.00', '210000.00', 'Efectivo', 'pendiente', 'entregado', 'Venta en caja registradora', 'web', '2026-09-14 15:52:36', '2026-09-14 15:52:36');
INSERT INTO `orders` (`id`, `order_number`, `user_id`, `customer_name`, `phone`, `customer_email`, `whatsapp`, `address`, `neighborhood`, `city`, `delivery_instructions`, `delivery_date`, `delivery_time`, `delivery_type`, `delivery_zone_id`, `delivery_cost`, `coupon_code`, `coupon_discount`, `discount_amount`, `subtotal`, `total`, `payment_method`, `payment_status`, `status`, `notes`, `source`, `created_at`, `updated_at`) VALUES ('3', 'DG-260921-90F2D', NULL, 'luis moreno', '3108690810', NULL, '3117540084', 'Calle 49ae #99', 'Urbana', 'Bogotá', 'casa', '2026-09-21', '', 'domicilio', '2', '7000.00', NULL, '0.00', '0.00', '25000.00', '32000.00', 'Efectivo', 'pendiente', 'pendiente', '', 'web', '2026-09-21 15:21:35', '2026-09-21 15:21:35');
INSERT INTO `orders` (`id`, `order_number`, `user_id`, `customer_name`, `phone`, `customer_email`, `whatsapp`, `address`, `neighborhood`, `city`, `delivery_instructions`, `delivery_date`, `delivery_time`, `delivery_type`, `delivery_zone_id`, `delivery_cost`, `coupon_code`, `coupon_discount`, `discount_amount`, `subtotal`, `total`, `payment_method`, `payment_status`, `status`, `notes`, `source`, `created_at`, `updated_at`) VALUES ('4', 'DG-260921-345B1', '2', 'Javier Ramirez', '3235808948', NULL, '3235808948', 'calle 49', 'Sabaneta', 'Bogotá', 'Casa', '2026-09-21', '', 'domicilio', '1', '5000.00', NULL, '0.00', '0.00', '90000.00', '95000.00', 'Nequi/Daviplata', 'pendiente', 'entregado', '', 'web', '2026-09-21 16:20:54', '2026-09-21 16:36:36');
INSERT INTO `orders` (`id`, `order_number`, `user_id`, `customer_name`, `phone`, `customer_email`, `whatsapp`, `address`, `neighborhood`, `city`, `delivery_instructions`, `delivery_date`, `delivery_time`, `delivery_type`, `delivery_zone_id`, `delivery_cost`, `coupon_code`, `coupon_discount`, `discount_amount`, `subtotal`, `total`, `payment_method`, `payment_status`, `status`, `notes`, `source`, `created_at`, `updated_at`) VALUES ('5', 'POS-260921-4798', '2', 'Javier Ramirez', '3108690810', NULL, '3235808948', 'Venta Directa en Tienda (POS)', NULL, NULL, NULL, NULL, NULL, 'recoger', NULL, '0.00', NULL, '0.00', '0.00', '22000.00', '22000.00', 'Efectivo', 'pendiente', 'entregado', 'Venta en caja registradora', 'web', '2026-09-21 16:55:43', '2026-09-21 16:55:43');
INSERT INTO `orders` (`id`, `order_number`, `user_id`, `customer_name`, `phone`, `customer_email`, `whatsapp`, `address`, `neighborhood`, `city`, `delivery_instructions`, `delivery_date`, `delivery_time`, `delivery_type`, `delivery_zone_id`, `delivery_cost`, `coupon_code`, `coupon_discount`, `discount_amount`, `subtotal`, `total`, `payment_method`, `payment_status`, `status`, `notes`, `source`, `created_at`, `updated_at`) VALUES ('6', 'DG-260921-7A867', '2', 'Javier Ramirez', '3235808948', NULL, '3235808948', 'calle 49', 'Sabaneta', 'Bogotá', 'Casa', '2026-09-21', '', 'domicilio', '1', '5000.00', NULL, '0.00', '0.00', '15000.00', '20000.00', 'Nequi/Daviplata', 'pendiente', 'entregado', '', 'web', '2026-09-21 23:00:30', '2026-09-21 23:02:08');
INSERT INTO `orders` (`id`, `order_number`, `user_id`, `customer_name`, `phone`, `customer_email`, `whatsapp`, `address`, `neighborhood`, `city`, `delivery_instructions`, `delivery_date`, `delivery_time`, `delivery_type`, `delivery_zone_id`, `delivery_cost`, `coupon_code`, `coupon_discount`, `discount_amount`, `subtotal`, `total`, `payment_method`, `payment_status`, `status`, `notes`, `source`, `created_at`, `updated_at`) VALUES ('7', 'DG-260923-43D98', '2', 'Javier Ramirez', '3235808948', 'jaralo45@hotmail.com', '3235808948', 'calle 49', 'Sabaneta', 'Medellín', 'Casa', '2026-09-23', '', 'domicilio', '1', '5000.00', NULL, '0.00', '0.00', '15000.00', '20000.00', 'Nequi/Daviplata', 'pendiente', 'pendiente', '', 'web', '2026-09-23 15:42:44', '2026-09-23 15:42:44');
INSERT INTO `orders` (`id`, `order_number`, `user_id`, `customer_name`, `phone`, `customer_email`, `whatsapp`, `address`, `neighborhood`, `city`, `delivery_instructions`, `delivery_date`, `delivery_time`, `delivery_type`, `delivery_zone_id`, `delivery_cost`, `coupon_code`, `coupon_discount`, `discount_amount`, `subtotal`, `total`, `payment_method`, `payment_status`, `status`, `notes`, `source`, `created_at`, `updated_at`) VALUES ('10', 'DG-260925-54103', '3', 'Daniel Restrepo', '3117540084', 'jrpoalvarez@hotmail.com', '3117540084', 'calle 49', 'Bello', 'Medellín', 'Casa', '2026-09-25', '', 'domicilio', '1', '5000.00', NULL, '0.00', '3000.00', '27000.00', '32000.00', 'Nequi/Daviplata', 'pendiente', 'pendiente', '', 'web', '2026-09-25 12:26:44', '2026-09-25 12:26:44');
INSERT INTO `orders` (`id`, `order_number`, `user_id`, `customer_name`, `phone`, `customer_email`, `whatsapp`, `address`, `neighborhood`, `city`, `delivery_instructions`, `delivery_date`, `delivery_time`, `delivery_type`, `delivery_zone_id`, `delivery_cost`, `coupon_code`, `coupon_discount`, `discount_amount`, `subtotal`, `total`, `payment_method`, `payment_status`, `status`, `notes`, `source`, `created_at`, `updated_at`) VALUES ('11', 'DG-260929-A21DA', NULL, 'luis moreno', '3108690810', 'nal.lucho88@gmail.com', '3108690810', 'Calle 49ae #99', 'Urbana', 'Medellín', 'casa', '2026-09-29', '', 'domicilio', '1', '5000.00', NULL, '0.00', '0.00', '90000.00', '95000.00', 'Nequi/Daviplata', 'pendiente', 'pendiente', '', 'web', '2026-09-29 10:42:15', '2026-09-29 10:42:15');
INSERT INTO `orders` (`id`, `order_number`, `user_id`, `customer_name`, `phone`, `customer_email`, `whatsapp`, `address`, `neighborhood`, `city`, `delivery_instructions`, `delivery_date`, `delivery_time`, `delivery_type`, `delivery_zone_id`, `delivery_cost`, `coupon_code`, `coupon_discount`, `discount_amount`, `subtotal`, `total`, `payment_method`, `payment_status`, `status`, `notes`, `source`, `created_at`, `updated_at`) VALUES ('12', 'DG-260929-BC304', '3', 'Daniel Restrepo', '3108690810', 'jrpoalvarez@hotmail.com', '3108690810', 'calle 49', 'Bello', 'Medellín', 'Casa', '2026-09-29', '', 'domicilio', '1', '5000.00', NULL, '0.00', '0.00', '90000.00', '95000.00', 'Nequi/Daviplata', 'pendiente', 'pendiente', '', 'web', '2026-09-29 14:22:01', '2026-09-29 14:22:01');

DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE `password_resets` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(150) NOT NULL,
  `token_hash` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `point_goals`;
CREATE TABLE `point_goals` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL,
  `points_required` int(11) NOT NULL,
  `reward_type` enum('coupon','discount','free_product') NOT NULL DEFAULT 'coupon',
  `reward_value` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `point_transactions`;
CREATE TABLE `point_transactions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `order_id` int(11) unsigned DEFAULT NULL,
  `points` int(11) NOT NULL,
  `type` enum('earned','spent','expired','adjustment') NOT NULL DEFAULT 'earned',
  `description` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `point_transactions_user_id_foreign` (`user_id`),
  CONSTRAINT `point_transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `point_transactions` (`id`, `user_id`, `order_id`, `points`, `type`, `description`, `created_at`) VALUES ('1', '2', '4', '90', 'earned', 'Compra en línea #DG-260921-345B1', '2026-09-21 16:20:54');
INSERT INTO `point_transactions` (`id`, `user_id`, `order_id`, `points`, `type`, `description`, `created_at`) VALUES ('2', '2', '5', '22', 'earned', 'Venta en Caja POS #POS-260921-4798', '2026-09-21 16:55:43');
INSERT INTO `point_transactions` (`id`, `user_id`, `order_id`, `points`, `type`, `description`, `created_at`) VALUES ('3', '2', '6', '15', 'earned', 'Compra en línea #DG-260921-7A867', '2026-09-21 23:00:30');
INSERT INTO `point_transactions` (`id`, `user_id`, `order_id`, `points`, `type`, `description`, `created_at`) VALUES ('4', '2', '7', '15', 'earned', 'Compra en línea #DG-260923-43D98', '2026-09-23 15:42:44');
INSERT INTO `point_transactions` (`id`, `user_id`, `order_id`, `points`, `type`, `description`, `created_at`) VALUES ('7', '3', '10', '27', 'earned', 'Compra en línea #DG-260925-54103', '2026-09-25 12:26:44');
INSERT INTO `point_transactions` (`id`, `user_id`, `order_id`, `points`, `type`, `description`, `created_at`) VALUES ('8', '3', '12', '90', 'earned', 'Compra en línea #DG-260929-BC304', '2026-09-29 14:22:01');

DROP TABLE IF EXISTS `product_images`;
CREATE TABLE `product_images` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(11) unsigned NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `is_main` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_images_product_id_foreign` (`product_id`),
  CONSTRAINT `product_images_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `product_images` (`id`, `product_id`, `image_path`, `is_main`, `created_at`) VALUES ('1', '1', 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?q=80&w=800&auto=format&fit=crop', '1', '2026-08-29 16:12:54');
INSERT INTO `product_images` (`id`, `product_id`, `image_path`, `is_main`, `created_at`) VALUES ('2', '2', 'https://images.unsplash.com/photo-1616541823729-00fe0aacd32c?q=80&w=800&auto=format&fit=crop', '1', '2026-08-29 16:12:54');
INSERT INTO `product_images` (`id`, `product_id`, `image_path`, `is_main`, `created_at`) VALUES ('3', '3', 'https://images.unsplash.com/photo-1499636136210-6f4ee915583e?q=80&w=800&auto=format&fit=crop', '1', '2026-08-29 16:12:54');
INSERT INTO `product_images` (`id`, `product_id`, `image_path`, `is_main`, `created_at`) VALUES ('4', '4', 'https://images.unsplash.com/photo-1533134242443-d4fd215305ad?q=80&w=800&auto=format&fit=crop', '1', '2026-08-29 16:12:54');
INSERT INTO `product_images` (`id`, `product_id`, `image_path`, `is_main`, `created_at`) VALUES ('5', '5', 'https://images.unsplash.com/photo-1571115177098-24ec42ed204d?q=80&w=800&auto=format&fit=crop', '1', '2026-08-29 16:12:54');
INSERT INTO `product_images` (`id`, `product_id`, `image_path`, `is_main`, `created_at`) VALUES ('7', '7', 'https://images.unsplash.com/photo-1605807646983-377bc5a76493?q=80&w=800&auto=format&fit=crop', '1', '2026-08-29 16:12:54');
INSERT INTO `product_images` (`id`, `product_id`, `image_path`, `is_main`, `created_at`) VALUES ('8', '8', 'http://localhost/DGracia/public/uploads/products/1788024030_1479a899477fd829e195.jpg', '1', '2026-08-29 17:20:30');
INSERT INTO `product_images` (`id`, `product_id`, `image_path`, `is_main`, `created_at`) VALUES ('9', '9', 'http://localhost/DGracia/public/uploads/products/1788024149_5d688da4280ab4aa246f.jpg', '1', '2026-08-29 17:22:29');
INSERT INTO `product_images` (`id`, `product_id`, `image_path`, `is_main`, `created_at`) VALUES ('10', '6', 'http://localhost/DGracia/public/uploads/products/1788026124_37065552f3b56818a459.jpg', '1', '2026-08-29 17:55:24');
INSERT INTO `product_images` (`id`, `product_id`, `image_path`, `is_main`, `created_at`) VALUES ('12', '10', 'http://localhost/DGracia/public/uploads/products/1788295153_ca22635b2fdc96b4e60a.jpg', '1', '2026-09-01 20:39:13');

DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(11) unsigned NOT NULL,
  `name` varchar(150) NOT NULL,
  `slug` varchar(150) NOT NULL,
  `short_description` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `cost` decimal(10,2) DEFAULT 0.00,
  `ingredients` text DEFAULT NULL,
  `portions` varchar(50) DEFAULT NULL,
  `availability` tinyint(1) NOT NULL DEFAULT 1,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `products_category_id_foreign` (`category_id`),
  CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `short_description`, `description`, `price`, `cost`, `ingredients`, `portions`, `availability`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES ('1', '1', 'Torta de Chocolate Supremo', 'torta-de-chocolate-supremo', 'Deliciosa torta de chocolate con triple relleno.', 'Nuestra Torta de Chocolate Supremo es la elección perfecta para los amantes del cacao. Elaborada con cacao al 70%, cuenta con un triple relleno de ganache y cubierta de crema de chocolate.', '95000.00', '0.00', 'Chocolate 70%, harina, huevos, leche, crema de leche, mantequilla.', '15 personas', '1', '1', '2026-08-29 16:12:54', '2026-08-29 17:17:16', NULL);
INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `short_description`, `description`, `price`, `cost`, `ingredients`, `portions`, `availability`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES ('2', '1', 'Torta Red Velvet', 'torta-red-velvet', 'Clásica Red Velvet con frosting de queso crema.', 'El balance perfecto entre el sabor a cacao suave y la acidez del frosting de queso crema. Una torta elegante y deliciosa.', '90000.00', '0.00', 'Harina, cacao, buttermilk, colorante natural, queso crema, mantequilla, azúcar.', '12 personas', '1', '1', '2026-08-29 16:12:54', '2026-08-29 16:12:54', NULL);
INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `short_description`, `description`, `price`, `cost`, `ingredients`, `portions`, `availability`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES ('3', '2', 'Galletas Choco-Chips (Caja x6)', 'galletas-choco-chips', 'Galletas crujientes por fuera y suaves por dentro.', 'Caja con 6 galletas grandes estilo neoyorquino, repletas de chips de chocolate semiamargo y un toque de sal marina.', '25000.00', '0.00', 'Harina, chips de chocolate semiamargo, mantequilla, azúcar morena, huevos, sal marina.', '6 porciones', '1', '1', '2026-08-29 16:12:54', '2026-08-29 16:12:54', NULL);
INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `short_description`, `description`, `price`, `cost`, `ingredients`, `portions`, `availability`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES ('4', '3', 'Cheesecake de Frutos Rojos', 'cheesecake-de-frutos-rojos', 'Cheesecake cremoso con mermelada artesanal.', 'Cremoso cheesecake horneado al estilo New York, cubierto con nuestra mermelada artesanal de fresas, moras y arándanos.', '15000.00', '0.00', 'Queso crema, galleta de vainilla, mantequilla, huevos, fresas, moras, arándanos.', '1 persona', '1', '1', '2026-08-29 16:12:54', '2026-08-29 16:12:54', NULL);
INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `short_description`, `description`, `price`, `cost`, `ingredients`, `portions`, `availability`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES ('5', '3', 'Tiramisú Clásico', 'tiramisu-clasico', 'Postre italiano con café y mascarpone.', 'Auténtico postre italiano con bizcotelas bañadas en café espresso y licor de café, intercaladas con suave crema mascarpone.', '18000.00', '0.00', 'Queso mascarpone, café espresso, bizcotelas, cacao en polvo, huevos, azúcar.', '1 persona', '1', '1', '2026-08-29 16:12:54', '2026-08-29 16:12:54', NULL);
INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `short_description`, `description`, `price`, `cost`, `ingredients`, `portions`, `availability`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES ('6', '1', 'Torta de Zanahoria', 'torta-de-zanahoria', 'Con nueces y cubierta de queso crema.', 'Húmeda torta de zanahoria con especias, nueces troceadas y una generosa capa de frosting de queso crema.', '80000.00', '0.00', 'Zanahoria fresca, nueces, canela, harina, huevos, aceite vegetal, queso crema.', '10 personas', '1', '1', '2026-08-29 16:12:54', '2026-08-29 17:55:24', NULL);
INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `short_description`, `description`, `price`, `cost`, `ingredients`, `portions`, `availability`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES ('7', '2', 'Galletas de Avena y Pasas', 'galletas-de-avena-y-pasas', 'Galletas saludables y deliciosas (Caja x6).', 'Nuestras galletas de avena son suaves, masticables y están llenas de pasas jugosas. Perfectas para un antojo más saludable.', '22000.00', '0.00', 'Avena en hojuelas, pasas, canela, harina, mantequilla, huevos.', '6 porciones', '1', '1', '2026-08-29 16:12:54', '2026-08-29 16:12:54', NULL);
INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `short_description`, `description`, `price`, `cost`, `ingredients`, `portions`, `availability`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES ('8', '3', 'Alfajor', 'alfajor-508', 'Alfajor de Arequipe', 'Rico alfajor de arequie', '5012.00', '0.00', '', '', '1', '1', '2026-08-29 17:20:30', '2026-09-01 20:38:13', '2026-09-01 20:38:13');
INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `short_description`, `description`, `price`, `cost`, `ingredients`, `portions`, `availability`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES ('9', '2', 'Alfajor', 'alfajor-183', '', 'Delicioso alfajor cubierto de guayaba', '10000.00', '0.00', '', '', '1', '1', '2026-08-29 17:22:29', '2026-08-31 15:38:31', '2026-08-31 15:38:31');
INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `short_description`, `description`, `price`, `cost`, `ingredients`, `portions`, `availability`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES ('10', '3', 'Merengon ', 'merengon-663', 'Merengón de melocoton', 'Delicioso merengón de melocotón con fresas y crema chantilly', '12000.00', '0.00', '', '', '1', '1', '2026-09-01 20:39:13', '2026-09-01 20:39:13', NULL);

DROP TABLE IF EXISTS `promotions`;
CREATE TABLE `promotions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `type` enum('modal','flotante') NOT NULL DEFAULT 'modal',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `ratings`;
CREATE TABLE `ratings` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `order_id` int(11) unsigned NOT NULL,
  `order_item_id` int(11) unsigned NOT NULL,
  `product_id` int(11) unsigned NOT NULL,
  `rating` tinyint(1) NOT NULL,
  `comment` text DEFAULT NULL,
  `status` enum('visible','oculta','eliminada') NOT NULL DEFAULT 'visible',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_item_id` (`order_item_id`),
  KEY `ratings_user_id_foreign` (`user_id`),
  KEY `ratings_order_id_foreign` (`order_id`),
  KEY `product_id_status` (`product_id`,`status`),
  CONSTRAINT `ratings_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `ratings_order_item_id_foreign` FOREIGN KEY (`order_item_id`) REFERENCES `order_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `ratings_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `ratings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `ratings` (`id`, `user_id`, `order_id`, `order_item_id`, `product_id`, `rating`, `comment`, `status`, `created_at`, `updated_at`) VALUES ('1', '3', '12', '14', '2', '5', 'Super rica', 'visible', '2026-09-29 14:22:36', '2026-09-29 14:22:36');

DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(100) NOT NULL,
  `value` text DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `settings` (`id`, `key`, `value`, `description`) VALUES ('1', 'BUSINESS_NAME', 'Dulce Gracia', 'Nombre de la empresa');
INSERT INTO `settings` (`id`, `key`, `value`, `description`) VALUES ('2', 'BUSINESS_ADDRESS', 'Calle Falsa 123, Ciudad', 'Dirección de la repostería');
INSERT INTO `settings` (`id`, `key`, `value`, `description`) VALUES ('3', 'BUSINESS_PHONE', '+57 300 000 0000', 'Teléfono principal');
INSERT INTO `settings` (`id`, `key`, `value`, `description`) VALUES ('4', 'WHATSAPP_NUMBER', '573000000000', 'Número de WhatsApp (con código de país sin +)');
INSERT INTO `settings` (`id`, `key`, `value`, `description`) VALUES ('5', 'ADVISOR_NAME', 'María', 'Nombre de la asesora');
INSERT INTO `settings` (`id`, `key`, `value`, `description`) VALUES ('6', 'BUSINESS_HOURS', '', 'Horarios de atención');
INSERT INTO `settings` (`id`, `key`, `value`, `description`) VALUES ('7', 'FACEBOOK_URL', 'https://facebook.com/dulcegracia', 'URL de Facebook');
INSERT INTO `settings` (`id`, `key`, `value`, `description`) VALUES ('8', 'INSTAGRAM_URL', 'https://instagram.com/dulcegracia', 'URL de Instagram');
INSERT INTO `settings` (`id`, `key`, `value`, `description`) VALUES ('9', 'ABOUT_US_TEXT', 'En Dulce Gracia endulzamos tus momentos especiales con las mejores recetas artesanales.', 'Texto sobre nosotros');
INSERT INTO `settings` (`id`, `key`, `value`, `description`) VALUES ('10', 'WHATSAPP_MESSAGE', 'Hola, soy cliente de Dulce Gracia. 😊\\nSe me antojó algo y quisiera realizar un pedido.\\nMi nombre es:\\nMi pedido es:\\n¿Me pueden ayudar?', 'Mensaje predeterminado de WhatsApp');
INSERT INTO `settings` (`id`, `key`, `value`, `description`) VALUES ('11', 'site_name', 'Dulce Gracia', NULL);
INSERT INTO `settings` (`id`, `key`, `value`, `description`) VALUES ('12', 'site_description', '', NULL);
INSERT INTO `settings` (`id`, `key`, `value`, `description`) VALUES ('13', 'contact_phone', '', NULL);
INSERT INTO `settings` (`id`, `key`, `value`, `description`) VALUES ('14', 'contact_whatsapp', '3108690810', NULL);
INSERT INTO `settings` (`id`, `key`, `value`, `description`) VALUES ('15', 'contact_email', 'nal.lucho8@hotmail.com', NULL);
INSERT INTO `settings` (`id`, `key`, `value`, `description`) VALUES ('16', 'social_instagram', 'https://www.instagram.com/luis.a.190219/', NULL);
INSERT INTO `settings` (`id`, `key`, `value`, `description`) VALUES ('17', 'social_facebook', 'https://www.facebook.com/luis.morenojaramillo.5', NULL);
INSERT INTO `settings` (`id`, `key`, `value`, `description`) VALUES ('18', 'header_text', '', NULL);
INSERT INTO `settings` (`id`, `key`, `value`, `description`) VALUES ('19', 'hero_title', '', NULL);
INSERT INTO `settings` (`id`, `key`, `value`, `description`) VALUES ('20', 'hero_subtitle', '', NULL);
INSERT INTO `settings` (`id`, `key`, `value`, `description`) VALUES ('21', 'contact_address', '', NULL);
INSERT INTO `settings` (`id`, `key`, `value`, `description`) VALUES ('22', 'discount_stackable_mode', 'max_only', NULL);

DROP TABLE IF EXISTS `user_cart`;
CREATE TABLE `user_cart` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `product_id` int(11) unsigned NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_cart_user_id_foreign` (`user_id`),
  KEY `user_cart_product_id_foreign` (`product_id`),
  CONSTRAINT `user_cart_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `user_cart_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `user_cart` (`id`, `user_id`, `product_id`, `quantity`, `updated_at`) VALUES ('2', '2', '2', '1', '2026-09-23 15:48:32');

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `whatsapp` varchar(50) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','customer') NOT NULL DEFAULT 'customer',
  `is_loyal` tinyint(1) DEFAULT 0,
  `points` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` (`id`, `name`, `lastname`, `email`, `phone`, `whatsapp`, `password`, `role`, `is_loyal`, `points`, `created_at`, `updated_at`, `deleted_at`) VALUES ('1', 'Administrador', NULL, 'admin@dulcegracia.com', NULL, NULL, '$2y$10$t5KAZRqvnQVQKk6QAsWsbOoo2AyJgLmzE3ZLY7kVVH2kg9GuJLtfC', 'admin', '0', '0', '2026-08-29 16:12:44', '2026-09-23 16:08:23', NULL);
INSERT INTO `users` (`id`, `name`, `lastname`, `email`, `phone`, `whatsapp`, `password`, `role`, `is_loyal`, `points`, `created_at`, `updated_at`, `deleted_at`) VALUES ('2', 'Javier', 'Ramirez', 'jaralo45@hotmail.com', '3108690810', '3235808948', '$2y$10$EXTJLZbJvTxDY/AqUAK/VuYpdiIjXkQSQ/imfxeAOA..18/KyD8tm', 'customer', '0', '142', '2026-09-21 16:03:36', '2026-09-23 15:42:44', NULL);
INSERT INTO `users` (`id`, `name`, `lastname`, `email`, `phone`, `whatsapp`, `password`, `role`, `is_loyal`, `points`, `created_at`, `updated_at`, `deleted_at`) VALUES ('3', 'Daniel', 'Restrepo', 'jrpoalvarez@hotmail.com', '3117540084', '3117540084', '$2y$10$y0qkR0tVKQmZT/CDhTHraO/3Nc/cwJF1tP962XfOWpnW/iX3l1f3a', 'customer', '0', '117', '2026-09-23 16:02:28', '2026-09-29 14:22:01', NULL);

SET FOREIGN_KEY_CHECKS=1;
