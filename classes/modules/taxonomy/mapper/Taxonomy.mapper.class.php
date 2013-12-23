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

class PluginMinimarket_ModuleTaxonomy_MapperTaxonomy extends Mapper {

    /**
     * �������� ����������
     *
     * @param PluginMinimarket_ModuleTaxonomy_EntityTaxonomy $oTaxonomy    ������ ����������
     *
     * @return int
     */
	public function AddTaxonomy(PluginMinimarket_ModuleTaxonomy_EntityTaxonomy $oTaxonomy) {
		$sql = "INSERT INTO ".Config::Get('db.table.minimarket_taxonomy'). "
					(`parent_id`,
					`name`,
					`url`,
					`type`,
					`description`)
				VALUES(?d, ?, ?, ?, ?)
		";
		return $this->oDb->query(
			$sql,
			$oTaxonomy->getParentId(),
			$oTaxonomy->getName(),
			$oTaxonomy->getURL(),
			$oTaxonomy->getType(),
			$oTaxonomy->getDescription()
		);
	}

    /**
     * ���������� ������ ���������� �� ������ ID ���������
     *
     * @param array $aId    ������ ID ���������
     *
     * @return array
     */
	public function GetTaxonomiesByParents($aId) {
		if (!is_array($aId)) $aId = array($aId);
        if (count($aId) == 0) {
            return array();
        }
        $sql = "SELECT
						*
					FROM
						" . Config::Get('db.table.minimarket_taxonomy') . "
					WHERE
						`parent_id` IN(?a)";
        $aResult = array();
        $aRows = $this->oDb->select(
			$sql,
			$aId
		);
        if ($aRows) {
            foreach ($aRows as $aRow) {
				$oTaxonomy = Engine::GetEntity('PluginMinimarket_Taxonomy_Taxonomy', $aRow);
                $aResult[$oTaxonomy->getParentId()][$oTaxonomy->getType()][] = $oTaxonomy;
            }
        }
        return $aResult;
	}

    /**
     * ���������� ������ ID ��������� �� ������ ID ����������
     *
     * @param array $aTaxonomyId    ������ ID ����������
     *
     * @return array
     */
	public function GetIdParentsByIdTaxonomies($aTaxonomyId) {
		if (!is_array($aTaxonomyId)) $aTaxonomyId = array($aTaxonomyId);
        if (count($aTaxonomyId) == 0) {
            return array();
        }
        $sql = "SELECT
						`parent_id`, `id`
					FROM
						" . Config::Get('db.table.minimarket_taxonomy') . "
					WHERE
						`id` IN(?a)";
        $aResult = array();
        $aRows = $this->oDb->select($sql, $aTaxonomyId);
        if ($aRows) {
            foreach ($aRows as $aRow) {
				$aResult[$aRow['id']][] = $aRow['parent_id'];
            }
        }
        return $aResult;
	}

    /**
     * ���������� ������ ���������� ������ ID ����������
     *
     * @param array $aId    ������ ID ����������
     *
     * @return array
     */
	public function GetTaxonomiesByArrayId($aId) {
		if(!is_array($aId)) $aId=array($aId);
        if (count($aId) == 0) {
            return array();
        }
        $sql = "SELECT
						*
					FROM
						" . Config::Get('db.table.minimarket_taxonomy') . "
					WHERE
						`id` IN(?a) 
					ORDER BY
						`sort` DESC
						";
        $aResult = array();
        $aRows = $this->oDb->select(
			$sql,
			$aId
		);
        if ($aRows) {
            foreach ($aRows as $aRow) {
                $aResult[] = Engine::GetEntity('PluginMinimarket_Taxonomy_Taxonomy', $aRow);
            }
        }
        return $aResult;
	}

    /**
     * ���������� ������ ���������� �� ID �������� � ���� ����������
     *
     * @param int    $iId      ID ��������
     * @param string $sType    ��� ����������
     *
     * @return array
     */
	public function GetTaxonomiesByParentIdAndType($iId, $sType) {
        $sql = "SELECT
						*
					FROM
						" . Config::Get('db.table.minimarket_taxonomy') . "
					WHERE
						`parent_id` = ?d AND `type` = ?";
        $aResult = array();
        $aRows = $this->oDb->select(
			$sql, 
			$iId,
			$sType
		);
        if ($aRows) {
            foreach ($aRows as $aRow) {
                $aResult[] = Engine::GetEntity('PluginMinimarket_Taxonomy_Taxonomy', $aRow);
            }
        }
        return $aResult;
	}

    /**
     * ���������� ������ ���������� �� ID ��������
     *
     * @param int $iId    ID �������� ����������
     *
     * @return array
     */
	public function GetTaxonomiesByParentId($iId) {
        $sql = "SELECT
						*
					FROM
						" . Config::Get('db.table.minimarket_taxonomy') . "
					WHERE
						`parent_id` = ?d";
        $aResult = array();
        $aRows = $this->oDb->select(
			$sql,
			$iId
		);
        if ($aRows) {
            foreach ($aRows as $aRow) {
                $aResult[] = Engine::GetEntity('PluginMinimarket_Taxonomy_Taxonomy', $aRow);
            }
        }
        return $aResult;
	}

    /**
     * ���������� ������ ���������� �� ����
     *
     * @param string $sType    ��� ����������
     *
     * @return array
     */
	public function GetTaxonomiesByType($sType) {
        $sql = "SELECT
						*
					FROM
						" . Config::Get('db.table.minimarket_taxonomy') . "
					WHERE
						`type` = ?
					ORDER BY
						`sort` DESC
					";
        $aTaxonomies = array();
        $aRows = $this->oDb->select(
			$sql,
			$sType
		);
        if ($aRows) {
            foreach ($aRows as $aRow) {
				$oTaxonomy = Engine::GetEntity('PluginMinimarket_Taxonomy_Taxonomy', $aRow);
                $aTaxonomies[$oTaxonomy->getId()] = $oTaxonomy;
            }
        }
        return $aTaxonomies;
	}

    /**
     * ���������� ���������� �� ID
     *
     * @param int $iId    ID ����������
     *
     * @return PluginMinimarket_Taxonomy_Taxonomy|bool
     */
	public function GetTaxonomyById($iId) {
        $sql = "SELECT
						*
					FROM
						" . Config::Get('db.table.minimarket_taxonomy') . "
					WHERE
						`id` = ?d
					";
        if ($aRow = $this->oDb->selectRow(
				$sql,
				$iId
			)
		) {
            return Engine::GetEntity('PluginMinimarket_Taxonomy_Taxonomy', $aRow);
        }
        return false;
	}

    /**
     * ��������� ����������
     *
     * @param PluginMinimarket_ModuleTaxonomy_EntityTaxonomy $oTaxonomy    ������ ����������
     *
     * @return int|bool
     */
	public function UpdateTaxonomy(PluginMinimarket_ModuleTaxonomy_EntityTaxonomy $oTaxonomy) {
        $sql = "UPDATE " . Config::Get('db.table.minimarket_taxonomy') . "
			SET
				`name` = ?,
				`url` = ?,
				`parent_id` = ?,
				`sort` = ?d,
				`description` = ?
			WHERE
				`id` = ?d
		";
        $bResult = $this->oDb->query(
			$sql,
			$oTaxonomy->getName(),
			$oTaxonomy->getURL(),
			$oTaxonomy->getParentId(),
			$oTaxonomy->getSort(),
			$oTaxonomy->getDescription(),
			$oTaxonomy->getId()
		);
        return $bResult !== false;
	}

    /**
     * ���������� ���������� ���������� �� ������ ID � ����� ����������
     *
     * @param array $aId      ������ ID ����������
     * @param array $aType    ������ ����� ����������
     *
     * @return int|bool
     */
	public function GetCountTaxonomyByArrayIdAndArrayType($aId, $aType) {
		if (!is_array($aId)) $aId = array($aId);
		if (!is_array($aType)) $aType = array($aType);
		if (empty($aId) || empty($aType)) return false;
        $sql = "SELECT
						COUNT(*) as count
					FROM
						" . Config::Get('db.table.minimarket_taxonomy') . "
					WHERE
						`id` IN (?a) AND `type` IN (?a) ";
        if ($aRow = $this->oDb->selectRow(
				$sql, 
				$aId, 
				$aType
			)
		) {
            return $aRow['count'];
        }
        return false;
	}

    /**
     * ������� ���������� �� �������
     *
     * @param PluginMinimarket_ModuleTaxonomy_EntityTaxonomy $oTaxonomy    ������ ����������
     *
     * @return int|bool
     */
	public function DeleteTaxonomy(PluginMinimarket_ModuleTaxonomy_EntityTaxonomy $oTaxonomy) {
        $sql = "DELETE FROM " . Config::Get('db.table.minimarket_taxonomy') . "
			WHERE
				`id` = ?d
		";
        return $this->oDb->query(
			$sql, 
			$oTaxonomy->getId()
		) !== false;
	}

    /**
     * ������� ���������� �� ������ ID
     *
     * @param array $aId    ������ ID ����������
     *
     * @return int|bool
     */
	public function DeleteTaxonomiesByArrayId($aId) {
		$sWhere = '`id` IN (';
		$sParam = '';
		foreach($aId as $key => $val) {
			$sWhere .= (int)$val;
			if($key < count($aId) - 1) {
				$sWhere .= ", ";
			}
		}
		$sWhere.=')';
        $sql = "DELETE FROM " . Config::Get('db.table.minimarket_taxonomy') . "
			WHERE
				".$sWhere."
		";
        return $this->oDb->query($sql) !== false;
	}

    /**
     * ���������� ������ ���������� �� �������
     *
     * @param array $aFilter    ������ �������
     * @param array $aOrder     ����������
     *
     * @return array|bool
     */
	public function GetTaxonomiesByFilter($aFilter, $aOrder) {
        $aOrderAllow = array();
        $sOrder = '';
        if (is_array($aOrder) && $aOrder) {
            foreach ($aOrder as $key => $value) {
                if (!in_array($key, $aOrderAllow)) {
                    unset($aOrder[$key]);
                } elseif (in_array($value, array('asc', 'desc'))) {
                    $sOrder .= " `{$key}` {$value},";
                }
            }
            $sOrder = trim($sOrder, ',');
        }
        if ($sOrder == '') {
            $sOrder = ' `sort` desc ';
        }

        $sql = "SELECT
					*
				FROM
					" . Config::Get('db.table.minimarket_taxonomy') . "
				WHERE
					1 = 1
					{ AND `id` = ?d }
					{ AND `url` = ? }
					{ AND `parent_id` = ?d }
					{ AND `type` = ? }
				ORDER BY {$sOrder} ;";
        $aResult = array();
        $aRows = $this->oDb->select(
            $sql,
            isset($aFilter['id']) ? $aFilter['id'] : DBSIMPLE_SKIP,
            isset($aFilter['url']) ? $aFilter['url'] : DBSIMPLE_SKIP,
            isset($aFilter['parent_id']) ? $aFilter['parent_id'] : DBSIMPLE_SKIP,
            isset($aFilter['type']) ? $aFilter['type'] : DBSIMPLE_SKIP
        );
        if ($aRows) {
            foreach ($aRows as $aRow) {
                $aResult[] = Engine::GetEntity('PluginMinimarket_Taxonomy_Taxonomy', $aRow);
            }
        }
        return $aResult;
	}
}
?>