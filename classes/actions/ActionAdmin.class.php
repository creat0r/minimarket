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

class PluginMinimarket_ActionAdmin extends PluginMinimarket_Inherits_ActionAdmin {

	/**
	 * Регистрируем евенты
	 *
	 */
	protected function RegisterEvent() {
		$this->AddEvent('attributes','EventAttributes');
		$this->AddEvent('attributadd','EventAttributadd');
		$this->AddEvent('attributedit','EventAttributedit');
		$this->AddEvent('attributdelete','EventAttributdelete');
		
		$this->AddEvent('attributescategories','EventAttributescategories');
		$this->AddEvent('attributescategoryadd','EventAttributescategoryadd');
		$this->AddEvent('attributescategoryedit','EventAttributescategoryedit');
		$this->AddEvent('attributescategorydelete','EventAttributescategorydelete');
		
		$this->AddEvent('propertyadd','EventPropertyadd');
		$this->AddEvent('propertyedit','EventPropertyedit');
		$this->AddEvent('propertydelete','EventPropertydelete');
		
		$this->AddEvent('mm_categories','EventMmcategories');
		$this->AddEvent('mm_category_add','EventMmcategoryadd');
		$this->AddEvent('mm_category_edit','EventMmcategoryedit');
		$this->AddEvent('mm_category_delete','EventMmcategorydelete');
		
		$this->AddEvent('mm_brands','EventMmbrands');
		$this->AddEvent('mm_brand_add','EventMmbrandadd');
		$this->AddEvent('mm_brand_edit','EventMmcbrandedit');
		$this->AddEvent('mm_brand_delete','EventMmbranddelete');
		
		$this->AddEvent('mm_delivery_services','EventDeliveryServices');
		$this->AddEvent('mm_delivery_service_add','EventDeliveryServiceAdd');
		$this->AddEvent('mm_delivery_service_edit','EventDeliveryServiceEdit');
		$this->AddEvent('mm_delivery_service_delete','EventDeliveryServiceDelete');
		
		$this->AddEvent('mm_delivery_services_automatic','EventDeliveryServicesAutomatic');
		$this->AddEvent('mm_delivery_service_automatic_edit','EventDeliveryServiceAutomaticEdit');
		
		$this->AddEvent('mm_pay_systems','EventPaySystems');
		$this->AddEvent('mm_pay_system_edit','EventPaySystemEdit');

		$this->AddEvent('mm_location_groups','EventLocationGroups');
		$this->AddEvent('mm_location_group_add','EventLocationGroupAdd');
		$this->AddEvent('mm_location_group_edit','EventLocationGroupEdit');
		$this->AddEvent('mm_location_group_delete','EventLocationGroupDelete');
		
		$this->AddEvent('mm_orders','EventOrders');
		$this->AddEvent('mm_order_edit','EventOrderEdit');
		$this->AddEvent('mm_order_delete','EventOrderDelete');
		
		$this->AddEvent('ajaxchangeordertaxonomies', 'EventAjaxChangeOrderTaxonomies');
		
		parent::RegisterEvent();
	}

    public function EventOrderDelete() {
        $this->Security_ValidateSendForm();
        /**
         * Получаем объект заказа
         */
        if (
			!($aResult = $this->PluginMinimarket_Order_GetOrdersByFilter(array('id'=>$this->GetParam(0)), ''))
			|| !isset($aResult['collection'][$this->GetParam(0)])
		) {
            return parent::EventNotFound();
        }
		$oOrder = $aResult['collection'][$this->GetParam(0)];
		/**
		 * Удаляем связи между заказом и товарами (т.е. полностью очищаем корзину)
		 */
		$this->PluginMinimarket_Order_DeleteCartObjectsByOrder($oOrder->getId());
		/**
		 * Удаляем заказ
		 */
        $this->PluginMinimarket_Order_DeleteOrder($oOrder->getId());
        Router::Location('admin/mm_orders/');
	}
	
    public function EventOrderEdit() {
        /**
         * Устанавливаем шаблон вывода
         */
		$this->SetTemplateAction('orders/edit');
		$this->_setTitle($this->Lang_Get('plugin.minimarket.order_edit_title'));
        /**
         * Получаем объект заказа
         */
        if (
			!($aResult = $this->PluginMinimarket_Order_GetOrdersByFilter(array('id'=>$this->GetParam(0)), ''))
			|| !isset($aResult['collection'][$this->GetParam(0)])
		) {
            return parent::EventNotFound();
        }
		$oOrder = $aResult['collection'][$this->GetParam(0)];
		$this->Viewer_Assign('oOrder', $oOrder);
		/**
		 * Загружаем гео-объект привязки
		 */
		$oGeoTarget = $this->Geo_GetTargetByTarget('order', $oOrder->getId());
		$this->Viewer_Assign('oGeoTarget', $oGeoTarget);
		/**
		 * Загружаем в шаблон список стран, регионов, городов
		 */
		$aCountries = $this->Geo_GetCountries(array(), array('sort' => 'asc'), 1, 300);
		$this->Viewer_Assign('aGeoCountries', $aCountries['collection']);
		if ($oGeoTarget) {
			if ($oGeoTarget->getCountryId()) {
				$aRegions = $this->Geo_GetRegions(
					array('country_id' => $oGeoTarget->getCountryId()), array('sort' => 'asc'), 1, 500
				);
				$this->Viewer_Assign('aGeoRegions', $aRegions['collection']);
			}
			if ($oGeoTarget->getRegionId()) {
				$aCities = $this->Geo_GetCities(
					array('region_id' => $oGeoTarget->getRegionId()), array('sort' => 'asc'), 1, 500
				);
				$this->Viewer_Assign('aGeoCities', $aCities['collection']);
			}
		}
		if ($oGeoTarget) {
			/**
			 * Загружаем в шаблон список доступных служб доставки (по выбранному городу)
			 */
			$this->Viewer_Assign('aDeliveryService', $this->PluginMinimarket_Delivery_GetActivationDeliveryServicesByCity($oGeoTarget->getCityId()));
		}
		/**
		 * Загружаем в шаблон список доступныхсистем оплаты, относительно данного заказа
		 */
		$this->Viewer_Assign('aPaySystem', $this->PluginMinimarket_Pay_GetAvailablePaySystemsByOrder($oOrder));
		/**
		 * По объекту заказа получаем список ID товаров, с ним связанных
		 */
		$aCartObjects = $this->PluginMinimarket_Order_GetCartObjectsByOrder($oOrder->getId());
		/**
		 * По списку ID получаем массив товаров
		 */
		$aProducts = $this->PluginMinimarket_Product_GetProductsAdditionalData(array_keys($aCartObjects));
		$this->Viewer_Assign('aProducts', $aProducts);
		$this->Viewer_Assign('aCartObjects', $aCartObjects);
		
		$_REQUEST['client_name'] = $oOrder->getClientName();
		$_REQUEST['client_index'] = $oOrder->getClientIndex();
		$_REQUEST['client_address'] = $oOrder->getClientAddress();
		$_REQUEST['client_phone'] = $oOrder->getClientPhone();
		$_REQUEST['client_comment'] = $oOrder->getClientComment();
		
        /**
         * Проверяем, отправлена ли форма с данными
         */
        if (isset($_REQUEST['submit'])) {
			if (
				!isPost('submit')
				|| !$oOrder->getStatus()
				|| !getRequest('status', null, 'post')
			) {
				return false;
			}
			/**
			 * Проверяем пришедший статус
			 */
			if (!in_array(getRequest('status', null, 'post'), array(1, 2, 3))) {
				$this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
				return false;
			}
			$oOrder->setStatus(getRequest('status', null, 'post'));
			$this->PluginMinimarket_Order_AddOrUpdateOrder($oOrder);
			Router::Location('admin/mm_orders/');
		}
	}
	
    public function EventOrders() {
        /**
         * Устанавливаем шаблон вывода
         */
		$this->SetTemplateAction('orders/list');
		$this->_setTitle($this->Lang_Get('plugin.minimarket.orders'));

        // * Передан ли номер страницы
        $nPage = $this->_getPageNum();
				
		$aResult = $this->PluginMinimarket_Order_GetOrdersByFilter(array(), '', $nPage, Config::Get('minimarket.admin.order.per_page'));
        $aPaging = $this->Viewer_MakePaging($aResult['count'], $nPage, Config::Get('minimarket.admin.order.per_page'), 4,
            Router::GetPath('admin') . 'mm_orders/');

        /**
         * Загружаем в шаблон заказы
         */		
		$this->Viewer_Assign('aOrder', $aResult['collection']);
		$this->Viewer_Assign('aPaging', $aPaging);
	}
	
    public function EventDeliveryServicesAutomatic() {
        /**
         * Устанавливаем шаблон вывода
         */
		$this->SetTemplateAction('delivery_services/automatic/list');
		$this->_setTitle($this->Lang_Get('plugin.minimarket.delivery_services'));
        /**
         * Загружаем в шаблон настраиваемые службы доставки
         */
		$this->Viewer_Assign('aDeliveryServices', $this->PluginMinimarket_Delivery_GetDeliveryServicesByType('automatic'));
	}
	
    public function EventDeliveryServiceAutomaticEdit() {
        /**
         * Получаем объект службы доставки
         */
        if(
			!($oDeliveryService = $this->PluginMinimarket_Delivery_GetDeliveryServiceById($this->GetParam(0)))
			|| $oDeliveryService->getType() != 'automatic'
		) {
			return parent::EventNotFound();
        }
        switch ($oDeliveryService->getKey()) {
			default:
				/**
				 * Для внешних служб доставки, реализованных отдельным плагином
				 */
				if(!$this->EditDeliveryServiceExternal($oDeliveryService)) return parent::EventNotFound();
		}
	}
	
    public function EditDeliveryServiceExternal($oDeliveryService) {
		$bOK = false;
		$sTitle = $this->Lang_Get('plugin.minimarket.delivery_service_edit_title');
		/**
		 * Запускаем выполнение хука
		 */
		$this->Hook_Run(
			'minimarket_delivery_service_external_edit',
			array('oDeliveryService'=>$oDeliveryService, 'bOK'=>&$bOK, 'sTitle'=>&$sTitle)
		);
		/**
		 * Установка шаблона
		 */
		$this->SetTemplateAction('delivery_services/automatic/external');
		$this->_setTitle($sTitle);
        /**
         * Получаем список групп местоположений
         */		
        $this->Viewer_Assign('aLocationGroups', $this->PluginMinimarket_Taxonomy_getTaxonomiesByType('location_group'));
        /**
         * Получаем список систем оплаты
         */		
        $this->Viewer_Assign('aPaySystems', $this->PluginMinimarket_Pay_GetAllPaySystems());
		return $bOK;
	}
	
    public function EventPaySystemEdit() {
        /**
         * Получаем объект системы оплаты
         */
        if (!($oPaySystem = $this->PluginMinimarket_Pay_GetPaySystemById($this->GetParam(0)))) {
            return parent::EventNotFound();
        }
        switch ($oPaySystem->getKey()) {
			case 'cash':
				/**
				 * Расчет наличными
				 */
				$this->EditPaySystemCash($oPaySystem);
				break;
			default:
				/**
				 * Для внешних систем оплаты, реализованных отдельным плагином
				 */
				if(!$this->EditPaySystemExternal($oPaySystem)) return parent::EventNotFound();
		}
	}
	
	protected function EditPaySystemExternal($oPaySystem) {
		$bOK = false;
		$sTitle = $this->Lang_Get('plugin.minimarket.pay_system_edit_title');
		/**
		 * Запускаем выполнение хуков
		 */
		$this->Hook_Run('minimarket_pay_system_external_edit', array('oPaySystem'=>$oPaySystem,'bOK'=>&$bOK,'sTitle'=>&$sTitle));
		/**
		 * Установка шаблона
		 */
		$this->SetTemplateAction('pay_systems/external');
		$this->_setTitle($sTitle);
		return $bOK;
	}
	
	protected function EditPaySystemCash($oPaySystem) {
        /**
         * Устанавливаем шаблон вывода
         */
		$this->SetTemplateAction('pay_systems/cash');
		$this->_setTitle($this->Lang_Get('plugin.minimarket.pay_system_cash_edit_title'));
        /**
         * Проверяем отправлена ли форма с данными
         */
        if (isset($_REQUEST['cash_submit'])) {
			if (!isPost('cash_submit')) {
				return false;
			}
			/**
			 * Проверка корректности полей формы
			 */
			if (!$this->CheckPaySystemCashFields()) {
				return false;
			}
			$oPaySystem->setKey('cash');
			$oPaySystem->setName(getRequest('name'));
			$oPaySystem->setActivation(getRequest('activation'));
			if ($this->PluginMinimarket_Pay_AddOrUpdatePaySystem($oPaySystem)) {
				Router::Location('admin/mm_pay_systems/?edit=success');
			}
        } else {
            $_REQUEST['name'] = $oPaySystem->getName();
            $_REQUEST['activation'] = $oPaySystem->getActivation();
        }
	}
	
    public function EventPaySystems() {
        /**
         * Устанавливаем шаблон вывода
         */
		$this->SetTemplateAction('pay_systems/list');
		$this->_setTitle($this->Lang_Get('plugin.minimarket.pay_systems'));
        /**
         * Получаем список групп местоположений
         */
        $this->Viewer_Assign('aPaySystems', $this->PluginMinimarket_Pay_GetAllPaySystems());
	}
	
    public function EventLocationGroupDelete() {
        $this->Security_ValidateSendForm();
        if (!($oLocationGroup = $this->PluginMinimarket_Taxonomy_GetTaxonomyById($this->GetParam(0))) || $oLocationGroup->getTaxonomyType()!='location_group') {
            return parent::EventNotFound();
        }
		/**
		 * Удаляем связи местоположений
		 */
		$this->PluginMinimarket_Link_DeleteLinkByParentAndType($oLocationGroup->getId(),'location_group_city');
		/**
		 * Удаляем группу местоположений
		 */
        $this->PluginMinimarket_Taxonomy_DeleteTaxonomy($oLocationGroup);
        Router::Location('admin/mm_location_groups/');
	}
	
    public function EventLocationGroupEdit() {
        /**
         * Устанавливаем шаблон вывода
         */
		$this->SetTemplateAction('location_groups/add');
		$this->_setTitle($this->Lang_Get('plugin.minimarket.location_group_editing'));
        /**
         * Получаем группу местоположений
         */
        if (!($oLocationGroup = $this->PluginMinimarket_Taxonomy_GetTaxonomyById($this->GetParam(0))) || $oLocationGroup->getTaxonomyType()!='location_group') {
            return parent::EventNotFound();
        }
		$this->Viewer_Assign('oLocationGroup', $oLocationGroup);
        /**
         * Загружаем в шаблон список стран, регионов, городов
         */
		$aGeoCountries = $this->PluginMinimarket_Geo_GetCountriesByConfig();
		$aGeoRegions = $this->PluginMinimarket_Geo_GetRegionsByCountries($aGeoCountries['id']);
		$this->Viewer_Assign('aGeoCountries', $aGeoCountries['collection']);
		$this->Viewer_Assign('aGeoRegions', $aGeoRegions['collection']);
		$this->Viewer_Assign('aGeoCities', $this->PluginMinimarket_Geo_GetCitiesByRegions($aGeoRegions['id']));

        /**
         * Проверяем отправлена ли форма с данными
         */
        if (isset($_REQUEST['location_group_add_submit'])) {
			/**
			 * Проверка корректности полей формы
			 */
			if(!$this->CheckLocationGroupFields()) {
				return false;
			}
			/**
			 * Обновляем объект Группа местоположений
			 */
			$oLocationGroup->setName(getRequest('location_group_name'));

			if($this->PluginMinimarket_Taxonomy_UpdateTaxonomy($oLocationGroup)) {
				/**
				 * Удаляем старые связи местоположений
				 */
				$this->PluginMinimarket_Link_DeleteLinkByParentAndType($oLocationGroup->getId(),'location_group_city');
				/**
				 * Создаем объект каждого местоположения и добавляем в БД одним запросом
				 */
				$aCitiesId = getRequest('location_group_city_id');
				if(!is_array($aCitiesId)) {
					$aCitiesId = array($aCitiesId);
				}
				$aObjectCity = array();
				foreach($aCitiesId as $idCity) {
					$oLink = Engine::GetEntity('PluginMinimarket_ModuleLink_EntityLink');
					$oLink->setObjectId((int)$idCity);
					$oLink->setParentId($oLocationGroup->getId());
					$oLink->setObjectType('location_group_city');
					$aObjectCity[] = $oLink;
				}
				$this->PluginMinimarket_Link_AddLinks($aObjectCity);
				Router::Location('admin/mm_location_groups/?edit=success');
			}
        } else {
            $_REQUEST['location_group_name'] = $oLocationGroup->getName();
            $_REQUEST['location_group_city_id'] = $this->PluginMinimarket_Link_GetLinksByParentAndType($oLocationGroup->getId(),'location_group_city');
        }
	}
	
    public function EventLocationGroupAdd() {
        /**
         * Устанавливаем шаблон вывода
         */
		$this->SetTemplateAction('location_groups/add');
		$this->_setTitle($this->Lang_Get('plugin.minimarket.location_group_adding'));
				
        /**
         * Загружаем в шаблон список стран, регионов, городов
         */
		$aGeoCountries = $this->PluginMinimarket_Geo_GetCountriesByConfig();
		$aGeoRegions = $this->PluginMinimarket_Geo_GetRegionsByCountries($aGeoCountries['id']);
		$this->Viewer_Assign('aGeoCountries', $aGeoCountries['collection']);
		$this->Viewer_Assign('aGeoRegions', $aGeoRegions['collection']);
		$this->Viewer_Assign('aGeoCities', $this->PluginMinimarket_Geo_GetCitiesByRegions($aGeoRegions['id']));
		
        /**
         * Проверяем отправлена ли форма с данными
         */
        if(!isPost('location_group_add_submit')) {
            return false;
        }
		
        /**
         * Проверка корректности полей формы
         */
        if(!$this->CheckLocationGroupFields()) {
            return false;
        }

        /**
         * Создаем и добавляем в БД объект Группа местоположений
         */		
        $oLocationGroup = Engine::GetEntity('PluginMinimarket_ModuleTaxonomy_EntityTaxonomy');
        $oLocationGroup->setTaxonomyType('location_group');
        $oLocationGroup->setName(getRequest('location_group_name'));

        if($nId = $this->PluginMinimarket_Taxonomy_AddTaxonomy($oLocationGroup)) {
			/**
			 * Создаем объект каждого местоположения и добавляем в БД одним запросом
			 */
			$aCitiesId = getRequest('location_group_city_id');
			if(!is_array($aCitiesId)) {
				$aCitiesId = array($aCitiesId);
			}
			$aObjectCity = array();
			foreach($aCitiesId as $idCity) {
				$oLink = Engine::GetEntity('PluginMinimarket_ModuleLink_EntityLink');
				$oLink->setObjectId((int)$idCity);
				$oLink->setParentId((int)$nId);
				$oLink->setObjectType('location_group_city');
				$aObjectCity[] = $oLink;
			}
			$this->PluginMinimarket_Link_AddLinks($aObjectCity);
            Router::Location('admin/mm_location_groups/?add=success');
        }
	}
	
    public function EventLocationGroups() {
        /**
         * Устанавливаем шаблон вывода
         */
		$this->SetTemplateAction('location_groups/groups');
		$this->_setTitle($this->Lang_Get('plugin.minimarket.location_groups'));
        /**
         * Получаем список групп местоположений
         */		
        $this->Viewer_Assign('aLocationGroups', $this->PluginMinimarket_Taxonomy_getTaxonomiesByType('location_group'));
	}

    public function EventDeliveryServiceDelete() {
        $this->Security_ValidateSendForm();
		
        /**
         * Получаем объект службы доставки
         */
        if(
			!($oDeliveryService = $this->PluginMinimarket_Delivery_GetDeliveryServiceById($this->GetParam(0)))
			|| $oDeliveryService->getType() != 'tunable'
		) {
			return parent::EventNotFound();
        }

        $this->PluginMinimarket_Delivery_DeleteDeliveryService($oDeliveryService);
        Router::Location('admin/mm_delivery_services/');
	}
	
    public function EventDeliveryServiceEdit() {
        /**
         * Устанавливаем шаблон вывода
         */
		$this->SetTemplateAction('delivery_services/tunable/add');
		$this->_setTitle($this->Lang_Get('plugin.minimarket.delivery_service_editing'));
        /**
         * Получаем объект службы доставки
         */
        if(
			!($oDeliveryService = $this->PluginMinimarket_Delivery_GetDeliveryServiceById($this->GetParam(0)))
			|| $oDeliveryService->getType() != 'tunable'
		) {
			return parent::EventNotFound();
        }
        $this->Viewer_Assign('oDeliveryService', $oDeliveryService);
        /**
         * Получаем список групп местоположений
         */		
        $this->Viewer_Assign('aLocationGroups', $this->PluginMinimarket_Taxonomy_getTaxonomiesByType('location_group'));
        /**
         * Получаем список систем оплаты
         */		
        $this->Viewer_Assign('aPaySystems', $this->PluginMinimarket_Pay_GetAllPaySystems());
        /**
         * Проверяем, отправлена ли форма с данными
         */
        if (isset($_REQUEST['delivery_service_add_submit'])) {
			$oDeliveryService->_setValidateScenario('service');
			/**
			 * Заполняем поля для валидации
			 */
			$oDeliveryService->setName(strip_tags(getRequestStr('name')));
			$oDeliveryService->setActivation(getRequestStr('activation'));
			$oDeliveryService->setTimeFrom(getRequestStr('time_from'));
			$oDeliveryService->setTimeTo(getRequestStr('time_to'));
			$oDeliveryService->setWeightFrom(getRequestStr('weight_from'));
			$oDeliveryService->setWeightTo(getRequestStr('weight_to'));
			$oDeliveryService->setOrderValueFrom(getRequestStr('order_value_from'));
			$oDeliveryService->setOrderValueTo(getRequestStr('order_value_to'));
			$oDeliveryService->setProcessingCosts(getRequestStr('processing_costs'));
			$oDeliveryService->setCostCalculation(getRequestStr('cost_calculation'));
			$oDeliveryService->setCost(getRequestStr('cost'));
			$oDeliveryService->setDescription(getRequestStr('description'));
			$oDeliveryService->setLocationGroups(getRequestPost('location_groups'));
			$oDeliveryService->setPaySystems(getRequestPost('pay_systems'));
			/**
			 * Проверка корректности полей формы
			 */
			if(!$this->CheckDeliveryServiceFields($oDeliveryService)) {
				return false;
			}
			/**
			 * Обновляем службу доставки
			 */			
			if($this->PluginMinimarket_Delivery_UpdateDeliveryService($oDeliveryService)) {
				Router::Location('admin/mm_delivery_services/?edit=success');
			}
        } else {
			$_REQUEST['name'] = $oDeliveryService->getName();
			$_REQUEST['activation'] = $oDeliveryService->getActivation();
			$_REQUEST['time_from'] = $oDeliveryService->getTimeFrom();
			$_REQUEST['time_to'] = $oDeliveryService->getTimeTo();
			$_REQUEST['weight_from'] = $oDeliveryService->getWeightFrom();
			$_REQUEST['weight_to'] = $oDeliveryService->getWeightTo();
			$_REQUEST['order_value_from'] = $oDeliveryService->getOrderValueFrom();
			$_REQUEST['order_value_to'] = $oDeliveryService->getOrderValueTo();
			$_REQUEST['processing_costs'] = $oDeliveryService->getProcessingCosts();
			$_REQUEST['cost_calculation'] = $oDeliveryService->getCostCalculation();
			$_REQUEST['cost'] = $oDeliveryService->getCost();
			$_REQUEST['description'] = $oDeliveryService->getDescription();
			/**
			 * Получаем список ID групп местоположений, связанных с данной службой доставки
			 */
			$_REQUEST['location_groups'] = $this->PluginMinimarket_Link_GetLinksByParentAndType($oDeliveryService->getId(),'delivery_service_location_group');
			/**
			 * Получаем список ID систем оплаты, связанных с данной службой доставки
			 */
			$_REQUEST['pay_systems'] = $this->PluginMinimarket_Link_GetLinksByParentAndType($oDeliveryService->getId(),'delivery_service_pay_system');
        }
	}
	
    public function EventDeliveryServiceAdd() {
        /**
         * Устанавливаем шаблон вывода
         */
		$this->SetTemplateAction('delivery_services/tunable/add');
		$this->_setTitle($this->Lang_Get('plugin.minimarket.delivery_service_adding'));
        /**
         * Получаем список групп местоположений
         */		
        $this->Viewer_Assign('aLocationGroups', $this->PluginMinimarket_Taxonomy_getTaxonomiesByType('location_group'));
        /**
         * Получаем список систем оплаты
         */		
        $this->Viewer_Assign('aPaySystems', $this->PluginMinimarket_Pay_GetAllPaySystems());
        /**
         * Проверяем отправлена ли форма с данными
         */
        if(!isPost('delivery_service_add_submit')) {
            return false;
        }
		$oDeliveryService = Engine::GetEntity('PluginMinimarket_ModuleDelivery_EntityService');
		$oDeliveryService->_setValidateScenario('service');
		/**
		 * Заполняем поля для валидации
		 */
		$oDeliveryService->setName(strip_tags(getRequestStr('name')));
		$oDeliveryService->setActivation(getRequestStr('activation'));
		$oDeliveryService->setTimeFrom(getRequestStr('time_from'));
		$oDeliveryService->setTimeTo(getRequestStr('time_to'));
		$oDeliveryService->setWeightFrom(getRequestStr('weight_from'));
		$oDeliveryService->setWeightTo(getRequestStr('weight_to'));
		$oDeliveryService->setOrderValueFrom(getRequestStr('order_value_from'));
		$oDeliveryService->setOrderValueTo(getRequestStr('order_value_to'));
		$oDeliveryService->setProcessingCosts(getRequestStr('processing_costs'));
		$oDeliveryService->setCostCalculation(getRequestStr('cost_calculation'));
		$oDeliveryService->setCost(getRequestStr('cost'));
		$oDeliveryService->setDescription(getRequestStr('description'));
		$oDeliveryService->setLocationGroups(getRequestPost('location_groups'));
		$oDeliveryService->setPaySystems(getRequestPost('pay_systems'));
		$oDeliveryService->setType('tunable');
        /**
         * Проверка корректности полей формы
         */
        if(!$this->CheckDeliveryServiceFields($oDeliveryService)) {
            return false;
        }
        /**
         * Добавляем службу доставки в БД
         */
        if ($this->PluginMinimarket_Delivery_AddDeliveryService($oDeliveryService)) {
            Router::Location('admin/mm_delivery_services/');
        }
	}
	
    public function EventDeliveryServices() {
        /**
         * Устанавливаем шаблон вывода
         */
		$this->SetTemplateAction('delivery_services/tunable/list');
		$this->_setTitle($this->Lang_Get('plugin.minimarket.delivery_services'));
        /**
         * Загружаем в шаблон настраиваемые службы доставки
         */
		$this->Viewer_Assign('aDeliveryServices', $this->PluginMinimarket_Delivery_GetDeliveryServicesByType('tunable'));
	}
	
    public function EventAttributescategoryedit() {
        /**
         * Устанавливаем шаблон вывода
         */
        $this->_setTitle($this->Lang_Get('plugin.minimarket.attributes_category_edit_title'));
        $this->SetTemplateAction('attributescategoryadd');
        /**
         * Получаем аттрибут
         */
        if (!($oAttributesCategory=$this->PluginMinimarket_Taxonomy_GetTaxonomyById($this->GetParam(0))) || $oAttributesCategory->getTaxonomyType()!='attributes_category') {
            return parent::EventNotFound();
        }
        $this->Viewer_Assign('oAttributesCategory', $oAttributesCategory);
        $this->Viewer_Assign('aAttributes', $this->PluginMinimarket_Taxonomy_GetTaxonomiesByType('attribut'));
		
        /**
         * Проверяем отправлена ли форма с данными
         */
        if (isset($_REQUEST['submit_attributes_category_add'])) {
            /**
             * Обрабатываем отправку формы
             */
            return $this->SubmitAttributesCategoryEdit($oAttributesCategory);
        } else {
            $_REQUEST['adding_attributes_category_name'] = $oAttributesCategory->getName();
        }
	}
	
	protected function SubmitAttributesCategoryEdit($oAttributesCategory) {
        /**
         * Проверка корректности полей формы
         */
        if (!$this->CheckAttributesCategoryFields('edit')) {
            return false;
        }
		
        $oAttributesCategory->setTaxonomyConfig(serialize(getRequest('attribut_sel')));
        $oAttributesCategory->setName(getRequest('adding_attributes_category_name'));

        if ($this->PluginMinimarket_Taxonomy_UpdateTaxonomy($oAttributesCategory)) {
            Router::Location('admin/attributescategories/?edit=success');
        }
	}
	
    public function EventAttributescategorydelete() {
        $this->Security_ValidateSendForm();
		
        if (false!==($oAttributesCategory = $this->PluginMinimarket_Taxonomy_GetTaxonomyById($this->GetParam(0))) && $oAttributesCategory->getTaxonomyType()=='attributes_category') {
			$this->PluginMinimarket_Taxonomy_DeleteAttribut($oAttributesCategory);
			Router::Location('admin/attributescategories/');
        } else {
			return parent::EventNotFound();
		}
	}
	
    public function EventAttributescategoryadd() {
		$this->_setTitle($this->Lang_Get('plugin.minimarket.adding_attributes_category'));
        /**
         * Обрабатываем отправку формы
         */
        return $this->SubmitAttributesCategoryAdd();
	}
	
	protected function SubmitAttributesCategoryAdd() {
        /**
         * Проверяем отправлена ли форма с данными
         */
        if (!isPost('submit_attributes_category_add')) {
            return false;
        }
		
        /**
         * Проверка корректности полей формы
         */
        if (!$this->CheckAttributesCategoryFields()) {
            return false;
        }
		
        $oAttributesCategory = Engine::GetEntity('PluginMinimarket_ModuleTaxonomy_EntityTaxonomy');
        $oAttributesCategory->setTaxonomyType('attributes_category');
        $oAttributesCategory->setName(getRequest('adding_attributes_category_name'));

        if ($sId = $this->PluginMinimarket_Taxonomy_AddTaxonomy($oAttributesCategory)) {
            Router::Location('admin/attributescategoryedit/'.$sId.'/');
        }
	}
	
    public function EventAttributescategories() {
		$this->_setTitle($this->Lang_Get('plugin.minimarket.attributes_category'));
		
        /*
         * Получаем список
         */
        $this->Viewer_Assign('aAttributesCategory', $this->PluginMinimarket_Taxonomy_getTaxonomiesByType('attributes_category'));
		
		if (getRequest('add')) $this->Message_AddNoticeSingle($this->Lang_Get('plugin.minimarket.adding_attribut_category_ok'));
		if (getRequest('edit')) $this->Message_AddNoticeSingle($this->Lang_Get('plugin.minimarket.adding_attribut_category_edit'));
	}
	
    public function EventAjaxChangeOrderTaxonomies() {
        /**
         * Устанавливаем формат ответа
         */
        $this->Viewer_SetResponseAjax('json');

        if (!$this->User_IsAuthorization()) {
            $this->Message_AddErrorSingle($this->Lang_Get('need_authorization'), $this->Lang_Get('error'));
            return;
        }
        if (!$this->oUserCurrent->isAdministrator()) {
            $this->Message_AddErrorSingle($this->Lang_Get('need_authorization'), $this->Lang_Get('error'));
            return;
        }
        if (!getRequest('order')) {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error'), $this->Lang_Get('error'));
            return;
        }


        if (is_array(getRequest('order'))) {

            foreach (getRequest('order') as $oOrder) {
                if (is_numeric($oOrder['order']) && is_numeric($oOrder['id']) && $oTaxonomy = $this->PluginMinimarket_Taxonomy_GetTaxonomyById($oOrder['id'])) {
                    $oTaxonomy->setTaxonomySort($oOrder['order']);
                    $this->PluginMinimarket_Taxonomy_UpdateTaxonomy($oTaxonomy);
                }
            }

            $this->Message_AddNoticeSingle($this->Lang_Get('action.admin.save_sort_success'));
            return;
        } else {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error'), $this->Lang_Get('error'));
            return;
        }

    }
	
	protected function EventMmbranddelete() {
        $this->Security_ValidateSendForm();
		
        if (!($oBrand = $this->PluginMinimarket_Taxonomy_GetTaxonomyById($this->GetParam(0))) || $oBrand->getTaxonomyType()!='brand') {
            return parent::EventNotFound();
        }

        $this->PluginMinimarket_Taxonomy_DeleteTaxonomy($oBrand);
        Router::Location('admin/mm_brands/');
	}
	
	protected function EventMmcbrandedit() {
        /**
         * Устанавливаем шаблон вывода
         */
        $this->_setTitle($this->Lang_Get('plugin.minimarket.brand_edit_title'));
        $this->SetTemplateAction('brandadd');
        /**
         * Получаем свойство аттрибута
         */
        if (!($oBrand = $this->PluginMinimarket_Taxonomy_GetTaxonomyById($this->GetParam(0))) || $oBrand->getTaxonomyType()!='brand') {
            return parent::EventNotFound();
        }
        $this->Viewer_Assign('oBrand', $oBrand);

        /**
         * Проверяем отправлена ли форма с данными
         */
        if (isset($_REQUEST['submit_brand_add'])) {
            /**
             * Обрабатываем отправку формы
             */
            return $this->SubmitBrandEdit($oBrand);
        } else {
            $_REQUEST['brand_name'] = $oBrand->getName();
            $_REQUEST['brand_url'] = $oBrand->getURL();
            $_REQUEST['brand_description'] = $oBrand->getDescription();
        }
	}
	
	protected function SubmitBrandEdit($oBrand) {
        /**
         * Проверяем отправлена ли форма с данными
         */
        if (!isPost('submit_brand_add')) {
            return false;
        }
		
        /**
         * Проверка корректности полей формы
         */
		
        if (!$this->CheckBrandFields($oBrand->getURL())) {
            return false;
        }
        $oBrand->setName(getRequest('brand_name'));
        $oBrand->setURL(getRequest('brand_url'));
        $oBrand->setParent(0);
        $oBrand->setDescription(getRequest('brand_description'));
        if ($this->PluginMinimarket_Taxonomy_UpdateTaxonomy($oBrand)) {
            Router::Location('admin/mm_brands/?edit=success');
        }
	}

	protected function EventMmbrandadd() {
        /**
         * Устанавливаем шаблон вывода
         */
        $this->_setTitle($this->Lang_Get('plugin.minimarket.brand_add_title'));
        $this->SetTemplateAction('brandadd');

        /**
         * Обрабатываем отправку формы
         */
        return $this->SubmitBrandAdd();
	}

	protected function SubmitBrandAdd() {
        /**
         * Проверяем отправлена ли форма с данными
         */
        if (!isPost('submit_brand_add')) {
            return false;
        }
		
        /**
         * Проверка корректности полей формы
         */
        if (!$this->CheckBrandFields()) {
            return false;
        }
		
        $oBrand = Engine::GetEntity('PluginMinimarket_ModuleTaxonomy_EntityTaxonomy');
        $oBrand->setParent(0);
        $oBrand->setTaxonomyType('brand');
        $oBrand->setName(getRequest('brand_name'));
        $oBrand->setURL(getRequest('brand_url'));
        $oBrand->setDescription(getRequest('brand_description'));

        if ($this->PluginMinimarket_Taxonomy_AddTaxonomy($oBrand)) {
            Router::Location('admin/mm_brands/?add=success');
        }
	}
	
	protected function EventMmbrands() {
        /**
         * Устанавливаем шаблон вывода
         */
        $this->_setTitle($this->Lang_Get('plugin.minimarket.brands'));
        $this->SetTemplateAction('brands');
		
        /*
         * Получаем список
         */
        $this->Viewer_Assign('aBrands', $this->PluginMinimarket_Taxonomy_getTaxonomiesByType('brand'));
		
		if (getRequest('add')) $this->Message_AddNoticeSingle($this->Lang_Get('plugin.minimarket.brans_add_ok'));
		if (getRequest('edit')) $this->Message_AddNoticeSingle($this->Lang_Get('plugin.minimarket.brans_edit_ok'));
	}
	
	protected function EventMmcategorydelete() {
        $this->Security_ValidateSendForm();
		
        if (!($oCategory = $this->PluginMinimarket_Taxonomy_GetTaxonomyById($this->GetParam(0))) || $oCategory->getTaxonomyType()!='category') {
            return parent::EventNotFound();
        }
		
		$aChildrenId=$this->PluginMinimarket_Taxonomy_GetArrayIdChildrenCategoriesByIdCategory($this->GetParam(0));
		
        $this->PluginMinimarket_Taxonomy_DeleteTaxonomiesByArrayId($aChildrenId);
        Router::Location('admin/mm_categories/');
	}
	
	protected function EventMmcategoryedit() {
        /**
         * Устанавливаем шаблон вывода
         */
        $this->_setTitle($this->Lang_Get('plugin.minimarket.category_edit_title'));
        $this->SetTemplateAction('categoryadd');	
        /**
         * Получаем свойство аттрибута
         */
        if (!($oCategory = $this->PluginMinimarket_Taxonomy_GetTaxonomyById($this->GetParam(0))) || $oCategory->getTaxonomyType()!='category') {
            return parent::EventNotFound();
        }
        $this->Viewer_Assign('oCategory', $oCategory);
		$this->Viewer_Assign('aCategories', $this->PluginMinimarket_Taxonomy_GetTreeCategories());

        /**
         * Проверяем отправлена ли форма с данными
         */
        if (isset($_REQUEST['submit_category_add'])) {
            /**
             * Обрабатываем отправку формы
             */
            return $this->SubmitCategoryEdit($oCategory);
        } else {
            $_REQUEST['category_name'] = $oCategory->getName();
            $_REQUEST['category_url'] = $oCategory->getURL();
            $_REQUEST['category_select'] = $oCategory->getParent();
            $_REQUEST['category_description'] = $oCategory->getDescription();
        }	
	}
	
	protected function SubmitCategoryEdit($oCategory) {
        /**
         * Проверяем отправлена ли форма с данными
         */
        if (!isPost('submit_category_add')) {
            return false;
        }
		
        /**
         * Проверка корректности полей формы
         */
        if (!$this->CheckMMCategoryFields($oCategory->getURL(),$oCategory->getParent(),$oCategory->getId())) {
            return false;
        }
        $oCategory->setName(getRequest('category_name'));
        $oCategory->setURL(getRequest('category_url'));
        $oCategory->setParent(getRequest('category_select'));
        $oCategory->setDescription(getRequest('category_description'));
        if ($this->PluginMinimarket_Taxonomy_UpdateTaxonomy($oCategory)) {
            Router::Location('admin/mm_categories/?edit=success');
        }
	}
	
	protected function EventMmcategoryadd() {
        /**
         * Устанавливаем шаблон вывода
         */
        $this->_setTitle($this->Lang_Get('plugin.minimarket.category_add_title'));
        $this->SetTemplateAction('categoryadd');
		
		$this->Viewer_Assign('aCategories', $this->PluginMinimarket_Taxonomy_GetTreeCategories());
		
        /**
         * Обрабатываем отправку формы
         */
        return $this->SubmitCategoryAdd();
	}
	
	protected function EventMmcategories() {
        /**
         * Устанавливаем шаблон вывода
         */
        $this->_setTitle($this->Lang_Get('plugin.minimarket.categories'));
        $this->SetTemplateAction('categories');
		
        /*
         * Получаем список
         */
        $this->Viewer_Assign('aCategories', $this->PluginMinimarket_Taxonomy_GetTreeCategories());
		
		if (getRequest('add')) $this->Message_AddNoticeSingle($this->Lang_Get('plugin.minimarket.category_add_ok'));
		if (getRequest('edit')) $this->Message_AddNoticeSingle($this->Lang_Get('plugin.minimarket.category_edit_ok'));
	}
	
	protected function EventAttributdelete() {
        $this->Security_ValidateSendForm();
		
        if (!$oAttribut = $this->PluginMinimarket_Taxonomy_GetTaxonomyById($this->GetParam(0))) {
            return parent::EventNotFound();
        }

        $this->PluginMinimarket_Taxonomy_DeleteAttribut($oAttribut);
        Router::Location('admin/attributes/');
	}
	
	protected function EventPropertydelete() {
        $this->Security_ValidateSendForm();
		
        if (!$oProperty = $this->PluginMinimarket_Taxonomy_GetTaxonomyById($this->GetParam(0))) {
            return parent::EventNotFound();
        }

        $this->PluginMinimarket_Taxonomy_DeleteTaxonomy($oProperty);
        Router::Location('admin/attributedit/' . $oProperty->getParent() . '/?propertydelete=success');
	}
	
	protected function EventPropertyedit() {
        /**
         * Устанавливаем шаблон вывода
         */
        $this->_setTitle($this->Lang_Get('plugin.minimarket.property_edit_title'));
        $this->SetTemplateAction('propertyadd');	
        /**
         * Получаем свойство аттрибута
         */
        if (!($oProperty = $this->PluginMinimarket_Taxonomy_GetTaxonomyById($this->GetParam(0))) || $oProperty->getParent()==0) {
            return parent::EventNotFound();
        }
        $this->Viewer_Assign('oProperty', $oProperty);
		
		$oAttribut=$this->PluginMinimarket_Taxonomy_GetTaxonomyById($oProperty->getParent());
        $this->Viewer_Assign('oAttribut',$oAttribut);
		
        /**
         * Проверяем отправлена ли форма с данными
         */
        if (isset($_REQUEST['submit_property_add'])) {
            /**
             * Обрабатываем отправку формы
             */
            return $this->SubmitPropertyEdit($oProperty,$oAttribut);
        } else {
            $_REQUEST['property_id'] = $oProperty->getId();
            $_REQUEST['property_name'] = $oProperty->getName();
            $_REQUEST['property_url'] = $oProperty->getURL();
            $_REQUEST['property_description'] = $oProperty->getDescription();
        }
	}
	
	protected function EventPropertyadd() {
        /**
         * Получаем аттрибут
         */
        if (!$oAttribut = $this->PluginMinimarket_Taxonomy_GetTaxonomyById($this->GetParam(0))) {
            return parent::EventNotFound();
        }
		
		$this->_setTitle($this->Lang_Get('plugin.minimarket.property_add_title'));
		
		$this->Viewer_Assign('oAttribut', $oAttribut);
		
        /**
         * Обрабатываем отправку формы
         */
        return $this->SubmitPropertyAdd($oAttribut);
	}
	
	protected function SubmitPropertyAdd($oAttribut) {
        /**
         * Проверяем отправлена ли форма с данными
         */
        if (!isPost('submit_property_add')) {
            return false;
        }
		
        /**
         * Проверка корректности полей формы
         */
        if (!$this->CheckPropertyFields()) {
            return false;
        }
		
        $oProperty = Engine::GetEntity('PluginMinimarket_ModuleTaxonomy_EntityTaxonomy');
        $oProperty->setParent($oAttribut->getId());
        $oProperty->setTaxonomyType('property');
        $oProperty->setName(getRequest('property_name'));
        $oProperty->setURL(getRequest('property_url'));
        $oProperty->setDescription(getRequest('property_description'));

        if ($this->PluginMinimarket_Taxonomy_AddTaxonomy($oProperty)) {
            Router::Location('admin/attributedit/'.$oAttribut->getId().'/?add=success');
        }
	}
		
	protected function EventAttributedit() {
        /**
         * Устанавливаем шаблон вывода
         */
        $this->_setTitle($this->Lang_Get('plugin.minimarket.attribut_edit_title'));
        $this->SetTemplateAction('attributadd');	
        /**
         * Получаем аттрибут
         */
        if (!($oAttribut = $this->PluginMinimarket_Taxonomy_GetTaxonomyById($this->GetParam(0))) || $oAttribut->getParent()!=0) {
            return parent::EventNotFound();
        }
        $this->Viewer_Assign('oAttribut', $oAttribut);
        $this->Viewer_Assign('aProperties', $this->PluginMinimarket_Taxonomy_GetPropertiesByAttributId($oAttribut->getId()));
		
        /**
         * Проверяем отправлена ли форма с данными
         */
        if (isset($_REQUEST['submit_attribut_add'])) {
            /**
             * Обрабатываем отправку формы
             */
            return $this->SubmitAttributEdit($oAttribut);
        } else {
            $_REQUEST['attribut_id'] = $oAttribut->getId();
            $_REQUEST['adding_attribut_name'] = $oAttribut->getName();
            $_REQUEST['adding_attribut_url'] = $oAttribut->getURL();
            $_REQUEST['adding_attribut_description'] = $oAttribut->getDescription();
        }
	}
	
	protected function SubmitAttributEdit($oAttribut) {
        /**
         * Проверяем отправлена ли форма с данными
         */
        if (!isPost('submit_attribut_add')) {
            return false;
        }
		
        /**
         * Проверка корректности полей формы
         */
        if (!$this->CheckAttributFields()) {
            return false;
        }
		
        $oAttribut->setParent(0);
        $oAttribut->setName(getRequest('adding_attribut_name'));
        $oAttribut->setURL(getRequest('adding_attribut_url'));
        $oAttribut->setDescription(getRequest('adding_attribut_description'));

        if ($this->PluginMinimarket_Taxonomy_UpdateTaxonomy($oAttribut)) {
            Router::Location('admin/attributedit/'.$oAttribut->getId().'/');
        }
	}
	
	protected function SubmitPropertyEdit($oProperty,$oAttribut) {
        /**
         * Проверяем отправлена ли форма с данными
         */
        if (!isPost('submit_property_add')) {
            return false;
        }
		
        /**
         * Проверка корректности полей формы
         */
        if (!$this->CheckPropertyFields()) {
            return false;
        }
        $oProperty->setName(getRequest('property_name'));
        $oProperty->setURL(getRequest('property_url'));
        $oProperty->setDescription(getRequest('property_description'));

        if ($this->PluginMinimarket_Taxonomy_UpdateTaxonomy($oProperty)) {
            Router::Location('admin/attributedit/'.$oAttribut->getId().'/?edit=success');
        }
	}

	protected function EventAttributes() {
		$this->_setTitle($this->Lang_Get('plugin.minimarket.attributes'));
		
        /*
         * Получаем список
         */
        $this->Viewer_Assign('aAttributes', $this->PluginMinimarket_Taxonomy_getTaxonomiesByType('attribut'));
        $this->Viewer_Assign('aAttributesCategories', $this->PluginMinimarket_Taxonomy_getTaxonomiesByType('attributes_category'));
		
		if (getRequest('add')) $this->Message_AddNoticeSingle($this->Lang_Get('plugin.minimarket.adding_attribut_ok'));
		if (getRequest('edit')) $this->Message_AddNoticeSingle($this->Lang_Get('plugin.minimarket.adding_attribut_edit'));
	}

	protected function EventAttributadd() {
		$this->_setTitle($this->Lang_Get('plugin.minimarket.adding_attribut'));
        /**
         * Обрабатываем отправку формы
         */
        return $this->SubmitAttributAdd();
	}
	
	protected function SubmitAttributAdd() {
        /**
         * Проверяем отправлена ли форма с данными
         */
        if (!isPost('submit_attribut_add')) {
            return false;
        }
		
        /**
         * Проверка корректности полей формы
         */
        if (!$this->CheckAttributFields()) {
            return false;
        }
		
        $oAttribut = Engine::GetEntity('PluginMinimarket_ModuleTaxonomy_EntityTaxonomy');
        $oAttribut->setParent(0);
        $oAttribut->setTaxonomyType('attribut');
        $oAttribut->setName(getRequest('adding_attribut_name'));
        $oAttribut->setURL(getRequest('adding_attribut_url'));
        $oAttribut->setDescription(getRequest('adding_attribut_description'));

        if ($sId = $this->PluginMinimarket_Taxonomy_AddTaxonomy($oAttribut)) {
            Router::Location('admin/attributedit/'.$sId.'/');
        }
	}
	
	protected function SubmitCategoryAdd() {
        /**
         * Проверяем отправлена ли форма с данными
         */
        if (!isPost('submit_category_add')) {
            return false;
        }
		
        /**
         * Проверка корректности полей формы
         */
        if (!$this->CheckMMCategoryFields()) {
            return false;
        }
		
        $oCategory = Engine::GetEntity('PluginMinimarket_ModuleTaxonomy_EntityTaxonomy');
        $oCategory->setParent(getRequest('category_select'));
        $oCategory->setTaxonomyType('category');
        $oCategory->setName(getRequest('category_name'));
        $oCategory->setURL(getRequest('category_url'));
        $oCategory->setDescription(getRequest('category_description'));

        if ($this->PluginMinimarket_Taxonomy_AddTaxonomy($oCategory)) {
            Router::Location('admin/mm_categories/?add=success');
        }
	}
	
	protected function CheckDeliveryServiceFields($oDeliveryService) {
		$this->Security_ValidateSendForm();
		$bOk=true;
		/**
		 * Валидируем службу доставки
		 */
		if (!$oDeliveryService->_Validate()) {
			$this->Message_AddError($oDeliveryService->_getValidateError(),$this->Lang_Get('error'));
			$bOk=false;
		}
		return $bOk;
	}
	
	protected function CheckLocationGroupFields() {
        $this->Security_ValidateSendForm();

        $bOk = true;

        if (!func_check(getRequest('location_group_name', null, 'post'), 'text', 2, 50)) {
            $this->Message_AddError($this->Lang_Get('plugin.minimarket.location_group_adding_name_error'), $this->Lang_Get('error'));
            $bOk = false;
        }

        if (!count(getRequest('location_group_city_id', null, 'post'))) {
            $this->Message_AddError($this->Lang_Get('plugin.minimarket.location_group_adding_sity_id_error'), $this->Lang_Get('error'));
            $bOk = false;
        }

        return $bOk;
	}
	
	protected function CheckAttributesCategoryFields($sEdit=null) {
        $this->Security_ValidateSendForm();

        $bOk = true;

        if (!func_check(getRequest('adding_attributes_category_name', null, 'post'), 'text', 2, 50)) {
            $this->Message_AddError($this->Lang_Get('plugin.minimarket.adding_attributes_category_name_error'), $this->Lang_Get('error'));
            $bOk = false;
        }
		if($sEdit) {
			if(is_array(getRequest('attribut_sel'))) {
				foreach(getRequest('attribut_sel') as $val) {
					if(!($oAttribut=$this->PluginMinimarket_Taxonomy_GetTaxonomyById($val)) || $oAttribut->getTaxonomyType()!='attribut') {
						$this->Message_AddError($this->Lang_Get('system_error'), $this->Lang_Get('error'));
						$bOk = false;
						break;
					}
				}
			}
		}
        return $bOk;
	}
	
	protected function CheckAttributFields() {
        $this->Security_ValidateSendForm();

        $bOk = true;

        if (!func_check(getRequest('adding_attribut_name', null, 'post'), 'text', 2, 50)) {
            $this->Message_AddError($this->Lang_Get('plugin.minimarket.adding_attribut_name_error'), $this->Lang_Get('error'));
            $bOk = false;
        }
        if (!func_check(getRequest('adding_attribut_description', null, 'post'), 'text', 0, 2000)) {
            $this->Message_AddError($this->Lang_Get('plugin.minimarket.adding_attribut_description_error'), $this->Lang_Get('error'));
            $bOk = false;
        }
        return $bOk;
	}
	
	protected function CheckMMCategoryFields($oldURL=null,$oldParent=null,$sId=null) {
        $this->Security_ValidateSendForm();
        $bOk = true;
		if($sId) {
			if(getRequest('category_select', null, 'post')==$sId) {
				$this->Message_AddError($this->Lang_Get('plugin.minimarket.category_select_parent_error'), $this->Lang_Get('error'));
				$bOk = false;
			}
		}
		if(!func_check(getRequest('category_select', null, 'post'), 'id') || (getRequest('category_select')!=0 && (!($oCategory=$this->PluginMinimarket_Taxonomy_GetTaxonomyById(getRequest('category_select'))) || $oCategory->getTaxonomyType()!='category'))) {
            $this->Message_AddError($this->Lang_Get('plugin.minimarket.category_select_error'), $this->Lang_Get('error'));
            $bOk = false;			
		}
		if($oTaxonomy=$this->PluginMinimarket_Taxonomy_GetTaxonomyByURLAndParentId(getRequest('category_url', null, 'post'),getRequest('category_select', null, 'post'),'category')) {
			if(!$oldURL || ($oldURL && $oldURL!=$oTaxonomy->getURL()) || ($oldParent!=getRequest('category_select'))) {
				$this->Message_AddError($this->Lang_Get('plugin.minimarket.category_select_double_error'), $this->Lang_Get('error'));
				$bOk = false;
			}
		}
        if (!func_check(getRequest('category_name', null, 'post'), 'text', 2, 50)) {
            $this->Message_AddError($this->Lang_Get('plugin.minimarket.category_name_error'), $this->Lang_Get('error'));
            $bOk = false;
        }
        if (!func_check(getRequest('category_url', null, 'post'), 'login', 1, 50) || in_array(getRequest('adding_attribut_url', null, 'post'), array_keys(Config::Get('router.page')))) {
            $this->Message_AddError($this->Lang_Get('plugin.minimarket.category_url_error'), $this->Lang_Get('error'));
            $bOk = false;
        }
        if (!func_check(getRequest('category_description', null, 'post'), 'text', 0, 2000)) {
            $this->Message_AddError($this->Lang_Get('plugin.minimarket.category_description_error'), $this->Lang_Get('error'));
            $bOk = false;
        }
        return $bOk;
	}
	
	protected function CheckPropertyFields() {
        $this->Security_ValidateSendForm();

        $bOk = true;

        if (!func_check(getRequest('property_name', null, 'post'), 'text', 2, 50)) {
            $this->Message_AddError($this->Lang_Get('plugin.minimarket.property_name_error'), $this->Lang_Get('error'));
            $bOk = false;
        }
        if (!func_check(getRequest('property_description', null, 'post'), 'text', 0, 2000)) {
            $this->Message_AddError($this->Lang_Get('plugin.minimarket.property_description_error'), $this->Lang_Get('error'));
            $bOk = false;
        }

        return $bOk;
	}
	
	protected function CheckBrandFields($oldURL=null) {
        $this->Security_ValidateSendForm();

        $bOk = true;
		
        if (!func_check(getRequest('brand_name', null, 'post'), 'text', 2, 50)) {
            $this->Message_AddError($this->Lang_Get('plugin.minimarket.brand_name_error'), $this->Lang_Get('error'));
            $bOk = false;
        }
        if (!func_check(getRequest('brand_url', null, 'post'), 'login', 1, 50) || in_array(getRequest('brand_url', null, 'post'), array_keys(Config::Get('router.page')))) {
            $this->Message_AddError($this->Lang_Get('plugin.minimarket.brand_url_error'), $this->Lang_Get('error'));
            $bOk = false;
        }
		if($oTaxonomy=$this->PluginMinimarket_Taxonomy_GetTaxonomyByURLAndParentId(getRequest('brand_url', null, 'post'),0,'brand')) {
			if(!$oldURL || ($oldURL && $oldURL!=$oTaxonomy->getURL())) {
				$this->Message_AddError($this->Lang_Get('plugin.minimarket.brand_double_error'), $this->Lang_Get('error'));
				$bOk = false;
			}
		}
        if (!func_check(getRequest('brand_description', null, 'post'), 'text', 0, 4000)) {
            $this->Message_AddError($this->Lang_Get('plugin.minimarket.brand_description_error'), $this->Lang_Get('error'));
            $bOk = false;
        }

        return $bOk;
	}
	
	protected function CheckPaySystemCashFields() {
        $this->Security_ValidateSendForm();

        $bOk = true;
		
        if (!func_check(getRequest('name', null, 'post'), 'text', 2, 50)) {
            $this->Message_AddError($this->Lang_Get('plugin.minimarket.pay_system_cash_name_error'), $this->Lang_Get('error'));
            $bOk = false;
        }

        return $bOk;
	}
}
?>