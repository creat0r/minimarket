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

class PluginMinimarket_ModuleDelivery_EntityService extends PluginMinimarket_ModuleDelivery_EntityDelivery {
    /**
     * Определяем правила валидации
     */
    public function Init() {
        parent::Init();
        $this->aValidateRules[] = array(
            'name', 'string', 'min' => 2, 'max' => 50,
            'allowEmpty' => false,
            'label' => $this->Lang_Get('plugin.minimarket.delivery_service_adding_name'),
            'on' => array('service','default')
        );
        $this->aValidateRules[] = array(
            'time_from', 'number', 'min' => 0, 'max' => 100,
			'integerOnly' => true,
            'label' => $this->Lang_Get('plugin.minimarket.delivery_service_adding_delivery_time'),
            'on' => array('service')
        );
        $this->aValidateRules[] = array(
            'time_to', 'number', 'min' => 0, 'max' => 100,
			'integerOnly' => true,
            'label' => $this->Lang_Get('plugin.minimarket.delivery_service_adding_delivery_time'),
            'on' => array('service')
        );
        $this->aValidateRules[] = array(
            'weight_from', 'number', 'min' => 0, 'max' => 10000,
            'label' => $this->Lang_Get('plugin.minimarket.delivery_service_adding_weight'),
            'on' => array('service')
        );
        $this->aValidateRules[] = array(
            'weight_to', 'number', 'min' => 0, 'max' => 10000,
            'label' => $this->Lang_Get('plugin.minimarket.delivery_service_adding_weight'),
            'on' => array('service')
        );
        $this->aValidateRules[] = array(
            'order_value_from', 'number', 'min' => 0, 'max' => 10000,
            'label' => $this->Lang_Get('plugin.minimarket.delivery_service_adding_order_value'),
            'on' => array('service')
        );
        $this->aValidateRules[] = array(
            'order_value_to', 'number', 'min' => 0, 'max' => 10000,
            'label' => $this->Lang_Get('plugin.minimarket.delivery_service_adding_order_value'),
            'on' => array('service')
        );
        $this->aValidateRules[] = array(
            'processing_costs', 'number', 'min' => 0, 'max' => 10000,
            'label' => $this->Lang_Get('plugin.minimarket.delivery_service_adding_processing_costs'),
            'on' => array('service')
        );
        $this->aValidateRules[] = array(
            'cost_calculation', 'cost_calculation',
            'on' => array('service')
        );
        $this->aValidateRules[] = array(
            'cost', 'cost',
            'on' => array('service')
        );
        $this->aValidateRules[] = array(
            'description', 'string', 'min' => 0, 'max' => 200,
            'label' => $this->Lang_Get('plugin.minimarket.delivery_service_adding_description'),
            'on' => array('service','default')
        );
        $this->aValidateRules[] = array(
            'location_groups', 'location_groups',
            'on' => array('service','default')
        );
        $this->aValidateRules[] = array(
            'pay_systems', 'pay_systems',
            'on' => array('service','default')
        );
	}
		
    public function ValidateCostCalculation($iValue) {
        if (in_array($iValue,array(1,2,3))) {
            return true;
        }
        return $this->Lang_Get('plugin.minimarket.delivery_service_adding_cost_calculation_error');
    }
		
    public function ValidateCost($sValue) {
        if(preg_match('/^[0-9]{0,5}\.?[0-9]{1,2}\%?$/', $sValue) || $sValue == '') {
            return true;
        }
        return $this->Lang_Get('plugin.minimarket.delivery_service_adding_cost_error');
    }
	
    public function ValidateLocationGroups($aValue) {
        if(
			is_array($aValue) && 
			!empty($aValue) &&
			($iCount = $this->PluginMinimarket_Taxonomy_GetCountTaxonomyByArrayIdAndArrayType($aValue, 'location_group')) &&
			$iCount == count($aValue)
		) {
            return true;
        }
        return $this->Lang_Get('plugin.minimarket.delivery_service_adding_location_group_error');
    }
	
    public function ValidatePaySystems($aValue) {
        if(
			is_array($aValue) && 
			!empty($aValue) &&
			($aPaySystems = $this->PluginMinimarket_Pay_GetPaySystemsByArrayId($aValue)) &&
			count($aPaySystems) == count($aValue)
		) {
            return true;
        }
        return $this->Lang_Get('plugin.minimarket.delivery_service_adding_pay_system_error');
    }
	
}

// EOF