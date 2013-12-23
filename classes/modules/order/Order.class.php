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

class PluginMinimarket_ModuleOrder extends Module {

	/**
	 * Статусы заказа
	 */
	const ORDER_STATUS_CART_INIT           = 1;
	const ORDER_STATUS_CART_FULL           = 2;
	const ORDER_STATUS_ADDRESS_SAVED       = 3;
	const ORDER_STATUS_DELIVERY_SELECTED   = 4;
	const ORDER_STATUS_PAY_SYSTEM_SELECTED = 5;
	const ORDER_STATUS_PAYD                = 6;
	const ORDER_STATUS_DELIVERED           = 7;

	protected $oMapper;

	/**
	 * Инициализация модуля
	 */
	public function Init() {
		$this->oMapper = Engine::GetMapper(__CLASS__);
	}

    /**
     * Определяет, соответствует ли заказ запрашиваемому статусу
     *
	 * @param PluginMinimarket_ModuleOrder_EntityOrder $oOrder           Объект заказа
	 * @param string                                   $sStatus          Статус состояния заказа
	 * @param bool                                     $bDeleteCookie    Удалять ли куку перед возвратом false
	 *
     * @return bool
     */
	public function CheckStepOrder($oOrder, $sStatus, $bDeleteCookie = null) {
		/**
		 * Если заказ уже оплачен либо доставлен -- возвращает ошибку
		 */
		if (in_array($oOrder->getStatus(), array(self::ORDER_STATUS_PAYD, self::ORDER_STATUS_DELIVERED))) {
			if ($bDeleteCookie) $this->DeleteCookieOrder();
			return false;
		}
		/**
		 * Если статус заказа не равен запрашиваемому -- делает редирект на необходимый шаг заказа
		 */
		if ($oOrder->getStatus() != $sStatus) {
			switch ($oOrder->getStatus()) {
				case self::ORDER_STATUS_CART_INIT:
					Router::Location("cart/");
				case self::ORDER_STATUS_CART_FULL:
					Router::Location("order/address/");
				case self::ORDER_STATUS_ADDRESS_SAVED:
					Router::Location("order/delivery/");
				case self::ORDER_STATUS_DELIVERY_SELECTED:
					if (false !== ($oPayment = $this->PluginMinimarket_Payment_GetPaymentByIdObjectPaymentAndTypeObjectPayment($oOrder->getId(), 'order'))) {
						Router::Location("payment/{$oPayment->getId()}/");
					}
					if ($bDeleteCookie) $this->DeleteCookieOrder();
					return false;
				case self::ORDER_STATUS_PAY_SYSTEM_SELECTED:
					if (false !== ($oPayment = $this->PluginMinimarket_Payment_GetPaymentByIdObjectPaymentAndTypeObjectPayment($oOrder->getId(), 'order'))) {
						Router::Location("payment/init/{$oPayment->getId()}/");
					}
					if ($bDeleteCookie) $this->DeleteCookieOrder();
					return false;
			}
		}
		return true;
	}

    /**
     * Удаляет куку заказа
     */	
	public function DeleteCookieOrder() {
		setcookie('minimarket_order_target_tmp', null, -1, Config::Get('sys.cookie.path'), Config::Get('sys.cookie.host'));
	}

    /**
     * Генерирует уникальный ключ. Если такой уже существует, генерирует новый.
     *
     * @return string
     */	
	public function GetUniqueKeyForOrder() {
		while (true) {
			$sUnique = func_generator(32);
			if (!($oOrder = $this->GetOrderByKey($sUnique))) break;
		}
		return $sUnique;
	}
	
    /**
     * Возвращает заказ по ID
     *
	 * @param string $iId    ID заказа
	 *
     * @return PluginMinimarket_ModuleOrder_EntityOrder|bool
     */
	public function GetOrderById($iId) {
		return $this->oMapper->GetOrderById($iId);
	}
	
    /**
     * Возвращает объект заказа при наличии соответствующей куки
     *
     * @return PluginMinimarket_ModuleOrder_EntityOrder|bool
     */
	public function GetOrderByCookie() {
		if (
			isset($_COOKIE['minimarket_order_target_tmp'])
			&& !empty($_COOKIE['minimarket_order_target_tmp'])
			&& false !== ($oOrder = $this->GetOrderByKey($_COOKIE['minimarket_order_target_tmp']))
		) {
			/**
			 * Обновление времени действия куки
			 */
			setcookie('minimarket_order_target_tmp', $_COOKIE['minimarket_order_target_tmp'], time() + 24 * 3600, Config::Get('sys.cookie.path'), Config::Get('sys.cookie.host'));
			return $oOrder;
		} else {
			return false;
		}
	}
	
    /**
     * Устанавливает куку для работы с заказом
     *
     * @return string
     */
	public function InitCookieForOrder() {
		/**
		 * Генерация уникального ключа на 100% (относительно таблицы minimarket_order)
		 */
		$sCookie = $this->GetUniqueKeyForOrder();
		setcookie('minimarket_order_target_tmp',  $sCookie, time () + 24 * 3600, Config::Get('sys.cookie.path'), Config::Get('sys.cookie.host'));
		return $sCookie;
	}

    /**
     * Возвращает заказ по ключу
     *
	 * @param string $sKey    Уникальный ключ заказа
	 *
     * @return PluginMinimarket_ModuleOrder_EntityOrder|bool
     */
	public function GetOrderByKey($sKey) {
		return $this->oMapper->GetOrderByKey($sKey);
	}
	
    /**
     * Добавляет либо обновляет (если запись с таким ключом уже существует) заказ
     *
	 * @param PluginMinimarket_ModuleOrder_EntityOrder $oOrder    Объект заказа
	 *
     * @return bool
     */
	public function AddOrUpdateOrder($oOrder) {
		return $this->oMapper->AddOrUpdateOrder($oOrder);
	}
	
    /**
     * Возвращает запись из корзины по ID заказа и ID товара
     *
	 * @param int $iOrder      ID заказа
	 * @param int $iProduct    ID товара
	 * 
     * @return PluginMinimarket_ModuleOrder_EntityOrderCart|null
     */
	public function GetCartObjectByOrderAndProduct($iOrder, $iProduct) {
		return $this->oMapper->GetCartObjectByOrderAndProduct($iOrder, $iProduct);
	}
	
    /**
     * Возвращает список записей из корзины по ID заказа
     *
	 * @param int $iOrder    ID заказа
	 *
     * @return array
     */
	public function GetCartObjectsByOrder($iOrder) {
		return $this->oMapper->GetCartObjectsByOrder($iOrder);
	}
	
    /**
     * Обновляет запись в корзине
     *
	 * @param PluginMinimarket_ModuleOrder_EntityOrderCart $oCart    Объект записи в корзине
	 *
     * @return bool
     */
	public function UpdateCartObject(PluginMinimarket_ModuleOrder_EntityOrderCart $oCart) {
		return $this->oMapper->UpdateCartObject($oCart);
	}
	
    /**
     * Добавляет запись в корзину
     *
	 * @param PluginMinimarket_ModuleOrder_EntityOrderCart $oCart    Объект записи
	 *
     * @return int|null
     */
	public function AddCartObject(PluginMinimarket_ModuleOrder_EntityOrderCart $oCart) {
		return $this->oMapper->AddCartObject($oCart);
	}
	
    /**
     * Удаляет запись из корзины
     *
	 * @param PluginMinimarket_ModuleOrder_EntityOrderCart $oCartObject    Объект записи
	 *
     * @return bool
     */
	public function DeleteCartObject(PluginMinimarket_ModuleOrder_EntityOrderCart $oCartObject) {
		return $this->oMapper->DeleteCartObject($oCartObject);
	}
	
    /**
     * Удаляет запись из корзины по ID заказа и ID товара
     *
	 * @param int $iOrderId      ID заказа
	 * @param int $iProductId    ID продукта
	 *
     * @return bool
     */
	public function DeleteCartObjectByOrderIdAndProductId($iOrderId, $iProductId) {
		return $this->oMapper->DeleteCartObjectByOrderIdAndProductId($iOrderId, $iProductId);
	}
	
    /**
     * Удаляет все записи из корзины по ID заказа
     *
	 * @param int $iOrderId    ID заказа
	 *
     * @return bool
     */
	public function DeleteCartObjectsByOrder($iOrderId) {
		return $this->oMapper->DeleteCartObjectsByOrder($iOrderId);
	}

    /**
     * Удаляет заказ по ID
     *
	 * @param int $iOrderId    ID заказа
	 *
     * @return bool
     */
	public function DeleteOrder($iOrderId) {
		return $this->oMapper->DeleteOrder($iOrderId);
	}
	
    /**
     * Получает список заказов по фильтру
     *
     * @param array $aFilter      Фильтр выборки
     * @param array $aOrder       Сортировка
     * @param int   $iCurrPage    Номер текущей страницы
     * @param int   $iPerPage     Количество элементов на одну страницу
     *
     * @return array
     */
    public function GetOrdersByFilter($aFilter, $aOrder, $iCurrPage = 1, $iPerPage = 10) {
        if (!is_numeric($iCurrPage) || $iCurrPage <= 0) {
            $iCurrPage = 1;
        }
		$data = array(
			'collection' => $this->oMapper->GetOrdersByFilter($aFilter, $aOrder, $iCount, $iCurrPage, $iPerPage),
			'count'      => $iCount
		);
        $data['collection'] = $this->GetOrdersAdditionalData($data['collection']);

        return $data;
    }
	
    /**
     * Получает дополнительные данные (объекты) для заказов по их ID
     *
     * @param array $aOrderId    Список ID заказов
     *
     * @return array
     */
	public function GetOrdersAdditionalData($aOrderId) {
        if (!is_array($aOrderId)) {
            $aOrderId = array($aOrderId);
        }
        /**
         * Получаем "голые" заказы
         */
        $aOrder = $this->GetOrdersByArrayId($aOrderId);
        /**
         * Тут необходимо формировать и добавлять дополнительные данные к каждому объекту заказа
         */
		return $aOrder;
	}
	
    /**
     * Возвращает список заказов по ID
     *
     * @param array $aOrderId    Список ID комментариев
     *
     * @return array
     */
	public function GetOrdersByArrayId($aOrderId) {
		return $this->oMapper->GetOrdersByArrayId($aOrderId);
	}
	
    /**
     * Возвращает сумму стоимости товаров, находящихся в корзине
     *
	 * @param PluginMinimarket_ModuleOrder_EntityOrder $oOrder    Объект заказа
	 *
     * @return array
     */
	public function GetCartSumDataByOrder(PluginMinimarket_ModuleOrder_EntityOrder $oOrder) {
		$aCurrency = $this->PluginMinimarket_Currency_GetAllCurrency();
		/**
		 * Определение, в какой валюте будут отображаться товары в корзине
		 */
		$oCurrency = $this->PluginMinimarket_Currency_GetCurrencyBySettings('cart');
		/**
		 * Получение списка ID продуктов, находящихся в корзине
		 */
		$aCartObjects = $this->PluginMinimarket_Order_GetCartObjectsByOrder($oOrder->getId());
		/**
		 * Получение списка продуктов по ID
		 */
		$aProducts = $this->PluginMinimarket_Product_GetProductsAdditionalData(array_keys($aCartObjects));
		/**
		 * Подсчет суммы. Значение количества знаков после запятой берется из валюты корзины
		 */
		$nCartSum = 0;
		foreach($aProducts as $oProduct) {
			$nCartSum += ($oProduct->getPrice() / (($oCurrency->getCourse() / $oCurrency->getNominal()) / ($aCurrency[$oProduct->getCurrency()]->getCourse() / $aCurrency[$oProduct->getCurrency()]->getNominal()))) * $aCartObjects[$oProduct->getId()];
		}
		return array(
			'cart_sum' => $nCartSum,
			'cart_sum_currency' => $this->PluginMinimarket_Currency_GetSumByFormat(
				$nCartSum / Config::Get('plugin.minimarket.settings.factor'),
				$oCurrency->getDecimalPlaces(),
				$oCurrency->getFormat()
			),
			'format' => $oCurrency->getFormat(),
			'decimal_places' => $oCurrency->getDecimalPlaces(),
		);
	}
}
?>