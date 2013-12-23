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

class PluginMinimarket_ModuleGeo extends PluginMinimarket_Inherits_ModuleGeo {

	protected $oMapper;
	
	/**
	 * Инициализация модуля
	 */
	public function Init() {
		parent::Init();
		$this->oMapper = Engine::GetMapper(__CLASS__);
		/**
		 * Добавляем новый тип объекта
		 */
		$this->AddTargetType('order');
	}
	
    /**
     * Возвращает список стран, указанных в конфиге
     *
     * @return array
     */
	public function GetCountriesByConfig() {
        return $this->oMapper->GetCountriesByConfig();
	}
	
    /**
     * Возвращает список регионов по списку стран
     *
     * @param array $aCountries    Список ID стран
     *
     * @return array
     */
	public function GetRegionsByCountries($aCountries) {
        return $this->oMapper->GetRegionsByCountries($aCountries);
	}
	
    /**
     * Возвращает список городов по списку регионов
     *
     * @param array $aRegions    Список ID регионов
     *
     * @return array
     */
	public function GetCitiesByRegions($aRegions) {
        return $this->oMapper->GetCitiesByRegions($aRegions);
	}

    public function CheckTargetOrder($iTargetId) {
		return true;
    }
}
?>