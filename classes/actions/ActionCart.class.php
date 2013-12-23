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
		if (false !== ($oOrder = $this->PluginMinimarket_Order_GetOrderByCookie())) {
			/**
			 * Позволяет ли статус заказа удалять товары из корзины
			 */
			if ($oOrder->getStatus() != PluginMinimarket_ModuleOrder::ORDER_STATUS_CART_INIT) {
				return parent::EventNotFound();
			}
			if ($oCartObject = $this->PluginMinimarket_Order_GetCartObjectByOrderAndProduct($oOrder->getId(), $this->GetParam(0))) {
				$this->PluginMinimarket_Order_DeleteCartObject($oCartObject);
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
		if (false !== ($oOrder = $this->PluginMinimarket_Order_GetOrderByCookie())) {
			/**
			 * Позволяет ли статус заказа изменять количество товаров в корзине
			 */
			if ($oOrder->getStatus() != PluginMinimarket_ModuleOrder::ORDER_STATUS_CART_INIT) {
				return parent::EventNotFound();
			}
			if ($oCart = $this->PluginMinimarket_Order_GetCartObjectByOrderAndProduct($oOrder->getId(), (int)getRequest('product', null, 'post'))) {
				$oCart->setProductCount((int)getRequest('count', null, 'post'));
				$this->PluginMinimarket_Order_UpdateCartObject($oCart);
			}
		}
	}
	
	protected function EventCart() {
		/**
		 * Установка шаблона
		 */
		$this->SetTemplateAction('cart');
		$this->Viewer_Assign('noSidebar', true);
		/**
		 * Получение объекта заказа по ключу
		 * и определение доступа к текущему шагу заказа
		 */
		if (
			false !== ($oOrder = $this->PluginMinimarket_Order_GetOrderByCookie())
			&& false !== $this->PluginMinimarket_Order_CheckStepOrder($oOrder, PluginMinimarket_ModuleOrder::ORDER_STATUS_CART_INIT, true)
		) {
			/**
			 * Получение списка ID товаров, связанных с объектом заказа
			 */
			$aCartObjects = $this->PluginMinimarket_Order_GetCartObjectsByOrder($oOrder->getId());
			/**
			 * Если пользователь хочет перейти к вводу адреса и была нажата кнопка Далее
			 */
			if (isset($_REQUEST['submit'])) {
				/**
				 * Удаление товаров с нулевым количеством
				 */
				foreach ($aCartObjects as $iProductId => $iCount) {
					if ($iCount < 1) {
						unset($aCartObjects[$iProductId]);
						$this->PluginMinimarket_Order_DeleteCartObjectByOrderIdAndProductId($oOrder->getId(), $iProductId);
					}
				}
				if (!empty($aCartObjects)) {
					/**
					 * Обновление статуса заказа
					 */
					$oOrder->setStatus(PluginMinimarket_ModuleOrder::ORDER_STATUS_CART_FULL);
					$this->PluginMinimarket_Order_AddOrUpdateOrder($oOrder);
					/**
					 * Перенаправление на страницу ввода адреса
					 */
					Router::Location('order/address/');
				} else {
					Router::Location('cart/');
				}
			}
			/**
			 * Получение списка товаров по списку ID
			 */
			$aProducts = $this->PluginMinimarket_Product_GetProductsAdditionalData(array_keys($aCartObjects), 'cart_price_currency', $aCartObjects);
			/**
			 * Передача данных в шаблон
			 */
			$this->Viewer_Assign('aProducts', $aProducts);
			$this->Viewer_Assign('aCartObjects', $aCartObjects);
			$this->Viewer_Assign('aCartSumData', $this->PluginMinimarket_Order_GetCartSumDataByOrder($oOrder));
		}
	}
	
	protected function EventAddProduct() {
		$bError = true;
		/**
		 * Проверка, можно ли получить объект заказа по идентификатору из куки, и, если это так -- не ошибочен ли запрашиваемый шаг заказа
		 */
		if (
			false !== ($oOrder = $this->PluginMinimarket_Order_GetOrderByCookie())
			&& false !== $this->PluginMinimarket_Order_CheckStepOrder($oOrder, PluginMinimarket_ModuleOrder::ORDER_STATUS_CART_INIT, true)
		) {
			$bError = false;
		}
		/**
		 * Если объект заказа не получен -- инициализация нового
		 */
		if ($bError === true) {
			$sCookie = $this->PluginMinimarket_Order_InitCookieForOrder();
			$oOrder = Engine::GetEntity('PluginMinimarket_ModuleOrder_EntityOrder');
			$oOrder->setKey($sCookie);
			$oOrder->setTimeOrderInit(time());
			$oOrder->setStatus(PluginMinimarket_ModuleOrder::ORDER_STATUS_CART_INIT);
			if ($this->oUserCurrent) {
				$oOrder->setUserId($this->oUserCurrent->getId());
			}
		}
		/**
		 * Сохранение объекта заказа
		 */
		$this->PluginMinimarket_Order_AddOrUpdateOrder($oOrder);
		/**
		 * Получение только что добавленного/обновленного объекта заказа обратно по ключу
		 */
		$oOrder = $this->PluginMinimarket_Order_GetOrderByKey($oOrder->getKey());
		/**
		 * Проверка переданного идентификатора товара на наличие
		 */
		if (!($oProduct = $this->PluginMinimarket_Product_GetProductById($this->GetParam(0)))) {
			return parent::EventNotFound();
		}
		/**
		 * Создание объекта связи между заказом и добавляемым товаром в корзину
		 * Если такой товар уже есть в карзине, то осуществляется инкремент его количества. Иначе -- добавление этого объекта в БД
		 */
		if ($oCart = $this->PluginMinimarket_Order_GetCartObjectByOrderAndProduct($oOrder->getId(), $oProduct->getId())) {
			$oCart->setProductCount($oCart->getProductCount() + 1);
			$this->PluginMinimarket_Order_UpdateCartObject($oCart);
		} else {
			$oCart = Engine::GetEntity('PluginMinimarket_ModuleOrder_EntityOrderCart');
			$oCart->setOrderId($oOrder->getId());
			$oCart->setProductId($oProduct->getId());
			$oCart->setProductCount(1);
			$this->PluginMinimarket_Order_AddCartObject($oCart);
		}
		/**
		 * Обновление заказа
		 */
		$this->PluginMinimarket_Order_AddOrUpdateOrder($oOrder);
		
		Router::Location('cart');
	}
}
?>