<?php

class PluginMinimarket_ModulePayment extends Module {	

	/**
	 * Состояния платежа
	 */
	const PAYMENT_STATUS_NEW  = 1;
	const PAYMENT_STATUS_SOLD = 2;
	
	/**
	 * Контроль доступа к платежу (контроль доступа осуществляется методом оплачиваемого объекта)
	 */
	const PAYMENT_ACCESS_INDEX   = 1; // Страница выбора системы оплаты
	const PAYMENT_ACCESS_INIT    = 2; // Страница оплаты выбранной системой оплаты

	/**
	 * Типы проверок оплачиваемого объекта
	 */
	const PAYMENT_OBJECT_PAYMENT_CHECK_MAKE_PAYMENT      = 1;
	const PAYMENT_OBJECT_PAYMENT_CHECK_SELECT_PAY_SYSTEM = 2;

	/**
	 * Типы уведомлений оплачиваемого объекта
	 */
	const PAYMENT_NOTICE_OBJECT_PAYMENT_OF_PAY_SYSTEM_SELECTED = 1;
	const PAYMENT_NOTICE_OBJECT_PAYMENT_OF_PAYMENT_PAYD        = 2;

	/**
	 * Ошибки создания объекта платежа
	 */
	const PAYMENT_ERROR_FAILED_SUM      = 1;
	const PAYMENT_ERROR_FAILED_CURRENCY = 2;
	const PAYMENT_ERROR_FAILED_MAKE     = 3;

	/**
	 * Ошибки систем оплаты
	 */
	const PAYMENT_ERROR_ROBOX_RESULT_NUMBER      = 1;
	const PAYMENT_ERROR_ROBOX_RESULT_SUM         = 2;
	const PAYMENT_ERROR_ROBOX_RESULT_STATUS      = 3;
	const PAYMENT_ERROR_ROBOX_RESULT_PASSWORD_2  = 4;
	const PAYMENT_ERROR_ROBOX_RESULT_SIG         = 5;
	const PAYMENT_ERROR_ROBOX_SUCCESS_NUMBER     = 6;
	const PAYMENT_ERROR_ROBOX_SUCCESS_SUM        = 7;
	const PAYMENT_ERROR_ROBOX_SUCCESS_STATUS     = 8;
	const PAYMENT_ERROR_ROBOX_SUCCESS_PASSWORD_1 = 9;
	const PAYMENT_ERROR_ROBOX_SUCCESS_SIG        = 10;
	const PAYMENT_ERROR_ROBOX_FAIL_NUMBER        = 11;
	const PAYMENT_ERROR_ROBOX_FAIL_SUM           = 12;
	const PAYMENT_ERROR_ROBOX_FAIL_STATUS        = 13;

	/**
	 * Ошибка, возникшая во время проверки оплачиваемого объекта
	 */
	const PAYMENT_ERROR_OBJECT_PAYMENT_CHECK = 1;

	protected $oMapper;

	/**
	 * Инициализация
	 */
	public function Init() {		
		$this->oMapper = Engine::GetMapper(__CLASS__);
	}

	/**
	 * Логирование ошибок возникающих при оплате
	 *
	 * @param int          $iNumberError    Номер ошибки
	 * @param unknown_type $var             Значение, из-за которого произошла ошибка
	 *
	 * @return int
	 */
	public function LogError($iNumberError, $var = null) {
		if (Config::Get('plugin.minimarket.module.payment.logs.error')) {
			$sOldName = $this->Logger_GetFileName();
			$this->Logger_SetFileName(Config::Get('plugin.minimarket.module.payment.logs.error'));
			$this->Logger_Error('Payment error: ' . $iNumberError . ' ' . var_export($var, true));
			$this->Logger_SetFileName($sOldName);
		}
		return $iNumberError;
	}

	/**
	 * Проверка оплачиваемого объекта
	 * 
	 * @param string $sObjectPaymentType    Тип оплачиваемого объекта
	 * @param int    $iObjectPaymentId      ID оплачиваемого объекта
	 * @param int    $iCheckType            Тип проверки объекта. Указывает, на какой стадии проверяется объект. Значение констант: PluginMinimarket_ModulePayment::PAYMENT_OBJECT_PAYMENT_CHECK_*
	 *
	 * @return bool
	 */
	public function CheckObjectPayment($sObjectPaymentType, $iObjectPaymentId, $iCheckType) {
		$sMethod = 'CheckObjectPayment' . ucfirst($sObjectPaymentType);
		if (method_exists($this, $sMethod)) {
			return $this->$sMethod($iObjectPaymentId, $iCheckType);
		}
		return false;
	}

	/**
	 * Уведомление оплачиваемого объекта о каком-либо событии
	 * 
	 * @param string $sObjectPaymentType    Тип оплачиваемого объекта
	 * @param int    $iObjectPaymentId      ID оплачиваемого объекта
	 * @param int    $iNoticeType           Тип уведомления. Значение констант: PluginMinimarket_ModulePayment::PAYMENT_OBJECT_PAYMENT_NOTICE_*
	 *
	 * @return bool
	 */
	public function NoticeObjectPayment($sObjectPaymentType, $iObjectPaymentId, $iNoticeType) {
		$sMethod = 'NoticeObjectPayment' . ucfirst($sObjectPaymentType);
		if (method_exists($this, $sMethod)) {
			return $this->$sMethod($iObjectPaymentId, $iNoticeType);
		}
	}
	
	/**
	 * Уведомление заказа о каком-либо событии
	 * 
	 * @param int    $iOrderId       ID заказа
	 * @param int    $iNoticeType    Тип уведомления. Значение констант: PluginMinimarket_ModulePayment::PAYMENT_OBJECT_PAYMENT_NOTICE_*
	 *
	 * @return bool
	 */
	public function NoticeObjectPaymentOrder($iOrderId, $iNoticeType) {
		if (false !== ($oOrder = $this->PluginMinimarket_Order_GetOrderById($iOrderId))) {
			switch ($iNoticeType) {
				case self::PAYMENT_NOTICE_OBJECT_PAYMENT_OF_PAY_SYSTEM_SELECTED:
					$oOrder->setStatus(PluginMinimarket_ModuleOrder::ORDER_STATUS_PAY_SYSTEM_SELECTED);
					$oOrder->setTimeSelectedPaySystem(time());
					$this->PluginMinimarket_Order_AddOrUpdateOrder($oOrder);
					/**
					 * Если выбранная система оплаты оказалась наличным расчетом -- происходит удаление идентификационой куки у пользователя
					 */
					if (
						false !== ($oPayment = $this->GetPaymentByIdObjectPaymentAndTypeObjectPayment($iOrderId, 'order'))
						&& false !== ($oPaySystem = $this->PluginMinimarket_Pay_GetPaySystemById($oPayment->getPaySystemId()))
						&& $oPaySystem->getKey() == 'cash'
					) {
						$this->PluginMinimarket_Order_DeleteCookieOrder();
					}
					break;
				case self::PAYMENT_NOTICE_OBJECT_PAYMENT_OF_PAYMENT_PAYD:
					$oOrder->setStatus(PluginMinimarket_ModuleOrder::ORDER_STATUS_PAYD);
					$oOrder->setTimePaymentSuccess(time());
					$this->PluginMinimarket_Order_AddOrUpdateOrder($oOrder);
					break;
			}
		}
	}	

	/**
	 * Создание платежа
	 * 
	 * @param string $sObjectPaymentType    Тип оплачиваемого объекта
	 * @param int    $iObjectPaymentId      ID оплачиваемого объекта
	 * @param float  $nSum                  Сумма платежа
	 * @param int    $iCurrencyId           ID валюты
	 * @param bool   $bRedirect             Делать ли редирект на страницу оплаты после успешного создания платежа
	 */
	public function MakePayment($sObjectPaymentType, $iObjectPaymentId, $nSum, $iCurrencyId, $bRedirect = true) {
		if (!is_float($nSum) and !is_numeric($nSum)) {
			$this->LogError(self::PAYMENT_ERROR_FAILED_SUM, $nSum);
			return false;
		}
		if (!$this->PluginMinimarket_Currency_GetCurrencyById($iCurrencyId)) {
			$this->LogError(self::PAYMENT_ERROR_FAILED_CURRENCY, $iCurrencyId);
			return false;
		}
		/**
		 * Проверка оплачиваемого объекта
		 */
		if (!$this->CheckObjectPayment($sObjectPaymentType, $iObjectPaymentId, self::PAYMENT_OBJECT_PAYMENT_CHECK_MAKE_PAYMENT)) {
			$this->LogError(self::PAYMENT_ERROR_OBJECT_PAYMENT_CHECK, array($sObjectPaymentType, $iObjectPaymentId, self::PAYMENT_OBJECT_PAYMENT_CHECK_MAKE_PAYMENT));
			return false;
		}
		/**
		 * Создание платежа
		 */
		$oPayment = Engine::GetEntity('PluginMinimarket_ModulePayment_EntityPayment');
		$oPayment->setSum($nSum);
		$oPayment->setCurrencyId($iCurrencyId);
		$oPayment->setTimeAdd(time());
		$oPayment->setIp(func_getIp());
		$oPayment->setStatus(self::PAYMENT_STATUS_NEW);
		$oPayment->setObjectPaymentId($iObjectPaymentId);
		$oPayment->setObjectPaymentType($sObjectPaymentType);
		if ($this->AddPayment($oPayment)) {
			if ($bRedirect) {
				Router::Location($this->GetPaymentUrl($oPayment));
			} else {
				return $oPayment;
			}
		}		
		$this->LogError(self::PAYMENT_ERROR_FAILED_MAKE, $oPayment);
		return false;
	}

	/**
	 * Возвращает полный URL на страницу выбора системы оплаты
	 *
	 * @param PluginMinimarket_ModulePayment_EntityPayment $oPayment    Объект платежа
	 *
	 * @return string
	 */
	public function GetPaymentUrl($oPayment) {
		return Router::GetPath('payment') . "{$oPayment->getId()}/";
	}

	/**
	 * Возвращает полный URL на страницу платежа
	 *
	 * @param PluginMinimarket_ModulePayment_EntityPayment $oPayment    Объект платежа
	 *
	 * @return string
	 */
	public function GetPaymentInitUrl($oPayment) {
		return Router::GetPath("payment/init/{$oPayment->getId()}");
	}

	/**
	 * Добавляет объект платежа
	 * 
	 * @param PluginMinimarket_ModulePayment_EntityPayment $oPayment    Объект платежа
	 *
	 * @return PluginMinimarket_ModulePayment_EntityPayment|bool
	 */
	public function AddPayment($oPayment) {
		if ($iId = $this->oMapper->AddPayment($oPayment)) {
			$oPayment->setId($iId);
			return $oPayment;
		}
		return false;
	}

	/**
	 * Возвращает объект платежа по ID
	 * 
	 * @param int $iId    ID платежа
	 *
	 * @return PluginMinimarket_ModulePayment_EntityPayment|bool
	 */
	public function GetPaymentById($iId) {
		return $this->oMapper->GetPaymentById($iId);
	}

	/**
	 * Возвращает список платежей по ID валюты
	 * 
	 * @param int $iCurrencyId    ID валюты
	 *
	 * @return array
	 */
	public function GetPaymentsByCurrencyId($iCurrencyId) {
		return $this->oMapper->GetPaymentsByCurrencyId($iCurrencyId);
	}

	/**
	 * Возвращает объект платежа по типу и ID оплачиваемого объекта 
	 * 
	 * @param int    $iObjectPaymentId      ID оплачиваемого объекта
	 * @param string $sObjectPaymentType    Тип оплачиваемого объекта
	 *
	 * @return PluginMinimarket_ModulePayment_EntityPayment|bool
	 */
	public function GetPaymentByIdObjectPaymentAndTypeObjectPayment($iObjectPaymentId, $sObjectPaymentType) {
		return $this->oMapper->GetPaymentByIdObjectPaymentAndTypeObjectPayment($iObjectPaymentId, $sObjectPaymentType);
	}

	/**
	 * Обновляет платеж
	 * 
	 * @param PluginMinimarket_ModulePayment_EntityPayment $oPayment    Объект платежа
	 * 
	 * @return bool
	 */
	public function UpdatePayment($oPayment) {
		return $this->oMapper->UpdatePayment($oPayment);
	}
	
	/**
	 * Проверка прав на доступ к платежу. Если метода нет, то считает платеж неразрешенным
	 * 
	 * @param PluginMinimarket_ModulePayment_EntityPayment $oPayment    Объект платежа
	 * @param init $sPaymentAccess                                      Контроль доступа к платежу. Значения констант: PluginMinimarket_ModulePayment::PAYMENT_ACCESS_*
	 *
	 * @return bool
	 */
	public function CheckAccessForPayment($oPayment, $sPaymentAccess) {
		if ($oPayment->getObjectPaymentType()) {
			$sMethod = 'CheckAccessForPaymentObjectPayment' . ucfirst($oPayment->getObjectPaymentType());
			if (method_exists($this, $sMethod)) {
				return $this->$sMethod($oPayment, $sPaymentAccess);
			}
		}
		return false;
	}
	
	/**
	 * Возвращает список доступных систем оплаты. Если метода нет, то вернет пустой список
	 * 
	 * @param PluginMinimarket_ModulePayment_EntityPayment $oPayment    Объект платежа
	 *
	 * @return array
	 */
	public function GetAvailablePaySystemsByPayment($oPayment) {
		if ($oPayment->getObjectPaymentType()) {
			$sMethod = 'GetAvailablePaySystemsByPaymentObjectPayment' . ucfirst($oPayment->getObjectPaymentType());
			if (method_exists($this, $sMethod)) {
				return $this->$sMethod($oPayment);
			}
		}
		return array();
	}

	/**
	 * Возвращает список доступных систем оплаты для оплаты заказа
	 * 
	 * @param PluginMinimarket_ModulePayment_EntityPayment $oPayment    Объект платежа
	 * 
	 * @return bool
	 */	
	protected function GetAvailablePaySystemsByPaymentObjectPaymentOrder(PluginMinimarket_ModulePayment_EntityPayment $oPayment) {	
		/**
		 * Получение заказа
		 */
		if (false !== ($oOrder = $this->PluginMinimarket_Order_GetOrderById($oPayment->getObjectPaymentId()))) {
			/**
			 * Возвращает доступные системы оплаты
			 */
			return $this->PluginMinimarket_Pay_GetAvailablePaySystemsByOrder($oOrder);
		}
		return array();
	}

	/**
	 * Проверка прав на доступ к платежу
	 * 
	 * @param PluginMinimarket_ModulePayment_EntityPayment $oPayment    Объект платежа
	 * @param init $sPaymentAccess                                      Контроль доступа к платежу. Значения констант: PluginMinimarket_ModulePayment::PAYMENT_ACCESS_*
	 * 
	 * @return bool
	 */
	protected function CheckAccessForPaymentObjectPaymentOrder(PluginMinimarket_ModulePayment_EntityPayment $oPayment, $sPaymentAccess) {
		switch ($sPaymentAccess) {
			/**
			 * Если запрос пришел со страницы выбора системы оплаты
			 */
			case self::PAYMENT_ACCESS_INDEX:
				/**
				 * Блокировка доступа к странице, если юзер не обладает идентификатором заказа или если статус заказа не соответствует запрашиваемому
				 */
				if (
					!($oOrder = $this->PluginMinimarket_Order_GetOrderByCookie())
					|| !$this->PluginMinimarket_Order_CheckStepOrder($oOrder, PluginMinimarket_ModuleOrder::ORDER_STATUS_DELIVERY_SELECTED)
				) {
					return false;
				}
				break;
			/**
			 * Если запрос пришел со страницы, инициализирующей процесс оплаты через ранее выбранную систему оплаты
			 */
			case self::PAYMENT_ACCESS_INIT:
				/**
				 * Блокировка доступа к странице, если идет обращение к несуществующему платежу или несуществующему заказу
				 * В этом месте можно ограничить доступ таким образом, что бы запрашиваемая страница была доступна любому юзеру в том случае, когда заказ оплачивается через наличный расчет
				 */
				if (
					!($oOrder = $this->PluginMinimarket_Order_GetOrderById($oPayment->getObjectPaymentId()))
					|| !$this->PluginMinimarket_Order_CheckStepOrder($oOrder, PluginMinimarket_ModuleOrder::ORDER_STATUS_PAY_SYSTEM_SELECTED)
				) {
					return false;
				}
				break;
		}
		/**
		 * Разрешение доступа к платежу
		 */
		return true;
	}

	/**
	 * Проверяет валидность заказа в момент формирования заказа
	 * 
	 * @param int $iOrderId      ID заказа
	 * @param int $iCheckType    Тип проверки объекта. Указывает, на какой стадии проверяется объект. Значение констант: PluginMinimarket_ModulePayment::PAYMENT_OBJECT_PAYMENT_CHECK_*
	 * 
	 * @return bool
	 */
	protected function CheckObjectPaymentOrder($iOrderId, $iCheckType) {
		if (in_array($iCheckType, array(self::PAYMENT_OBJECT_PAYMENT_CHECK_MAKE_PAYMENT, self::PAYMENT_OBJECT_PAYMENT_CHECK_SELECT_PAY_SYSTEM))) {
			/**
			 * Проверка, существует ли такой заказ
			 */
			if (!($oOrder = $this->PluginMinimarket_Order_GetOrderById($iOrderId))) return false;
			return true;
		}
		return false;
	}

	/**
	 * Платеж завершен успешно (выполняется по инициативе сервера платежной системы сразу после покупки)
	 * Именно в этом методе необходимо фиксировать факт оплаты
	 * Редиректы в данном методе не имеют смысла
	 * 
	 * @param PluginMinimarket_ModulePayment_EntityPayment $oPayment
	 */
	public function MakePaymentSuccess(PluginMinimarket_ModulePayment_EntityPayment $oPayment) {
		/**
		 * Обновление объекта платежа
		 */
		$oPayment->setStatus(self::PAYMENT_STATUS_SOLD);
		$oPayment->setTimeSold(time());
		$this->UpdatePayment($oPayment);
		/**
		 * Уведомление метода объекта оплаты об удачной проводке платежа
		 */
		$this->PluginMinimarket_Payment_NoticeObjectPayment(
			$oPayment->getObjectPaymentType(),
			$oPayment->getObjectPaymentId(),
			PluginMinimarket_ModulePayment::PAYMENT_NOTICE_OBJECT_PAYMENT_OF_PAYMENT_PAYD
		);
	}
}
?>