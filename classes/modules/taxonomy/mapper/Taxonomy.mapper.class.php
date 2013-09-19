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

	public function AddTaxonomy(PluginMinimarket_ModuleTaxonomy_EntityTaxonomy $oTaxonomy) {
		$sql = "INSERT INTO ".Config::Get('db.table.minimarket_taxonomy'). "
			(parent_id,
			taxonomy_name,
			taxonomy_url,
			taxonomy_type,
			taxonomy_description)
			VALUES(?d, ?, ?, ?, ?)
		";
		return $this->oDb->query($sql,$oTaxonomy->getParent(),$oTaxonomy->getName(),$oTaxonomy->getURL(),$oTaxonomy->getTaxonomyType(),$oTaxonomy->getDescription());
	}
	
	public function GetTaxonomiesByParentArray($aId) {
		if(!is_array($aId)) $aId=array($aId);
        if (count($aId) == 0) {
            return array();
        }
        $sql = "SELECT
						*
					FROM
						" . Config::Get('db.table.minimarket_taxonomy') . "
					WHERE
						parent_id IN(?a)";
        $aResult = array();
        $aRows = $this->oDb->select($sql,$aId);
        if ($aRows) {
            foreach ($aRows as $aRow) {
				$oTaxonomy = Engine::GetEntity('PluginMinimarket_Taxonomy_Taxonomy', $aRow);
                $aResult[$oTaxonomy->getParent()][$oTaxonomy->getTaxonomyType()][] = $oTaxonomy;
            }
        }
        return $aResult;
	}
	
	public function GetArrayIdParentByArrayIdTaxonomy($aTaxonomyId) {
		if(!is_array($aTaxonomyId)) $aTaxonomyId=array($aTaxonomyId);
        if (count($aTaxonomyId) == 0) {
            return array();
        }
        $sql = "SELECT
						parent_id,taxonomy_id
					FROM
						" . Config::Get('db.table.minimarket_taxonomy') . "
					WHERE
						taxonomy_id IN(?a)";
        $aResult = array();
        $aRows = $this->oDb->select($sql,$aTaxonomyId);
        if ($aRows) {
            foreach ($aRows as $aRow) {
				$aResult[$aRow['taxonomy_id']][] = $aRow['parent_id'];
            }
        }
        return $aResult;
	}
	
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
						taxonomy_id IN(?a) 
					ORDER BY
						taxonomy_sort DESC
						";
        $aResult = array();
        $aRows = $this->oDb->select($sql,$aId);
        if ($aRows) {
            foreach ($aRows as $aRow) {
                $aResult[] = Engine::GetEntity('PluginMinimarket_Taxonomy_Taxonomy', $aRow);
            }
        }
        return $aResult;
	}
	
	public function GetTaxonomiesByParentIdAndType($nId,$sType) {
        $sql = "SELECT
						*
					FROM
						" . Config::Get('db.table.minimarket_taxonomy') . "
					WHERE
						parent_id = ?d AND taxonomy_type = ?";
        $aResult = array();
        $aRows = $this->oDb->select($sql,$nId,$sType);
        if ($aRows) {
            foreach ($aRows as $aRow) {
                $aResult[] = Engine::GetEntity('PluginMinimarket_Taxonomy_Taxonomy', $aRow);
            }
        }
        return $aResult;
	}
	
	public function GetTaxonomiesByParentId($nId) {
        $sql = "SELECT
						*
					FROM
						" . Config::Get('db.table.minimarket_taxonomy') . "
					WHERE
						parent_id = ?d";
        $aResult = array();
        $aRows = $this->oDb->select($sql,$nId);
        if ($aRows) {
            foreach ($aRows as $aRow) {
                $aResult[] = Engine::GetEntity('PluginMinimarket_Taxonomy_Taxonomy', $aRow);
            }
        }
        return $aResult;
	}
	
	public function getTaxonomiesByType($sType) {
        $sql = "SELECT
						*
					FROM
						" . Config::Get('db.table.minimarket_taxonomy') . "
					WHERE
						taxonomy_type = ?
					ORDER BY
						taxonomy_sort DESC
					";
        $aTaxonomies = array();
        $aRows = $this->oDb->select($sql,$sType);
        if ($aRows) {
            foreach ($aRows as $aRow) {
				$oTaxonomy = Engine::GetEntity('PluginMinimarket_Taxonomy_Taxonomy', $aRow);
                $aTaxonomies[$oTaxonomy->getId()] = $oTaxonomy;
            }
        }
        return $aTaxonomies;
	}
	
	public function GetTaxonomyById($nId) {
        $sql = "SELECT
						*
					FROM
						" . Config::Get('db.table.minimarket_taxonomy') . "
					WHERE
						taxonomy_id = ?d
					";
        if ($aRow = $this->oDb->selectRow($sql, $nId)) {
            return Engine::GetEntity('PluginMinimarket_Taxonomy_Taxonomy', $aRow);
        }
        return false;
	}
	
	public function UpdateTaxonomy(PluginMinimarket_ModuleTaxonomy_EntityTaxonomy $oTaxonomy) {
        $sql = "UPDATE " . Config::Get('db.table.minimarket_taxonomy') . "
			SET
				taxonomy_name=?,
				taxonomy_url=?,
				parent_id=?,
				taxonomy_sort=?d,
				taxonomy_description=?,
				taxonomy_config=?
			WHERE
				taxonomy_id = ?d
		";
        $bResult = $this->oDb->query($sql,$oTaxonomy->getName(),$oTaxonomy->getURL(),
		$oTaxonomy->getParent(),$oTaxonomy->getTaxonomySort(),$oTaxonomy->getDescription(),
		$oTaxonomy->getTaxonomyConfig(),$oTaxonomy->getId());
        return $bResult !== false;
	}
	
	public function GetAttributByURL($nURL) {
        $sql = "SELECT
						*
					FROM
						" . Config::Get('db.table.minimarket_taxonomy') . "
					WHERE
						taxonomy_url = ? AND parent_id = ?
					";
        if ($aRow = $this->oDb->selectRow($sql, $nURL, 0)) {
            return Engine::GetEntity('PluginMinimarket_Taxonomy_Taxonomy', $aRow);
        }
        return null;
	}
	
	public function GetPropertiesByAttributId($nId) {
        $sql = "SELECT
						*
					FROM
						" . Config::Get('db.table.minimarket_taxonomy') . "
					WHERE
						parent_id = ?d AND taxonomy_type = ?
					ORDER BY
						taxonomy_sort DESC
					";
		$aProperties = array();
		$aRows = $this->oDb->select($sql,$nId,'property');
        if ($aRows) {
            foreach ($aRows as $aProperty) {
                $aProperties[] = Engine::GetEntity('PluginMinimarket_Taxonomy_Taxonomy', $aProperty);
            }
        }
        return $aProperties;
	}
	
	public function GetPropertyByURLAndParentId($nURL,$nId) {
        $sql = "SELECT
						*
					FROM
						" . Config::Get('db.table.minimarket_taxonomy') . "
					WHERE
						taxonomy_url = ? AND parent_id = ?d
					";
        if ($aRow = $this->oDb->selectRow($sql,$nURL,$nId)) {
            return Engine::GetEntity('PluginMinimarket_Taxonomy_Taxonomy', $aRow);
        }
        return null;
	}
	
	public function GetCountTaxonomyByArrayIdAndArrayType($aId, $aType) {
		if(!is_array($aId)) $aId=array($aId);
		if(!is_array($aType)) $aType=array($aType);
		if(empty($aId) || empty($aType)) return false;
        $sql = "SELECT
						COUNT(*) as count
					FROM
						" . Config::Get('db.table.minimarket_taxonomy') . "
					WHERE
						taxonomy_id IN (?a) AND taxonomy_type IN (?a) ";
        if ($aRow = $this->oDb->selectRow($sql,$aId,$aType)) {
            return $aRow['count'];
        }
        return null;
	}
	
	public function GetTaxonomyByURLAndParentId($nURL,$nId,$nType) {
        $sql = "SELECT
						*
					FROM
						" . Config::Get('db.table.minimarket_taxonomy') . "
					WHERE
						taxonomy_url = ? AND parent_id = ?d AND taxonomy_type = ?
					";
        if ($aRow = $this->oDb->selectRow($sql,$nURL,$nId,$nType)) {
            return Engine::GetEntity('PluginMinimarket_Taxonomy_Taxonomy', $aRow);
        }
        return null;
	}
	
	public function GetTaxonomiesByURLAndType($nURL,$nType) {
        $sql = "SELECT
						*
					FROM
						" . Config::Get('db.table.minimarket_taxonomy') . "
					WHERE
						taxonomy_url = ? AND taxonomy_type = ?
					";
        $aReturn = array();
        if ($aRows = $this->oDb->select($sql,$nURL,$nType)) {
            foreach ($aRows as $aRow) {
                $aReturn[] = Engine::GetEntity('PluginMinimarket_Taxonomy_Taxonomy', $aRow);
            }
        }
        return $aReturn;
	}
	
	public function DeleteTaxonomy(PluginMinimarket_ModuleTaxonomy_EntityTaxonomy $oTaxonomy) {
        $sql = "DELETE FROM " . Config::Get('db.table.minimarket_taxonomy') . "
			WHERE
				taxonomy_id = ?d
		";
        return $this->oDb->query($sql, $oTaxonomy->getId()) !== false;
	}
	
	public function DeleteTaxonomiesByArrayId($aId) {
		$sWhere='taxonomy_id IN (';
		$sParam='';
		foreach($aId as $key=>$val) {
			$sWhere.=(int)$val;
			if($key<count($aId)-1) {
				$sWhere.=", ";
			}
		}
		$sWhere.=')';
        $sql = "DELETE FROM " . Config::Get('db.table.minimarket_taxonomy') . "
			WHERE
				".$sWhere."
		";
        return $this->oDb->query($sql) !== false;
	}
	
	public function DeleteAttribut(PluginMinimarket_ModuleTaxonomy_EntityTaxonomy $oAttribut) {
        $sql = "DELETE FROM " . Config::Get('db.table.minimarket_taxonomy') . "
			WHERE
				taxonomy_id = ?d OR parent_id = ?d
		";
        return $this->oDb->query($sql,$oAttribut->getId(),$oAttribut->getId()) !== false;
	}
	
}
?>