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

CREATE TABLE IF NOT EXISTS `prefix_minimarket_product` (
	`product_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`product_name` varchar(200) NOT NULL,
	`product_url` varchar(200) NOT NULL,
	`product_brand` int(11) DEFAULT NULL,
	`product_price` float DEFAULT NULL,
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