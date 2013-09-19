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

class PluginMinimarket_ModuleStorage_MapperStorage extends Mapper {

    /**
     * Сохранение данных в хранилище
     *
     * @param   array   $aData
     * @return  bool
     */
    public function UpdateStorage($aData) {
        $sql = "REPLACE INTO " . Config::Get('db.table.minimarket_storage') . "(?#) VALUES(?a)";
        return ($this->oDb->query($sql, array_keys($aData[0]), array_values($aData)) !== false);
    }
	
    /**
     * Возвращает данные из хранилища
     *
     * @param	string			$sPrefix
     * @return	array
     */
    public function GetStorage($sPrefix = '') {

        if ($sPrefix) {
            $sql = "
                SELECT storage_key, storage_val
                FROM " . Config::Get('db.table.minimarket_storage') . "
                WHERE storage_key LIKE '" . $sPrefix . "%'";
        } else {
            $sql = "
                SELECT storage_key, storage_val
                FROM " . Config::Get('db.table.minimarket_storage') . "
            ";
        }
        $aRows = $this->oDb->query($sql);
        $aResult = array();
        if ($aRows)
            foreach ($aRows as $aRow) {
                $aResult[$aRow['storage_key']] = $aRow['storage_val'];
            }
        return $aResult;
    }
}
?>