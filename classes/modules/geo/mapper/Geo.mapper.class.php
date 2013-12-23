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

class PluginMinimarket_ModuleGeo_MapperGeo extends PluginMinimarket_Inherits_ModuleGeo_MapperGeo {

    /**
     * Возвращает список стран, указанных в конфиге
     *
     * @return array
     */
    public function GetCountriesByConfig() {
		$sWhere = '';
		$aCounties = Config::Get('plugin.minimarket.settings.location.counties');
		if(
			$aCounties && 
			is_array($aCounties) &&
			!empty($aCounties)
		) {
			$sWhere = "AND code IN('".join("', '",$aCounties)."')";
		}
        $sql
            = "SELECT
					*
				FROM
					" . Config::Get('db.table.geo_country') . "
				WHERE
					1=1
					".$sWhere."
				ORDER BY
					sort ASC";
        $aResult = array();
        $aRows = $this->oDb->select($sql);
        if ($aRows) {
            foreach ($aRows as $aRow) {
                $aResult['collection'][] = Engine::GetEntity('PluginMinimarket_ModuleGeo_EntityCountry', $aRow);
                $aResult['id'][] = $aRow['id'];
            }
        }
        return $aResult;
    }

    /**
     * Возвращает список регионов по списку стран
     *
     * @param array $aCountries    Список ID стран
     *
     * @return array
     */
    public function GetRegionsByCountries($aCountries) {
		if(!is_array($aCountries)) $aCountries = array($aCountries);
        $sql = "SELECT
					*
				FROM
					" . Config::Get('db.table.geo_region') . "
				WHERE
					country_id IN (?a)
				";
        $aResult = array();
        $aRows = $this->oDb->select($sql, $aCountries);
        if ($aRows) {
            foreach ($aRows as $aRow) {
                $aResult['collection'][$aRow['country_id']][] = Engine::GetEntity('PluginMinimarket_ModuleGeo_EntityRegion', $aRow);
				$aResult['id'][] = $aRow['id'];
            }
        }
        return $aResult;
    }

    /**
     * Возвращает список городов по списку регионов
     *
     * @param array $aRegions    Список ID регионов
     *
     * @return array
     */
    public function GetCitiesByRegions($aRegions) {
        $sql
            = "SELECT
					*
				FROM
					" . Config::Get('db.table.geo_city') . "
				WHERE
					region_id IN (?a)
				";
        $aResult = array();
        $aRows = $this->oDb->select($sql, $aRegions);
        if ($aRows) {
            foreach ($aRows as $aRow) {
                $aResult[$aRow['region_id']][] = Engine::GetEntity('PluginMinimarket_ModuleGeo_EntityCity', $aRow);
            }
        }
        return $aResult;
    }

}
?>