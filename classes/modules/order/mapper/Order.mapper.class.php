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

class PluginMinimarket_ModuleOrder_MapperOrder extends Mapper {

    /**
     * ���������� ����� �� �����
     *
	 * @param string $sKey    ���������� ���� ������
	 * 
     * @return PluginMinimarket_ModuleOrder_EntityOrder|bool
     */
	public function GetOrderByKey($sKey) {
        $sql = "SELECT
						*
					FROM
						" . Config::Get('db.table.minimarket_order') . "
					WHERE
						`key` = ?
					";
        if ($aRow = $this->oDb->selectRow($sql, $sKey)) {
            return Engine::GetEntity('PluginMinimarket_ModuleOrder_EntityOrder', $aRow);
        }
        return false;
	}
	
    /**
     * ���������� ����� �� ID
     *
	 * @param string $iId    ID ������
	 *
     * @return PluginMinimarket_ModuleOrder_EntityOrder|bool
     */
	public function GetOrderById($iId) {
        $sql = "SELECT
						*
					FROM
						" . Config::Get('db.table.minimarket_order') . "
					WHERE
						`id` = ?d
					";
        if ($aRow = $this->oDb->selectRow($sql, $iId)) {
            return Engine::GetEntity('PluginMinimarket_ModuleOrder_EntityOrder', $aRow);
        }
        return false;
	}
	
    /**
     * ��������� ���� ��������� (���� ������ � ����� ������ ��� ����������) �����
     *
	 * @param PluginMinimarket_ModuleOrder_EntityOrder $oOrder    ������ ������
	 * 
     * @return bool
     */
	public function AddOrUpdateOrder($oOrder) {
       $sql = "INSERT INTO " . Config::Get('db.table.minimarket_order') . "			
			(
			`user_id`,
			`key`,
			`client_name`,
			`client_index`,
			`client_address`,
			`client_phone`,
			`client_comment`,
			`delivery_service_id`,
			`status`,
			`time_order_init`,
			`time_selected_pay_system`,
			`time_payment_success`
			)
			VALUES(?d,?,?,?,?,?,?,?d,?d,?d,?d,?d)
			ON DUPLICATE KEY UPDATE 
				`user_id` = ?d,
				`key` = ?,
				`client_name` = ?,
				`client_index` = ?,
				`client_address` = ?,
				`client_phone` = ?,
				`client_comment` = ?,
				`delivery_service_id` = ?d,
				`status` = ?d,
				`time_order_init` = ?d,
				`time_selected_pay_system` = ?d,
				`time_payment_success` = ?d
		";
        $bResult = $this->oDb->query(
            $sql, 
			$oOrder->getUserId(),
			$oOrder->getKey(),
			$oOrder->getClientName(),
			$oOrder->getClientIndex(),
			$oOrder->getClientAddress(),
			$oOrder->getClientPhone(),
			$oOrder->getClientComment(),
			$oOrder->getDeliveryServiceId(),
			$oOrder->getStatus(),
			$oOrder->getTimeOrderInit(),
			$oOrder->getTimeSelectedPaySystem(),
			$oOrder->getTimePaymentSuccess(),
			
			$oOrder->getUserId(),
			$oOrder->getKey(),
			$oOrder->getClientName(),
			$oOrder->getClientIndex(),
			$oOrder->getClientAddress(),
			$oOrder->getClientPhone(),
			$oOrder->getClientComment(),
			$oOrder->getDeliveryServiceId(),
			$oOrder->getStatus(),
			$oOrder->getTimeOrderInit(),
			$oOrder->getTimeSelectedPaySystem(),
			$oOrder->getTimePaymentSuccess()
        );
        if ($bResult !== false) {
            return true;
        }
        return false;
	}
	
    /**
     * ���������� ������ �� ������� �� ID ������ � ID ������
     *
	 * @param int $iOrder      ID ������
	 * @param int $iProduct    ID ������
	 * 
     * @return PluginMinimarket_ModuleOrder_EntityOrderCart|null
     */
	public function GetCartObjectByOrderAndProduct($iOrder, $iProduct) {
        $sql = "SELECT
						*
					FROM
						" . Config::Get('db.table.minimarket_cart') . "
					WHERE
						order_id = ?d AND product_id = ?d
					";
        if ($aRow = $this->oDb->selectRow($sql, $iOrder, $iProduct)) {
            return Engine::GetEntity('PluginMinimarket_ModuleOrder_EntityOrderCart', $aRow);
        }
        return null;
	}
	
    /**
     * ��������� ������ � �������
     *
	 * @param PluginMinimarket_ModuleOrder_EntityOrderCart $oCart    ������ ������ � �������
	 * 
     * @return bool
     */
	public function UpdateCartObject($oCart) {
		$sql = "UPDATE " . Config::Get('db.table.minimarket_cart') . "
			SET 
				product_count = ?d
			WHERE
				order_id = ?d AND product_id = ?d
		";
        $bResult = $this->oDb->query(
            $sql, $oCart->getProductCount(), $oCart->getOrderId(), $oCart->getProductId()
        );
		return $bResult !== false;
	}
	
    /**
     * ��������� ������ � �������
     *
	 * @param PluginMinimarket_ModuleOrder_EntityOrderCart $oCart    ������ ������
	 *
     * @return int|null
     */
	public function AddCartObject($oCart) {
        $sql = "INSERT INTO " . Config::Get('db.table.minimarket_cart') . "
			(order_id,
			product_id,
			product_count
			)
			VALUES(?d,?d,?d)
		";
        $nId = $this->oDb->query(
            $sql, $oCart->getOrderId(), $oCart->getProductId(), $oCart->getProductCount()
        );
        if ($nId) {
            return $nId;
        }
        return false;
	}
	
    /**
     * ���������� ������ ������� �� ������� �� ID ������
     *
	 * @param int $iOrder    ID ������
	 *
     * @return array
     */
	public function GetCartObjectsByOrder($iOrder) {
		$sql = "
		SELECT
			product_id, product_count
		FROM
			" . Config::Get('db.table.minimarket_cart') . "
		WHERE
			order_id = ?d
		";

		$aLinks = array();
		if($aRows = $this->oDb->select($sql, $iOrder)) {
			foreach ($aRows as $aRow) {
				$aLinks[$aRow['product_id']] = $aRow['product_count'];
			}
		}
		return $aLinks ? $aLinks : array();
	}
	
    /**
     * ������� ������ �� �������
     *
	 * @param PluginMinimarket_ModuleOrder_EntityOrderCart $oCartObject    ������ ������
	 *
     * @return bool
     */
	public function DeleteCartObject(PluginMinimarket_ModuleOrder_EntityOrderCart $oCartObject) {
        $sql = "DELETE FROM " . Config::Get('db.table.minimarket_cart') . "
			WHERE
				order_id = ?d AND product_id = ?d
		";
        return $this->oDb->query($sql, $oCartObject->getOrderId(), $oCartObject->getProductId()) !== false;
	}
	
    /**
     * ������� ������ �� ������� �� ID ������ � ID ������
     *
	 * @param int $iOrderId      ID ������
	 * @param int $iProductId    ID ��������
	 *
     * @return bool
     */
	public function DeleteCartObjectByOrderIdAndProductId($iOrderId, $iProductId) {
        $sql = "DELETE FROM " . Config::Get('db.table.minimarket_cart') . "
			WHERE
				`order_id` = ?d AND `product_id` = ?d
		";
        return $this->oDb->query(
			$sql, 
			$iOrderId, 
			$iProductId
		) !== false;		
	}
	
    /**
     * �������� ������ ������� �� �������
     *
     * @param array $aFilter      ������ �������
     * @param array $aOrder       ����������
	 * @param int   $iCount       ���������� ����� ���������� ��������
     * @param int   $iCurrPage    ����� ������� ��������
     * @param int   $iPerPage     ���������� ��������� �� ���� ��������
     *
     * @return array
     */
	public function GetOrdersByFilter($aFilter, $aOrder, &$iCount, $iCurrPage, $iPerPage) {
        $aOrderAllow = array();
        $sOrder = '';
        if (is_array($aOrder) && $aOrder) {
            foreach ($aOrder as $key => $value) {
                if (!in_array($key, $aOrderAllow)) {
                    unset($aOrder[$key]);
                } elseif (in_array($value, array('asc', 'desc'))) {
                    $sOrder .= " {$key} {$value},";
                }
            }
            $sOrder = trim($sOrder, ',');
        }
        if ($sOrder == '') {
            $sOrder = ' id desc ';
        }

        $sql = "SELECT
					id
				FROM
					" . Config::Get('db.table.minimarket_order') . "
				WHERE
					1 = 1
					{ AND id = ?d }
				ORDER by {$sOrder}
				LIMIT ?d, ?d ;
					";
        $aResult = array();
        $aRows = $this->oDb->selectPage(
            $iCount, $sql,
            isset($aFilter['id']) ? $aFilter['id'] : DBSIMPLE_SKIP,
            ($iCurrPage - 1) * $iPerPage, $iPerPage
        );
        if ($aRows) {
            foreach ($aRows as $aRow) {
                $aResult[] = $aRow['id'];
            }
        }
        return $aResult;
	}
	
    /**
     * ���������� ������ ������� �� ID
     *
     * @param array $aOrderId    ������ ID ������������
     *
     * @return array
     */
	public function GetOrdersByArrayId($aOrderId) {
        if (!is_array($aOrderId) or count($aOrderId) == 0) {
            return array();
        }

        $sql = "SELECT
					*				
				FROM 
					" . Config::Get('db.table.minimarket_order') . "
				WHERE 	
					id IN(?a) 					
				ORDER by FIELD(id,?a)";
        $aOrder = array();
        if ($aRows = $this->oDb->select($sql, $aOrderId, $aOrderId)) {
            foreach ($aRows as $aRow) {
                $aOrder[$aRow['id']] = Engine::GetEntity('PluginMinimarket_ModuleOrder_EntityOrder', $aRow);
            }
        }
        return $aOrder;
    }
	
    /**
     * ������� ��� ������ �� ������� �� ID ������
     *
	 * @param int $iOrderId			ID ������
	 *
     * @return bool
     */
	public function DeleteCartObjectsByOrder($iOrderId) {
        $sql = "DELETE FROM " . Config::Get('db.table.minimarket_cart') . "
			WHERE
				order_id = ?d
		";
        return $this->oDb->query($sql, $iOrderId) !== false;
	}
	
    /**
     * ������� ����� �� ID
     *
	 * @param int $iOrderId    ID ������
	 *
     * @return bool
     */
	public function DeleteOrder($iOrderId) {
        $sql = "DELETE FROM " . Config::Get('db.table.minimarket_order') . "
			WHERE
				id = ?d
		";
        return $this->oDb->query($sql, $iOrderId) !== false;
	}
}
?>