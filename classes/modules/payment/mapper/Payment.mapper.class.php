<?php

class PluginMinimarket_ModulePayment_MapperPayment extends Mapper {

	/**
	 * Добавляет объект платежа
	 * 
	 * @param PluginMinimarket_ModulePayment_EntityPayment $oPayment    Объект платежа
	 *
	 * @return int|bool
	 */
	public function AddPayment($oPayment) {
		$sql = "INSERT INTO " . Config::Get('db.table.minimarket_payment') . "
			SET 
				`pay_system_id` = ?d,
				`sum` = ?,
				`currency_id` = ?,
				`time_add` = ?d,
				`ip` = ?,
				`status` = ?d,
				`object_payment_id` = ?d,
				`object_payment_type` = ?
		";			
		if (
			$iId = $this->oDb->query(
				$sql,
				$oPayment->getPaySystemId(),
				$oPayment->getSum(),
				$oPayment->getCurrencyId(),
				$oPayment->getTimeAdd(),
				$oPayment->getIp(),
				$oPayment->getStatus(),
				$oPayment->getObjectPaymentId(),
				$oPayment->getObjectPaymentType()
			)
		) {
			return $iId;
		}		
		return false;
	}

	/**
	 * Возвращает объект платежа по ID
	 * 
	 * @param int $iId    ID платежа
	 *
	 * @return PluginMinimarket_ModulePayment_EntityPayment|bool
	 */
	public function GetPaymentById($iId) {
		$sql = "SELECT * FROM ".Config::Get('db.table.minimarket_payment')." 
			WHERE
				`id` = ?d ";
		if ($aRow = $this->oDb->selectRow($sql, $iId)) {
			return Engine::GetEntity('PluginMinimarket_ModulePayment_EntityPayment', $aRow);
		}
		return false;
	}
	
	/**
	 * Возвращает список платежей по ID валюты
	 * 
	 * @param int $iCurrencyId    ID валюты
	 *
	 * @return array
	 */
	public function GetPaymentsByCurrencyId($iCurrencyId) {
        $sql = "SELECT
					*
				FROM 
					" . Config::Get('db.table.minimarket_payment') . "
				WHERE
					`currency_id` = ?d
					";
        $aResult = array();
        if ($aRows = $this->oDb->select($sql, $iCurrencyId)) {
            foreach ($aRows as $aRow) {
                $aResult[] = Engine::GetEntity('PluginMinimarket_ModulePayment_EntityPayment', $aRow);
            }
        }
        return $aResult;
	}

	/**
	 * Возвращает объект платежа по типу и ID оплачиваемого объекта 
	 * 
	 * @param int    $iObjectPaymentId      ID оплачиваемого объекта
	 * @param string $sObjectPaymentType    Тип оплачиваемого объекта
	 *
	 * @return PluginMinimarket_ModulePayment_EntityPayment|bool
	 */
	public function GetPaymentByIdObjectPaymentAndTypeObjectPayment($iObjectPaymentId, $sObjectPaymentType) {
		$sql = "SELECT * FROM ".Config::Get('db.table.minimarket_payment')." 
			WHERE
				`object_payment_id` = ?d AND `object_payment_type` = ?
				";
		if ($aRow = $this->oDb->selectRow($sql, $iObjectPaymentId, $sObjectPaymentType)) {
			return Engine::GetEntity('PluginMinimarket_ModulePayment_EntityPayment', $aRow);
		}
		return false;
	}

	/**
	 * Обновляет платеж
	 * 
	 * @param PluginMinimarket_ModulePayment_EntityPayment $oPayment    Объект платежа
	 * 
	 * @return bool
	 */
	public function UpdatePayment(PluginMinimarket_ModulePayment_EntityPayment $oPayment) {
		$sql = "UPDATE " . Config::Get('db.table.minimarket_payment') . " 
			SET 
				`pay_system_id` = ?d,
				`time_sold` = ?d,
				`status` = ?d
			WHERE 
				`id` = ?d
		";			
		if ($this->oDb->query(
			$sql,
			$oPayment->getPaySystemId(),
			$oPayment->getTimeSold(),
			$oPayment->getStatus(),
			$oPayment->getId()
		)) {
			return true;
		}		
		return false;
	}
}
?>