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

class PluginMinimarket_ActionProduct extends ActionPlugin {

	protected $oUserCurrent = null;
	
    public function Init() {
		/**
		 * Загрузка в шаблон JS текстовки
		 */
		$this->Lang_AddLangJs(
			array(
				'plugin.minimarket.product_photoset_photo_delete',
				'plugin.minimarket.product_photoset_mark_as_preview',
				'plugin.minimarket.product_photoset_photo_delete_confirm',
				'plugin.minimarket.product_photoset_is_preview',
				'plugin.minimarket.product_photoset_upload_choose',
			)
		);
        if ($this->User_IsAuthorization()) {
            $this->oUserCurrent = $this->User_GetUserCurrent();
        }
        if (!$this->oUserCurrent || !$this->oUserCurrent->isAdministrator()) {
            return Router::Location('error/404/');
        }
    }
	
	/**
	 * Регистрация евентов
	 */
	protected function RegisterEvent() {
		$this->AddEvent('add','EventProductAdd');
		$this->AddEvent('edit','EventProductEdit');
		$this->AddEvent('delete','EventProductDelete');

		$this->AddEvent('setimagedescription','EventSetPhotoDescription'); // Установка описания к изображению
		$this->AddEvent('deleteimage','EventDeletePhoto');                 // Удаление изображения
		$this->AddEvent('upload','EventUpload');                           // Загрузка изображения
	}
	
	/**
	 * AJAX загрузка изображений
	 *
	 * @return unknown
	 */
	protected function EventUpload() {
		/**
		 * Установка формата Ajax ответа
		 * В зависимости от типа загрузчика устанавливается тип ответа
		 */
		if (getRequest('is_iframe')) {
			$this->Viewer_SetResponseAjax('jsonIframe', false);
		} else {
			$this->Viewer_SetResponseAjax('json');
		}
		/**
		 * Проверка, авторизован ли юзер
		 */
        if ($this->User_IsAuthorization()) {
            $this->oUserCurrent = $this->User_GetUserCurrent();
        }
        if (!$this->oUserCurrent || !$this->oUserCurrent->isAdministrator()) {
            return Router::Action('error');
        }
		/**
		 * Файл был загружен?
		 */
		if (!isset($_FILES['Filedata']['tmp_name'])) {
			$this->Message_AddError($this->Lang_Get('system_error'), $this->Lang_Get('error'));
			return false;
		}
		$iProductId = getRequestStr('product_id');
		$sTargetId = null;
		$iCountPhotos = 0;
		/**
		 * Попытка определить временный код для нового товара, если от сервера не пришел ID товара. Если не получилось, то это ошибка
		 */
		if (!$iProductId) {
			$sTargetId = empty($_COOKIE['ls_photoset_target_tmp']) ? getRequestStr('ls_photoset_target_tmp') : $_COOKIE['ls_photoset_target_tmp'];
			if (!$sTargetId) {
				$this->Message_AddError($this->Lang_Get('system_error'), $this->Lang_Get('error'));
				return false;
			}
			$iCountPhotos = $this->PluginMinimarket_Product_GetCountPhotosByTargetTmp($sTargetId);
		} else {
			/**
			 * Загрузка фото к уже существующему товару
			 */
			$oProduct = $this->PluginMinimarket_Product_GetProductById($iProductId);
			if (!$oProduct) {
				$this->Message_AddError($this->Lang_Get('system_error'), $this->Lang_Get('error'));
				return false;
			}
			$iCountPhotos = $this->PluginMinimarket_Product_GetCountPhotosByProductId($iProductId);
		}
		/**
		 * Максимальное количество изображений к товару
		 */
		if ($iCountPhotos >= Config::Get('plugin.minimarket.product.photoset.count_photos_max')) {
			$this->Message_AddError($this->Lang_Get('minimarket.product.product_photoset_error_too_much_photos', array('MAX' => Config::Get('plugin.minimarket.product.photoset.count_photos_max'))), $this->Lang_Get('error'));
			return false;
		}
		/**
		 * Максимальный размер изображения
		 */
		if (filesize($_FILES['Filedata']['tmp_name']) > Config::Get('plugin.minimarket.product.photoset.photo_max_size') * 1024) {
			$this->Message_AddError($this->Lang_Get('minimarket.product.product_photoset_error_bad_filesize', array('MAX' => Config::Get('plugin.minimarket.product.photoset.photo_max_size'))), $this->Lang_Get('error'));
			return false;
		}
		/**
		 * Загрузка файла
		 */
		$sFile = $this->PluginMinimarket_Product_UploadProductPhoto($_FILES['Filedata']);
		if ($sFile) {
			/**
			 * Создание изображения
			 */
			$oPhoto = Engine::GetEntity('PluginMinimarket_Product_ProductPhoto');
			$oPhoto->setPath($sFile);
			if ($iProductId) {
				$oPhoto->setProductId($iProductId);
			} else {
				$oPhoto->setTargetTmp($sTargetId);
			}
			if ($oPhoto = $this->PluginMinimarket_Product_AddProductPhoto($oPhoto)) {
				$this->Viewer_AssignAjax('file', $oPhoto->getProductPhotoWebPath('100crop'));
				$this->Viewer_AssignAjax('id', $oPhoto->getId());
				$this->Message_AddNotice($this->Lang_Get('plugin.minimarket.product_photoset_photo_added'), $this->Lang_Get('attention'));
			} else {
				$this->Message_AddError($this->Lang_Get('system_error'), $this->Lang_Get('error'));
			}
		} else {
			$this->Message_AddError($this->Lang_Get('system_error'), $this->Lang_Get('error'));
		}
	}
	
	/**
	 * AJAX удаление изображения
	 *
	 */
	protected function EventDeletePhoto() {
		/**
		 * Установка формата Ajax ответа
		 */
		$this->Viewer_SetResponseAjax('json');
		/**
		 * Проверка, авторизован ли юзер
		 */
        if ($this->User_IsAuthorization()) {
            $this->oUserCurrent = $this->User_GetUserCurrent();
        }
        if (!$this->oUserCurrent || !$this->oUserCurrent->isAdministrator()) {
            return Router::Action('error');
        }
		/**
		 * Поиск изображения по ID
		 */
		$oPhoto = $this->PluginMinimarket_Product_GetProductPhotoById(getRequestStr('id'));
		if ($oPhoto) {
			if ($oPhoto->getProductId()) {
				if ($oProduct = $this->PluginMinimarket_Product_GetProductById($oPhoto->getProductId())) {
                    $this->PluginMinimarket_Product_DeleteProductPhoto($oPhoto);
                    /**
                     * Если происходит удаление главного изображения товара, то его необходимо сменить
                     */
                    if ($oPhoto->getId() == $oProduct->getMainPhotoId()) {
                        $aPhotos = $this->PluginMinimarket_Product_GetPhotosByProductId($oProduct->getId());
                        if(isset($aPhotos[0])) {
							$oProduct->setMainPhotoId($aPhotos[0]->getId());
						} else {
							$oProduct->setMainPhotoId(null);
						}
                    }
                    $this->PluginMinimarket_Product_UpdateProduct($oProduct);
                    $this->Message_AddNotice($this->Lang_Get('plugin.minimarket.product_photoset_photo_deleted'), $this->Lang_Get('attention'));
					
					return;
				}
			} else {
				$this->PluginMinimarket_Product_DeleteProductPhoto($oPhoto);
				$this->Message_AddNotice($this->Lang_Get('plugin.minimarket.product_photoset_photo_deleted'), $this->Lang_Get('attention'));
				return;
			}
		}
		$this->Message_AddError($this->Lang_Get('system_error'), $this->Lang_Get('error'));
	}
	
	protected function EventSetPhotoDescription() {
		/**
		 * Установка формата Ajax ответа
		 */
		$this->Viewer_SetResponseAjax('json');
		/**
		 * Проверка, авторизован ли юзер
		 */
        if ($this->User_IsAuthorization()) {
            $this->oUserCurrent = $this->User_GetUserCurrent();
        }
        if (!$this->oUserCurrent || !$this->oUserCurrent->isAdministrator()) {
            return Router::Action('error');
        }
		/**
		 * Поиск изображения по ID
		 */
		$oPhoto = $this->PluginMinimarket_Product_GetProductPhotoById(getRequestStr('id'));
		if ($oPhoto) {
			if ($oPhoto->getProductId()) {
				/**
				 * Проверка товара на существование
				 */
				if ($oProduct = $this->PluginMinimarket_Product_GetProductById($oPhoto->getProductId())) {
					$oPhoto->setDescription(htmlspecialchars(strip_tags(getRequestStr('text'))));
					$this->PluginMinimarket_Product_UpdateProductPhoto($oPhoto);
				}
			} else {
				$oPhoto->setDescription(htmlspecialchars(strip_tags(getRequestStr('text'))));
				$this->PluginMinimarket_Product_UpdateProductPhoto($oPhoto);
			}
		}
	}
	
	protected function EventProductDelete() {
		$this->Security_ValidateSendForm();
		
        if (!$oProduct = $this->PluginMinimarket_Product_GetProductById($this->GetParam(0))) {
            return parent::EventNotFound();
        }
		
        $this->PluginMinimarket_Product_DeleteProduct($oProduct);
		
        Router::Location('catalog/');
	}
	
	protected function EventProductEdit() {
		$sProductURL=$this->GetParam(0);
		if (!($oProduct=$this->PluginMinimarket_Product_GetProductByURL($sProductURL))) {
			return parent::EventNotFound();
		}
		
		$this->Viewer_AddHtmlTitle($this->Lang_Get('plugin.minimarket.product_product_editing'));
		$this->Viewer_Assign('aBrands', $this->PluginMinimarket_Taxonomy_GetTaxonomiesByType('brand'));
		$this->Viewer_Assign('aCategories', $this->PluginMinimarket_Taxonomy_GetTreeTaxonomiesByType('category'));
		$this->Viewer_Assign('aAttributes', $this->PluginMinimarket_Taxonomy_GetTaxonomiesByType('attribut'));
		$this->Viewer_Assign('aProperties', $this->PluginMinimarket_Taxonomy_GetTaxonomiesByType('property'));
		$this->Viewer_Assign('aAttributesCategories', $this->PluginMinimarket_Taxonomy_GetTaxonomiesByType('attributes_category'));
        /**
         * Получение всего списка валют
         */
		$this->Viewer_Assign('aCurrency', $this->PluginMinimarket_Currency_GetAllCurrency());
        /**
         * Получение валюты "по умолчанию" из настроек магазина
         */
		$this->Viewer_Assign('oCurrencyDefault', $this->PluginMinimarket_Currency_GetCurrencyBySettings('default'));
        /*
         * Получение списка связей между атрибутами и заголовками атрибутов
         */
		$this->Viewer_Assign('aAttributesCategoryAttribut', $this->PluginMinimarket_Link_GetLinksByType('attributes_category_attribut'));
		$this->SetTemplateAction('add');
		
		/**
		 * Проверка, отправлена ли форма
		 */
		if (isset($_REQUEST['submit_product_publish'])) {
			/**
			 * Обработка отправки формы
			 */
			return $this->SubmitEdit($oProduct);
		} else {
			/**
			 * Заполнение полей формы для редактирования
			 */
			$_REQUEST['product_id'] = $oProduct->getId();
			$_REQUEST['product_name'] = $oProduct->getName();
			$_REQUEST['product_manufacturer_code'] = $oProduct->getManufacturerCode();
			$_REQUEST['product_url'] = $oProduct->getURL();
			$_REQUEST['product_price'] = $oProduct->getPrice() / Config::Get('plugin.minimarket.settings.factor');
			$_REQUEST['product_currency'] = $oProduct->getCurrency();
			$_REQUEST['product_weight'] = $oProduct->getWeight();
			$_REQUEST['product_show'] = $oProduct->getShow();
			$_REQUEST['product_in_stock'] = $oProduct->getInStock();
			$_REQUEST['product_brand'] = $oProduct->getBrand();
			$_REQUEST['product_category'] = $oProduct->getCategory();
			
			$aPropertiesIdByProduct = $this->PluginMinimarket_Link_GetLinksByParentsAndType($oProduct->getId(), 'product_property');
			if (isset($aPropertiesIdByProduct[$oProduct->getId()])) {
				$_REQUEST['product_properties'] = $aPropertiesIdByProduct[$oProduct->getId()];
				
				$aAttributesIdByPropertiesId = $this->PluginMinimarket_Taxonomy_GetIdParentsByIdTaxonomies($aPropertiesIdByProduct[$oProduct->getId()]);
				/**
				 * Составление списка ID атрибутов
				 */
				$aIdAttribut = array();
				foreach($aAttributesIdByPropertiesId as $iIdAttribut) {
					$aIdAttribut[] = $iIdAttribut[0];
				}
				$_REQUEST['product_attributes'] = $aIdAttribut;
			}
			$aCharacteristics = $this->PluginMinimarket_Product_GetProductTaxonomiesByArrayProductIdAndType($oProduct->getId(), 'characteristics');
			$aCharacteristics = $aCharacteristics[$oProduct->getId()];
			$aCharacteristicsName=array();
			foreach($aCharacteristics as $oProductTaxonomy) {
				$aCharacteristicsName[] = $oProductTaxonomy->getText();
			}
			$_REQUEST['product_characteristics'] = join(', ', $aCharacteristicsName);
			$aFeatures = $this->PluginMinimarket_Product_GetProductTaxonomiesByArrayProductIdAndType($oProduct->getId(), 'features');
			$aFeatures = $aFeatures[$oProduct->getId()];
			$aFeaturesName = array();
			foreach($aFeatures as $oProductTaxonomy) {
				$aFeaturesName[] = $oProductTaxonomy->getText();
			}
			$_REQUEST['product_features'] = join(', ',$aFeaturesName);
			$_REQUEST['product_main_photo'] = $oProduct->getMainPhotoId();
			$_REQUEST['product_text'] = $oProduct->getText();
        }
		$this->Viewer_Assign('aPhotos', $this->PluginMinimarket_Product_GetPhotosByProductId($oProduct->getId()));
	}
	
	protected function EventProductAdd() {
		$this->Viewer_Assign('aCategories', $this->PluginMinimarket_Taxonomy_GetTreeTaxonomiesByType('category'));
		$this->Viewer_Assign('sMenuItemSelect', 'product');
        $this->Viewer_Assign('aAttributes', $this->PluginMinimarket_Taxonomy_GetTaxonomiesByType('attribut'));
        $this->Viewer_Assign('aProperties', $this->PluginMinimarket_Taxonomy_GetTaxonomiesByType('property'));
        $this->Viewer_Assign('aBrands', $this->PluginMinimarket_Taxonomy_GetTaxonomiesByType('brand'));
		$this->Viewer_Assign('aAttributesCategories', $this->PluginMinimarket_Taxonomy_GetTaxonomiesByType('attributes_category'));
        /**
         * Получение всего списка валют
         */
		$this->Viewer_Assign('aCurrency', $this->PluginMinimarket_Currency_GetAllCurrency());
        /**
         * Получение валюты "по умолчанию" из настроек магазина
         */
		$this->Viewer_Assign('oCurrencyDefault', $this->PluginMinimarket_Currency_GetCurrencyBySettings('default'));
        /*
         * Получение списка связей между атрибутами и заголовками атрибутов
         */
		$this->Viewer_Assign('aAttributesCategoryAttribut', $this->PluginMinimarket_Link_GetLinksByType('attributes_category_attribut'));
						
		if (!is_numeric(getRequest('product_id'))) {
			$_REQUEST['product_id'] = '';
		}
		/**
		 * Генерация ключа, если нет временного для нового товара. Если есть, то происходит загрузка изображения по этому ключу
		 */
		if (empty($_COOKIE['ls_photoset_target_tmp'])) {
			setcookie('ls_photoset_target_tmp', func_generator(), time() + 24 * 3600, Config::Get('sys.cookie.path'), Config::Get('sys.cookie.host'));
		} else {
			setcookie('ls_photoset_target_tmp', $_COOKIE['ls_photoset_target_tmp'], time() + 24 * 3600, Config::Get('sys.cookie.path'), Config::Get('sys.cookie.host'));
			$this->Viewer_Assign('aPhotos', $this->PluginMinimarket_Product_GetPhotosByTargetTmp($_COOKIE['ls_photoset_target_tmp']));
		}
		/**
		 * Обработка отправки формы
		 */
		return $this->SubmitAdd();
	}
	
	protected function SubmitEdit($oProduct) {
		$sProductURLOld = $oProduct->getURL();
		$oProduct->_setValidateScenario('product');
		/**
		 * Заполнение полей для валидации
		 */
		$oProduct->setName(strip_tags(getRequestStr('product_name')));
		$oProduct->setManufacturerCode(getRequestStr('product_manufacturer_code'));
		$oProduct->setURL(getRequestStr('product_url'));
		$oProduct->setPrice(getRequestStr('product_price'));
		$oProduct->setCurrency(getRequestStr('product_currency'));
		$oProduct->setWeight(getRequestStr('product_weight'));
		$oProduct->setShow(getRequestStr('product_show'));
		$oProduct->setInStock(getRequestStr('product_in_stock'));	
		$oProduct->setBrand(getRequestStr('product_brand'));
		$oProduct->setCategory(getRequestStr('product_category'));
		/**
		 * Получение идентификаторов атрибутов и свойств
		 */
		$aIdAttributesAndProperties = getRequestPost('product_attribut_and_property');		
		if (!is_array($aIdAttributesAndProperties)) $aIdAttributesAndProperties = array($aIdAttributesAndProperties);
		$aIdAttributes = array();
		$aIdProperties = array();
		foreach($aIdAttributesAndProperties as $iIdAttribut => $aAttributes) {
			if (is_array($aAttributes) && count($aAttributes > 0)) {
				$aIdAttributes[] = (int)$iIdAttribut;
				foreach ($aAttributes as $iProperty) {
					$aIdProperties[] = (int)$iProperty;
				}
			}
		}
		$_REQUEST['product_attributes'] = $aIdAttributes;
		$_REQUEST['product_properties'] = $aIdProperties;
		$_REQUEST['product_attribut_and_property'] = array_merge($aIdAttributes, $aIdProperties);
		$oProduct->setAttributAndProperty($_REQUEST['product_attribut_and_property']);
		$oProduct->setProperties($aIdProperties);
		$oProduct->setCharacteristics(getRequestStr('product_characteristics'));
		$oProduct->setFeatures(getRequestStr('product_features'));
		$oProduct->setText(getRequestStr('product_text'));
		/**
		 * Проверка корректности полей формы
		 */
		if (!$this->checkProductFields($oProduct)) {
			return false;
		}
		/**
		 * Умножение цены на коэффициент для более удобного хранения в БД
		 */
		$oProduct->setPrice(getRequestStr('product_price') ? getRequestStr('product_price') * Config::Get('plugin.minimarket.settings.factor'): 0);
		/**
		 * Если уже существует товар с таким URL
		 */
		if (($oProductByURL = $this->PluginMinimarket_Product_GetProductByURL(getRequestStr('product_url'))) && $oProductByURL->getURL() != $sProductURLOld) {
			$this->Message_AddErrorSingle($this->Lang_Get('plugin.minimarket.product_adding_url_double_error'), $this->Lang_Get('error'));
			return false;
		}
		/*
		 * Если есть прикрепленные изображения
		 */
		if ($aPhotos = $this->PluginMinimarket_Product_GetPhotosByProductId($oProduct->getId())) {
			if (!($oPhotoMain = $this->PluginMinimarket_Product_GetProductPhotoById(getRequestStr('product_main_photo')) and $oPhotoMain->getProductId() == $oProduct->getId())) {
				$oPhotoMain = $aPhotos[0];
			}
			$oProduct->setMainPhotoId($oPhotoMain->getId());
		}
		/**
		 * Обновление товара
		 */
		if ($this->PluginMinimarket_Product_UpdateProduct($oProduct)) {
			if (isset($aPhotos) && count($aPhotos)) {
				foreach ($aPhotos as $oPhoto) {
					$oPhoto->setTargetTmp(null);
					$oPhoto->setProductId($oProduct->getId());
					$this->PluginMinimarket_Product_UpdateProductPhoto($oPhoto);
				}
			}
			/**
			 * Удаление временной куки
			 */
			setcookie('ls_photoset_target_tmp', null);
			Router::Location($oProduct->getWebPathBuilder());
		} else {
			$this->Message_AddErrorSingle($this->Lang_Get('system_error'));
			return Router::Action('error');
		}
	}
	
	protected function SubmitAdd() {
		/**
		 * Проверка, отправлена ли форма с данными
		 */
		if (!isPost('submit_product_publish')) {
			return false;
		}
		$oProduct = Engine::GetEntity('PluginMinimarket_ModuleProduct_EntityProduct');
		$oProduct->_setValidateScenario('product');
		/**
		 * Заполнение поля для валидации
		 */
		$oProduct->setName(strip_tags(getRequestStr('product_name')));
		$oProduct->setManufacturerCode(getRequestStr('product_manufacturer_code'));
		$oProduct->setURL(getRequestStr('product_url'));
		$oProduct->setPrice(getRequestStr('product_price'));
		$oProduct->setCurrency(getRequestStr('product_currency'));
		$oProduct->setWeight(getRequestStr('product_weight'));
		$oProduct->setShow(getRequestStr('product_show'));
		$oProduct->setInStock(getRequestStr('product_in_stock'));
		$oProduct->setBrand(getRequestStr('product_brand'));
		$oProduct->setCategory(getRequestStr('product_category'));
		/**
		 * Получение идентификаторов атрибутов и свойств
		 */
		$aIdAttributesAndProperties = getRequestPost('product_attribut_and_property');
		if (!is_array($aIdAttributesAndProperties)) $aIdAttributesAndProperties = array($aIdAttributesAndProperties);
		$aIdAttributes = array();
		$aIdProperties = array();
		foreach ($aIdAttributesAndProperties as $iIdAttribut=>$aAttributes) {
			if ($aAttributes) {
				$aIdAttributes[] = (int)$iIdAttribut;
				foreach ($aAttributes as $iProperty) {
					$aIdProperties[] = (int)$iProperty;
				}
			}
		}
		$_REQUEST['product_attributes'] = $aIdAttributes;
		$_REQUEST['product_properties'] = $aIdProperties;
		$_REQUEST['product_attribut_and_property'] = array_merge($aIdAttributes,$aIdProperties);
		$oProduct->setProperties($aIdProperties);
		$oProduct->setCharacteristics(getRequestStr('product_characteristics'));
		$oProduct->setFeatures(getRequestStr('product_features'));
		$oProduct->setText(getRequestStr('product_text'));
		/**
		 * Проверка корректности полей формы
		 */
		if (!$this->checkProductFields($oProduct)) {
			return false;
		}
		/**
		 * Умножение цены на коэффициент для более удобного хранения в БД
		 */
		$oProduct->setPrice(getRequestStr('product_price') ? getRequestStr('product_price') * Config::Get('plugin.minimarket.settings.factor'): 0);
		/**
		 * Если уже существует товар с таким URL
		 */
		if (false !== ($oProductByURL = $this->PluginMinimarket_Product_GetProductByURL($oProduct->getURL()))) {
			$this->Message_AddErrorSingle($this->Lang_Get('plugin.minimarket.product_adding_url_double_error'), $this->Lang_Get('error'));
			return false;
		}
		/*
		 * Если есть прикрепленные изображения
		 */
		if ($sTargetTmp = $_COOKIE['ls_photoset_target_tmp']){
			if ($aPhotos = $this->PluginMinimarket_Product_GetPhotosByTargetTmp($sTargetTmp)){
				if (!($oPhotoMain = $this->PluginMinimarket_Product_GetProductPhotoById(getRequestStr('product_main_photo')) and $oPhotoMain->getTargetTmp() == $sTargetTmp)) {
					$oPhotoMain = $aPhotos[0];
				}
				if ($oPhotoMain){
					$oProduct->setMainPhotoId($oPhotoMain->getId());
				}
			}
		}
		/**
		 * Добавление товара
		 */
		if ($this->PluginMinimarket_Product_AddProduct($oProduct)) {
			/**
			 * Привязка изображения к ID товара
			 * здесь нужно это делать одним запросом, а не перебором сущностей
			 */
			if (isset($aPhotos) && count($aPhotos)) {
				foreach($aPhotos as $oPhoto) {
					$oPhoto->setTmp(null);
					$oPhoto->setProductId($oProduct->getId());
					$this->PluginMinimarket_Product_UpdateProductPhoto($oPhoto);
				}
			}
			/**
			 * Удаление временной куки
			 */
			setcookie('ls_photoset_target_tmp', null);
			Router::Location($oProduct->getWebPathBuilder());
		} else {
			$this->Message_AddErrorSingle($this->Lang_Get('system_error'));
			return Router::Action('error');
		}
	}
	/**
	 * Проверка полей формы
	 *
	 * @return bool
	 */
	protected function checkProductFields($oProduct) {
		$this->Security_ValidateSendForm();
		$bOk = true;
		/**
		 * Валидация товара
		 */
		if (!$oProduct->_Validate()) {
			$this->Message_AddError($oProduct->_getValidateError(), $this->Lang_Get('error'));
			$bOk = false;
		}
		return $bOk;
	}
}
?>