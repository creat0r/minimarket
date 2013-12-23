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

class PluginMinimarket_ModulePay_MapperPay extends Mapper {

    /**
     * Возвращает список всех систем оплаты
     *
     * @return array
     */
	public function GetAllPaySystems() {
        $sql = "SELECT
						*
					FROM
						" . Config::Get('db.table.minimarket_pay_system');
        $aResult = array();
        $aRows = $this->oDb->select($sql);
        if ($aRows) {
            foreach ($aRows as $aRow) {
                $aResult[] = Engine::GetEntity('PluginMinimarket_ModulePay_EntityPaySystem', $aRow);
			}
        }
        return $aResult;
	}
	
    /**
     * Получает объект системы оплаты по ID
     *
	 * @param string $iId    ID системы оплаты
     *
     * @return PluginMinimarket_ModulePay_EntityPaySystem|bool
     */
	public function GetPaySystemById($iId) {
        $sql = "SELECT
						*
					FROM
						" . Config::Get('db.table.minimarket_pay_system') . "
					WHERE
						id = ?d
					";
        if ($aRow = $this->oDb->selectRow($sql, $iId)) {
            return Engine::GetEntity('PluginMinimarket_ModulePay_EntityPaySystem', $aRow);
        }
        return false;
	}

    /**
     * Добавление новой системы оплаты
     * Если запись с таким уникальным ключом уже существует, то обновляет ее
     *
	 * @param PluginMinimarket_ModulePay_EntityPaySystem $oPaySystem    Объект системы оплаты
     *
     * @return bool
     */
	public function AddOrUpdatePaySystem($oPaySystem) {
       $sql = "INSERT INTO " . Config::Get('db.table.minimarket_pay_system') . "			
			(`key`,
			name,
			activation
			)
			VALUES(?,?,?)
			ON DUPLICATE KEY UPDATE 
				name = ?,
				activation = ?
		";
        $bResult = $this->oDb->query(
            $sql, 
			$oPaySystem->getKey(),
			$oPaySystem->getName(),
			$oPaySystem->getActivation(),
			$oPaySystem->getName(),
			$oPaySystem->getActivation()
        );
        if ($bResult !== false) {
            return true;
        }
        return false;
	}
	
    /**
     * Удаление системы оплаты
     *
	 * @param string $sKey    Ключ системы оплаты
	 * 
     * @return bool
     */
	public function DeletePaySystemByKey($sKey) {
        $sql = "DELETE FROM " . Config::Get('db.table.minimarket_pay_system') . "
			WHERE
				`key` = ?
		";
        return $this->oDb->query($sql, $sKey) !== false;
	}
	
    /**
     * Получает список объектов систем оплаты по списку ID систем оплаты
     *
	 * @param string $aId    Список ID систем оплаты
     *
     * @return array
     */
	public function GetPaySystemsByArrayId($aId) {
        if (!is_array($aId) || count($aId) == 0) {
            return array();
        }
        $sql = "SELECT
						*
					FROM
						" . Config::Get('db.table.minimarket_pay_system') . "
					WHERE
						id IN(?a)
					";
        $aPaySystems = array();
        if ($aRows = $this->oDb->select($sql, $aId)) {
            foreach ($aRows as $aRow) {
				$oPaySystem = Engine::GetEntity('PluginMinimarket_ModulePay_EntityPaySystem', $aRow);
                $aPaySystems[$oPaySystem->getId()] = $oPaySystem;
            }
        }
        return $aPaySystems;
	}
	
    /**
     * Возвращает список доступных систем оплаты по объекту заказа
     *
	 * @param PluginMinimarket_ModulePay_EntityPaySystem $oOrder    Объект системы оплаты
     *
     * @return array
     */
	public function GetAvailablePaySystemsByOrder($oOrder) {
        $sql = "SELECT
						*
					FROM
						" . Config::Get('db.table.minimarket_pay_system') . "
					WHERE
						id IN(
							SELECT 
								object_id
							FROM 
								" . Config::Get('db.table.minimarket_link') . "
							WHERE
								parent_id = ? AND object_type = 'delivery_service_pay_system'
						) AND activation = 1
					";
        $aPaySystems = array();
        if ($aRows = $this->oDb->select($sql, $oOrder->getDeliveryServiceId())) {
            foreach ($aRows as $aRow) {
                $aPaySystems[] = Engine::GetEntity('PluginMinimarket_ModulePay_EntityPaySystem', $aRow);
            }
        }
        return $aPaySystems;
	}
}
?>