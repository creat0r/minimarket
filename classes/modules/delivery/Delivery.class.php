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

class PluginMinimarket_ModuleDelivery extends Module {

	protected $oMapper;
	
	/**
	 * Инициализация модуля
	 */
	public function Init() {
		$this->oMapper=Engine::GetMapper(__CLASS__);
	}
	
    /**
     * Обновление службы доставки
     *
     * @param PluginMinimarket_ModuleDelivery_EntityService $oDeliveryService			Объект службы доставки
     *
     * @return bool
     */
	public function UpdateDeliveryService(PluginMinimarket_ModuleDelivery_EntityService $oDeliveryService) {
		if($this->oMapper->UpdateDeliveryService($oDeliveryService)) {
			/**
			 * Удаляем старые связи с группами местоположений и системами оплаты
			 */
			$this->PluginMinimarket_Link_DeleteLinkByParentAndType($oDeliveryService->getId(),'delivery_service_location_group');
			$this->PluginMinimarket_Link_DeleteLinkByParentAndType($oDeliveryService->getId(),'delivery_service_pay_system');
			/**
			 * Создаем новые связи между службой доставки и группами местоположений
			 */
			$aObjectLocationGroup = array();
			$aLocationGroupId = $oDeliveryService->getLocationGroups();
			foreach($aLocationGroupId as $idLocationGroup) {
				$oLink = Engine::GetEntity('PluginMinimarket_ModuleLink_EntityLink');
				$oLink->setObjectId($idLocationGroup);
				$oLink->setParentId($oDeliveryService->getId());
				$oLink->setObjectType('delivery_service_location_group');
				$aObjectLocationGroup[] = $oLink;
			}
			/**
			 * Создадим связи между службой доставки и системами оплаты
			 */
			$aObjectPaySystem = array();
			$aPaySystemId = $oDeliveryService->getPaySystems();
			foreach($aPaySystemId as $idPaySystem) {
				$oLink = Engine::GetEntity('PluginMinimarket_ModuleLink_EntityLink');
				$oLink->setObjectId($idPaySystem);
				$oLink->setParentId($oDeliveryService->getId());
				$oLink->setObjectType('delivery_service_pay_system');
				$aObjectPaySystem[] = $oLink;
			}
			/**
			 * Добавляем массив связей одним запросом
			 */
			$this->PluginMinimarket_Link_AddLinks($aObjectLocationGroup);
			$this->PluginMinimarket_Link_AddLinks($aObjectPaySystem);
			return true;
		}
		return false;
	}
	
    /**
     * Создание службы доставки
     *
     * @param PluginMinimarket_ModuleDelivery_EntityService $oDeliveryService			Объект службы доставки
     *
     * @return int|bool
     */
	public function AddDeliveryService(PluginMinimarket_ModuleDelivery_EntityService $oDeliveryService) {
		if($iId = $this->oMapper->AddDeliveryService($oDeliveryService)) {
			/**
			 * Создадим связи между службой доставки и группами местоположений
			 */
			$aObjectLocationGroup = array();
			$aLocationGroupId = $oDeliveryService->getLocationGroups();
			foreach($aLocationGroupId as $idLocationGroup) {
				$oLink = Engine::GetEntity('PluginMinimarket_ModuleLink_EntityLink');
				$oLink->setObjectId($idLocationGroup);
				$oLink->setParentId($iId);
				$oLink->setObjectType('delivery_service_location_group');
				$aObjectLocationGroup[] = $oLink;
			}
			/**
			 * Создадим связи между службой доставки и системами оплаты
			 */
			$aObjectPaySystem = array();
			$aPaySystemId = $oDeliveryService->getPaySystems();
			foreach($aPaySystemId as $idPaySystem) {
				$oLink = Engine::GetEntity('PluginMinimarket_ModuleLink_EntityLink');
				$oLink->setObjectId($idPaySystem);
				$oLink->setParentId($iId);
				$oLink->setObjectType('delivery_service_pay_system');
				$aObjectPaySystem[] = $oLink;
			}
			/**
			 * Добавляем массив связей одним запросом
			 */
			$this->PluginMinimarket_Link_AddLinks($aObjectLocationGroup);
			$this->PluginMinimarket_Link_AddLinks($aObjectPaySystem);
			return $iId;
		}
		return false;
	}
	
    /**
     * Возвращает список служб доставки по типу
     *
	 * @param string $sType			Тип службы доставки
     *
     * @return array
     */
	public function GetDeliveryServicesByType($sType) {
		return $this->oMapper->GetDeliveryServicesByType($sType);
	}
	
    /**
     * Возвращает список служб доставки по списку ID
     *
	 * @param array $aDeliveryServiceId			Список ID служб доставки
     *
     * @return array
     */
	public function GetDeliveryServicesByArrayId($aDeliveryServiceId) {
		return $this->oMapper->GetDeliveryServicesByArrayId($aDeliveryServiceId);
	}
	
    /**
     * Возвращает список активированных служб доставки по ID города, для которых они актуальны (настраивается в админке)
     *
	 * @param string $iCity			ID города
     *
     * @return array
     */
	public function GetActivationDeliveryServicesByCity($iCity) {
		return $this->oMapper->GetActivationDeliveryServicesByCity($iCity);
	}
	
    /**
     * Удаляет службу доставки
	 *
	 * @param PluginMinimarket_ModuleDelivery_EntityService $oDeliveryService			Объект службы доставки
     *
     * @return bool
     */
	public function DeleteDeliveryService($oDeliveryService) {
		if($this->oMapper->DeleteDeliveryService($oDeliveryService)) {
			/**
			 * Удаляем связи с группами местоположений
			 */
			$this->PluginMinimarket_Link_DeleteLinkByParentAndType($oDeliveryService->getId(),'delivery_service_location_group');
			/**
			 * Удаляем связи с системами оплаты
			 */
			$this->PluginMinimarket_Link_DeleteLinkByParentAndType($oDeliveryService->getId(),'delivery_service_pay_system');
			return true;
		}
		return false;
	}
	
    /**
     * Удаляет службу доставки по ключу
	 *
	 * @param  $sKey			Ключ службы доставки
     * @return bool
     */
	public function DeleteDeliveryServiceByKey($sKey) {
		if(
			false!==($oDeliveryService = $this->GetDeliveryServiceByKey($sKey))
			&& $this->oMapper->DeleteDeliveryServiceByKey($sKey)
		) {
			/**
			 * Удаляем связи с группами местоположений
			 */
			$this->PluginMinimarket_Link_DeleteLinkByParentAndType($oDeliveryService->getId(),'delivery_service_location_group');
			/**
			 * Удаляем связи с системами оплаты
			 */
			$this->PluginMinimarket_Link_DeleteLinkByParentAndType($oDeliveryService->getId(),'delivery_service_pay_system');
			return true;
		}
		return false;
	}
	
    /**
     * Возвращает объект службы доставки
     *
     * @param int $iId			ID службы доставки
     *
     * @return PluginMinimarket_ModuleDelivery_EntityService|null
     */
	public function GetDeliveryServiceById($iId) {
		return $this->oMapper->GetDeliveryServiceById($iId);
	}
	
    /**
     * Возвращает объект службы доставки по ключу
     *
     * @param int $sKey			Ключ службы доставки
     *
     * @return PluginMinimarket_ModuleDelivery_EntityService|null
     */
	public function GetDeliveryServiceByKey($sKey) {
		return $this->oMapper->GetDeliveryServiceByKey($sKey);
	}
	
    /**
     * Возвращает доступные службы доставки для конкретного заказа
     *
     * @param PluginMinimarket_ModuleOrder_EntityOrder $oOrder			Объект заказа
     *
     * @return array
     */
	public function GetAvailableDeliveryServicesByOrder($oOrder) {
		/**
		 * По объекту заказа получаем список ID товаров, с ним связанных
		 */
		$aCartObjects = $this->PluginMinimarket_Order_GetCartObjectsByOrder($oOrder->getId());
		/**
		 * По списку ID получаем массив товаров
		 */
		$aProducts = $this->PluginMinimarket_Product_GetProductsAdditionalData(array_keys($aCartObjects));
		/**
		 * Получаем гео-объект привязки
		 */
		$oGeoTarget = $this->Geo_GetTargetByTarget('order', $oOrder->getId());
		$aDeliveryServices = array();
		if (!$oGeoTarget) return $aDeliveryServices;
		/**
		 * Получаем список служ доставки по городу пользователя
		 */
		$aDeliveryServicesByCity = $this->PluginMinimarket_Delivery_GetActivationDeliveryServicesByCity($oGeoTarget->getCityId());
		/**
		 * Получаем доступные настраиваемые службы доставки
		 */
		$aDeliveryServices = $this->GetTunableDeliveryServicesByOrderAndByProducts($oOrder, $aProducts, $aCartObjects, $aDeliveryServicesByCity);
		/**
		 * Получаем доступные автоматические службы доставки
		 */
		$aDeliveryServices = $this->GetAutomaticDeliveryServicesByOrderAndByProducts($oOrder, $aProducts, $aCartObjects, $aDeliveryServicesByCity, $aDeliveryServices);
		return $aDeliveryServices;
	}
	
    /**
     * Возвращает список доступных автоматических служб доставки
     *
     * @param PluginMinimarket_ModuleOrder_EntityOrder				$oOrder						Объект заказа
     * @param array													$aProducts					Список товаров
     * @param array													$aCartObjects				Список ID товаров + количество каждого товара
     * @param array													$aDeliveryServicesByCity	Список всех служб доставки, доступных по ID города клиента
     * @param array|null											$aDeliveryServices			Список доступных служб доставки
     *
     * @return array
     */	
	public function GetAutomaticDeliveryServicesByOrderAndByProducts($oOrder, $aProducts, $aCartObjects, $aDeliveryServicesByCity, $aDeliveryServices = null) {
		if (!is_null($aDeliveryServices) && !is_array($aDeliveryServices)) $aDeliveryServices = array($aDeliveryServices);
		foreach($aDeliveryServicesByCity as $oDeliveryServiceAutomatic) {
			if ($oDeliveryServiceAutomatic->getType() == 'automatic') {
				switch ($oDeliveryServiceAutomatic->getKey()) {
					default:
						/**
						 * Для внешних служб доставки, реализованных отдельным плагином
						 */
						$oDeliveryServiceExternal = $this->GetDeliveryServiceExternalByOrderAndByProducts($oOrder, $aProducts, $aCartObjects, $oDeliveryServiceAutomatic);
						if ($oDeliveryServiceExternal['bOK'] === true) {
							$aDeliveryServices[] = $oDeliveryServiceExternal['oDeliveryService'];
						}
				}
			}
		}
		return $aDeliveryServices;
	}
	
    /**
     * Возвращает объект внешней службы доставки (реализованной другим плагином)
     *
     * @param PluginMinimarket_ModuleOrder_EntityOrder				$oOrder						Объект заказа
     * @param array													$aProducts					Список товаров
     * @param array													$aCartObjects				Список ID товаров + количество каждого товара
     * @param PluginMinimarket_ModuleDelivery_EntityService			$oDeliveryService			Объект службы доставки
     *
     * @return PluginMinimarket_ModuleDelivery_EntityService
     */		
	public function GetDeliveryServiceExternalByOrderAndByProducts($oOrder, $aProducts, $aCartObjects, $oDeliveryService) {
		$bOK = false;
		/**
		 * Запускаем выполнение хуков
		 */
		$this->Hook_Run('minimarket_get_delivery_service_external', array('bOK'=>&$bOK,'oDeliveryService' => &$oDeliveryService));
		return array('bOK' => $bOK,'oDeliveryService' => $oDeliveryService);
	}

    /**
     * Возвращает список доступных настраиваемых служб доставки
     *
     * @param PluginMinimarket_ModuleOrder_EntityOrder				$oOrder						Объект заказа
     * @param array													$aProducts					Список товаров
     * @param array													$aCartObjects				Список ID товаров + количество каждого товара
     * @param array													$aDeliveryServicesByCity	Список всех служб доставки, доступных по ID города клиента
     * @param array|null											$aDeliveryServices			Список доступных служб доставки
     *
     * @return array
     */	
	public function GetTunableDeliveryServicesByOrderAndByProducts($oOrder, $aProducts, $aCartObjects, $aDeliveryServicesByCity, $aDeliveryServices = null) {
		if (!is_null($aDeliveryServices) && !is_array($aDeliveryServices)) $aDeliveryServices = array($aDeliveryServices);
		foreach($aDeliveryServicesByCity as $oDeliveryServiceTunable) {
			if ($oDeliveryServiceTunable->getType() == 'tunable') {
				$oDeliveryService = Engine::GetEntity('PluginMinimarket_ModuleDelivery_EntityService');
				$oDeliveryService->setId($oDeliveryServiceTunable->getId());
				$oDeliveryService->setName($oDeliveryServiceTunable->getName());
				$oDeliveryService->setTimeFrom($oDeliveryServiceTunable->getTimeFrom());
				$oDeliveryService->setTimeTo($oDeliveryServiceTunable->getTimeTo());
				$oDeliveryService->setDescription($oDeliveryServiceTunable->getDescription());
				/**
				 * Расчитаем стоимость
				 * 
				 * Если стоимость -- это не процент от суммы
				 */
				if (!substr_count($oDeliveryServiceTunable->getCost(), '%')) {
					switch ($oDeliveryServiceTunable->getCostCalculation()) {
						case 1:
							/**
							 * Если стоимость расчитывается за весь заказ
							 */
							$oDeliveryService->setCost($oDeliveryServiceTunable->getCost());
							break;
						case 2:
							/**
							 * Если стоимость расчитывается за каждый товар
							 * Подсчитываем количество товаров
							 */
							$iCount = 0;
							foreach ($aCartObjects as $iCountProducts) {
								$iCount += $iCountProducts;
							}
							$oDeliveryService->setCost($oDeliveryServiceTunable->getCost() * $iCount);
							break;
						case 3:
							/**
							 * Если стоимость расчитывается относительно веса
							 * Подсчитываем общий вес в кг.
							 */
							$fWeight = 0;
							foreach ($aProducts as $oProduct) {
								$fWeight += $oProduct->getWeight();
							}
							$oDeliveryService->setCost($oDeliveryServiceTunable->getCost() * $fWeight);
							break;
					}
				} else {
					$oDeliveryService->setCost(($oOrder->getCartSum() / 100) * (float)$oDeliveryServiceTunable->getCost());
				}
				$aDeliveryServices[] = $oDeliveryService;
			}
		}
		return $aDeliveryServices;
	}
	
    /**
     * Добавление новой службы доставки
     * Если запись с таким уникальным ключом уже существует, то обновляет ее
     *
	 * @param PluginMinimarket_ModuleDelivery_EntityService $oDeliveryService			Объект службы доставки
     *
     * @return bool
     */
	public function AddOrUpdateDeliveryService(PluginMinimarket_ModuleDelivery_EntityService $oDeliveryService) {
		
		if(
			$this->oMapper->AddOrUpdateDeliveryService($oDeliveryService)
			&& false!==($oDeliveryServiceByKey = $this->GetDeliveryServiceByKey($oDeliveryService->getKey()))
		) {
			/**
			 * Удаляем старые связи с группами местоположений и системами оплаты
			 */
			$this->PluginMinimarket_Link_DeleteLinkByParentAndType($oDeliveryServiceByKey->getId(),'delivery_service_location_group');
			$this->PluginMinimarket_Link_DeleteLinkByParentAndType($oDeliveryServiceByKey->getId(),'delivery_service_pay_system');
			/**
			 * Создаем новые связи между службой доставки и группами местоположений
			 */
			$aObjectLocationGroup = array();
			$aLocationGroupId = $oDeliveryService->getLocationGroups();
			if(!empty($aLocationGroupId)) {
				foreach($aLocationGroupId as $idLocationGroup) {
					$oLink = Engine::GetEntity('PluginMinimarket_ModuleLink_EntityLink');
					$oLink->setObjectId($idLocationGroup);
					$oLink->setParentId($oDeliveryServiceByKey->getId());
					$oLink->setObjectType('delivery_service_location_group');
					$aObjectLocationGroup[] = $oLink;
				}
			}
			/**
			 * Создадим связи между службой доставки и системами оплаты
			 */
			$aObjectPaySystem = array();
			$aPaySystemId = $oDeliveryService->getPaySystems();
			if(!empty($aPaySystemId)) {
				foreach($aPaySystemId as $idPaySystem) {
					$oLink = Engine::GetEntity('PluginMinimarket_ModuleLink_EntityLink');
					$oLink->setObjectId($idPaySystem);
					$oLink->setParentId($oDeliveryServiceByKey->getId());
					$oLink->setObjectType('delivery_service_pay_system');
					$aObjectPaySystem[] = $oLink;
				}
			}
			/**
			 * Добавляем массив связей одним запросом
			 */
			$this->PluginMinimarket_Link_AddLinks($aObjectLocationGroup);
			$this->PluginMinimarket_Link_AddLinks($aObjectPaySystem);
			return true;
		}
		return false;
	}
}
?>