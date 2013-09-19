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

class PluginMinimarket_ModuleDelivery_MapperDelivery extends Mapper {
    /**
     * Создание службы доставки
     *
     * @param PluginMinimarket_ModuleDelivery_EntityService $oDeliveryService			Объект службы доставки
     *
     * @return int|bool
     */
	public function AddDeliveryService(PluginMinimarket_ModuleDelivery_EntityService $oDeliveryService) {
        $sql = "INSERT INTO " . Config::Get('db.table.minimarket_delivery_service') . "
			(name,
			activation,
			time_from,
			time_to,
			weight_from,
			weight_to,
			order_value_from,
			order_value_to,
			processing_costs,
			cost_calculation,
			cost,
			description,
			type
			)
			VALUES(?,?d,?d,?d,?,?,?,?,?,?d,?,?,?)
		";
        $nId = $this->oDb->query(
            $sql, $oDeliveryService->getName(), $oDeliveryService->getActivation(), $oDeliveryService->getTimeFrom(), 
			$oDeliveryService->getTimeTo(), $oDeliveryService->getWeightFrom(), $oDeliveryService->getWeightTo(), 
			$oDeliveryService->getOrderValueFrom(), $oDeliveryService->getOrderValueTo(), $oDeliveryService->getProcessingCosts(), 
			$oDeliveryService->getCostCalculation(), $oDeliveryService->getCost(), $oDeliveryService->getDescription(),
			$oDeliveryService->getType()
        );
        if ($nId) {
            return $nId;
        }
        return false;
	}
	
    /**
     * Возвращает список служб доставки по типу
     *
	 * @param string $sType			Тип службы доставки
     *
     * @return array
     */
	public function GetDeliveryServicesByType($sType) {
        $sql = "SELECT
					*
				FROM 
					" . Config::Get('db.table.minimarket_delivery_service') . "
				WHERE
					type = ?
					";
        $aDeliveryServices = array();
        if ($aRows = $this->oDb->select($sql,$sType)) {
            foreach ($aRows as $aDeliveryService) {
                $aDeliveryServices[] = Engine::GetEntity('PluginMinimarket_ModuleDelivery_EntityService', $aDeliveryService);
            }
        }
        return $aDeliveryServices;
	}
	
    /**
     * Возвращает список активированных служб доставки по ID города, для которых они актуальны (настраивается в админке)
     *
	 * @param string $iCity			ID города
     *
     * @return array
     */
	public function GetActivationDeliveryServicesByCity($iCity) {
        $sql = "SELECT
					*
				FROM 
					" . Config::Get('db.table.minimarket_delivery_service') . "
				WHERE
					id IN (
						SELECT 
								parent_id
							FROM 
								" . Config::Get('db.table.minimarket_link') . "
							WHERE
								object_id IN (
									SELECT 
										parent_id
									FROM 
										" . Config::Get('db.table.minimarket_link') . "
									WHERE
										object_id = ? AND object_type = 'location_group_city'
								) AND object_type = 'delivery_service_location_group'
					) AND activation = 1
					";
        $aDeliveryServices = array();
        if ($aRows = $this->oDb->select($sql,$iCity)) {
            foreach ($aRows as $aDeliveryService) {
                $aDeliveryServices[] = Engine::GetEntity('PluginMinimarket_ModuleDelivery_EntityService', $aDeliveryService);
            }
        }
        return $aDeliveryServices;
	}
	
    /**
     * Возвращает объект службы доставки
     *
     * @param int $iId			ID службы доставки
     *
     * @return PluginMinimarket_ModuleDelivery_EntityService|null
     */
	public function GetDeliveryServiceById($iId) {
        $sql = "SELECT
						*
					FROM
						" . Config::Get('db.table.minimarket_delivery_service') . "
					WHERE
						id = ?d
					";
        if ($aRow = $this->oDb->selectRow($sql, $iId)) {
            return Engine::GetEntity('PluginMinimarket_ModuleDelivery_EntityService', $aRow);
        }
        return null;
	}
	
    /**
     * Возвращает объект службы доставки по ключу
     *
     * @param int $sKey			Ключ службы доставки
     *
     * @return PluginMinimarket_ModuleDelivery_EntityService|null
     */
	public function GetDeliveryServiceByKey($sKey) {
        $sql = "SELECT
						*
					FROM
						" . Config::Get('db.table.minimarket_delivery_service') . "
					WHERE
						`key` = ?
					";
        if ($aRow = $this->oDb->selectRow($sql, $sKey)) {
            return Engine::GetEntity('PluginMinimarket_ModuleDelivery_EntityService', $aRow);
        }
        return null;
	}
	
    /**
     * Обновление службы доставки
     *
     * @param PluginMinimarket_ModuleDelivery_EntityService $oDeliveryService			Объект службы доставки
     *
     * @return bool
     */
	public function UpdateDeliveryService(PluginMinimarket_ModuleDelivery_EntityService $oDeliveryService) {
       $sql = "UPDATE " . Config::Get('db.table.minimarket_delivery_service') . "
			SET 
				name = ?,
				activation = ?d,
				time_from = ?d,
				time_to = ?d,
				weight_from = ?,
				weight_to = ?,
				order_value_from = ?,
				order_value_to = ?,
				processing_costs = ?,
				cost_calculation = ?,
				cost = ?,
				description = ?
			WHERE
				id = ?d
		";
        $bResult = $this->oDb->query(
            $sql, $oDeliveryService->getName(), $oDeliveryService->getActivation(), $oDeliveryService->getTimeFrom(), 
			$oDeliveryService->getTimeTo(), $oDeliveryService->getWeightFrom(), $oDeliveryService->getWeightTo(), 
			$oDeliveryService->getOrderValueFrom(), $oDeliveryService->getOrderValueTo(), $oDeliveryService->getProcessingCosts(), 
			$oDeliveryService->getCostCalculation(), $oDeliveryService->getCost(), $oDeliveryService->getDescription(), 
			$oDeliveryService->getId()
        );
        if ($bResult !== false) {
            return true;
        }
        return false;
	}
	
    /**
     * Удаляет службу доставки
	 *
	 * @param PluginMinimarket_ModuleDelivery_EntityService $oDeliveryService			Объект службы доставки
     *
     * @return bool
     */
	public function DeleteDeliveryService($oDeliveryService) {
        $sql = "
            DELETE FROM " . Config::Get('db.table.minimarket_delivery_service') . "
            WHERE id = ?d ";
        return ($this->oDb->query($sql, $oDeliveryService->getId()) !== false);
	}
	
    /**
     * Удаляет службу доставки по ключу
	 *
	 * @param  $sKey			Ключ службы доставки
     * @return bool
     */
	public function DeleteDeliveryServiceByKey($sKey) {
        $sql = "
            DELETE FROM " . Config::Get('db.table.minimarket_delivery_service') . "
            WHERE `key` = ? ";
        return ($this->oDb->query($sql, $sKey) !== false);
	}
	
    /**
     * Добавление новой службы доставки
     * Если запись с таким уникальным ключом уже существует, то обновляет ее
     *
	 * @param PluginMinimarket_ModuleDelivery_EntityService $oDeliveryService			Объект службы доставки
     *
     * @return bool
     */
	public function AddOrUpdateDeliveryService($oDeliveryService) {
       $sql = "INSERT INTO " . Config::Get('db.table.minimarket_delivery_service') . "			
			(`key`,
			name,
			activation,
			description,
			type
			)
			VALUES(?,?,?d,?,?)
			ON DUPLICATE KEY UPDATE 
				name = ?,
				activation = ?d,
				description = ?,
				type = ?
		";
        $bResult = $this->oDb->query(
            $sql, 
			$oDeliveryService->getKey(),
			$oDeliveryService->getName(),
			$oDeliveryService->getActivation(),
			$oDeliveryService->getDescription(),
			$oDeliveryService->getType(),
			$oDeliveryService->getName(),
			$oDeliveryService->getActivation(),
			$oDeliveryService->getDescription(),
			$oDeliveryService->getType()
        );
        if ($bResult !== false) {
            return true;
        }
        return false;
	}
	
    /**
     * Возвращает список служб доставки по списку ID
     *
	 * @param array $aDeliveryServiceId			Список ID служб доставки
     *
     * @return array
     */
	public function GetDeliveryServicesByArrayId($aDeliveryServiceId) {
        if (!is_array($aDeliveryServiceId) || count($aDeliveryServiceId) == 0) {
            return array();
        }
        $sql = "SELECT
					*
				FROM
					" . Config::Get('db.table.minimarket_delivery_service') . "
				WHERE
					id IN (?a)
					";
        $aDeliveryServices = array();
        if ($aRows = $this->oDb->select($sql,$aDeliveryServiceId)) {
            foreach ($aRows as $aDeliveryService) {
				$oDeliveryService = Engine::GetEntity('PluginMinimarket_ModuleDelivery_EntityService', $aDeliveryService);
                $aDeliveryServices[$oDeliveryService->getId()] = $oDeliveryService;
            }
        }
        return $aDeliveryServices;
	}
}
?>