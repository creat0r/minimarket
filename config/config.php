<?php
/*-------------------------------------------------------
*
*	Plugin "miniMarket"
*	Author: Stepanov Mark (nikto)
*	Official site: http://altocms.ru/profile/nikto/
*	Contact e-mail: markus1024@yandex.ru
*
---------------------------------------------------------
*/

Config::Set('db.table.minimarket_taxonomy', '___db.table.prefix___minimarket_taxonomy');
Config::Set('db.table.minimarket_product', '___db.table.prefix___minimarket_product');
Config::Set('db.table.minimarket_product_taxonomy', '___db.table.prefix___minimarket_product_taxonomy');
Config::Set('db.table.minimarket_product_property', '___db.table.prefix___minimarket_product_property');
Config::Set('db.table.minimarket_product_photo', '___db.table.prefix___minimarket_product_photo');

Config::Set('router.page.mm_product', 'PluginMinimarket_ActionProduct');
Config::Set('router.page.mm_ajax', 'PluginMinimarket_ActionMmajax');
Config::Set('router.page.catalog', 'PluginMinimarket_ActionCatalog');

Config::Set('minimarket.product.per_page', 20);			// Число товаров на одну страницу

Config::Set('minimarket.img_max_width', 10000);			// максимальная ширина загружаемых изображений в пикселях
Config::Set('minimarket.img_max_height', 10000);		// максимальная высота загружаемых изображений в пикселях

Config::Set('minimarket.product.photoset.photo_max_size',6*1024);	// максимально допустимый размер фото, Kb
Config::Set('minimarket.product.photoset.count_photos_min',1);		// минимальное количество фоток
Config::Set('minimarket.product.photoset.count_photos_max',30);		// максимальное количество фоток
Config::Set('minimarket.product.photoset.per_page',20);				// число фоток для одновременной загрузки
Config::Set(
	'minimarket.product.photoset.size', 
	array(
		array(
			'w' => 375,
			'h' => null,
			'crop' => false,
		),
		array(
			'w' => 150,
			'h' => null,
			'crop' => false,
		),
		array(
			'w' => 100,
			'h' => 65,
			'crop' => true,
		),
		array(
			'w' => 36,
			'h' => null,
			'crop' => false,
		)
	)
);

return $config;
?>