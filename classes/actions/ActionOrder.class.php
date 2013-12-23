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
		$this->AddEvent('nulled', 'EventNulled');
	}
	
	protected function EventNulled() {
		$this->Security_ValidateSendForm();
		$this->PluginMinimarket_Order_DeleteCookieOrder();
		Router::Location('catalog/');
	}

	protected function EventDelivery() {
		/**
		 * Установка шаблона
		 */
		$this->SetTemplateAction('delivery');
		$this->Viewer_Assign('noSidebar', true);
		/**
		 * Получение объекта заказа по ключу
		 */
		if (
			false !== ($oOrder = $this->PluginMinimarket_Order_GetOrderByCookie())
			&& false !== $this->PluginMinimarket_Order_CheckStepOrder($oOrder, PluginMinimarket_ModuleOrder::ORDER_STATUS_ADDRESS_SAVED)
		) {
			/**
			 * Получение доступных служб доставки, относительно данного заказа
			 */
			$aDeliveryServices = $this->PluginMinimarket_Delivery_GetAvailableDeliveryServicesByOrder($oOrder);
			/**
			 * Если отправлена форма
			 */
			if (isPost('submit') && getRequestStr('delivery')) {
				/**
				 * Проверка пришедшего ID службы доставки
				 */
				$bOK = false;
				foreach ($aDeliveryServices as $oDeliveryService) {
					if ($oDeliveryService->getId() == getRequestStr('delivery')) {
						$bOK = true;
						break;
					}
				}
				/**
				 * Если такая служба доставки существует -- обновление текущего заказа
				 */
				if ($bOK === true) {
					$oOrder->setDeliveryServiceId($oDeliveryService->getId());
					$oOrder->setStatus(PluginMinimarket_ModuleOrder::ORDER_STATUS_DELIVERY_SELECTED);
					$this->PluginMinimarket_Order_AddOrUpdateOrder($oOrder);
					/**
					 * Формирование объекта платежа
					 *
					 * Получение суммы товаров в корзине
					 */
					$aCartSumData = $this->PluginMinimarket_Order_GetCartSumDataByOrder($oOrder);
					/**
					 * Определение валюты расчета
					 */
					$oCurrency = $this->PluginMinimarket_Currency_GetCurrencyBySettings('cart');
					/**
					 * Подсчет суммы корзины и доставки
					 */
					$nSum = $aCartSumData['cart_sum'] + $oDeliveryService->getCartCost();
					/**
					 * Создание платежа
					 */
					$this->PluginMinimarket_Payment_MakePayment(
						'order',
						$oOrder->getId(),
						$nSum,
						$oCurrency->getId(),
						true
					);
				}
			}
			/**
			 * Получение списка ID товаров, связанных с заказом
			 */
			$aCartObjects = $this->PluginMinimarket_Order_GetCartObjectsByOrder($oOrder->getId());
			/**
			 * Получение списка товаров по списку ID товаров
			 */
			$aProducts = $this->PluginMinimarket_Product_GetProductsAdditionalData(array_keys($aCartObjects), 'cart_price_currency', $aCartObjects);
			/**
			 * Загрузка данных в шаблон
			 */
			$this->Viewer_Assign('aProducts', $aProducts);
			$this->Viewer_Assign('aCartObjects', $aCartObjects);
			$this->Viewer_Assign('aDeliveryServices', $aDeliveryServices);
			$this->Viewer_Assign('aCartSumData', $this->PluginMinimarket_Order_GetCartSumDataByOrder($oOrder));
		} else {
			return parent::EventNotFound();
		}
	}
	
	protected function EventAddress() {
		/**
		 * Установка шаблона
		 */
		$this->SetTemplateAction('address');
		$this->Viewer_Assign('noSidebar', true);
		/**
		 * Получение объекта заказа по ключу
		 */
		if (
			false !== ($oOrder = $this->PluginMinimarket_Order_GetOrderByCookie())
			&& false !== $this->PluginMinimarket_Order_CheckStepOrder($oOrder, PluginMinimarket_ModuleOrder::ORDER_STATUS_CART_FULL)
		) {
			/**
			 * Загрузка в шаблон списка стран, регионов, городов
			 */
			$aCountries = $this->Geo_GetCountries(array(), array('sort' => 'asc'), 1, 300);
			$this->Viewer_Assign('aGeoCountries', $aCountries['collection']);
			if (isPost('submit')) {
				/**
				 * Определение гео-объекта с точносью до города
				 */
				if (
					!getRequest('geo_city')
					|| !($oGeoObject = $this->Geo_GetGeoObject('city', getRequestStr('geo_city')))
				) {
					$this->Message_AddErrorSingle($this->Lang_Get('plugin.minimarket.order_address_city_error'), $this->Lang_Get('error'));
					return;
				}
				/**
				 * Проверка полей
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
				 * Обновление статуса, данных по доставке и способа оплаты
				 */		
				$oOrder->setStatus(PluginMinimarket_ModuleOrder::ORDER_STATUS_ADDRESS_SAVED);
				/**
				 * Создание связи с гео-объектом
				 */
				$this->Geo_CreateTarget($oGeoObject, 'order', $oOrder->getId());
				/**
				 * Обновление заказа
				 */
				$this->PluginMinimarket_Order_AddOrUpdateOrder($oOrder);
				/**
				 * Перенаправление на страницу выбора доставки
				 */
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