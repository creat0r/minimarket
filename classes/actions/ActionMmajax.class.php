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

class PluginMinimarket_ActionMmajax extends ActionPlugin {
    /**
     * Инициализация
     */
    public function Init() {
        // * Устанавливаем формат ответа
        $this->Viewer_SetResponseAjax('json');
    }
	
	/**
	 * Регистрируем евенты
	 *
	 */
	protected function RegisterEvent() {
		$this->AddEvent('mm_autocompleter', 'EventAutocompleterProductTaxonomies');
	}
	
    /**
     * Автоподставновка
     *
     */
    protected function EventAutocompleterProductTaxonomies() {
	
        // * Первые буквы переданы?
        if (!($sValue = getRequest('value', null, 'post')) || !is_string($sValue) || !in_array($this->GetParam(0),array('characteristics','features'))) {
            return;
        }
        $aItems = array();

        // * Формируем список
        $aProductTaxonomy = $this->PluginMinimarket_Product_GetProductTaxonomiesByLike($sValue,10,$this->GetParam(0));
		
        foreach ($aProductTaxonomy as $oProductTaxonomy) {
            $aItems[] = $oProductTaxonomy->getProductTaxonomyText();
        }
		
        // * Передаем результат в ajax ответ
        $this->Viewer_AssignAjax('aItems', $aItems);
    }

}
?>