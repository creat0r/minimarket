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

	protected $oMapper;
	
	/**
	 * Инициализация модуля
	 */
	public function Init() {
		$this->oMapper=Engine::GetMapper(__CLASS__);
	}

    /**
     * Генерирует уникальный ключ. Если такой уже существует, генерирует новый.
     *
     * @return string
     */	
	public function GetUniqueKeyForOrder() {
		while(true) {
			$sUnique = func_generator(32);
			if (!($oOrder = $this->GetOrderByKey($sUnique))) break;
		}
		return $sUnique;
	}
	
    /**
     * Возвращает заказ по ключу
     *
	 * @param string $sKey			Уникальный ключ заказа
     * @return PluginMinimarket_ModuleOrder_EntityOrder|bool
     */
	public function GetOrderByKey($sKey) {
		return $this->oMapper->GetOrderByKey($sKey);
	}
	
    /**
     * Добавляет либо обновляет (если запись с таким ключом уже существует) заказ
     *
	 * @param PluginMinimarket_ModuleOrder_EntityOrder $oOrder			Объект заказа
     * @return bool
     */
	public function AddOrUpdateOrder($oOrder) {
		return $this->oMapper->AddOrUpdateOrder($oOrder);
	}
	
    /**
     * Возвращает запись из корзины по ID заказа и ID товара
     *
	 * @param int $iOrder			ID заказа
	 * @param int $iProduct			ID товара
	 * 
     * @return PluginMinimarket_ModuleOrder_EntityOrderCart|null
     */
	public function GetCartObjectByOrderAndProduct($iOrder, $iProduct) {
		return $this->oMapper->GetCartObjectByOrderAndProduct($iOrder, $iProduct);
	}
	
    /**
     * Возвращает список записей из корзины по ID заказа
     *
	 * @param int $iOrder			ID заказа
     * @return array
     */
	public function GetCartObjectsByOrder($iOrder) {
		return $this->oMapper->GetCartObjectsByOrder($iOrder);
	}
	
    /**
     * Обновляет запись в корзине
     *
	 * @param PluginMinimarket_ModuleOrder_EntityOrderCart $oCart			Объект записи в корзине
     * @return bool
     */
	public function UpdateCartObject(PluginMinimarket_ModuleOrder_EntityOrderCart $oCart) {
		return $this->oMapper->UpdateCartObject($oCart);
	}
	
    /**
     * Добавляет запись в корзину
     *
	 * @param PluginMinimarket_ModuleOrder_EntityOrderCart $oCart			Объект записи
     * @return int|null
     */
	public function AddCartObject(PluginMinimarket_ModuleOrder_EntityOrderCart $oCart) {
		return $this->oMapper->AddCartObject($oCart);
	}
	
    /**
     * Удаляет запись из корзины
     *
	 * @param PluginMinimarket_ModuleOrder_EntityOrderCart $oCartObject			Объект записи
     * @return bool
     */
	public function DeleteCartObject(PluginMinimarket_ModuleOrder_EntityOrderCart $oCartObject) {
		return $this->oMapper->DeleteCartObject($oCartObject);
	}
	
    /**
     * Удаляет все записи из корзины по ID заказа
     *
	 * @param int $iOrderId			ID заказа
     * @return bool
     */
	public function DeleteCartObjectsByOrder($iOrderId) {
		return $this->oMapper->DeleteCartObjectsByOrder($iOrderId);
	}

    /**
     * Удаляет заказ по ID
     *
	 * @param int $iOrderId			ID заказа
	 *
     * @return bool
     */
	public function DeleteOrder($iOrderId) {
		return $this->oMapper->DeleteOrder($iOrderId);
	}
	
    /**
     * Получает список заказов по фильтру
     *
     * @param array $aFilter			Фильтр выборки
     * @param array $aOrder				Сортировка
     * @param int   $iCurrPage			Номер текущей страницы
     * @param int   $iPerPage			Количество элементов на одну страницу
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
     * @param array $aOrderId			Список ID заказов
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
         * Получаем списоки ID систем оплаты и служб доставки
         */
		$aDeliveryServiceId = array();
		$aPaySystemId = array();
		foreach ($aOrder as $oOrder) {
			$aDeliveryServiceId[] = $oOrder->getDeliveryServiceId();
			$aPaySystemId[] = $oOrder->getPaySystemId();
		}
		$aDeliveryService = $this->PluginMinimarket_Delivery_GetDeliveryServicesByArrayId($aDeliveryServiceId);
		$aPaySystem = $this->PluginMinimarket_Pay_GetPaySystemsByArrayId($aPaySystemId);
        /**
         * Добавляем дополнительные данные к списку заказов
         */
		foreach ($aOrder as $key=>$val) {
			if (isset($aDeliveryService[$val->getDeliveryServiceId()])) {
				$aOrder[$key]->setDeliveryServiceName($aDeliveryService[$val->getDeliveryServiceId()]->getName());
			}
				
			if (isset($aPaySystem[$val->getPaySystemId()]))
				$aOrder[$key]->setPaySystemName($aPaySystem[$val->getPaySystemId()]->getName());
		}
				
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
     * Возвращает сумму стоимости товаров, находящихся в корзине (не включая стоимость доставки)
     *
	 * @param PluginMinimarket_ModuleOrder_EntityOrder $oOrder			Объект заказа
     * @return float
     */
	public function GetCartSumByOrder(PluginMinimarket_ModuleOrder_EntityOrder $oOrder) {
		$aCartObjects = $this->PluginMinimarket_Order_GetCartObjectsByOrder($oOrder->getId());
		$aProducts = $this->PluginMinimarket_Product_GetProductsAdditionalData(array_keys($aCartObjects));
		$fCartSumm = 0;
		foreach($aProducts as $oProduct) {
			$fCartSumm += $oProduct->getPrice() * $aCartObjects[$oProduct->getId()];
		}
		return $fCartSumm;
	}
}
?>