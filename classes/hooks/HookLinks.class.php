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

/**
 * Регистрация хука для вывода ссылок
 *
 * @package hooks
 * @since 1.0
 */
class PluginMinimarket_HookLinks extends Hook {
	/**
	 * Регистрируем хуки
	 */
	public function RegisterHook() {
		$this->AddHook('template_admin_menu_items_end','AdminMarket');
		$this->AddHook('template_menu_create_item_select','CreateItemSelect');
		$this->AddHook('template_write_item','WindowWrite');
		$this->AddHook('template_menu_create_item','MenuCreateItem');
		$this->AddHook('template_main_menu_item','MainMenuItem');
	}
	/**
	 * Обработка хука вывода ссылки в админке
	 *
	 * @return string
	 */
	public function MainMenuItem() {
		return $this->Viewer_Fetch(Plugin::GetTemplatePath(__CLASS__).'main_menu_item.tpl');
	}
	public function AdminMarket() {
		return $this->Viewer_Fetch(Plugin::GetTemplatePath(__CLASS__).'links.tpl');
	}
	public function CreateItemSelect() {
		return $this->Viewer_Fetch(Plugin::GetTemplatePath(__CLASS__).'menu_create_item_select.tpl');
	}
	public function WindowWrite() {
		return $this->Viewer_Fetch(Plugin::GetTemplatePath(__CLASS__).'window_write.tpl');
	}
	public function MenuCreateItem() {
		return $this->Viewer_Fetch(Plugin::GetTemplatePath(__CLASS__).'menu_create_item.tpl');
	}
}
?>