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
    public function Init() {
		$this->Viewer_AppendScript(Plugin::GetTemplatePath(__CLASS__).'js/admin.js');
		parent::Init();
    }
	/**
	 * Регистрируем евенты
	 */
	protected function RegisterEvent() {
		$this->AddEvent('mm_attributes', 'EventAttributes');
		$this->AddEvent('mm_attribut_add', 'EventAttributAdd');
		$this->AddEvent('mm_attribut_edit', 'EventAttributEdit');
		$this->AddEvent('mm_attribut_delete', 'EventAttributDelete');
		
		$this->AddEvent('mm_attributes_categories', 'EventAttributesCategories');
		$this->AddEvent('mm_attributes_category_add', 'EventAttributesCategoryAdd');
		$this->AddEvent('mm_attributes_category_edit', 'EventAttributesCategoryEdit');
		$this->AddEvent('mm_attributes_category_delete', 'EventAttributesCategoryDelete');
		
		$this->AddEvent('mm_property_add', 'EventPropertyAdd');
		$this->AddEvent('mm_property_edit', 'EventPropertyEdit');
		$this->AddEvent('mm_property_delete', 'EventPropertyDelete');
		
		$this->AddEvent('mm_categories', 'EventCategories');
		$this->AddEvent('mm_category_add', 'EventCategoryAdd');
		$this->AddEvent('mm_category_edit', 'EventCategoryEdit');
		$this->AddEvent('mm_category_delete', 'EventCategoryDelete');
		
		$this->AddEvent('mm_brands', 'EventBrands');
		$this->AddEvent('mm_brand_add', 'EventBrandAdd');
		$this->AddEvent('mm_brand_edit', 'EventBrandEdit');
		$this->AddEvent('mm_brand_delete', 'EventBrandDelete');
		
		$this->AddEvent('mm_delivery_services', 'EventDeliveryServices');
		$this->AddEvent('mm_delivery_service_add', 'EventDeliveryServiceAdd');
		$this->AddEvent('mm_delivery_service_edit', 'EventDeliveryServiceEdit');
		$this->AddEvent('mm_delivery_service_delete', 'EventDeliveryServiceDelete');
		
		$this->AddEvent('mm_delivery_services_automatic', 'EventDeliveryServicesAutomatic');
		$this->AddEvent('mm_delivery_service_automatic_edit', 'EventDeliveryServiceAutomaticEdit');
		
		$this->AddEvent('mm_pay_systems', 'EventPaySystems');
		$this->AddEvent('mm_pay_system_edit', 'EventPaySystemEdit');

		$this->AddEvent('mm_location_groups', 'EventLocationGroups');
		$this->AddEvent('mm_location_group_add', 'EventLocationGroupAdd');
		$this->AddEvent('mm_location_group_edit', 'EventLocationGroupEdit');
		$this->AddEvent('mm_location_group_delete', 'EventLocationGroupDelete');
		
		$this->AddEvent('mm_orders', 'EventOrders');
		$this->AddEvent('mm_order_edit', 'EventOrderEdit');
		$this->AddEvent('mm_order_delete', 'EventOrderDelete');
		
		$this->AddEvent('mm_currency', 'EventCurrency');
		$this->AddEvent('mm_currency_add', 'EventCurrencyAdd');
		$this->AddEvent('mm_currency_edit', 'EventCurrencyEdit');
		$this->AddEvent('mm_currency_delete', 'EventCurrencyDelete');
		
		$this->AddEvent('mm_settings', 'EventSettings');
		
		$this->AddEvent('ajaxchangeordertaxonomies', 'EventAjaxChangeOrderTaxonomies');
		
		parent::RegisterEvent();
	}
	
	public function EventSettings() {
        /**
         * Устанавка шаблона вывода
         */
		$this->SetTemplateAction('settings/default');
		$this->_setTitle($this->Lang_Get('plugin.minimarket.admin_settings'));
		$this->Viewer_Assign('aCurrency', $this->PluginMinimarket_Currency_GetAllCurrency());
        /**
         * Проверка, отправлена ли форма с данными
         */
        if (isset($_REQUEST['button_submit'])) {
			if (!isPost('button_submit')) {
				return false;
			}
			/**
			 * Проверка корректности полей формы
			 */
			if (!$this->CheckSettingsBaseFields()) {
				return false;
			}
			$aDataStorage = array(
				array(
					'key' => 'minimarket_settings_base_default_currency',
					'val' => getRequest('default'),
				),
				array(
					'key' => 'minimarket_settings_base_cart_currency',
					'val' => getRequest('cart'),
				),
			);
			if ($this->PluginMinimarket_Storage_UpdateStorage($aDataStorage)) {
				Router::Location('admin/mm_settings/?edit=success');
			}
        } else {
			$aDataStorage = $this->PluginMinimarket_Storage_GetStorage('minimarket_settings_base_');
            $_REQUEST['default'] = isset($aDataStorage['minimarket_settings_base_default_currency']) ? $aDataStorage['minimarket_settings_base_default_currency'] : '';
            $_REQUEST['cart'] = isset($aDataStorage['minimarket_settings_base_cart_currency']) ? $aDataStorage['minimarket_settings_base_cart_currency'] : '';
        }
	}

	public function EventCurrencyDelete() {
        $this->Security_ValidateSendForm();
        /**
         * Получение объекта валюты
         */
        if (!($oCurency = $this->PluginMinimarket_Currency_GetCurrencyById($this->GetParam(0)))) {
            return parent::EventNotFound();
        }
		$bOk = true;
		/**
		 * Проверка, есть ли товары с данной валютой
		 */
		$aFilter = array(
			'currency' => $this->GetParam(0)
		);
		$aResult = $this->PluginMinimarket_Product_GetProductsByFilter($aFilter);
		if ($aResult['count'] != 0) {
			$this->Message_AddError(
				$this->Lang_Get('plugin.minimarket.admin_currency_delete_product_error'),
				$this->Lang_Get('error'),
				true
			);
			$bOk = false;
		}
		/**
		 * Проверка, есть ли службы доставки с данной валютой
		 */
		if ($this->PluginMinimarket_Delivery_GetCountDeliveryServicesByCurrency($this->GetParam(0))) {
			$this->Message_AddError(
				$this->Lang_Get('plugin.minimarket.admin_currency_delete_delivery_service_error'),
				$this->Lang_Get('error'),
				true
			);
			$bOk = false;
		}
		/**
		 * Проверка, есть ли счета на оплату с данной валютой
		 */
		$aPaymentByCurrencyId = $this->PluginMinimarket_Payment_GetPaymentsByCurrencyId($this->GetParam(0));
		if (!empty($aPaymentByCurrencyId)) {
			$this->Message_AddError(
				$this->Lang_Get('plugin.minimarket.admin_currency_delete_payment_error'),
				$this->Lang_Get('error'),
				true
			);
			$bOk = false;
		}
		/**
		 * Удаление валюты
		 */
        if ($bOk === true) $this->PluginMinimarket_Currency_DeleteCurrency($this->GetParam(0));
        Router::Location('admin/mm_currency/');
	}

	public function EventCurrencyEdit() {
        /**
         * Устанавка шаблона вывода
         */
		$this->SetTemplateAction('currency/add');
		$this->_setTitle($this->Lang_Get('plugin.minimarket.admin_currency_adding'));
        /**
         * Получение объекта валюты
         */
        if (
			!($oCurrency = $this->PluginMinimarket_Currency_GetCurrencyById($this->GetParam(0)))
		) {
            return parent::EventNotFound();
        }
		$this->Viewer_Assign('oCurrency', $oCurrency);
        /**
         * Проверка, отправлена ли форма с данными
         */
        if (isPost('button_submit')) {
			/**
			 * Проверка корректности полей формы
			 */
			if (!$this->CheckCurrencyFields($oCurrency)) {
				return false;
			}
			/**
			 * Обновление объекта валюты в БД
			 */
			$oCurrency->setKey(getRequest('key'));
			$oCurrency->setNominal((int)getRequest('nominal'));
			$oCurrency->setCourse(getRequest('course') * Config::Get('plugin.minimarket.settings.factor'));
			$oCurrency->setFormat(getRequest('format'));
			$oCurrency->setDecimalPlaces(getRequest('decimal_places'));
			if ($this->PluginMinimarket_Currency_UpdateCurrency($oCurrency)) {
				Router::Location('admin/mm_currency/?edit=success');
			}
        } else {
			$_REQUEST['key'] = $oCurrency->getKey();
			$_REQUEST['nominal'] = $oCurrency->getNominal();
			$_REQUEST['course'] = $oCurrency->getCourse() / Config::Get('plugin.minimarket.settings.factor');
			$_REQUEST['format'] = $oCurrency->getFormat();
			$_REQUEST['decimal_places'] = $oCurrency->getDecimalPlaces();
		}
	}

	public function EventCurrencyAdd() {
        /**
         * Устанавливаем шаблон вывода
         */
		$this->SetTemplateAction('currency/add');
		$this->_setTitle($this->Lang_Get('plugin.minimarket.admin_currency_adding'));
        /**
         * Проверяем отправлена ли форма с данными
         */
        if (!isPost('button_submit')) {
            return false;
        }
        /**
         * Проверка корректности полей формы
         */
        if (!$this->CheckCurrencyFields()) {
            return false;
        }
        /**
         * Создание объекта валюты и добавление его в БД
         */
        $oCurrency = Engine::GetEntity('PluginMinimarket_ModuleCurrency_EntityCurrency');
        $oCurrency->setKey(getRequest('key'));
        $oCurrency->setNominal((int)getRequest('nominal'));
        $oCurrency->setCourse(getRequest('course') * Config::Get('plugin.minimarket.settings.factor'));
        $oCurrency->setFormat(getRequest('format'));
        $oCurrency->setDecimalPlaces(getRequest('decimal_places'));
        if ($this->PluginMinimarket_Currency_AddCurrency($oCurrency)) {
            Router::Location('admin/mm_currency/?add=success');
        }
	}
	
	public function EventCurrency() {
        /**
         * Установка шаблона
         */
		$this->SetTemplateAction('currency/list');
		$this->_setTitle($this->Lang_Get('plugin.minimarket.admin_currency'));
        /*
         * Получение списка валют
         */
        $this->Viewer_Assign('aCurrency', $this->PluginMinimarket_Currency_GetAllCurrency());
	}

    public function EventOrderDelete() {
        $this->Security_ValidateSendForm();
        /**
         * Получение объекта заказа
         */
        if (
			!($aResult = $this->PluginMinimarket_Order_GetOrdersByFilter(array('id' => $this->GetParam(0)), ''))
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
         * Установка шаблона вывода
         */
		$this->SetTemplateAction('orders/edit');
		$this->_setTitle($this->Lang_Get('plugin.minimarket.admin_order_edit_title'));
        /**
         * Получение объекта заказа
         * Это АХТУНГ, если честно. Нужно написать нормальную функцию получения заказа по ID
         */
        if (
			!($aResult = $this->PluginMinimarket_Order_GetOrdersByFilter(array('id' => $this->GetParam(0)), ''))
			|| !isset($aResult['collection'][$this->GetParam(0)])
		) {
            return parent::EventNotFound();
        }
		$oOrder = $aResult['collection'][$this->GetParam(0)];
		$this->Viewer_Assign('oOrder', $oOrder);
		$this->Viewer_Assign('aCartSumData', $this->PluginMinimarket_Order_GetCartSumDataByOrder($oOrder));
		/**
		 * Загрузка гео-объекта привязки
		 */
		$oGeoTarget = $this->Geo_GetTargetByTarget('order', $oOrder->getId());
		$this->Viewer_Assign('oGeoTarget', $oGeoTarget);
		/**
		 * Загрузка в шаблон списока стран, регионов, городов
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
		/**
		 * Загрузка в шаблон списка служб доставки (отнросительно текущего заказа)
		 */
		$aDeliveryService = $this->PluginMinimarket_Delivery_GetAvailableDeliveryServicesByOrder($oOrder);
		$this->Viewer_Assign('aDeliveryService', $aDeliveryService);
		/**
		 * Загрузка в шаблон службы доставки, принадлежащей текущему заказу
		 */
		foreach ($aDeliveryService as $oDeliveryServiceByOrder) {
			if ($oDeliveryServiceByOrder->getId() == $oOrder->getDeliveryServiceId()) {
				$this->Viewer_Assign('oDeliveryServiceByOrder', $oDeliveryServiceByOrder);
				break;
			}
		}
		/**
		 * Загрузка в шаблон объекта оплаты и списка доступных систем оплаты (относительно текущего заказа)
		 */
		if (
			false !== ($oPayment = $this->PluginMinimarket_Payment_GetPaymentByIdObjectPaymentAndTypeObjectPayment($oOrder->getId(), 'order'))
			&& false !== ($oCurrency = $this->PluginMinimarket_Currency_GetCurrencyById($oPayment->getCurrencyId()))
		) {
			$oPayment->setSumCurrency(
				$this->PluginMinimarket_Currency_GetSumByFormat(
					$oPayment->getSum() / Config::Get('plugin.minimarket.settings.factor'),
					$oPayment->getDecimalPlaces(),
					$oCurrency->getFormat()
				)
			);
			$this->Viewer_Assign('oPayment', $oPayment);
			$this->Viewer_Assign('aPaySystem', $this->PluginMinimarket_Pay_GetAvailablePaySystemsByOrder($oOrder));
		}
		/**
		 * Получение списка ID товаров, принадлежащий текущему заказу
		 */
		$aCartObjects = $this->PluginMinimarket_Order_GetCartObjectsByOrder($oOrder->getId());
		/**
		 * Получение списка товаров по списку ID товаров
		 */
		$aProducts = $this->PluginMinimarket_Product_GetProductsAdditionalData(array_keys($aCartObjects), 'cart_price_currency', $aCartObjects);
		$this->Viewer_Assign('aProducts', $aProducts);
		$this->Viewer_Assign('aCartObjects', $aCartObjects);
		
		$_REQUEST['client_name'] = $oOrder->getClientName();
		$_REQUEST['client_index'] = $oOrder->getClientIndex();
		$_REQUEST['client_address'] = $oOrder->getClientAddress();
		$_REQUEST['client_phone'] = $oOrder->getClientPhone();
		$_REQUEST['client_comment'] = $oOrder->getClientComment();
		
        /**
         * Попытка обработать заказ как "Оплачен"
         */
        if (isset($_REQUEST['order_paid'])) {
			if (
				$oOrder->getStatus() == PluginMinimarket_ModuleOrder::ORDER_STATUS_PAY_SYSTEM_SELECTED
				&& isset($oPayment)
				&& $oPayment !== false
			) {
				/**
				 * Проводка платежа
				 */
				$this->PluginMinimarket_Payment_MakePaymentSuccess($oPayment);
			}
			Router::Location("admin/mm_order_edit/{$oOrder->getId()}/");
		}
		
        /**
         * Попытка обработать заказ как "Доставлен"
         */
        if (isset($_REQUEST['order_delivered'])) {
			if (
				$oOrder->getStatus() == PluginMinimarket_ModuleOrder::ORDER_STATUS_PAYD
				&& isset($oPayment)
				&& $oPayment !== false
			) {
				/**
				 * Изменение статуса у объекта заказа на "Доставлен"
				 */
				$oOrder->setStatus(PluginMinimarket_ModuleOrder::ORDER_STATUS_DELIVERED);
				$this->PluginMinimarket_Order_AddOrUpdateOrder($oOrder);
			}
			Router::Location("admin/mm_order_edit/{$oOrder->getId()}/");
		}
	}
	
    public function EventOrders() {
        /**
         * Устанавливаем шаблон вывода
         */
		$this->SetTemplateAction('orders/list');
		$this->_setTitle($this->Lang_Get('plugin.minimarket.admin_orders'));

        // * Передан ли номер страницы
        $nPage = $this->_getPageNum();
				
		$aResult = $this->PluginMinimarket_Order_GetOrdersByFilter(array(), '', $nPage, Config::Get('plugin.minimarket.admin.order.per_page'));
        $aPaging = $this->Viewer_MakePaging($aResult['count'], $nPage, Config::Get('plugin.minimarket.admin.order.per_page'), 4,
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
		$this->_setTitle($this->Lang_Get('plugin.minimarket.admin_delivery_services'));
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
				if (!$this->EditDeliveryServiceExternal($oDeliveryService)) return parent::EventNotFound();
		}
	}
	
    public function EditDeliveryServiceExternal($oDeliveryService) {
		$bOK = false;
		$sTitle = $this->Lang_Get('plugin.minimarket.admin_delivery_service_edit_title');
		/**
		 * Запускаем выполнение хука
		 */
		$this->Hook_Run(
			'minimarket_delivery_service_external_edit',
			array('oDeliveryService' => $oDeliveryService, 'bOK' => &$bOK, 'sTitle' => &$sTitle)
		);
		/**
		 * Установка шаблона
		 */
		$this->SetTemplateAction('delivery_services/automatic/external');
		$this->_setTitle($sTitle);
        /**
         * Получаем список групп местоположений
         */
        $this->Viewer_Assign('aLocationGroups', $this->PluginMinimarket_Taxonomy_GetTaxonomiesByType('location_group'));
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
				if (!$this->EditPaySystemExternal($oPaySystem)) return parent::EventNotFound();
		}
	}
	
	protected function EditPaySystemExternal($oPaySystem) {
		$bOK = false;
		$sTitle = $this->Lang_Get('plugin.minimarket.admin_pay_system_edit_title');
		/**
		 * Запускаем выполнение хуков
		 */
		$this->Hook_Run('minimarket_pay_system_external_edit', array('oPaySystem' => $oPaySystem,'bOK' => &$bOK, 'sTitle' => &$sTitle));
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
		$this->_setTitle($this->Lang_Get('plugin.minimarket.admin_pay_system_cash_edit_title'));
        /**
         * Проверяем отправлена ли форма с данными
         */
        if (isset($_REQUEST['button_submit'])) {
			if (!isPost('button_submit')) {
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
		$this->_setTitle($this->Lang_Get('plugin.minimarket.admin_pay_systems'));
        /**
         * Получаем список групп местоположений
         */
        $this->Viewer_Assign('aPaySystems', $this->PluginMinimarket_Pay_GetAllPaySystems());
	}
	
    public function EventLocationGroupDelete() {
        $this->Security_ValidateSendForm();
        if (!($oLocationGroup = $this->PluginMinimarket_Taxonomy_GetTaxonomyById($this->GetParam(0))) || $oLocationGroup->getType() != 'location_group') {
            return parent::EventNotFound();
        }
		/**
		 * Удаляем связи местоположений
		 */
		$this->PluginMinimarket_Link_DeleteLinkByParentAndType($oLocationGroup->getId(), 'location_group_city');
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
		$this->_setTitle($this->Lang_Get('plugin.minimarket.admin_location_group_editing'));
        /**
         * Получаем группу местоположений
         */
        if (!($oLocationGroup = $this->PluginMinimarket_Taxonomy_GetTaxonomyById($this->GetParam(0))) || $oLocationGroup->getType() != 'location_group') {
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
        if (isset($_REQUEST['button_submit'])) {
			/**
			 * Проверка корректности полей формы
			 */
			if (!$this->CheckLocationGroupFields()) {
				return false;
			}
			/**
			 * Обновляем объект Группа местоположений
			 */
			$oLocationGroup->setName(getRequest('name'));
			if ($this->PluginMinimarket_Taxonomy_UpdateTaxonomy($oLocationGroup)) {
				/**
				 * Удаляем старые связи местоположений
				 */
				$this->PluginMinimarket_Link_DeleteLinkByParentAndType($oLocationGroup->getId(), 'location_group_city');
				/**
				 * Создаем объект каждого местоположения и добавляем в БД одним запросом
				 */
				$aCitiesId = getRequest('array_city_id');
				if (!is_array($aCitiesId)) {
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
            $_REQUEST['name'] = $oLocationGroup->getName();
            $_REQUEST['array_city_id'] = $this->PluginMinimarket_Link_GetLinksByParentAndType($oLocationGroup->getId(), 'location_group_city');
        }
	}
	
    public function EventLocationGroupAdd() {
        /**
         * Устанавливаем шаблон вывода
         */
		$this->SetTemplateAction('location_groups/add');
		$this->_setTitle($this->Lang_Get('plugin.minimarket.admin_location_group_adding'));
				
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
        if (!isPost('button_submit')) {
            return false;
        }
        /**
         * Проверка корректности полей формы
         */
        if (!$this->CheckLocationGroupFields()) {
            return false;
        }
        /**
         * Создаем и добавляем в БД объект Группа местоположений
         */		
        $oLocationGroup = Engine::GetEntity('PluginMinimarket_ModuleTaxonomy_EntityTaxonomy');
        $oLocationGroup->setType('location_group');
        $oLocationGroup->setName(getRequest('name'));
        if ($nId = $this->PluginMinimarket_Taxonomy_AddTaxonomy($oLocationGroup)) {
			/**
			 * Создаем объект каждого местоположения и добавляем в БД одним запросом
			 */
			$aCitiesId = getRequest('array_city_id');
			if (!is_array($aCitiesId)) {
				$aCitiesId = array($aCitiesId);
			}
			$aObjectCity = array();
			foreach ($aCitiesId as $idCity) {
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
		$this->SetTemplateAction('location_groups/list');
		$this->_setTitle($this->Lang_Get('plugin.minimarket.admin_location_groups'));
        /**
         * Получаем список групп местоположений
         */
        $this->Viewer_Assign('aLocationGroups', $this->PluginMinimarket_Taxonomy_GetTaxonomiesByType('location_group'));
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
		$this->_setTitle($this->Lang_Get('plugin.minimarket.admin_delivery_service_editing'));
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
        $this->Viewer_Assign('aLocationGroups', $this->PluginMinimarket_Taxonomy_GetTaxonomiesByType('location_group'));
        /**
         * Получаем список систем оплаты
         */		
        $this->Viewer_Assign('aPaySystems', $this->PluginMinimarket_Pay_GetAllPaySystems());
        /**
         * Получение всего списка валют
         */
		$this->Viewer_Assign('aCurrency', $this->PluginMinimarket_Currency_GetAllCurrency());
        /**
         * Получение валюты "по умолчанию" из настроек магазина
         */
		$this->Viewer_Assign('oCurrencyDefault', $this->PluginMinimarket_Currency_GetCurrencyBySettings('default'));
        /**
         * Проверяем, отправлена ли форма с данными
         */
        if (isset($_REQUEST['button_submit'])) {
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
			$oDeliveryService->setCurrency(getRequestStr('currency'));
			$oDeliveryService->setDescription(getRequestStr('description'));
			$oDeliveryService->setLocationGroups(getRequestPost('location_groups'));
			$oDeliveryService->setPaySystems(getRequestPost('pay_systems'));
			/**
			 * Проверка корректности полей формы
			 */
			if (!$this->CheckDeliveryServiceFields($oDeliveryService)) {
				return false;
			}
			/**
			 * Умножение стоимости доставки на идентификатор для избавления числа от дробной части
			 */
			$oDeliveryService->setCost(getRequestStr('cost') * Config::Get('plugin.minimarket.settings.factor'));
			/**
			 * Обновляем службу доставки
			 */			
			if ($this->PluginMinimarket_Delivery_UpdateDeliveryService($oDeliveryService)) {
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
			$_REQUEST['cost'] = $oDeliveryService->getCost() / Config::Get('plugin.minimarket.settings.factor');
			$_REQUEST['currency'] = $oDeliveryService->getCurrency();
			$_REQUEST['description'] = $oDeliveryService->getDescription();
			/**
			 * Получаем список ID групп местоположений, связанных с данной службой доставки
			 */
			$_REQUEST['location_groups'] = $this->PluginMinimarket_Link_GetLinksByParentAndType($oDeliveryService->getId(), 'delivery_service_location_group');
			/**
			 * Получаем список ID систем оплаты, связанных с данной службой доставки
			 */
			$_REQUEST['pay_systems'] = $this->PluginMinimarket_Link_GetLinksByParentAndType($oDeliveryService->getId(), 'delivery_service_pay_system');
        }
	}
	
    public function EventDeliveryServiceAdd() {
        /**
         * Устанавливаем шаблон вывода
         */
		$this->SetTemplateAction('delivery_services/tunable/add');
		$this->_setTitle($this->Lang_Get('plugin.minimarket.admin_delivery_service_adding'));
        /**
         * Получаем список групп местоположений
         */
        $this->Viewer_Assign('aLocationGroups', $this->PluginMinimarket_Taxonomy_GetTaxonomiesByType('location_group'));
        /**
         * Получаем список систем оплаты
         */
        $this->Viewer_Assign('aPaySystems', $this->PluginMinimarket_Pay_GetAllPaySystems());
        /**
         * Получение всего списка валют
         */
		$this->Viewer_Assign('aCurrency', $this->PluginMinimarket_Currency_GetAllCurrency());
        /**
         * Получение валюты "по умолчанию" из настроек магазина
         */
		$this->Viewer_Assign('oCurrencyDefault', $this->PluginMinimarket_Currency_GetCurrencyBySettings('default'));
        /**
         * Проверяем отправлена ли форма с данными
         */
        if (!isPost('button_submit')) {
            return false;
        }
		$oDeliveryService = Engine::GetEntity('PluginMinimarket_ModuleDelivery_EntityDeliveryService');
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
		$oDeliveryService->setCurrency(getRequestStr('currency'));
		$oDeliveryService->setDescription(getRequestStr('description'));
		$oDeliveryService->setLocationGroups(getRequestPost('location_groups'));
		$oDeliveryService->setPaySystems(getRequestPost('pay_systems'));
		$oDeliveryService->setType('tunable');
        /**
         * Проверка корректности полей формы
         */
        if (!$this->CheckDeliveryServiceFields($oDeliveryService)) {
            return false;
        }
		/**
		 * Умножение стоимости доставки на идентификатор для избавления числа от дробной части
		 */
		$oDeliveryService->setCost(getRequestStr('cost') * Config::Get('plugin.minimarket.settings.factor'));
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
		$this->_setTitle($this->Lang_Get('plugin.minimarket.admin_delivery_services'));
        /**
         * Загружаем в шаблон настраиваемые службы доставки
         */
		$this->Viewer_Assign('aDeliveryServices', $this->PluginMinimarket_Delivery_GetDeliveryServicesByType('tunable'));
	}
	
    public function EventAttributesCategoryEdit() {
        /**
         * Устанавливаем шаблон вывода
         */
		$this->SetTemplateAction('attributes_categories/add');
        $this->_setTitle($this->Lang_Get('plugin.minimarket.admin_attributes_category_edit_title'));
        /**
         * Получаем атрибут
         */
        if (!($oAttributesCategory = $this->PluginMinimarket_Taxonomy_GetTaxonomyById($this->GetParam(0))) || $oAttributesCategory->getType() != 'attributes_category') {
            return parent::EventNotFound();
        }
        $this->Viewer_Assign('oAttributesCategory', $oAttributesCategory);
        $this->Viewer_Assign('aAttributes', $this->PluginMinimarket_Taxonomy_GetTaxonomiesByType('attribut'));
        /**
         * Получаем связи между категориями атрибутов и атрибутами
         */
		$this->Viewer_Assign('aAttributesCategoryAttribut', $this->PluginMinimarket_Link_GetLinksByParentAndType($oAttributesCategory->getId(), 'attributes_category_attribut'));
        /**
         * Проверяем отправлена ли форма с данными
         */
        if (isset($_REQUEST['button_submit'])) {
            /**
             * Проверка корректности полей формы
             */
			if (!$this->CheckAttributesCategoryFields('edit')) {
				return false;
			}
			$oAttributesCategory->setName(getRequest('name'));
			if ($this->PluginMinimarket_Taxonomy_UpdateTaxonomy($oAttributesCategory)) {
				/**
				 * Удаляем старые связи категорий атбирутов с атрибутами
				 */
				$this->PluginMinimarket_Link_DeleteLinkByParentAndType($oAttributesCategory->getId(), 'attributes_category_attribut');
				/**
				 * Создаем объект связи категории атрибута с атрибутом и добавляем в БД одним запросом
				 */
				$aAttributId = getRequest('attribut_sel');
				if (!is_array($aAttributId)) {
					$aAttributId = array($aAttributId);
				}
				$aObjectAttribut = array();
				foreach($aAttributId as $iIdAttribut) {
					$oLink = Engine::GetEntity('PluginMinimarket_ModuleLink_EntityLink');
					$oLink->setObjectId((int)$iIdAttribut);
					$oLink->setParentId($oAttributesCategory->getId());
					$oLink->setObjectType('attributes_category_attribut');
					$aObjectAttribut[] = $oLink;
				}
				$this->PluginMinimarket_Link_AddLinks($aObjectAttribut);
				Router::Location('admin/mm_attributes_categories/?edit=success');
			}
        } else {
            $_REQUEST['name'] = $oAttributesCategory->getName();
        }
	}

    public function EventAttributesCategoryDelete() {
        $this->Security_ValidateSendForm();
		
        if (false !== ($oAttributesCategory = $this->PluginMinimarket_Taxonomy_GetTaxonomyById($this->GetParam(0))) && $oAttributesCategory->getType() == 'attributes_category') {
			$this->PluginMinimarket_Taxonomy_DeleteTaxonomy($oAttributesCategory);
			Router::Location('admin/mm_attributes_categories/');
        } else {
			return parent::EventNotFound();
		}
	}
	
    public function EventAttributesCategoryAdd() {
        /**
         * Устанавливаем шаблон вывода
         */
		$this->SetTemplateAction('attributes_categories/add');
		$this->_setTitle($this->Lang_Get('plugin.minimarket.admin_attributes_category_adding'));
        /**
         * Обрабатываем отправку формы
         */
        return $this->SubmitAttributesCategoryAdd();
	}
	
	protected function SubmitAttributesCategoryAdd() {
        /**
         * Проверяем отправлена ли форма с данными
         */
        if (!isPost('button_submit')) {
            return false;
        }
        /**
         * Проверка корректности полей формы
         */
        if (!$this->CheckAttributesCategoryFields()) {
            return false;
        }
        $oAttributesCategory = Engine::GetEntity('PluginMinimarket_ModuleTaxonomy_EntityTaxonomy');
        $oAttributesCategory->setType('attributes_category');
        $oAttributesCategory->setName(getRequest('name'));

        if ($sId = $this->PluginMinimarket_Taxonomy_AddTaxonomy($oAttributesCategory)) {
            Router::Location('admin/mm_attributes_category_edit/' . $sId . '/');
        }
	}
	
    public function EventAttributesCategories() {
        /**
         * Устанавливаем шаблон вывода
         */
		$this->SetTemplateAction('attributes_categories/list');
		$this->_setTitle($this->Lang_Get('plugin.minimarket.admin_attributes_category'));
		
        /*
         * Получаем список
         */
        $this->Viewer_Assign('aAttributesCategory', $this->PluginMinimarket_Taxonomy_GetTaxonomiesByType('attributes_category'));
		
		if (getRequest('add')) $this->Message_AddNoticeSingle($this->Lang_Get('plugin.minimarket.admin_attributes_category_adding_ok'));
		if (getRequest('edit')) $this->Message_AddNoticeSingle($this->Lang_Get('plugin.minimarket.admin_attributes_category_adding_edit'));
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
                    $oTaxonomy->setSort($oOrder['order']);
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
	
	protected function EventBrandDelete() {
        $this->Security_ValidateSendForm();
		
        if (!($oBrand = $this->PluginMinimarket_Taxonomy_GetTaxonomyById($this->GetParam(0))) || $oBrand->getType() != 'brand') {
            return parent::EventNotFound();
        }

        $this->PluginMinimarket_Taxonomy_DeleteTaxonomy($oBrand);
        Router::Location('admin/mm_brands/');
	}
	
	protected function EventBrandEdit() {
        /**
         * Устанавливаем шаблон вывода
         */
        $this->_setTitle($this->Lang_Get('plugin.minimarket.admin_brand_edit_title'));
        $this->SetTemplateAction('brands/add');
        /**
         * Получаем свойство аттрибута
         */
        if (!($oBrand = $this->PluginMinimarket_Taxonomy_GetTaxonomyById($this->GetParam(0))) || $oBrand->getType() != 'brand') {
            return parent::EventNotFound();
        }
        $this->Viewer_Assign('oBrand', $oBrand);

        /**
         * Проверяем отправлена ли форма с данными
         */
        if (isset($_REQUEST['button_submit'])) {
            /**
             * Обрабатываем отправку формы
             */
            return $this->SubmitBrandEdit($oBrand);
        } else {
            $_REQUEST['name'] = $oBrand->getName();
            $_REQUEST['url'] = $oBrand->getURL();
            $_REQUEST['description'] = $oBrand->getDescription();
        }
	}
	
	protected function SubmitBrandEdit($oBrand) {
        /**
         * Проверяем, отправлена ли форма с данными
         */
        if (!isPost('button_submit')) {
            return false;
        }
        /**
         * Проверка корректности полей формы
         */
        if (!$this->CheckBrandFields($oBrand->getURL())) {
            return false;
        }
		$oBrand->setParentId(0);
        $oBrand->setName(getRequest('name'));
        $oBrand->setURL(getRequest('url'));
        $oBrand->setDescription(getRequest('description'));
        if ($this->PluginMinimarket_Taxonomy_UpdateTaxonomy($oBrand)) {
            Router::Location('admin/mm_brands/?edit=success');
        }
	}

	protected function EventBrandAdd() {
        /**
         * Устанавливаем шаблон вывода
         */
        $this->_setTitle($this->Lang_Get('plugin.minimarket.admin_brand_add_title'));
        $this->SetTemplateAction('brands/add');

        /**
         * Обрабатываем отправку формы
         */
        return $this->SubmitBrandAdd();
	}

	protected function SubmitBrandAdd() {
        /**
         * Проверяем отправлена ли форма с данными
         */
        if (!isPost('button_submit')) {
            return false;
        }
        /**
         * Проверка корректности полей формы
         */
        if (!$this->CheckBrandFields()) {
            return false;
        }
        $oBrand = Engine::GetEntity('PluginMinimarket_ModuleTaxonomy_EntityTaxonomy');
        $oBrand->setParentId(0);
        $oBrand->setType('brand');
        $oBrand->setName(getRequest('name'));
        $oBrand->setURL(getRequest('url'));
        $oBrand->setDescription(getRequest('description'));

        if ($this->PluginMinimarket_Taxonomy_AddTaxonomy($oBrand)) {
            Router::Location('admin/mm_brands/?add=success');
        }
	}
	
	protected function EventBrands() {
        /**
         * Устанавливаем шаблон вывода
         */
        $this->_setTitle($this->Lang_Get('plugin.minimarket.admin_brands'));
        $this->SetTemplateAction('brands/list');
        /*
         * Получаем список
         */
        $this->Viewer_Assign('aBrands', $this->PluginMinimarket_Taxonomy_GetTaxonomiesByType('brand'));
		if (getRequest('add')) $this->Message_AddNoticeSingle($this->Lang_Get('plugin.minimarket.admin_brans_add_ok'));
		if (getRequest('edit')) $this->Message_AddNoticeSingle($this->Lang_Get('plugin.minimarket.admin_brans_edit_ok'));
	}
	
	protected function EventCategoryDelete() {
        $this->Security_ValidateSendForm();
		
        if (!($oCategory = $this->PluginMinimarket_Taxonomy_GetTaxonomyById($this->GetParam(0))) || $oCategory->getType() != 'category') {
            return parent::EventNotFound();
        }
		
		$aChildrenId = array();
		$aChildrenId = $this->PluginMinimarket_Taxonomy_GetIdChildrenTaxonomiesByTypeAndIdParentTaxonomy($this->GetParam(0), 'category');
		$aChildrenId[] = $this->GetParam(0);
		
        $this->PluginMinimarket_Taxonomy_DeleteTaxonomiesByArrayId($aChildrenId);
        Router::Location('admin/mm_categories/');
	}
	
	protected function EventCategoryEdit() {
        /**
         * Устанавливаем шаблон вывода
         */
        $this->_setTitle($this->Lang_Get('plugin.minimarket.admin_category_edit_title'));
        $this->SetTemplateAction('categories/add');	
        /**
         * Получаем свойство аттрибута
         */
        if (!($oCategory = $this->PluginMinimarket_Taxonomy_GetTaxonomyById($this->GetParam(0))) || $oCategory->getType() != 'category') {
            return parent::EventNotFound();
        }
        $this->Viewer_Assign('oCategory', $oCategory);
		$this->Viewer_Assign('aCategories', $this->PluginMinimarket_Taxonomy_GetTreeTaxonomiesByType('category'));

        /**
         * Проверяем отправлена ли форма с данными
         */
        if (isset($_REQUEST['button_submit'])) {
            /**
             * Обрабатываем отправку формы
             */
            return $this->SubmitCategoryEdit($oCategory);
        } else {
            $_REQUEST['name'] = $oCategory->getName();
            $_REQUEST['url'] = $oCategory->getURL();
			$_REQUEST['description'] = $oCategory->getDescription();
            $_REQUEST['category_select'] = $oCategory->getParentId();
        }	
	}
	
	protected function SubmitCategoryEdit($oCategory) {
        /**
         * Проверяем отправлена ли форма с данными
         */
        if (!isPost('button_submit')) {
            return false;
        }
		
        /**
         * Проверка корректности полей формы
         */
        if (!$this->CheckMMCategoryFields($oCategory->getURL(), $oCategory->getParentId(), $oCategory->getId())) {
            return false;
        }
        $oCategory->setName(getRequest('name'));
        $oCategory->setURL(getRequest('url'));
        $oCategory->setParentId(getRequest('category_select'));
        $oCategory->setDescription(getRequest('description'));
        if ($this->PluginMinimarket_Taxonomy_UpdateTaxonomy($oCategory)) {
            Router::Location('admin/mm_categories/?edit=success');
        }
	}
	
	protected function EventCategoryAdd() {
        /**
         * Устанавливаем шаблон вывода
         */
        $this->_setTitle($this->Lang_Get('plugin.minimarket.admin_category_add_title'));
        $this->SetTemplateAction('categories/add');	
		$this->Viewer_Assign('aCategories', $this->PluginMinimarket_Taxonomy_GetTreeTaxonomiesByType('category'));
        /**
         * Обрабатываем отправку формы
         */
        return $this->SubmitCategoryAdd();
	}
	
	protected function EventCategories() {
        /**
         * Устанавливаем шаблон вывода
         */
        $this->_setTitle($this->Lang_Get('plugin.minimarket.admin_categories'));
        $this->SetTemplateAction('categories/list');
		
        /*
         * Получаем список
         */
        $this->Viewer_Assign('aCategories', $this->PluginMinimarket_Taxonomy_GetTreeTaxonomiesByType('category'));
		
		if (getRequest('add')) $this->Message_AddNoticeSingle($this->Lang_Get('plugin.minimarket.admin_category_add_ok'));
		if (getRequest('edit')) $this->Message_AddNoticeSingle($this->Lang_Get('plugin.minimarket.admin_category_edit_ok'));
	}
	
	protected function EventAttributDelete() {
        $this->Security_ValidateSendForm();
		
        if (!$oAttribut = $this->PluginMinimarket_Taxonomy_GetTaxonomyById($this->GetParam(0))) {
            return parent::EventNotFound();
        }
        /**
         * Получаем список ID свойств, принадлежащих данному атрибуту
         */
		$aId = $this->PluginMinimarket_Taxonomy_GetIdChildrenTaxonomiesByTypeAndIdParentTaxonomy($this->GetParam(0), 'property');
		$aId[] = $this->GetParam(0);
        /**
         * По списку ID удаляем атрибут и принадлежащие ему свойства
         */
        $this->PluginMinimarket_Taxonomy_DeleteTaxonomiesByArrayId($aId);
        Router::Location('admin/mm_attributes/');
	}
	
	protected function EventPropertyDelete() {
        $this->Security_ValidateSendForm();
		
        if (!$oProperty = $this->PluginMinimarket_Taxonomy_GetTaxonomyById($this->GetParam(0))) {
            return parent::EventNotFound();
        }

        $this->PluginMinimarket_Taxonomy_DeleteTaxonomy($oProperty);
        Router::Location('admin/mm_attribut_edit/' . $oProperty->getParentId());
	}
	
	protected function EventPropertyEdit() {
        /**
         * Устанавливаем шаблон вывода
         */
		$this->SetTemplateAction('property/add');
        $this->_setTitle($this->Lang_Get('plugin.minimarket.admin_property_edit_title'));
        /**
         * Получаем свойство аттрибута
         */
        if (!($oProperty = $this->PluginMinimarket_Taxonomy_GetTaxonomyById($this->GetParam(0))) || $oProperty->getParentId() == 0) {
            return parent::EventNotFound();
        }
        $this->Viewer_Assign('oProperty', $oProperty);
		
		$oAttribut=$this->PluginMinimarket_Taxonomy_GetTaxonomyById($oProperty->getParentId());
        $this->Viewer_Assign('oAttribut', $oAttribut);
		
        /**
         * Проверяем отправлена ли форма с данными
         */
        if (isset($_REQUEST['button_submit'])) {
            /**
             * Обрабатываем отправку формы
             */
            return $this->SubmitPropertyEdit($oProperty,$oAttribut);
        } else {
            $_REQUEST['property_id'] = $oProperty->getId();
            $_REQUEST['name'] = $oProperty->getName();
            $_REQUEST['property_url'] = $oProperty->getURL();
            $_REQUEST['description'] = $oProperty->getDescription();
        }
	}
	
	protected function EventPropertyAdd() {
        /**
         * Получаем аттрибут
         */
        if (!$oAttribut = $this->PluginMinimarket_Taxonomy_GetTaxonomyById($this->GetParam(0))) {
            return parent::EventNotFound();
        }
        /*
         * Устанавливаем шаблон вывода
         */
		$this->SetTemplateAction('property/add');
		$this->_setTitle($this->Lang_Get('plugin.minimarket.admin_property_adding_title'));
		
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
        if (!isPost('button_submit')) {
            return false;
        }
		
        /**
         * Проверка корректности полей формы
         */
        if (!$this->CheckPropertyFields()) {
            return false;
        }
		
        $oProperty = Engine::GetEntity('PluginMinimarket_ModuleTaxonomy_EntityTaxonomy');
        $oProperty->setParentId($oAttribut->getId());
        $oProperty->setType('property');
        $oProperty->setName(getRequest('name'));
        $oProperty->setURL(getRequest('property_url'));
        $oProperty->setDescription(getRequest('description'));

        if ($this->PluginMinimarket_Taxonomy_AddTaxonomy($oProperty)) {
            Router::Location('admin/mm_attribut_edit/'.$oAttribut->getId() . '/?add=success');
        }
	}
		
	protected function EventAttributEdit() {
        /*
         * Устанавливаем шаблон вывода
         */
		$this->SetTemplateAction('attributes/add');
		$this->_setTitle($this->Lang_Get('plugin.minimarket.admin_attribut_edit_title'));
        /**
         * Получаем аттрибут
         */
        if (!($oAttribut = $this->PluginMinimarket_Taxonomy_GetTaxonomyById($this->GetParam(0))) || $oAttribut->getParentId() != 0) {
            return parent::EventNotFound();
        }
        $this->Viewer_Assign('oAttribut', $oAttribut);
		$aFilter = array (
			'parent_id' => $oAttribut->getId(),
			'type' => 'property',
		);
		$this->Viewer_Assign('aProperties', $this->PluginMinimarket_Taxonomy_GetTaxonomiesByFilter($aFilter, ''));
		
        /**
         * Проверяем отправлена ли форма с данными
         */
        if (isset($_REQUEST['button_submit'])) {
            /**
             * Обрабатываем отправку формы
             */
            return $this->SubmitAttributEdit($oAttribut);
        } else {
            $_REQUEST['id'] = $oAttribut->getId();
            $_REQUEST['name'] = $oAttribut->getName();
            $_REQUEST['description'] = $oAttribut->getDescription();
        }
	}
	
	protected function SubmitAttributEdit($oAttribut) {
        /**
         * Проверка корректности полей формы
         */
        if (!$this->CheckAttributFields()) {
            return false;
        }
        $oAttribut->setParentId(0);
        $oAttribut->setName(getRequest('name'));
        $oAttribut->setDescription(getRequest('description'));
        if ($this->PluginMinimarket_Taxonomy_UpdateTaxonomy($oAttribut)) {
            Router::Location('admin/mm_attribut_edit/'.$oAttribut->getId() . '/');
        }
	}
	
	protected function SubmitPropertyEdit($oProperty,$oAttribut) {
        /**
         * Проверяем отправлена ли форма с данными
         */
        if (!isPost('button_submit')) {
            return false;
        }
		
        /**
         * Проверка корректности полей формы
         */
        if (!$this->CheckPropertyFields()) {
            return false;
        }
        $oProperty->setName(getRequest('name'));
        $oProperty->setURL(getRequest('property_url'));
        $oProperty->setDescription(getRequest('description'));

        if ($this->PluginMinimarket_Taxonomy_UpdateTaxonomy($oProperty)) {
            Router::Location('admin/mm_attribut_edit/'.$oAttribut->getId() . '/?edit=success');
        }
	}

	protected function EventAttributes() {
        /*
         * Установка шаблона вывода
         */
		$this->SetTemplateAction('attributes/list');
		$this->_setTitle($this->Lang_Get('plugin.minimarket.admin_attributes'));
        /*
         * Получение списка атрибутов
         */
        $this->Viewer_Assign('aAttributes', $this->PluginMinimarket_Taxonomy_GetTaxonomiesByType('attribut'));
        /*
         * Получение списка заголовка атрибутов
         */
        $this->Viewer_Assign('aAttributesCategories', $this->PluginMinimarket_Taxonomy_GetTaxonomiesByType('attributes_category'));
        /*
         * Получение списка связей между атрибутами и заголовками атрибутов
         */
		$this->Viewer_Assign('aAttributesCategoryAttribut', $this->PluginMinimarket_Link_GetLinksByType('attributes_category_attribut'));
		if (getRequest('add')) $this->Message_AddNoticeSingle($this->Lang_Get('plugin.minimarket.admin_attribut_adding_ok'));
		if (getRequest('edit')) $this->Message_AddNoticeSingle($this->Lang_Get('plugin.minimarket.admin_attribut_adding_edit'));
	}

	protected function EventAttributAdd() {
        /*
         * Устанавливаем шаблон вывода
         */
		$this->SetTemplateAction('attributes/add');
		$this->_setTitle($this->Lang_Get('plugin.minimarket.admin_attribut_adding'));
        /**
         * Обрабатываем отправку формы
         */
        return $this->SubmitAttributAdd();
	}
	
	protected function SubmitAttributAdd() {
        /**
         * Проверяем отправлена ли форма с данными
         */
        if (!isPost('button_submit')) {
            return false;
        }
		
        /**
         * Проверка корректности полей формы
         */
        if (!$this->CheckAttributFields()) {
            return false;
        }
		
        $oAttribut = Engine::GetEntity('PluginMinimarket_ModuleTaxonomy_EntityTaxonomy');
        $oAttribut->setParentId(0);
        $oAttribut->setType('attribut');
        $oAttribut->setName(getRequest('name'));
        $oAttribut->setDescription(getRequest('description'));

        if ($sId = $this->PluginMinimarket_Taxonomy_AddTaxonomy($oAttribut)) {
            Router::Location('admin/mm_attribut_edit/' . $sId . '/');
        }
	}
	
	protected function SubmitCategoryAdd() {
        /**
         * Проверяем отправлена ли форма с данными
         */
        if (!isPost('button_submit')) {
            return false;
        }
        /**
         * Проверка корректности полей формы
         */
        if (!$this->CheckMMCategoryFields()) {
            return false;
        }
        $oCategory = Engine::GetEntity('PluginMinimarket_ModuleTaxonomy_EntityTaxonomy');
        $oCategory->setParentId(getRequest('category_select'));
        $oCategory->setType('category');
        $oCategory->setName(getRequest('name'));
        $oCategory->setURL(getRequest('url'));
        $oCategory->setDescription(getRequest('description'));
        if ($this->PluginMinimarket_Taxonomy_AddTaxonomy($oCategory)) {
            Router::Location('admin/mm_categories/?add=success');
        }
	}
	
	protected function CheckDeliveryServiceFields($oDeliveryService) {
		$this->Security_ValidateSendForm();
		$bOk = true;
		/**
		 * Валидируем службу доставки
		 */
		if (!$oDeliveryService->_Validate()) {
			$this->Message_AddError($oDeliveryService->_getValidateError(), $this->Lang_Get('error'));
			$bOk = false;
		}
		return $bOk;
	}
	
	protected function CheckLocationGroupFields() {
        $this->Security_ValidateSendForm();

        $bOk = true;

        if (!func_check(getRequest('name', null, 'post'), 'text', 2, 50)) {
            $this->Message_AddError($this->Lang_Get('plugin.minimarket.admin_location_group_adding_name_error'), $this->Lang_Get('error'));
            $bOk = false;
        }

        if (!count(getRequest('array_city_id', null, 'post'))) {
            $this->Message_AddError($this->Lang_Get('plugin.minimarket.admin_location_group_adding_sity_id_error'), $this->Lang_Get('error'));
            $bOk = false;
        }

        return $bOk;
	}
	
	protected function CheckAttributesCategoryFields($sEdit = null) {
        $this->Security_ValidateSendForm();

        $bOk = true;

        if (!func_check(getRequest('name', null, 'post'), 'text', 2, 50)) {
            $this->Message_AddError($this->Lang_Get('plugin.minimarket.admin_attributes_category_adding_name_error'), $this->Lang_Get('error'));
            $bOk = false;
        }
		if ($sEdit) {
			if (is_array(getRequest('attribut_sel'))) {
				/**
				 * Данную проверку нужно сделать красиво -- одним запросом
				 */
				foreach (getRequest('attribut_sel') as $val) {
					if (!($oAttribut = $this->PluginMinimarket_Taxonomy_GetTaxonomyById($val)) || $oAttribut->getType() != 'attribut') {
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
        if (!func_check(getRequest('name', null, 'post'), 'text', 2, 50)) {
            $this->Message_AddError($this->Lang_Get('plugin.minimarket.admin_attribut_adding_name_error'), $this->Lang_Get('error'));
            $bOk = false;
        }
        if (!func_check(getRequest('description', null, 'post'), 'text', 0, 2000)) {
            $this->Message_AddError($this->Lang_Get('plugin.minimarket.admin_attribut_adding_description_error'), $this->Lang_Get('error'));
            $bOk = false;
        }
        return $bOk;
	}
	
	protected function CheckMMCategoryFields($oldURL = null, $oldParent = null, $sId = null) {
        $this->Security_ValidateSendForm();
        $bOk = true;
		if ($sId) {
			if(getRequest('category_select', null, 'post')==$sId) {
				$this->Message_AddError($this->Lang_Get('plugin.minimarket.admin_category_adding_select_parent_error'), $this->Lang_Get('error'));
				$bOk = false;
			}
		}
		if (!func_check(getRequest('category_select', null, 'post'), 'id') || (getRequest('category_select')!=0 && (!($oCategory = $this->PluginMinimarket_Taxonomy_GetTaxonomyById(getRequest('category_select'))) || $oCategory->getType() != 'category'))) {
            $this->Message_AddError($this->Lang_Get('plugin.minimarket.admin_category_adding_select_error'), $this->Lang_Get('error'));
            $bOk = false;			
		}
		$aFilter = array (
			'url' => getRequest('url', null, 'post'),
			'parent_id' => getRequest('category_select', null, 'post'),
			'type' => 'category',
		);
		if ($aTaxonomy = $this->PluginMinimarket_Taxonomy_GetTaxonomiesByFilter($aFilter, '')) {
			if (!$oldURL || ($oldURL && $oldURL != $aTaxonomy[0]->getURL()) || ($oldParent != getRequest('category_select'))) {
				$this->Message_AddError($this->Lang_Get('plugin.minimarket.admin_category_adding_select_double_error'), $this->Lang_Get('error'));
				$bOk = false;
			}
		}
        if (!func_check(getRequest('name', null, 'post'), 'text', 2, 50)) {
            $this->Message_AddError($this->Lang_Get('plugin.minimarket.admin_category_adding_name_error'), $this->Lang_Get('error'));
            $bOk = false;
        }
        if (!func_check(getRequest('url', null, 'post'), 'login', 1, 50)) {
            $this->Message_AddError($this->Lang_Get('plugin.minimarket.admin_category_adding_url_error'), $this->Lang_Get('error'));
            $bOk = false;
        }
        if (!func_check(getRequest('description', null, 'post'), 'text', 0, 2000)) {
            $this->Message_AddError($this->Lang_Get('plugin.minimarket.admin_category_adding_description_error'), $this->Lang_Get('error'));
            $bOk = false;
        }
        return $bOk;
	}
	
	protected function CheckPropertyFields() {
        $this->Security_ValidateSendForm();

        $bOk = true;

        if (!func_check(getRequest('name', null, 'post'), 'text', 2, 50)) {
            $this->Message_AddError($this->Lang_Get('plugin.minimarket.admin_property_adding_name_error'), $this->Lang_Get('error'));
            $bOk = false;
        }
        if (!func_check(getRequest('description', null, 'post'), 'text', 0, 2000)) {
            $this->Message_AddError($this->Lang_Get('plugin.minimarket.admin_property_adding_description_error'), $this->Lang_Get('error'));
            $bOk = false;
        }

        return $bOk;
	}
	
	protected function CheckBrandFields($oldURL = null) {
        $this->Security_ValidateSendForm();

        $bOk = true;
		
        if (!func_check(getRequest('name', null, 'post'), 'text', 2, 50)) {
            $this->Message_AddError($this->Lang_Get('plugin.minimarket.admin_brand_adding_name_error'), $this->Lang_Get('error'));
            $bOk = false;
        }
        if (!func_check(getRequest('url', null, 'post'), 'login', 1, 50) || in_array(getRequest('url', null, 'post'), array_keys(Config::Get('router.page')))) {
            $this->Message_AddError($this->Lang_Get('plugin.minimarket.admin_brand_adding_url_error'), $this->Lang_Get('error'));
            $bOk = false;
        }
		$aFilter = array (
			'url' => getRequest('url', null, 'post'),
			'type' => 'brand',
		);
		if($aTaxonomy = $this->PluginMinimarket_Taxonomy_GetTaxonomiesByFilter($aFilter, '')) {
			if(!$oldURL || ($oldURL && $oldURL != $aTaxonomy[0]->getURL())) {
				$this->Message_AddError($this->Lang_Get('plugin.minimarket.admin_brand_adding_double_error'), $this->Lang_Get('error'));
				$bOk = false;
			}
		}
        if (!func_check(getRequest('description', null, 'post'), 'text', 0, 4000)) {
            $this->Message_AddError($this->Lang_Get('plugin.minimarket.admin_brand_adding_description_error'), $this->Lang_Get('error'));
            $bOk = false;
        }

        return $bOk;
	}
	
	protected function CheckPaySystemCashFields() {
        $this->Security_ValidateSendForm();

        $bOk = true;
		
        if (!func_check(getRequest('name', null, 'post'), 'text', 2, 50)) {
            $this->Message_AddError($this->Lang_Get('plugin.minimarket.admin_pay_system_cash_name_error'), $this->Lang_Get('error'));
            $bOk = false;
        }

        return $bOk;
	}
	
	protected function CheckCurrencyFields($oCurrency = null) {
        $this->Security_ValidateSendForm();
        $bOk = true;		
        if (!func_check(getRequest('key', null, 'post'), 'login', 2, 50)) {
            $this->Message_AddError($this->Lang_Get('plugin.minimarket.admin_currency_adding_key_error'), $this->Lang_Get('error'));
            $bOk = false;
        }
        if ($oCurrencyByKey = $this->PluginMinimarket_Currency_GetCurrencyByKey(getRequest('key', null, 'post'))) {
			if (!$oCurrency || ($oCurrency && $oCurrency->getKey() != $oCurrencyByKey->getKey())) {
				$this->Message_AddError($this->Lang_Get('plugin.minimarket.admin_currency_adding_key_double_error'), $this->Lang_Get('error'));
				$bOk = false;
			}
        }
        if (!getRequest('nominal', null, 'post') || ((int)getRequest('nominal', null, 'post') < 1) || !func_check(getRequest('nominal', null, 'post'), 'id', 1, 5)) {
            $this->Message_AddError($this->Lang_Get('plugin.minimarket.admin_currency_adding_nominal_error'), $this->Lang_Get('error'));
            $bOk = false;
        }
        if (!func_check(getRequest('course', null, 'post'), 'float', 1, 8) || getRequest('course', null, 'post') < 1) {
            $this->Message_AddError($this->Lang_Get('plugin.minimarket.admin_currency_adding_course_error'), $this->Lang_Get('error'));
            $bOk = false;
        }
        if (!func_check(getRequest('format', null, 'post'), 'text', 1, 50) || substr_count(getRequest('format', null, 'post'), '#') != 1) {
            $this->Message_AddError($this->Lang_Get('plugin.minimarket.admin_currency_adding_format_error'), $this->Lang_Get('error'));
            $bOk = false;
        }
        if (!func_check(getRequest('decimal_places', null, 'post'), 'id', 0, 1)) {
            $this->Message_AddError($this->Lang_Get('plugin.minimarket.admin_currency_adding_decimal_places_error'), $this->Lang_Get('error'));
            $bOk = false;
        }
        return $bOk;		
	}
	
	protected function CheckSettingsBaseFields() {
        $this->Security_ValidateSendForm();
        $bOk = true;
        if (false === ($oCurrency = $this->PluginMinimarket_Currency_GetCurrencyByKey(getRequest('default', null, 'post')))) {
            $this->Message_AddError($this->Lang_Get('plugin.minimarket.admin_settings_base_get_default_currency_error'), $this->Lang_Get('error'));
            $bOk = false;
        }
        if (false === ($oCurrency = $this->PluginMinimarket_Currency_GetCurrencyByKey(getRequest('cart', null, 'post')))) {
            $this->Message_AddError($this->Lang_Get('plugin.minimarket.admin_settings_base_get_cart_currency_error'), $this->Lang_Get('error'));
            $bOk = false;
        }
		return $bOk;
	}

    /**
     * Выполняется при завершении работы экшена
     */
    public function EventShutdown() {
        /**
         * Загрузка в шаблон необходимых переменных
         */
		$this->Viewer_Assign('ORDER_STATUS_CART_INIT', PluginMinimarket_ModuleOrder::ORDER_STATUS_CART_INIT);
		$this->Viewer_Assign('ORDER_STATUS_CART_FULL', PluginMinimarket_ModuleOrder::ORDER_STATUS_CART_FULL);
		$this->Viewer_Assign('ORDER_STATUS_ADDRESS_SAVED', PluginMinimarket_ModuleOrder::ORDER_STATUS_ADDRESS_SAVED);
		$this->Viewer_Assign('ORDER_STATUS_DELIVERY_SELECTED', PluginMinimarket_ModuleOrder::ORDER_STATUS_DELIVERY_SELECTED);
		$this->Viewer_Assign('ORDER_STATUS_PAY_SYSTEM_SELECTED', PluginMinimarket_ModuleOrder::ORDER_STATUS_PAY_SYSTEM_SELECTED);
		$this->Viewer_Assign('ORDER_STATUS_PAYD', PluginMinimarket_ModuleOrder::ORDER_STATUS_PAYD);
		$this->Viewer_Assign('ORDER_STATUS_DELIVERED', PluginMinimarket_ModuleOrder::ORDER_STATUS_DELIVERED);
		
		$this->Viewer_Assign('DELIVERY_COST_CALCULATION_ENTIRE_ORDER', PluginMinimarket_ModuleDelivery::DELIVERY_COST_CALCULATION_ENTIRE_ORDER);
		$this->Viewer_Assign('DELIVERY_COST_CALCULATION_ONE_ITEM', PluginMinimarket_ModuleDelivery::DELIVERY_COST_CALCULATION_ONE_ITEM);
    }
}
?>