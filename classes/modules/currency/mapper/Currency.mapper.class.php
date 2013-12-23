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

class PluginMinimarket_ModuleCurrency_MapperCurrency extends Mapper {

    /**
     * ���������� ���������� ������� �� �������
     *
     * @param array $aFilter    ������ �������
     *
     * @return int|bool
     */
	public function GetCountCurrencyByFilter($aFilter) {
        $sql = "SELECT
					COUNT(*) as count
				FROM
					" . Config::Get('db.table.minimarket_currency') . "
				WHERE
					1 = 1
					{ AND `default` = ?d }
					{ AND `cart` = ? }
				";
        $aRow = $this->oDb->selectRow(
            $sql,
            isset($aFilter['default']) ? $aFilter['default'] : DBSIMPLE_SKIP,
            isset($aFilter['cart']) ? $aFilter['cart'] : DBSIMPLE_SKIP
        );
        if ($aRow) {
			return $aRow['count'];
        }
        return false;
	}

    /**
     * �������� ������
     *
     * @param PluginMinimarket_ModuleCurrency_EntityCurrency $oCurrency    ������ ������
     *
     * @return int
     */
	public function AddCurrency(PluginMinimarket_ModuleCurrency_EntityCurrency $oCurrency) {
		$sql = "INSERT INTO ".Config::Get('db.table.minimarket_currency'). "
					(`key`,
					`nominal`,
					`course`,
					`format`,
					`decimal_places`)
				VALUES(?, ?d, ?, ?, ?d)
		";
		return $this->oDb->query(
			$sql,
			$oCurrency->getKey(),
			$oCurrency->getNominal(),
			$oCurrency->getCourse(),
			$oCurrency->getFormat(),
			$oCurrency->getDecimalPlaces()
		);
	}

    /**
     * ���������� ������ ������ �� �����
     *
     * @param string $sKey    ���� ������
     *
     * @return PluginMinimarket_ModuleCurrency_EntityCurrency|bool
     */
	public function GetCurrencyByKey($sKey) {
        $sql = "SELECT
						*
					FROM
						" . Config::Get('db.table.minimarket_currency') . "
					WHERE
						`key` = ?
					";
        if ($aRow = $this->oDb->selectRow(
				$sql,
				$sKey
			)
		) {
            return Engine::GetEntity('PluginMinimarket_ModuleCurrency_EntityCurrency', $aRow);
        }
        return false;
	}

    /**
     * ���������� ������ ������ �� ID
     *
     * @param string $iId    ID ������
     *
     * @return PluginMinimarket_ModuleCurrency_EntityCurrency|bool
     */
	public function GetCurrencyById($iId) {
        $sql = "SELECT
						*
					FROM
						" . Config::Get('db.table.minimarket_currency') . "
					WHERE
						`id` = ?
					";
        if ($aRow = $this->oDb->selectRow(
				$sql,
				$iId
			)
		) {
            return Engine::GetEntity('PluginMinimarket_ModuleCurrency_EntityCurrency', $aRow);
        }
        return false;
	}

    /**
     * ���������� ��� ������ �������
     *
     * @return array
     */
	public function GetAllCurrency() {
        $sql = "SELECT
						*
					FROM
						" . Config::Get('db.table.minimarket_currency');
        $aResult = array();
        $aRows = $this->oDb->select($sql);
        if ($aRows) {
            foreach ($aRows as $aRow) {
                $aResult[$aRow['id']] = Engine::GetEntity('PluginMinimarket_ModuleCurrency_EntityCurrency', $aRow);
            }
        }
        return $aResult;
	}

    /**
     * ��������� ������
     *
     * @param PluginMinimarket_ModuleCurrency_EntityCurrency $oCurrency    ������ ������
     *
     * @return int|bool
     */
	public function UpdateCurrency(PluginMinimarket_ModuleCurrency_EntityCurrency $oCurrency) {
        $sql = "UPDATE " . Config::Get('db.table.minimarket_currency') . "
			SET
				`key` = ?,
				`nominal` = ?d,
				`course` = ?,
				`format` = ?,
				`decimal_places` = ?d
			WHERE
				`id` = ?d
		";
        $bResult = $this->oDb->query(
			$sql,
			$oCurrency->getKey(),
			$oCurrency->getNominal(),
			$oCurrency->getCourse(),
			$oCurrency->getFormat(),
			$oCurrency->getDecimalPlaces(),
			$oCurrency->getId()
		);
        return $bResult !== false;
	}
	
    /**
     * �������� ������
     *
     * @param int $iId    ID ������
     *
     * @return bool
     */
	public function DeleteCurrency($iId) {
        $sql = "
            DELETE FROM " . Config::Get('db.table.minimarket_currency') . "
            WHERE 
				id = ?d ";
        return ($this->oDb->query($sql, $iId) !== false);
	}
}
?>