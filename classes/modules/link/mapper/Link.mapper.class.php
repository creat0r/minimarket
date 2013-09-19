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

class PluginMinimarket_ModuleLink_MapperLink extends Mapper {

    /**
     * Добавляет связь
     *
     * @param PluginMinimarket_ModuleLink_EntityLink $oLink    Объект связи
     *
     * @return int|bool
     */
    public function AddLink(PluginMinimarket_ModuleLink_EntityLink $oLink) {
        $sql = "INSERT INTO " . Config::Get('db.table.minimarket_link') . "
			(object_id,
			object_type,
			parent_id
			)
			VALUES(?d,?,?d)
		";
        $nId = $this->oDb->query(
            $sql, $oLink->getObjectId(), $oLink->getObjectType(),  $oLink->getParentId()
        );
        if ($nId) {
            return $nId;
        }
        return false;
    }

    /**
     * Добавляет массив связей одним запросом
     *
     * @param array $aObjectCity			Массив связей
     *
     * @return int|bool
     */
    public function AddLinks($aObjectCity) {
		if(!is_array($aObjectCity) || empty($aObjectCity)) return false;
		$sValues = '';
		foreach($aObjectCity as $key=>$oLink) {
			$sValues .= "({$oLink->getObjectId()}, '{$oLink->getObjectType()}', {$oLink->getParentId()}),";
		}
		$sValues = substr($sValues, 0, strlen($sValues)-1);
        $sql = "INSERT INTO " . Config::Get('db.table.minimarket_link') . "
			(object_id,
			object_type,
			parent_id
			)
			VALUES{$sValues};
		";
        $nId = $this->oDb->query($sql);
        if ($nId) {
            return $nId;
        }
        return false;
    }

    /**
     * Удаляет связи по ID родителя и типу объекта
     *
     * @param int $nParentId			ID родителя
     * @param string $sObjectType		Тип объекта
     *
     * @return bool
     */
    public function DeleteLinkByParentAndType($nParentId, $sObjectType) {
        $sql = "DELETE FROM " . Config::Get('db.table.minimarket_link') . "
			WHERE
				parent_id = ?d AND object_type = ?
		";
        return $this->oDb->query($sql, $nParentId, $sObjectType) !== false;
    }
	
    /**
     * Возвращает список связей по ID родителя и типу объекта
     *
     * @param int $nParentId			ID родителя
     * @param string $sObjectType		Тип объекта
     *
     * @return array
     */
    public function GetLinksByParentAndType($nParentId, $sObjectType) {
		$sql = "
		SELECT
			object_id
		FROM
			" . Config::Get('db.table.minimarket_link') . "
		WHERE
			parent_id = ?d AND object_type = ? ";

		$aLinks = array();
		if($aRows = $this->oDb->select($sql, $nParentId, $sObjectType)) {
			foreach ($aRows as $aRow) {
				$aLinks[] = $aRow['object_id'];
			}
		}
		return $aLinks ? $aLinks : array();
    }
	
    /**
     * Возвращает списком количество повторений связей по ID родителя и типу объекта
     *
     * @param int $nParentId			ID родителя
     * @param string $sObjectType		Тип объекта
     *
     * @return array
     */
    public function GetCountLinksByParentAndType($nParentId, $sObjectType) {
		$sql = "
		SELECT
			object_id, COUNT(*) AS cnt
		FROM
			" . Config::Get('db.table.minimarket_link') . "
		WHERE
			parent_id = ?d AND object_type = ? 
		GROUP BY
			object_id
		";

		$aLinks = array();
		if($aRows = $this->oDb->select($sql, $nParentId, $sObjectType)) {
			foreach ($aRows as $aRow) {
				$aLinks[$aRow['object_id']] = $aRow['cnt'];
			}
		}
		return $aLinks ? $aLinks : array();
    }
	
}
?>