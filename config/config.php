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
Config::Set('db.table.minimarket_product_photo', '___db.table.prefix___minimarket_product_photo');
Config::Set('db.table.minimarket_link', '___db.table.prefix___minimarket_link');
Config::Set('db.table.minimarket_delivery_service', '___db.table.prefix___minimarket_delivery_service');
Config::Set('db.table.minimarket_pay_system', '___db.table.prefix___minimarket_pay_system');
Config::Set('db.table.minimarket_storage', '___db.table.prefix___minimarket_storage');
Config::Set('db.table.minimarket_order', '___db.table.prefix___minimarket_order');
Config::Set('db.table.minimarket_cart', '___db.table.prefix___minimarket_cart');
Config::Set('db.table.minimarket_currency', '___db.table.prefix___minimarket_currency');
Config::Set('db.table.minimarket_payment', '___db.table.prefix___minimarket_payment');

Config::Set('router.page.mm_product', 'PluginMinimarket_ActionProduct');
Config::Set('router.page.mm_ajax', 'PluginMinimarket_ActionMmajax');
Config::Set('router.page.catalog', 'PluginMinimarket_ActionCatalog');
Config::Set('router.page.cart', 'PluginMinimarket_ActionCart');
Config::Set('router.page.order', 'PluginMinimarket_ActionOrder');
Config::Set('router.page.payment', 'PluginMinimarket_ActionPayment');

$config['admin']['order']['per_page'] = 10;    // Число заказов на одну страницу в админке

$config['product']['per_page'] = 20;             // Число товаров на одну страницу
$config['product']['img_max_width'] = 10000;     // максимальная ширина загружаемых изображений в пикселях
$config['product']['img_max_height'] = 10000;    // максимальная высота загружаемых изображений в пикселях

$config['settings']['factor'] = pow(10, 8);    // Коэффициент, на который умножаются цены для хранения в БД. 
                                               // Допускается изменение данного параметра только сразу после чистой установки плагина.
                                               // Иначе, расчеты цен будут не верными.

$config['product']['photoset']['photo_max_size'] = 6 * 1024;    // максимально допустимый размер фото, Kb
$config['product']['photoset']['count_photos_min'] = 1;         // минимальное количество фоток
$config['product']['photoset']['count_photos_max'] = 30;        // максимальное количество фоток
$config['product']['photoset']['per_page'] = 20;                // число фоток для одновременной загрузки
$config['product']['photoset']['size'] = array(
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
);

/**
 * Список стран, которые будут доступны при создании группы местоположений
 * Если не указана ни одна страна, то из БД будут браться все страны
 */
$config['settings']['location']['counties'] = array('RU', 'UA', 'BY');

$config['module']['payment']['logs']['error'] = 'module_payment_error.log';    // null либо имя файла для лога ошибок

return $config;
?>