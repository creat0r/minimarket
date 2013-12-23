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

class PluginMinimarket_WidgetFilter extends Widget {
    public function Exec() {
        $aFeatures = $this->PluginMinimarket_Product_GetProductTaxonomiesByType('features');
		/**
		 * Удаление повторяющихся Особенностей
		 */
		$aNewFeatures=array();
		foreach($aFeatures as $oFeatures) {
			if (!in_array($oFeatures->getText(), $aNewFeatures)) $aNewFeatures[] = $oFeatures->getText();
		}
		$aAttributes = $this->PluginMinimarket_Taxonomy_GetTaxonomiesByType('attribut');
		$aProperties = $this->PluginMinimarket_Taxonomy_GetTaxonomiesByType('property');
		$aSortParams = $this->PluginMinimarket_Product_GetArraySortParams();
		/**
		 * Определение, какая вкладка в фильтре будет активной
		 */
		$sPros = false;
		if (isset($aSortParams['pros'])) $sPros = true;		
		/**
		 * Сборка массива, состоящего из атрибутов, которые должны быть активны в фильтре
		 */
		$aIdAttributesActive = array();
		$aIdPropertiesActive = explode("~", $aSortParams['pros']);
		foreach($aProperties as $oProperty) {
			if(in_array($oProperty->getId(), $aIdPropertiesActive)) $aIdAttributesActive[] = $oProperty->getParentId();
		}
		$aProperties = $this->PluginMinimarket_Product_GetListProductPropertiesByPropertiesAndAttributes(array_merge($aProperties, $aAttributes));
		$this->Viewer_Assign('aSortParams', $aSortParams);
		$this->Viewer_Assign('sPros', $sPros);
		$this->Viewer_Assign('aProperties', $aProperties);
        $this->Viewer_Assign('aFeatures', $aNewFeatures);
        $this->Viewer_Assign('aIdAttributesActive', $aIdAttributesActive);
    }
}
?>