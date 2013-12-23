CREATE TABLE IF NOT EXISTS `prefix_minimarket_taxonomy` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`parent_id` int(11) DEFAULT '0',
	`sort` int(11) DEFAULT 0,
	`name` varchar(50) DEFAULT NULL,
	`url` varchar(50) DEFAULT NULL,
	`type` varchar(50) NOT NULL,
	`description` varchar(5000) DEFAULT NULL,
	PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS `prefix_minimarket_link` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`object_id` int(11) DEFAULT 0,
	`object_type` varchar(50) NOT NULL,
	`parent_id` int(11) DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS `prefix_minimarket_delivery_service` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`key`  varchar(100) DEFAULT NULL,
	`name` varchar(50) NOT NULL,
	`activation` tinyint(1) DEFAULT NULL,
	`time_from` int(11) DEFAULT NULL,
	`time_to` int(11) DEFAULT NULL,
	`weight_from` float DEFAULT NULL,
	`weight_to` float DEFAULT NULL,
	`order_value_from` float DEFAULT NULL,
	`order_value_to` float DEFAULT NULL,
	`processing_costs` float DEFAULT NULL,
	`cost_calculation` int(1) NOT NULL,
	`cost` bigint(20) NOT NULL,
	`currency` int(11) DEFAULT NULL,
	`description` varchar(200) DEFAULT NULL,
	`type` varchar(50) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE (`key`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS `prefix_minimarket_product` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(200) NOT NULL,
	`url` varchar(200) NOT NULL,
	`brand` int(11) DEFAULT NULL,
	`price` bigint(20) DEFAULT NULL,
	`currency` int(11) NOT NULL,
	`weight` int(11) DEFAULT NULL,
	`show` tinyint(1) DEFAULT NULL,
	`in_stock` tinyint(1) DEFAULT NULL,
	`manufacturer_code` varchar(200) DEFAULT NULL,
	`category` int(11) DEFAULT NULL,
	`main_photo_id` int(11) DEFAULT NULL,
	`text` varchar(5000) DEFAULT NULL,
	PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS `prefix_minimarket_product_taxonomy` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`product_id` int(11) NOT NULL,
	`text` varchar(200) NOT NULL,
	`type` varchar(200) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS `prefix_minimarket_product_photo` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`product_id` int(11) unsigned DEFAULT NULL,
	`path` varchar(255) NOT NULL,
	`description` text,
	`target_tmp` varchar(40) DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `product_id` (`product_id`),
	KEY `target_tmp` (`target_tmp`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS `prefix_minimarket_order` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`user_id` int(11) DEFAULT NULL,
	`key` varchar(32) NOT NULL,
	`client_name` varchar(200) DEFAULT NULL,
	`client_index` varchar(10) DEFAULT NULL,
	`client_address` varchar(200) DEFAULT NULL,
	`client_phone` varchar(200) DEFAULT NULL,
	`client_comment` varchar(200) DEFAULT NULL,
	`delivery_service_id` int(11) DEFAULT NULL,
	`time_order_init` int(11) DEFAULT NULL,
	`time_selected_pay_system` int(11) DEFAULT NULL,
	`time_payment_success` int(11) DEFAULT NULL,
	`status` int(1) DEFAULT NULL,
	PRIMARY KEY (`id`),
	UNIQUE (`key`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS `prefix_minimarket_cart` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`order_id` INT(11) NOT NULL,
	`product_id` INT(11) NOT NULL,
	`product_count` INT(11) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS `prefix_minimarket_pay_system` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`key`  varchar(100) NOT NULL,
	`name` varchar(200) NOT NULL,
	`activation` tinyint(1) DEFAULT NULL,
	PRIMARY KEY (`id`),
	UNIQUE (`key`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS `prefix_minimarket_storage` (
  `key` varchar(100) NOT NULL,
  `val` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`key`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS `prefix_minimarket_currency` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`key` varchar(5) NOT NULL,
	`nominal` int(5) NOT NULL,
	`course` bigint(20) NOT NULL,
	`format` varchar(50) NOT NULL,
	`decimal_places` int(1) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE (`key`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS `prefix_minimarket_payment` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`pay_system_id` int(11) DEFAULT NULL,
	`sum` bigint(20) NOT NULL,
	`decimal_places` int(1) NOT NULL,
	`currency_id` int(11) NOT NULL,
	`time_add` int(11) NOT NULL,
	`time_sold` int(11) DEFAULT NULL,
	`ip` varchar(20) NOT NULL,
	`status` int(1) NOT NULL,
	`object_payment_id` int(11) NOT NULL,
	`object_payment_type` varchar(50) NOT NULL,
	PRIMARY KEY (`id`),
	KEY `currency_id` (`currency_id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;

--
-- Добавление системы оплаты
--
INSERT IGNORE INTO `prefix_minimarket_pay_system` (`key`, `name`, `activation`) VALUES ('cash', 'Наличными', 1);
--
-- Добавление валюты
--
INSERT IGNORE INTO `prefix_minimarket_currency` (`key`, `nominal`, `course`, `format`, `decimal_places`) VALUES ('USD', 1, 3033000000, '# $', 2);
--
-- Добавление настроек
--
INSERT IGNORE INTO `prefix_minimarket_storage` (`key`, `val`) VALUES ('minimarket_settings_base_default_currency', 'USD');
INSERT IGNORE INTO `prefix_minimarket_storage` (`key`, `val`) VALUES ('minimarket_settings_base_cart_currency', 'USD');