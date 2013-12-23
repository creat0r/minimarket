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

class PluginMinimarket_ActionPayment extends ActionPlugin {

    public function Init() {

    }

	protected function RegisterEvent() {
		$this->AddEventPreg('/^\d+$/i', 'EventIndex');
		$this->AddEvent('init', 'EventInit');
	}

	protected function EventInit() {
		if (
			/**
			 * Проверка платежа на существование
			 */
			!($oPayment = $this->PluginMinimarket_Payment_GetPaymentById($this->GetParam(0)))
			/**
			 * Проверка состояния платежа
			 */
			|| $oPayment->getStatus() != PluginMinimarket_ModulePayment::PAYMENT_STATUS_NEW
			/**
			 * Проверка доступа к платежу методом оплачиваемого объекта
			 */
			|| !$this->PluginMinimarket_Payment_CheckAccessForPayment($oPayment, PluginMinimarket_ModulePayment::PAYMENT_ACCESS_INIT)
		) {
			return parent::EventNotFound();
		}
		/**
		 * Инициализация запуска метода, выводящего в шаблон всю необходимую информацию для совершения платежа
		 */
		if (!$this->InitPayment($oPayment)) return parent::EventNotFound();
	}

	/**
	 * Производит инициализацию метода, который в свою очередь выводит страницу для совершения платежа через выбранную ранее платежную систему
	 * 
	 * @param PluginMinimarket_ModulePayment_EntityPayment $oPayment    Объект платежа
	 * 
	 * @return bool
	 */
	protected function InitPayment($oPayment) {
		if (false !== ($oPaySystem = $this->PluginMinimarket_Pay_GetPaySystemById($oPayment->getPaySystemId()))) {
			$sMethod = 'InitPayment' . ucfirst($oPaySystem->getKey());
			$this->SetTemplateAction("{$oPaySystem->getKey()}_{$oPayment->getObjectPaymentType()}");
			return $this->$sMethod($oPayment);
		}
		return false;
	}

	/**
	 * Страница оплаты счета через Наличные
	 * 
	 * @param PluginMinimarket_ModulePayment_EntityPayment $oPayment    Объект платежа
	 * 
	 * @return bool
	 */
	protected function InitPaymentCash($oPayment) {
		$this->Viewer_Assign('oPayment', $oPayment);
		$this->Viewer_Assign('noSidebar', true);
		return true;
	}

	protected function EventIndex() {
		if (
			/**
			 * Проверка платежа на существование
			 */
			!($oPayment = $this->PluginMinimarket_Payment_GetPaymentById($this->sCurrentEvent))
			/**
			 * Проверка состония платежа
			 */
			|| $oPayment->getStatus() != PluginMinimarket_ModulePayment::PAYMENT_STATUS_NEW
			/**
			 * Проверка доступа к платежу методом оплачиваемого объекта
			 */
			|| !$this->PluginMinimarket_Payment_CheckAccessForPayment($oPayment, PluginMinimarket_ModulePayment::PAYMENT_ACCESS_INDEX)
		) {
			return parent::EventNotFound();
		}
		/**
		 * Получение доступных систем оплаты
		 */
		$aPaySystem = $this->PluginMinimarket_Payment_GetAvailablePaySystemsByPayment($oPayment);
		if (!count($aPaySystem)) {
			$this->Message_AddErrorSingle($this->Lang_Get('plugin.minimarket.payment_available_pay_systems_error'), $this->Lang_Get('error'));
			return Router::Action('error');
		}
		if (isPost('submit') && getRequestStr('pay_system')) {
			/**
			 * Проверка пришедшего ID системы оплаты
			 */
			$bOK = false;
			foreach ($aPaySystem as $oPaySystem) {
				if ($oPaySystem->getId() == getRequestStr('pay_system')) {
					$bOK = true;
					break;
				}
			}
			/**
			 * Если такая система оплаты существует среди доступных
			 * и если проходит проверку оплачиваемый объект
			 */
			if (
				$bOK === true
				&& $this->PluginMinimarket_Payment_CheckObjectPayment(
					$oPayment->getObjectPaymentType(),
					$oPayment->getObjectPaymentId(),
					PluginMinimarket_ModulePayment::PAYMENT_OBJECT_PAYMENT_CHECK_SELECT_PAY_SYSTEM
				)
			) {
				/**
				 * Обновление платежа
				 */
				$oPayment->setPaySystemId(getRequestStr('pay_system'));
				$this->PluginMinimarket_Payment_UpdatePayment($oPayment);
				/**
				 * Уведомление метода объекта оплаты о том, что система оплаты выбрана
				 */
				$this->PluginMinimarket_Payment_NoticeObjectPayment(
					$oPayment->getObjectPaymentType(),
					$oPayment->getObjectPaymentId(),
					PluginMinimarket_ModulePayment::PAYMENT_NOTICE_OBJECT_PAYMENT_OF_PAY_SYSTEM_SELECTED
				);
				Router::Location($this->PluginMinimarket_Payment_GetPaymentInitUrl($oPayment));
			}
		}
		$this->Viewer_Assign('aPaySystem', $aPaySystem);
		$this->Viewer_Assign('noSidebar', true);
		$this->SetTemplateAction("index_{$oPayment->getObjectPaymentType()}");
	}
}
?>