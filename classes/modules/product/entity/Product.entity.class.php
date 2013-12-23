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

class PluginMinimarket_ModuleProduct_EntityProduct extends Entity {
    /**
     * Определение правил валидации
     */
    public function Init() {
        parent::Init();
        $this->aValidateRules[] = array(
            'name', 'string', 'min' => 2, 'max' => 200,
            'allowEmpty' => false,
            'label' => $this->Lang_Get('plugin.minimarket.product_adding_title'),
            'on' => array('product')
        );
        $this->aValidateRules[] = array(
            'url', 'url',
            'on' => array('product')
        );
        $this->aValidateRules[] = array(
            'brand', 'brand',
            'on' => array('product')
        );
        $this->aValidateRules[] = array(
            'category', 'category',
            'on' => array('product')
        );
        $this->aValidateRules[] = array(
            'attribut_and_property', 'attribut_and_property',
            'on' => array('product')
        );
        $this->aValidateRules[] = array(
            'currency', 'currency',
            'on' => array('product')
        );
        $this->aValidateRules[] = array(
            'manufacturer_code', 'string', 'max' => 200,
			'label' => $this->Lang_Get('plugin.minimarket.product_adding_manufacturer_code'),
            'on' => array('product')
        );
        $this->aValidateRules[] = array(
            'price', 'number', 'max' => pow(10, 9), 'min' => 0,
			'allowEmpty' => false,
			'label' => $this->Lang_Get('plugin.minimarket.product_adding_price'),
            'on' => array('product')
        );
        $this->aValidateRules[] = array(
            'weight', 'number', 'max' => 100000, 'min' => 0,
			'allowEmpty' => false,
			'label' => $this->Lang_Get('plugin.minimarket.product_adding_weight'),
            'on' => array('product')
        );
        $this->aValidateRules[] = array(
            'show', 'boolean',
			'label' => $this->Lang_Get('plugin.minimarket.product_adding_show'),
            'on' => array('product')
        );
        $this->aValidateRules[] = array(
            'in_stock', 'boolean',
			'label' => $this->Lang_Get('plugin.minimarket.product_adding_in_stock'),
            'on' => array('product')
        );
        $this->aValidateRules[] = array(
            'characteristics', 'tags', 'count' => 50,
            'allowEmpty' => false,
            'label' => $this->Lang_Get('plugin.minimarket.product_adding_characteristics'),
            'on' => array('product')
        );
        $this->aValidateRules[] = array(
            'features', 'tags', 'count' => 50,
            'allowEmpty' => false,
            'label' => $this->Lang_Get('plugin.minimarket.product_adding_features'),
            'on' => array('product')
        );
        $this->aValidateRules[] = array(
            'text', 'string', 'max' => 5000,
            'label' => $this->Lang_Get('plugin.minimarket.description'),
            'on' => array('product')
        );
	}
	
    public function ValidateAttributAndProperty($aValue, $aParams) {
		/**
		 * Проверка, являются ли все пришедшие значение атрибутами и свойствами
		 */
        if (empty($aValue) || count($aValue) == $this->PluginMinimarket_Taxonomy_GetCountTaxonomyByArrayIdAndArrayType($aValue, array('attribut', 'property'))) {
            return true;
        }
        return $this->Lang_Get('system_error');
    }
	
    public function ValidateCurrency($aValue, $aParams) {
        if ($this->PluginMinimarket_Currency_GetCurrencyById($aValue)) {
            return true;
        }
        return $this->Lang_Get('plugin.minimarket.product_adding_currency_error');
    }
	
    public function ValidateCategory($sValue, $aParams) {
        if ($sValue == 0 || (false !== ($oCategory = $this->PluginMinimarket_Taxonomy_GetTaxonomyById((int)$sValue)) && $oCategory->getType() == 'category')) {
            return true;
        }
        return $this->Lang_Get('plugin.minimarket.product_adding_category_error');
    }
	
    public function ValidateBrand($sValue, $aParams) {
        if ($sValue == 0 || (false!==($oBrand=$this->PluginMinimarket_Taxonomy_GetTaxonomyById((int)$sValue)) && $oBrand->getType() == 'brand')) {
            return true;
        }
        return $this->Lang_Get('plugin.minimarket.product_adding_brand_error');
    }
	
    public function ValidateUrl($sValue, $aParams) {
        if (func_check($sValue, 'login', 2, 200)) {
            return true;
        }
        return $this->Lang_Get('plugin.minimarket.product_adding_url_error');
    }
		
	// этот метод нужно удалить, и заменить его (везде где он используется) на PluginMinimarket_Product_GetArrayWebPathToProductByProducts
	public function getWebPathBuilder(){
		$aCategoriesByProduct=$this->PluginMinimarket_Taxonomy_GetChainsTaxonomiesByTaxonomies(array($this->PluginMinimarket_Taxonomy_GetTaxonomyById($this->getCategory())));
		$sWebPathProduct=Config::Get('path.root.url').'catalog/';
		if(isset($aCategoriesByProduct[0])) {
			foreach($aCategoriesByProduct[0] as $oCategory) {
				$sWebPathProduct.=$oCategory->getURL().'/';
			}
		}
		return $sWebPathProduct.$this->getURL().'/';
	}
}
?>