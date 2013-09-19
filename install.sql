CREATE TABLE IF NOT EXISTS `prefix_minimarket_taxonomy` (
	`taxonomy_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`parent_id` int(11) DEFAULT '0',
	`taxonomy_sort` int(11) DEFAULT 0,
	`taxonomy_name` varchar(50) DEFAULT NULL,
	`taxonomy_url` varchar(50) DEFAULT NULL,
	`taxonomy_type` varchar(50) NOT NULL,
	`taxonomy_description` varchar(5000) DEFAULT NULL,
	`taxonomy_config` text DEFAULT NULL,
	PRIMARY KEY (`taxonomy_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `prefix_minimarket_link` (
	`link_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`object_id` int(11) DEFAULT 0,
	`object_type` varchar(50) NOT NULL,
	`parent_id` int(11) DEFAULT 0,
	PRIMARY KEY (`link_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
	`cost` varchar(10) NOT NULL,
	`description` varchar(200) DEFAULT NULL,
	`type` varchar(50) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `prefix_minimarket_product` (
	`product_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`product_name` varchar(200) NOT NULL,
	`product_url` varchar(200) NOT NULL,
	`product_brand` int(11) DEFAULT NULL,
	`product_price` float DEFAULT NULL,
	`product_weight` float DEFAULT NULL,
	`product_show` tinyint(1) DEFAULT NULL,
	`product_in_stock` tinyint(1) DEFAULT NULL,
	`product_manufacturer_code` varchar(200) DEFAULT NULL,
	`product_category` int(11) DEFAULT NULL,
	`product_main_photo_id` int(11) DEFAULT NULL,
	`product_text` varchar(5000) DEFAULT NULL,
	`product_photo` text DEFAULT NULL,
	PRIMARY KEY (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `prefix_minimarket_product_taxonomy` (
	`product_taxonomy_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`product_id` int(11) NOT NULL,
	`product_taxonomy_text` varchar(200) NOT NULL,
	`product_taxonomy_type` varchar(200) NOT NULL,
	PRIMARY KEY (`product_taxonomy_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `prefix_minimarket_product_property` (
	`product_property_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`product_id` int(11) NOT NULL,
	`property_id` int(11) NOT NULL,
	PRIMARY KEY (`product_property_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `prefix_minimarket_product_photo` (
  `product_photo_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) unsigned DEFAULT NULL,
  `product_photo_path` varchar(255) NOT NULL,
  `product_photo_description` text,
  `product_photo_target_tmp` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`product_photo_id`),
  KEY `product_id` (`product_id`),
  KEY `product_photo_target_tmp` (`product_photo_target_tmp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `prefix_minimarket_order` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`user_id` int(11) DEFAULT NULL,
	`key` varchar(32) NOT NULL,
	`cart_sum` float DEFAULT NULL,
	`client_name` varchar(200) DEFAULT NULL,
	`client_index` varchar(10) DEFAULT NULL,
	`client_address` varchar(200) DEFAULT NULL,
	`client_phone` varchar(200) DEFAULT NULL,
	`client_comment` varchar(200) DEFAULT NULL,
	`delivery_service_id` int(11) DEFAULT NULL,
	`delivery_service_time_from` int(3) DEFAULT NULL,
	`delivery_service_time_to` int(3) DEFAULT NULL,
	`delivery_service_sum` float DEFAULT NULL,
	`pay_system_id` INT(11) DEFAULT NULL,
	`time` int(11) DEFAULT NULL,
	`status` int(1) DEFAULT NULL,
	PRIMARY KEY (`id`),
	UNIQUE (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `prefix_minimarket_cart` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`order_id` INT(11) NOT NULL,
	`product_id` INT(11) NOT NULL,
	`product_count` INT(11) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `prefix_minimarket_pay_system` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`key`  varchar(100) NOT NULL,
	`name` varchar(200) NOT NULL,
	`activation` tinyint(1) DEFAULT NULL,
	PRIMARY KEY (`id`),
	UNIQUE (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `prefix_minimarket_storage` (
  `storage_key` varchar(100) NOT NULL,
  `storage_val` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`storage_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT IGNORE INTO `prefix_minimarket_pay_system` (`key`,`name`,`activation`) VALUES ('cash','Наличными',1);