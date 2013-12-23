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

class PluginMinimarket_ModulePay extends Module {

	protected $oMapper;
	
	/**
	 * Инициализация модуля
	 */
	public function Init() {
		$this->oMapper=Engine::GetMapper(__CLASS__);
	}
	
    /**
     * Возвращает список всех систем оплаты
     *
     * @return array
     */
	public function GetAllPaySystems() {
		return $this->oMapper->GetAllPaySystems();
	}
	
    /**
     * Получает объект системы оплаты по ID
     *
	 * @param string $iId    ID системы оплаты
     *
     * @return PluginMinimarket_ModulePay_EntityPaySystem|bool
     */
	public function GetPaySystemById($iId) {
		return $this->oMapper->GetPaySystemById($iId);
	}
	
    /**
     * Возвращает список доступных систем оплаты по объекту заказа
     *
	 * @param PluginMinimarket_ModulePay_EntityPaySystem $oOrder    Объект системы оплаты
     *
     * @return array
     */
	public function GetAvailablePaySystemsByOrder(PluginMinimarket_ModuleOrder_EntityOrder $oOrder) {
		return $this->oMapper->GetAvailablePaySystemsByOrder($oOrder);
	}
	
    /**
     * Получает список объектов систем оплаты по списку ID систем оплаты
     *
	 * @param string $aId    Список ID систем оплаты
     *
     * @return array
     */
	public function GetPaySystemsByArrayId($aId) {
		return $this->oMapper->GetPaySystemsByArrayId($aId);
	}
	
    /**
     * Добавление новой системы оплаты
     * Если запись с таким уникальным ключом уже существует, то обновляет ее
     *
	 * @param PluginMinimarket_ModulePay_EntityPaySystem $oPaySystem    Объект системы оплаты
     *
     * @return bool
     */
	public function AddOrUpdatePaySystem(PluginMinimarket_ModulePay_EntityPaySystem $oPaySystem) {
		return $this->oMapper->AddOrUpdatePaySystem($oPaySystem);
	}
	
    /**
     * Удаление системы оплаты
     *
	 * @param string $sKey    Ключ системы оплаты
	 * 
     * @return bool
     */
	public function DeletePaySystemByKey($sKey) {
		return $this->oMapper->DeletePaySystemByKey($sKey);
	}
}
?>