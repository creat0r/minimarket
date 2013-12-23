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

class PluginMinimarket_ModuleCurrency extends Module {

	protected $oMapper;
	
	/**
	 * Инициализация модуля
	 */
	public function Init() {
		$this->oMapper = Engine::GetMapper(__CLASS__);
	}
	
    /**
     * Возвращает количество записей по фильтру
     *
     * @param array $aFilter    Фильтр выборки
     *
     * @return int|bool
     */
    public function GetCountCurrencyByFilter($aFilter) {
        if ($iResult = $this->oMapper->GetCountCurrencyByFilter($aFilter)) {
            return $iResult;
        }
        return false;
    }

    /**
     * Создание валюты
     *
     * @param PluginMinimarket_ModuleCurrency_EntityCurrency $oCurrency    Объект валюты
     *
     * @return int
     */
	public function AddCurrency(PluginMinimarket_ModuleCurrency_EntityCurrency $oCurrency) {
		return $this->oMapper->AddCurrency($oCurrency);
	}

    /**
     * Возвращает объект валюты по ключу
     *
     * @param string $sKey    Ключ валюты
     *
     * @return PluginMinimarket_ModuleCurrency_EntityCurrency|bool
     */
	public function GetCurrencyByKey($sKey) {
		return $this->oMapper->GetCurrencyByKey($sKey);
	}

    /**
     * Возвращает валюту (объект), установленную в настройках сайта
     *
     * @param string $sType    "Тип" валюты: default либо cart
     *
     * @return PluginMinimarket_ModuleCurrency_EntityCurrency
     */
	public function GetCurrencyBySettings($sType) {
		/**
		 * Попытка получить объект валюты по идентификатору в из раздела настроек
		 */
		$aDataStorage = $this->PluginMinimarket_Storage_GetStorage('minimarket_settings_base_');
		return $this->GetCurrencyByKey(
			$sType == 'default'
				? $aDataStorage['minimarket_settings_base_default_currency']
				: $aDataStorage['minimarket_settings_base_cart_currency']
		);
	}

    /**
     * Возвращает объект валюты по ID
     *
     * @param string $iId    ID валюты
     *
     * @return PluginMinimarket_ModuleCurrency_EntityCurrency|bool
     */
	public function GetCurrencyById($iId) {
		return $this->oMapper->GetCurrencyById($iId);
	}

    /**
     * Обновляет валюту
     *
     * @param PluginMinimarket_ModuleCurrency_EntityCurrency $oCurrency    Объект валюты
     *
     * @return bool
     */
	public function UpdateCurrency(PluginMinimarket_ModuleCurrency_EntityCurrency $oCurrency) {
        if ($this->oMapper->UpdateCurrency($oCurrency)) {
            return true;
        }
        return false;
	}

    /**
     * Возвращает все валюты списком
     *
     * @return array
     */
	public function GetAllCurrency() {
		return $this->oMapper->GetAllCurrency();
	}

    /**
     * Удаление валюты
     *
     * @param int $iId    ID валюты
     *
     * @return bool
     */
	public function DeleteCurrency($iId) {
		return $this->oMapper->DeleteCurrency($iId);
	}
	

    /**
     * Форматирование цены (для вывода в шаблон либо для математических операций) относительно формата валюты
     *
     * @param int       $nSum              Цена товара, сумма платежа и проч.
     * @param int       $iDecimalPlaces    Количество знаков после запятой
     * @param string    $sFormat           Формат вывода суммы в шаблон
     * @param bool      $bMaths            Если true, возвращает в формате для математических операций
     *
     * @return string
     */
	public function GetSumByFormat($nSum, $iDecimalPlaces, $sFormat = null, $bMaths = null) {
		$nSum *= pow(10, $iDecimalPlaces);
		$nSum = (int)$nSum;
		$nSum /= pow(10, $iDecimalPlaces);
		if ($bMaths) return number_format($nSum, $iDecimalPlaces, '.', '');
		return str_replace('#', number_format($nSum, $iDecimalPlaces, ',', ' '), $sFormat);
	}
}
?>