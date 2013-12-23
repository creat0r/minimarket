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
     * ���������� ������ � ���������
     *
     * @param array    $aData
     *
     * @return bool
     */
    public function UpdateStorage($aData) {
        $sql = "REPLACE INTO " . Config::Get('db.table.minimarket_storage') . "(?#) VALUES(?a)";
        return ($this->oDb->query($sql, array_keys($aData[0]), array_values($aData)) !== false);
    }
	
    /**
     * ���������� ������ �� ���������
     *
     * @param string|null    $sPrefix
     *
     * @return array
     */
    public function GetStorage($sPrefix = '') {

        if ($sPrefix) {
            $sql = "
                SELECT `key`, `val`
                FROM " . Config::Get('db.table.minimarket_storage') . "
                WHERE `key` LIKE '" . $sPrefix . "%'";
        } else {
            $sql = "
                SELECT `key`, `val`
                FROM " . Config::Get('db.table.minimarket_storage') . "
            ";
        }
        $aRows = $this->oDb->query($sql);
        $aResult = array();
        if ($aRows)
            foreach ($aRows as $aRow) {
                $aResult[$aRow['key']] = $aRow['val'];
            }
        return $aResult;
    }
}
?>