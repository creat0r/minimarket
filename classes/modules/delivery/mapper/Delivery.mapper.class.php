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
     * �������� ������ ��������
     *
     * @param PluginMinimarket_ModuleDelivery_EntityDeliveryService $oDeliveryService    ������ ������ ��������
     *
     * @return int|bool
     */
	public function AddDeliveryService(PluginMinimarket_ModuleDelivery_EntityDeliveryService $oDeliveryService) {
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
			currency,
			description,
			type
			)
			VALUES(?,?d,?d,?d,?,?,?,?,?,?d,?,?d,?,?)
		";
        $nId = $this->oDb->query(
            $sql,
			$oDeliveryService->getName(),
			$oDeliveryService->getActivation(),
			$oDeliveryService->getTimeFrom(),
			$oDeliveryService->getTimeTo(),
			$oDeliveryService->getWeightFrom(),
			$oDeliveryService->getWeightTo(),
			$oDeliveryService->getOrderValueFrom(),
			$oDeliveryService->getOrderValueTo(),
			$oDeliveryService->getProcessingCosts(),
			$oDeliveryService->getCostCalculation(),
			$oDeliveryService->getCost(),
			$oDeliveryService->getCurrency(),
			$oDeliveryService->getDescription(),
			$oDeliveryService->getType()
        );
        if ($nId) {
            return $nId;
        }
        return false;
	}
	
    /**
     * ���������� ������ ����� �������� �� ����
     *
	 * @param string $sType    ��� ������ ��������
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
        if ($aRows = $this->oDb->select($sql, $sType)) {
            foreach ($aRows as $aDeliveryService) {
                $aDeliveryServices[] = Engine::GetEntity('PluginMinimarket_ModuleDelivery_EntityDeliveryService', $aDeliveryService);
            }
        }
        return $aDeliveryServices;
	}
	
    /**
     * ���������� ������ �������������� ����� �������� �� ID ������, ��� ������� ��� ��������� (������������� � �������)
     *
	 * @param string $iCity    ID ������
     *
     * @return array
     */
	public function GetActivationDeliveryServicesByCity($iCity) {
        $sql_lgc = "SELECT 
					parent_id
				FROM 
					" . Config::Get('db.table.minimarket_link') . "
				WHERE
					object_id = ? AND object_type = 'location_group_city'
					";
        $aLocationGroupCity = array();
        if ($aRows = $this->oDb->select($sql_lgc,$iCity)) {
            foreach ($aRows as $aRow) {
                $aLocationGroupCity[] = $aRow['parent_id'];
            }
        }
				
        $aDeliveryServiceLocationGroup = array();
		if (!empty($aLocationGroupCity)) {
			$sql_dslg = "SELECT 
							parent_id
						FROM 
							" . Config::Get('db.table.minimarket_link') . "
						WHERE
							object_id IN ('" . join("', '", $aLocationGroupCity) . "') 
							AND object_type = 'delivery_service_location_group' ";
			if ($aRows = $this->oDb->select($sql_dslg,$iCity)) {
				foreach ($aRows as $aRow) {
					$aDeliveryServiceLocationGroup[] = $aRow['parent_id'];
				}
			}
		}
		
        $aDeliveryServices = array();
		if (!empty($aLocationGroupCity) && !empty($aDeliveryServiceLocationGroup)) {
			$sql = "SELECT
						*
					FROM 
						" . Config::Get('db.table.minimarket_delivery_service') . "
					WHERE
						id IN (
							'" . join("', '", $aDeliveryServiceLocationGroup) . "'
						) AND activation = 1
						";
			if ($aRows = $this->oDb->select($sql,$iCity)) {
				foreach ($aRows as $aDeliveryService) {
					$aDeliveryServices[] = Engine::GetEntity('PluginMinimarket_ModuleDelivery_EntityDeliveryService', $aDeliveryService);
				}
			}
		}
		
        return $aDeliveryServices;
	}
	
    /**
     * ���������� ������ ������ ��������
     *
     * @param int $iId    ID ������ ��������
     *
     * @return PluginMinimarket_ModuleDelivery_EntityDeliveryService|null
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
            return Engine::GetEntity('PluginMinimarket_ModuleDelivery_EntityDeliveryService', $aRow);
        }
        return null;
	}
	
    /**
     * ���������� ������ ������ �������� �� �����
     *
     * @param int $sKey    ���� ������ ��������
     *
     * @return PluginMinimarket_ModuleDelivery_EntityDeliveryService|null
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
            return Engine::GetEntity('PluginMinimarket_ModuleDelivery_EntityDeliveryService', $aRow);
        }
        return null;
	}
	
    /**
     * ���������� ������ ��������
     *
     * @param PluginMinimarket_ModuleDelivery_EntityDeliveryService $oDeliveryService    ������ ������ ��������
     *
     * @return bool
     */
	public function UpdateDeliveryService(PluginMinimarket_ModuleDelivery_EntityDeliveryService $oDeliveryService) {
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
				currency = ?d,
				description = ?
			WHERE
				id = ?d
		";
        $bResult = $this->oDb->query(
            $sql,
			$oDeliveryService->getName(),
			$oDeliveryService->getActivation(),
			$oDeliveryService->getTimeFrom(), 
			$oDeliveryService->getTimeTo(),
			$oDeliveryService->getWeightFrom(),
			$oDeliveryService->getWeightTo(), 
			$oDeliveryService->getOrderValueFrom(),
			$oDeliveryService->getOrderValueTo(),
			$oDeliveryService->getProcessingCosts(), 
			$oDeliveryService->getCostCalculation(),
			$oDeliveryService->getCost(),
			$oDeliveryService->getCurrency(),
			$oDeliveryService->getDescription(), 
			$oDeliveryService->getId()
        );
        if ($bResult !== false) {
            return true;
        }
        return false;
	}
	
    /**
     * ������� ������ ��������
	 *
	 * @param PluginMinimarket_ModuleDelivery_EntityDeliveryService $oDeliveryService    ������ ������ ��������
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
     * ������� ������ �������� �� �����
	 *
	 * @param strung $sKey    ���� ������ ��������
	 *
     * @return bool
     */
	public function DeleteDeliveryServiceByKey($sKey) {
        $sql = "
            DELETE FROM " . Config::Get('db.table.minimarket_delivery_service') . "
            WHERE `key` = ? ";
        return ($this->oDb->query($sql, $sKey) !== false);
	}
	
    /**
     * ���������� ����� ������ ��������
     * ���� ������ � ����� ���������� ������ ��� ����������, �� ��������� ��
     *
	 * @param PluginMinimarket_ModuleDelivery_EntityDeliveryService $oDeliveryService    ������ ������ ��������
     *
     * @return bool
     */
	public function AddOrUpdateDeliveryService($oDeliveryService) {
       $sql = "INSERT INTO " . Config::Get('db.table.minimarket_delivery_service') . "			
			(`key`,
			name,
			activation,
			currency,
			description,
			type
			)
			VALUES(?,?,?d,?d,?,?)
			ON DUPLICATE KEY UPDATE 
				name = ?,
				activation = ?d,
				currency = ?d,
				description = ?,
				type = ?
		";
        $bResult = $this->oDb->query(
            $sql, 
			$oDeliveryService->getKey(),
			$oDeliveryService->getName(),
			$oDeliveryService->getActivation(),
			$oDeliveryService->getCurrency(),
			$oDeliveryService->getDescription(),
			$oDeliveryService->getType(),
			
			$oDeliveryService->getName(),
			$oDeliveryService->getActivation(),
			$oDeliveryService->getCurrency(),
			$oDeliveryService->getDescription(),
			$oDeliveryService->getType()
        );
        if ($bResult !== false) {
            return true;
        }
        return false;
	}
	
    /**
     * ���������� ������ ����� �������� �� ������ ID
     *
	 * @param array $aDeliveryServiceId    ������ ID ����� ��������
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
				$oDeliveryService = Engine::GetEntity('PluginMinimarket_ModuleDelivery_EntityDeliveryService', $aDeliveryService);
                $aDeliveryServices[$oDeliveryService->getId()] = $oDeliveryService;
            }
        }
        return $aDeliveryServices;
	}
	
    /**
     * ���������� ���������� ����� �������� �� ������
     *
	 * @param string $iCurrency    ID ������
     *
     * @return array
     */
	public function GetCountDeliveryServicesByCurrency($iCurrency) {
        $sql = "SELECT
					COUNT(*) as count
				FROM
					" . Config::Get('db.table.minimarket_delivery_service') . "
				WHERE
					`currency` = ?d
				";
        $aRow = $this->oDb->selectRow(
            $sql, $iCurrency
        );
        if ($aRow) {
			return $aRow['count'];
        }
        return false;
	}
}
?>