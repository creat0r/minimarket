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
 * Запрещаем напрямую через браузер обращение к этому файлу.
 */
if (!class_exists('Plugin')) {
	die('Hacking attempt!');
}

class PluginMinimarket extends Plugin {
	/**
	 * Инициализация плагина
	 */
	public function Init() {
		$this->Viewer_AppendScript(Plugin::GetTemplatePath(__CLASS__).'js/minimarket.js');
		$this->Viewer_AppendScript(Plugin::GetTemplatePath(__CLASS__).'js/minimarket_photoset.js');
		$this->Viewer_AppendScript(Plugin::GetTemplatePath(__CLASS__).'js/chosen.jquery.min.js');
		$this->Viewer_AppendScript(Plugin::GetTemplatePath(__CLASS__).'js/jquery.liTranslit.js');
		
		$this->Viewer_AppendStyle(Plugin::GetTemplatePath(__CLASS__).'css/minimarket.css');
		$this->Viewer_AppendStyle(Plugin::GetTemplatePath(__CLASS__).'css/chosen.min.css');
	}
	/**
	 * Активация плагина miniMarket.
	 * Создание таблиц в базе данных при их отсутствии.
	 */
	public function Activate() {
		$this->ExportSQL(dirname(__FILE__).'/install.sql');
		return true;
	}
    protected $aInherits = array(
        'action' => array(
            'ActionAdmin'
        ),
        'module' => array(
            'ModuleGeo'
        ),
    );
    protected $aDelegates = array(
        'template' => array(
            'menu.catalog.tpl' => '_menu.catalog.tpl',
        ),
    );
}
?>