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

class PluginMinimarket_ActionCart extends ActionPlugin {
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
		$this->AddEvent('', 'EventCart');
		$this->AddEvent('add', 'EventAddProduct');
		$this->AddEvent('update', 'EventUpdate');
		$this->AddEvent('delete', 'EventDelete');
	}
	
	protected function EventDelete() {
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
			if ($oCartObject = $this->PluginMinimarket_Order_GetCartObjectByOrderAndProduct($oOrder->getId(), $this->GetParam(0))) {
				$this->PluginMinimarket_Order_DeleteCartObject($oCartObject);
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
				 * Подсчитаем стоимость корзины и обновим заказ
				 */
				$oOrder->setCartSum(number_format($this->PluginMinimarket_Order_GetCartSumByOrder($oOrder),2,'.',''));
				$this->PluginMinimarket_Order_AddOrUpdateOrder($oOrder);
			}
		}
		Router::Location('cart');
	}
	
	protected function EventUpdate() {
		/**
		 * Устанавливаем формат ответа
		 */
        $this->Viewer_SetResponseAjax('json');
		/**
		 * Получаем объект заказа по ключу
		 */
		if (
			isset($_COOKIE['minimarket_order_target_tmp']) 
			&& !empty($_COOKIE['minimarket_order_target_tmp'])
			&& false!==($oOrder = $this->PluginMinimarket_Order_GetOrderByKey($_COOKIE['minimarket_order_target_tmp']))
			&& !$oOrder->getStatus()
		) {
			if ($oCart = $this->PluginMinimarket_Order_GetCartObjectByOrderAndProduct($oOrder->getId(), (int)getRequest('product', null, 'post'))) {
				$oCart->setProductCount((int)getRequest('count', null, 'post'));
				$this->PluginMinimarket_Order_UpdateCartObject($oCart);
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
				 * Подсчитаем стоимость корзины и обновим заказ
				 */
				$oOrder->setCartSum(number_format($this->PluginMinimarket_Order_GetCartSumByOrder($oOrder),2,'.',''));
				$this->PluginMinimarket_Order_AddOrUpdateOrder($oOrder);
			}
		}
	}
	
	protected function EventCart() {
		if (isset($_REQUEST['submit'])) Router::Location('order/address/');
		/**
		 * Устанавливаем шаблон
		 */
		$this->SetTemplateAction('cart');
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
			 * По объекту заказа получаем список ID товаров, с ним связанных
			 */
			$aCartObjects = $this->PluginMinimarket_Order_GetCartObjectsByOrder($oOrder->getId());
			/**
			 * По списку ID получаем массив товаров
			 */
			$aProducts = $this->PluginMinimarket_Product_GetProductsAdditionalData(array_keys($aCartObjects));
			$this->Viewer_Assign('aProducts', $aProducts);
			$this->Viewer_Assign('aCartObjects', $aCartObjects);
		}
	}
	
	protected function EventAddProduct() {
		$oOrder = Engine::GetEntity('PluginMinimarket_ModuleOrder_EntityOrder');
		/**
		 * Если нет временного ключа для нового заказа, то генерируем. Иначе просто обновляем его время действия.
		 */
		if (empty($_COOKIE['minimarket_order_target_tmp'])) {
			/**
			 * Генерируем 100% уникальный ключ (относительно таблицы minimarket_order)
			 */
			$sCookie = $this->PluginMinimarket_Order_GetUniqueKeyForOrder();
			setcookie('minimarket_order_target_tmp',  $sCookie, time()+24*3600, Config::Get('sys.cookie.path'), Config::Get('sys.cookie.host'));
		} else {
			/**
			 * Если по существующему ключу не можем получить заказ, то генерируем новый
			 */
			$sCookie = $_COOKIE['minimarket_order_target_tmp'];
			if (false!==($oOrder = $this->PluginMinimarket_Order_GetOrderByKey($sCookie)) && $oOrder->getStatus() < 2) {
				$oOrder->setClientName($oOrder->getClientName());
				$oOrder->setClientIndex($oOrder->getClientIndex());
				$oOrder->setClientAddress($oOrder->getClientAddress());
				$oOrder->setClientPhone($oOrder->getClientPhone());
				$oOrder->setClientComment($oOrder->getClientComment());				
			} else {
				$oOrder = Engine::GetEntity('PluginMinimarket_ModuleOrder_EntityOrder');
				$sCookie = $this->PluginMinimarket_Order_GetUniqueKeyForOrder();				
			}
			setcookie('minimarket_order_target_tmp', $sCookie, time()+24*3600, Config::Get('sys.cookie.path'), Config::Get('sys.cookie.host'));
		}
		/**
		 * Если статус не нулевой -- редиректим на страницу оплаты
		 */
		if ($oOrder->getStatus()) {
			Router::Location('order/payment/');
		}
        if ($this->oUserCurrent) {
            $oOrder->setUserId($this->oUserCurrent->getId());
        }
		$oOrder->setKey($sCookie);
		/**
		 * Обнуляем статус, время заказа, данные по доставке и способ оплаты
		 */		
		$oOrder->setStatus(null);
		$oOrder->setTime(null);
		$oOrder->setDeliveryServiceTimeFrom(null);
		$oOrder->setDeliveryServiceTimeTo(null);
		$oOrder->setDeliveryServiceId(null);
		$oOrder->setDeliveryServiceSum(null);
		$oOrder->setPaySystemId(null);
		/**
		 * Сохраняем объект заказа
		 */
		$this->PluginMinimarket_Order_AddOrUpdateOrder($oOrder);
		/**
		 * Достаем только что добавленный/обновленный объект заказа обратно по ключу
		 */
		$oOrder = $this->PluginMinimarket_Order_GetOrderByKey($sCookie);
		/**
		 * Проверяем переданный идентификатор товара на наличие
		 */
		if(!($oProduct = $this->PluginMinimarket_Product_getProductById($this->GetParam(0)))) {
			return parent::EventNotFound();
		}
		/**
		 * Создаем объект связи между заказом и добавляемым товаром в корзину
		 * Если такой товар уже есть в карзине, то делаем инкремент его количества. Иначе -- добавляем этот объект в БД.
		 */
		if ($oCart = $this->PluginMinimarket_Order_GetCartObjectByOrderAndProduct($oOrder->getId(), $oProduct->getId())) {
			$oCart->setProductCount($oCart->getProductCount()+1);
			$this->PluginMinimarket_Order_UpdateCartObject($oCart);
		} else {
			$oCart = Engine::GetEntity('PluginMinimarket_ModuleOrder_EntityOrderCart');
			$oCart->setOrderId($oOrder->getId());
			$oCart->setProductId($oProduct->getId());
			$oCart->setProductCount(1);
			$this->PluginMinimarket_Order_AddCartObject($oCart);
		}
		/**
		 * Подсчитаем стоимость корзины и обновим заказ
		 */
		$oOrder->setCartSum(number_format($this->PluginMinimarket_Order_GetCartSumByOrder($oOrder),2,'.',''));
		$this->PluginMinimarket_Order_AddOrUpdateOrder($oOrder);
		
		Router::Location('cart');
	}
}
?>