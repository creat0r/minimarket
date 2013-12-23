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

class PluginMinimarket_ModuleLink extends Module {

	protected $oMapper;
	
	/**
	 * Инициализация модуля
	 */
	public function Init() {
		$this->oMapper=Engine::GetMapper(__CLASS__);
	}
	
    /**
     * Добавляет связь
     *
     * @param PluginMinimarket_ModuleLink_EntityLink $oLink    Объект связи
     *
     * @return int|bool
     */
	public function AddLink(PluginMinimarket_ModuleLink_EntityLink $oLink) {
		return $this->oMapper->AddLink($oLink);
	}
	
    /**
     * Добавляет массив связей одним запросом
     *
     * @param array $aObjectCity    Массив связей
     *
     * @return int|bool
     */
	public function AddLinks($aObjectCity) {
		return $this->oMapper->AddLinks($aObjectCity);
	}
	
    /**
     * Удаляет связи по ID родителя и типу объекта
     *
     * @param int    $iParentId      ID родителя
     * @param string $sObjectType    Тип объекта
     *
     * @return bool
     */
	public function DeleteLinkByParentAndType($iParentId, $sObjectType) {
		return $this->oMapper->DeleteLinkByParentAndType($iParentId, $sObjectType);
	}
	
    /**
     * Возвращает список связей по ID родителя и типу объекта
     *
     * @param int    $iParentId      ID родителя
     * @param string $sObjectType    Тип объекта
     *
     * @return array
     */
	public function GetLinksByParentAndType($iParentId, $sObjectType) {
		return $this->oMapper->GetLinksByParentAndType($iParentId, $sObjectType);
	}
	
    /**
     * Возвращает список ID связей по списку ID родителей и типу объекта
     *
     * @param int    $aParentId      ID родителя
     * @param string $sObjectType    Тип объекта
     *
     * @return array
     */
    public function GetLinksByParentsAndType($aParentId, $sObjectType) {
		return $this->oMapper->GetLinksByParentsAndType($aParentId, $sObjectType);
    }
	
    /**
     * Возвращает список связей по типу объекта
     *
     * @param string $sObjectType    Тип объекта
     *
     * @return array
     */
	public function GetLinksByType($sObjectType) {
		return $this->oMapper->GetLinksByType($sObjectType);
	}
	
    /**
     * Возвращает списком количество повторений связей по ID родителя и типу объекта
     *
     * @param int    $iParentId      ID родителя
     * @param string $sObjectType    Тип объекта
     *
     * @return array
     */
	public function GetCountLinksByParentAndType($iParentId, $sObjectType) {
		return $this->oMapper->GetCountLinksByParentAndType($iParentId, $sObjectType);
	}

}
?>