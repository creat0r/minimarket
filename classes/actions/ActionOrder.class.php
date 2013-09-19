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

class PluginMinimarket_ActionOrder extends ActionPlugin {
    /**
     * Текущий пользователь
     *
     * @var ModuleUser_EntityUser|null
     */
    protected $oUserCurrent = null;

    public function Init() {
        if ($this->User_IsAuthorization()) {
            $this->oUserCurrent = $this->User_GetUserCurrent();
        }
    }

	protected function RegisterEvent() {
		$this->AddEvent('address', 'EventAddress');
		$this->AddEvent('delivery', 'EventDelivery');
		$this->AddEvent('pay', 'EventPay');
		$this->AddEvent('payment', 'EventPayment');
		$this->AddEvent('ok', 'EventOk');
	}
	
	protected function EventOk() {
		/**
		 * Устанавливаем шаблон
		 */
		$this->SetTemplateAction('ok');
		$this->Viewer_Assign('noSidebar', true);
		setcookie('minimarket_order_target_tmp', null, -1, Config::Get('sys.cookie.path'), Config::Get('sys.cookie.host'));
	}
	
	protected function EventPayment() {
		$this->Viewer_Assign('noSidebar', true);	
		/**
		 * Получаем объект заказа по ключу
		 */
		if (
			isset($_COOKIE['minimarket_order_target_tmp']) 
			&& !empty($_COOKIE['minimarket_order_target_tmp'])
			&& false!==($oOrder = $this->PluginMinimarket_Order_GetOrderByKey($_COOKIE['minimarket_order_target_tmp']))
			&& $oOrder->getStatus() < 2
		) {
			/**
			 * Если статус нулевой -- очищаем куку и показываем ошибку
			 */
			if (!$oOrder->getStatus()) {
				setcookie('minimarket_order_target_tmp', null, -1, Config::Get('sys.cookie.path'), Config::Get('sys.cookie.host'));
				return parent::EventNotFound();
			}
			/**
			 * Получаем объект системы оплаты
			 */
			if(
				!($oPaySystem = $this->PluginMinimarket_Pay_GetPaySystemById($oOrder->getPaySystemId()))
				|| !$oPaySystem->getActivation()
			) {
				return parent::EventNotFound();
			}
			switch ($oPaySystem->getKey()) {
				case 'cash':
					/**
					 * Система оплаты: Наличными
					 */
					$this->RunPaySystemCash($oPaySystem, $oOrder);
					break;
				default:
					/**
					 * Для внешних систем оплаты, реализованных отдельным плагином
					 */
					if (!$this->RunPaySystemExternal($oPaySystem, $oOrder)) return parent::EventNotFound();
			}
		} else {
			return parent::EventNotFound();
		}
	}
	
	protected function RunPaySystemExternal($oPaySystem, $oOrder) {
		$bOK = false;
		/**
		 * Запускаем выполнение хуков
		 */
		$this->Hook_Run(
			'minimarket_pay_system_external_run',
			array(
				'oPaySystem'=>$oPaySystem,
				'oOrder'=>$oOrder,
				'bOK'=>&$bOK
			)
		);
		/**
		 * Установка шаблона
		 */
		$this->SetTemplateAction('run_pay_system/external');
		return $bOK;
	}
	
	protected function RunPaySystemCash() {
        /**
         * Сразу редиректим на страницу с сообщением об удачном заказе
         */
		Router::Location('order/ok/');
	}
	
	protected function EventPay() {
		/**
		 * Устанавливаем шаблон
		 */
		$this->SetTemplateAction('pay');
		$this->Viewer_Assign('noSidebar', true);
		/**
		 * Получаем объект заказа по ключу
		 */
		if (
			isset($_COOKIE['minimarket_order_target_tmp']) 
			&& !empty($_COOKIE['minimarket_order_target_tmp'])
			&& false!==($oOrder = $this->PluginMinimarket_Order_GetOrderByKey($_COOKIE['minimarket_order_target_tmp']))
			&& $oOrder->getStatus() < 2
		) {
			/**
			 * Если статус не нулевой -- редиректим на страницу оплаты
			 */
			if ($oOrder->getStatus()) {
				Router::Location('order/payment/');
			}
			/**
			 * Получаем доступные системы оплаты, относительно данного заказа
			 */
			$aPaySystems = $this->PluginMinimarket_Pay_GetAvailablePaySystemsByOrder($oOrder);
			if (isPost('submit') && getRequestStr('pay_system')) {
				/**
				 * Проверяем пришедший ID системы оплаты
				 */
				$bOK = false;
				foreach ($aPaySystems as $oPaySystem) {
					if ($oPaySystem->getId() == getRequestStr('pay_system')) {
						$bOK = true;
						break;
					}
				}
				/**
				 * Если такая система оплаты существует -- обновляем текущий заказ
				 */
				if ($bOK === true) {
					$oOrder->setPaySystemId($oPaySystem->getId());
					$oOrder->setStatus(1);
					$oOrder->setTime(time());
					$this->PluginMinimarket_Order_AddOrUpdateOrder($oOrder);
					Router::Location('order/payment/');
				}
			}
			$this->Viewer_Assign('aPaySystems', $aPaySystems);
		} else {
			return parent::EventNotFound();
		}
	}
	
	protected function EventDelivery() {
		/**
		 * Устанавливаем шаблон
		 */
		$this->SetTemplateAction('delivery');
		$this->Viewer_Assign('noSidebar', true);
		/**
		 * Получаем объект заказа по ключу
		 */
		if (
			isset($_COOKIE['minimarket_order_target_tmp']) 
			&& !empty($_COOKIE['minimarket_order_target_tmp'])
			&& false!==($oOrder = $this->PluginMinimarket_Order_GetOrderByKey($_COOKIE['minimarket_order_target_tmp']))
			&& $oOrder->getStatus() < 2
		) {
			/**
			 * Если статус не нулевой -- редиректим на страницу оплаты
			 */
			if ($oOrder->getStatus()) {
				Router::Location('order/payment/');
			}
			/**
			 * Получаем доступные службы доставки, относительно данного заказа
			 */
			$aDeliveryServices = $this->PluginMinimarket_Delivery_GetAvailableDeliveryServicesByOrder($oOrder);
			/**
			 * Если отправлена форма
			 */
			if (isPost('submit') && getRequestStr('delivery')) {
				/**
				 * Проверяем пришедший ID службы доставки
				 */
				$bOK = false;
				foreach ($aDeliveryServices as $oDeliveryService) {
					if ($oDeliveryService->getId() == getRequestStr('delivery')) {
						$bOK = true;
						break;
					}
				}
				/**
				 * Если такая служба доставки существует -- обновляем текущий заказ
				 */
				if ($bOK === true) {
					$oOrder->setDeliveryServiceId($oDeliveryService->getId());
					$oOrder->setDeliveryServiceTimeFrom($oDeliveryService->getTimeFrom());
					$oOrder->setDeliveryServiceTimeTo($oDeliveryService->getTimeTo());
					$oOrder->setDeliveryServiceSum(number_format($oDeliveryService->getCost(),2,'.',''));
					/**
					 * Обнуляем статус, время заказа и способ оплаты
					 */		
					$oOrder->setStatus(null);
					$oOrder->setTime(null);
					$oOrder->setPaySystemId(null);
					
					$this->PluginMinimarket_Order_AddOrUpdateOrder($oOrder);
					
					Router::Location('order/pay/');
				}
			}
			/**
			 * По объекту заказа получаем список ID товаров, с ним связанных
			 */
			$aCartObjects = $this->PluginMinimarket_Order_GetCartObjectsByOrder($oOrder->getId());
			/**
			 * По списку ID получаем массив товаров
			 */
			$aProducts = $this->PluginMinimarket_Product_GetProductsAdditionalData(array_keys($aCartObjects));
			/**
			 * Загружаем данные в шаблон
			 */
			$this->Viewer_Assign('aProducts', $aProducts);
			$this->Viewer_Assign('aCartObjects', $aCartObjects);
			$this->Viewer_Assign('aDeliveryServices', $aDeliveryServices);
			$this->Viewer_Assign('oOrder', $oOrder);
		} else {
			return parent::EventNotFound();
		}
	}
	
	protected function EventAddress() {
		/**
		 * Устанавливаем шаблон
		 */
		$this->SetTemplateAction('address');
		$this->Viewer_Assign('noSidebar', true);
		/**
		 * Получаем объект заказа по ключу
		 */
		if (
			isset($_COOKIE['minimarket_order_target_tmp']) 
			&& !empty($_COOKIE['minimarket_order_target_tmp'])
			&& false!==($oOrder = $this->PluginMinimarket_Order_GetOrderByKey($_COOKIE['minimarket_order_target_tmp']))
			&& $oOrder->getStatus() < 2
		) {
			/**
			 * Если статус не нулевой -- редиректим на страницу оплаты
			 */
			if ($oOrder->getStatus()) {
				Router::Location('order/payment/');
			}
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
			if (isPost('submit')) {
				/**
				 * Определяем гео-объект с точносью до города
				 */
				if (
					!getRequest('geo_city')
					|| !($oGeoObject = $this->Geo_GetGeoObject('city', getRequestStr('geo_city')))
				) {
					$this->Message_AddErrorSingle($this->Lang_Get('plugin.minimarket.order_address_city_error'), $this->Lang_Get('error'));
					return;
				}
				/**
				 * Проверяем поля
				 */
				func_check(getRequestStr('name'), 'text', 2,50)
					? $oOrder->setClientName(getRequestStr('name'))
					: $oOrder->setClientName(null);
				
				func_check(getRequestStr('index'), 'text', 2,50)
					? $oOrder->setClientIndex(getRequestStr('index'))
					: $oOrder->setClientIndex(null);
				
				func_check(getRequestStr('address'), 'text', 2,100)
					? $oOrder->setClientAddress(getRequestStr('address'))
					: $oOrder->setClientAddress(null);
				
				func_check(getRequestStr('phone'), 'text', 2,50)
					? $oOrder->setClientPhone(getRequestStr('phone'))
					: $oOrder->setClientPhone(null);
					
				func_check(getRequestStr('comment'), 'text', 2,50)
					? $oOrder->setClientComment(getRequestStr('comment'))
					: $oOrder->setClientComment(null);

				/**
				 * Обнуляем статус, данные по доставке и способ оплаты
				 */		
				$oOrder->setStatus(null);
				$oOrder->setTime(null);
				$oOrder->setDeliveryServiceTimeFrom(null);
				$oOrder->setDeliveryServiceTimeTo(null);
				$oOrder->setDeliveryServiceId(null);
				$oOrder->setDeliveryServiceSum(null);
				$oOrder->setPaySystemId(null);
				/**
				 * Создаем связь с гео-объектом
				 */
				$this->Geo_CreateTarget($oGeoObject, 'order', $oOrder->getId());
				/**
				 * Обновляем заказ
				 */
				$this->PluginMinimarket_Order_AddOrUpdateOrder($oOrder);
				
				Router::Location('order/delivery/');
			} else {
				$_REQUEST['name'] = $oOrder->getClientName();
				$_REQUEST['index'] = $oOrder->getClientIndex();
				$_REQUEST['address'] = $oOrder->getClientAddress();
				$_REQUEST['phone'] = $oOrder->getClientPhone();
				$_REQUEST['comment'] = $oOrder->getClientComment();
			}
		} else {
			return parent::EventNotFound();
		}
	}
}
?>