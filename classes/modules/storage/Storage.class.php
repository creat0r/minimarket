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

class PluginMinimarket_ModuleStorage extends Module {

	protected $oMapper;
	
	/**
	 * Инициализация модуля
	 */
	public function Init() {
		$this->oMapper=Engine::GetMapper(__CLASS__);
	}
	
    /**
     * Сохранение данных в хранилище
     *
     * @param array    $aData
     *
     * @return bool
     */
	public function UpdateStorage($aData) {
		return $this->oMapper->UpdateStorage($aData);
	}

    /**
     * Возвращает данные из хранилища
     *
     * @param string    $sPrefix
     *
     * @return array
     */
    public function GetStorage($sPrefix = null) {
		return $this->oMapper->GetStorage($sPrefix);
    }
}
?>