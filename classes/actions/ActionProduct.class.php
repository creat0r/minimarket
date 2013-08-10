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
		 * Загружаем в шаблон JS текстовки
		 */
		$this->Lang_AddLangJs(array(
								  'topic_photoset_photo_delete','topic_photoset_mark_as_preview','topic_photoset_photo_delete_confirm',
								  'plugin.minimarket.product_photoset_is_preview','topic_photoset_upload_choose'
							  ));
							  
        if ($this->User_IsAuthorization()) {
            $this->oUserCurrent = $this->User_GetUserCurrent();
        }
        if (!$this->oUserCurrent || !$this->oUserCurrent->isAdministrator()) {
            return Router::Location('error/404/');
        }
    }
	
	/**
	 * Регистрируем евенты
	 *
	 */
	protected function RegisterEvent() {
		$this->AddEvent('add','EventProductadd');
		$this->AddEvent('edit','EventProductedit');
		$this->AddEvent('delete','EventProductdelete');
		
		// фото
		$this->AddEvent('setimagedescription','EventSetPhotoDescription'); // Установка описания к фото
		$this->AddEvent('deleteimage','EventDeletePhoto'); // Удаление изображения
		$this->AddEvent('upload','EventUpload'); // Загрузка изображения
	}
	
	/**
	 * AJAX загрузка фоток
	 *
	 * @return unknown
	 */
	protected function EventUpload() {
		/**
		 * Устанавливаем формат Ajax ответа
		 * В зависимости от типа загрузчика устанавливается тип ответа
		 */
		if (getRequest('is_iframe')) {
			$this->Viewer_SetResponseAjax('jsonIframe', false);
		} else {
			$this->Viewer_SetResponseAjax('json');
		}
		/**
		 * Проверяем авторизован ли юзер
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
		// Если от сервера не пришёл id топика, то пытаемся определить временный код для нового топика. Если и его нет, то это ошибка
		if (!$iProductId) {
			$sTargetId = empty($_COOKIE['ls_photoset_target_tmp']) ? getRequestStr('ls_photoset_target_tmp') : $_COOKIE['ls_photoset_target_tmp'];
			if (!$sTargetId) {
				$this->Message_AddError($this->Lang_Get('system_error'), $this->Lang_Get('error'));
				return false;
			}
			$iCountPhotos = $this->PluginMinimarket_Product_getCountPhotosByTargetTmp($sTargetId);
		} else {
			/**
			 * Загрузка фото к уже существующему товару
			 */
			$oProduct = $this->PluginMinimarket_Product_getProductById($iProductId);
			if (!$oProduct) {
				$this->Message_AddError($this->Lang_Get('system_error'), $this->Lang_Get('error'));
				return false;
			}
			$iCountPhotos = $this->PluginMinimarket_Product_getCountPhotosByProductId($iProductId);
		}
		/**
		 * Максимальное количество фото в топике
		 */
		if ($iCountPhotos >= Config::Get('module.topic.photoset.count_photos_max')) {
			$this->Message_AddError($this->Lang_Get('topic_photoset_error_too_much_photos', array('MAX' => Config::Get('module.topic.photoset.count_photos_max'))), $this->Lang_Get('error'));
			return false;
		}
		/**
		 * Максимальный размер фото
		 */
		if (filesize($_FILES['Filedata']['tmp_name']) > Config::Get('module.topic.photoset.photo_max_size')*1024) {
			$this->Message_AddError($this->Lang_Get('topic_photoset_error_bad_filesize', array('MAX' => Config::Get('module.topic.photoset.photo_max_size'))), $this->Lang_Get('error'));
			return false;
		}
		/**
		 * Загружаем файл
		 */
		$sFile = $this->PluginMinimarket_Product_UploadProductPhoto($_FILES['Filedata']);
		if ($sFile) {
			/**
			 * Создаем фото
			 */
			$oPhoto = Engine::GetEntity('PluginMinimarket_Product_ProductPhoto');
			$oPhoto->setProductPhotoPath($sFile);
			if ($iProductId) {
				$oPhoto->setProductId($iProductId);
			} else {
				$oPhoto->setProductPhotoTargetTmp($sTargetId);
			}
			if ($oPhoto = $this->PluginMinimarket_Product_addProductPhoto($oPhoto)) {
				$this->Viewer_AssignAjax('file', $oPhoto->getProductPhotoWebPath('100crop'));
				$this->Viewer_AssignAjax('id', $oPhoto->getProductPhotoId());
				$this->Message_AddNotice($this->Lang_Get('topic_photoset_photo_added'), $this->Lang_Get('attention'));
			} else {
				$this->Message_AddError($this->Lang_Get('system_error'), $this->Lang_Get('error'));
			}
		} else {
			$this->Message_AddError($this->Lang_Get('system_error'), $this->Lang_Get('error'));
		}
	}
	
	/**
	 * AJAX удаление фото
	 *
	 */
	protected function EventDeletePhoto() {
		/**
		 * Устанавливаем формат Ajax ответа
		 */
		$this->Viewer_SetResponseAjax('json');
		/**
		 * Проверяем авторизован ли юзер
		 */
        if ($this->User_IsAuthorization()) {
            $this->oUserCurrent = $this->User_GetUserCurrent();
        }
        if (!$this->oUserCurrent || !$this->oUserCurrent->isAdministrator()) {
            return Router::Action('error');
        }
		/**
		 * Поиск фото по id
		 */
		$oPhoto = $this->PluginMinimarket_Product_getProductPhotoById(getRequestStr('id'));
		if ($oPhoto) {
			if ($oPhoto->getProductId()) {
				/**
				 * Проверяем права на топик
				 */
				if ($oProduct = $this->PluginMinimarket_Product_getProductById($oPhoto->getProductId())) {

                    $this->PluginMinimarket_Product_deleteProductPhoto($oPhoto);
                    /**
                     * Если удаляем главную фотку продукта, то её необходимо сменить
                     */
                    if ($oPhoto->getProductPhotoId() == $oProduct->getMainPhotoId()) {
                        $aPhotos = $this->PluginMinimarket_Product_getPhotosByProductId($oProduct->getId());
                        if(isset($aPhotos[0])) {
							$oProduct->setMainPhotoId($aPhotos[0]->getId());
						} else {
							$oProduct->setMainPhotoId(null);
						}
                    }
                    $this->PluginMinimarket_Product_UpdateProduct($oProduct);
                    $this->Message_AddNotice($this->Lang_Get('topic_photoset_photo_deleted'), $this->Lang_Get('attention'));
					
					return;
				}
			} else {
				$this->PluginMinimarket_Product_deleteProductPhoto($oPhoto);
				$this->Message_AddNotice($this->Lang_Get('topic_photoset_photo_deleted'), $this->Lang_Get('attention'));
				return;
			}
		}
		$this->Message_AddError($this->Lang_Get('system_error'), $this->Lang_Get('error'));
	}
	
	protected function EventSetPhotoDescription() {
		/**
		 * Устанавливаем формат Ajax ответа
		 */
		$this->Viewer_SetResponseAjax('json');
		/**
		 * Проверяем авторизован ли юзер
		 */
        if ($this->User_IsAuthorization()) {
            $this->oUserCurrent = $this->User_GetUserCurrent();
        }
        if (!$this->oUserCurrent || !$this->oUserCurrent->isAdministrator()) {
            return Router::Action('error');
        }
		/**
		 * Поиск фото по id
		 */
		$oPhoto = $this->PluginMinimarket_Product_getProductPhotoById(getRequestStr('id'));
		if ($oPhoto) {
			if ($oPhoto->getProductId()) {
				// проверяем товар на существование
				if ($oProduct = $this->PluginMinimarket_Product_getProductById($oPhoto->getProductId())) {
					$oPhoto->setProductPhotoDescription(htmlspecialchars(strip_tags(getRequestStr('text'))));
					$this->PluginMinimarket_Product_updateProductPhoto($oPhoto);
				}
			} else {
				$oPhoto->setProductPhotoDescription(htmlspecialchars(strip_tags(getRequestStr('text'))));
				$this->PluginMinimarket_Product_updateProductPhoto($oPhoto);
			}
		}
	}
	
	protected function EventProductdelete() {
		$this->Security_ValidateSendForm();
		
        if (!$oProduct = $this->PluginMinimarket_Product_GetProductById($this->GetParam(0))) {
            return parent::EventNotFound();
        }
		
        $this->PluginMinimarket_Product_deleteProduct($oProduct);
		
        Router::Location('catalog/');
	}
	
	protected function EventProductedit() {
		$sProductURL=$this->GetParam(0);
		if (!($oProduct=$this->PluginMinimarket_Product_getProductByURL($sProductURL))) {
			return parent::EventNotFound();
		}
		
		$this->Viewer_AddHtmlTitle($this->Lang_Get('plugin.minimarket.product_edit'));
		$this->Viewer_Assign('aBrands', $this->PluginMinimarket_Taxonomy_getTaxonomiesByType('brand'));
		$this->Viewer_Assign('aCategories', $this->PluginMinimarket_Taxonomy_GetTreeCategories());
		$this->Viewer_Assign('aAttributes', $this->PluginMinimarket_Taxonomy_getTaxonomiesByType('attribut'));
		$this->Viewer_Assign('aProperties', $this->PluginMinimarket_Taxonomy_getTaxonomiesByType('property'));
		$this->Viewer_Assign('aAttributesCategories', $this->PluginMinimarket_Taxonomy_getTaxonomiesByType('attributes_category'));

		$this->SetTemplateAction('add');
		
		/**
		 * Проверяем отправлена ли форма с данными(хотяб одна кнопка)
		 */
		if (isset($_REQUEST['submit_product_publish'])) {
			/**
			 * Обрабатываем отправку формы
			 */
			return $this->SubmitEdit($oProduct);
		} else {
			/**
			 * Заполняем поля формы для редактирования
			 * 
			 */
			$_REQUEST['product_id'] = $oProduct->getId();
			$_REQUEST['product_name'] = $oProduct->getName();
			$_REQUEST['product_manufacturer_code'] = $oProduct->getManufacturerCode();
			$_REQUEST['product_url'] = $oProduct->getURL();
			$_REQUEST['product_price'] = $oProduct->getPrice();
			$_REQUEST['product_brand'] = $oProduct->getBrand();
			$_REQUEST['product_category'] = $oProduct->getCategory();
			
			$aPropertiesIdByProduct = $this->PluginMinimarket_Product_getArrayProductPropertyIdByArrayProductId($oProduct->getId());
			if(isset($aPropertiesIdByProduct[$oProduct->getId()])) {
				$_REQUEST['product_properties'] = $aPropertiesIdByProduct[$oProduct->getId()];
				
				$aAttributesIdByPropertiesId = $this->PluginMinimarket_Taxonomy_GetArrayIdParentByArrayIdTaxonomy($aPropertiesIdByProduct[$oProduct->getId()]);
				// получим одномерный массив, состоящий только из ID атрибутов
				$aIdAttribut = array();
				foreach($aAttributesIdByPropertiesId as $iIdAttribut) {
					$aIdAttribut[] = $iIdAttribut[0];
				}
				$_REQUEST['product_attributes'] = $aIdAttribut;
			}
			
			$aCharacteristics = $this->PluginMinimarket_Product_GetArrayProductTaxonomyByArrayProductIdAndType($oProduct->getId(), 'characteristics');
			$aCharacteristics = $aCharacteristics[$oProduct->getId()];
			$aCharacteristicsName=array();
			foreach($aCharacteristics as $oProductTaxonomy) {
				$aCharacteristicsName[] = $oProductTaxonomy->getProductTaxonomyText();
			}
			$_REQUEST['product_characteristics'] = join(', ',$aCharacteristicsName);
			
			$aFeatures = $this->PluginMinimarket_Product_GetArrayProductTaxonomyByArrayProductIdAndType($oProduct->getId(), 'features');
			$aFeatures = $aFeatures[$oProduct->getId()];
			$aFeaturesName=array();
			foreach($aFeatures as $oProductTaxonomy) {
				$aFeaturesName[] = $oProductTaxonomy->getProductTaxonomyText();
			}
			$_REQUEST['product_features'] = join(', ',$aFeaturesName);

			$_REQUEST['product_main_photo'] = $oProduct->getMainPhotoId();
			$_REQUEST['product_text'] = $oProduct->getText();
        }
		$this->Viewer_Assign('aPhotos', $this->PluginMinimarket_Product_getPhotosByProductId($oProduct->getId()));
	}
	
	protected function EventProductadd() {
		$this->Viewer_Assign('aCategories', $this->PluginMinimarket_Taxonomy_GetTreeCategories());
		$this->Viewer_Assign('sMenuItemSelect', 'product');
        $this->Viewer_Assign('aAttributes', $this->PluginMinimarket_Taxonomy_getTaxonomiesByType('attribut'));
        $this->Viewer_Assign('aProperties', $this->PluginMinimarket_Taxonomy_getTaxonomiesByType('property'));
        $this->Viewer_Assign('aBrands', $this->PluginMinimarket_Taxonomy_getTaxonomiesByType('brand'));
		$this->Viewer_Assign('aAttributesCategories', $this->PluginMinimarket_Taxonomy_getTaxonomiesByType('attributes_category'));
						
		if (!is_numeric(getRequest('product_id'))) {
			$_REQUEST['product_id']='';
		}
		/**
		 * Если нет временного ключа для нового товара, то генерируем. если есть, то загружаем фото по этому ключу
		 */
		if (empty($_COOKIE['ls_photoset_target_tmp'])) {
			setcookie('ls_photoset_target_tmp',  func_generator(), time()+24*3600,Config::Get('sys.cookie.path'),Config::Get('sys.cookie.host'));
		} else {
			setcookie('ls_photoset_target_tmp', $_COOKIE['ls_photoset_target_tmp'], time()+24*3600,Config::Get('sys.cookie.path'),Config::Get('sys.cookie.host'));
			$this->Viewer_Assign('aPhotos', $this->PluginMinimarket_Product_getPhotosByTargetTmp($_COOKIE['ls_photoset_target_tmp']));
		}
		
		/**
		 * Обрабатываем отправку формы
		 */
		return $this->SubmitAdd();
	}
	
	protected function SubmitEdit($oProduct) {
		$sProductURLOld = $oProduct->getURL();
		$oProduct->_setValidateScenario('product');
		/**
		 * Заполняем поля для валидации
		 */
		$oProduct->setName(strip_tags(getRequestStr('product_name')));
		$oProduct->setManufacturerCode(getRequestStr('product_manufacturer_code'));
		$oProduct->setURL(getRequestStr('product_url'));
		$oProduct->setPrice(getRequestStr('product_price'));
		$oProduct->setBrand(getRequestStr('product_brand'));
		$oProduct->setCategory(getRequestStr('product_category'));
		
		// получим идентификаторы атрибутов и свойств
		$aIdAttributesAndProperties=getRequestPost('product_attribut_and_property');		
		if(!is_array($aIdAttributesAndProperties)) $aIdAttributesAndProperties=array($aIdAttributesAndProperties);
		$aIdAttributes=array();
		$aIdProperties=array();
		
		foreach($aIdAttributesAndProperties as $iIdAttribut=>$aAttributes) {
			if(is_array($aAttributes) && count($aAttributes > 0)) {
				$aIdAttributes[]=(int)$iIdAttribut;
				foreach($aAttributes as $iProperty) {
					$aIdProperties[]=(int)$iProperty;
				}
			}
		}
		
		$_REQUEST['product_attributes']=$aIdAttributes;
		$_REQUEST['product_properties']=$aIdProperties;
		$_REQUEST['product_attribut_and_property']=array_merge($aIdAttributes,$aIdProperties);
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
		 * Если уже существует продукт с таким URL
		 */
		if (($oProductByURL=$this->PluginMinimarket_Product_getProductByURL(getRequestStr('product_url'))) && $oProductByURL->getURL()!=$sProductURLOld) {
			$this->Message_AddErrorSingle($this->Lang_Get('plugin.minimarket.product_create_url_double_error'),$this->Lang_Get('error'));
			return false;
		}
		/*
		 * Если есть прикрепленные фото
		 */
		if($aPhotos = $this->PluginMinimarket_Product_getPhotosByProductId($oProduct->getId())) {
			if (!($oPhotoMain=$this->PluginMinimarket_Product_getProductPhotoById(getRequestStr('product_main_photo')) and $oPhotoMain->getProductId() == $oProduct->getId())) {
				$oPhotoMain=$aPhotos[0];
			}
			$oProduct->setMainPhotoId($oPhotoMain->getProductPhotoId());
		}
		/**
		 * Обновляем товар
		 */
		if ($this->PluginMinimarket_Product_UpdateProduct($oProduct)) {
			if (isset($aPhotos) && count($aPhotos)) {
				foreach($aPhotos as $oPhoto) {
					$oPhoto->setTargetTmp('clear');
					$oPhoto->setTopicId(null);
					$this->Topic_updateTopicPhoto($oPhoto);
				}
			}
			/**
			 * Удаляем временную куку
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
		 * Проверяем отправлена ли форма с данными
		 */
		if (!isPost('submit_product_publish')) {
			return false;
		}
				
		$oProduct=Engine::GetEntity('PluginMinimarket_ModuleProduct_EntityProduct');
		$oProduct->_setValidateScenario('product');
		/**
		 * Заполняем поля для валидации
		 */
		$oProduct->setName(strip_tags(getRequestStr('product_name')));
		$oProduct->setManufacturerCode(getRequestStr('product_manufacturer_code'));
		$oProduct->setURL(getRequestStr('product_url'));
		$oProduct->setPrice(getRequestStr('product_price'));
		$oProduct->setBrand(getRequestStr('product_brand'));
		$oProduct->setCategory(getRequestStr('product_category'));
		
		// получим идентификаторы атрибутов и свойств
		$aIdAttributesAndProperties=getRequestPost('product_attribut_and_property');		
		if(!is_array($aIdAttributesAndProperties)) $aIdAttributesAndProperties=array($aIdAttributesAndProperties);
		$aIdAttributes=array();
		$aIdProperties=array();
		foreach($aIdAttributesAndProperties as $iIdAttribut=>$aAttributes) {
			if($aAttributes) {
				$aIdAttributes[]=(int)$iIdAttribut;
				foreach($aAttributes as $iProperty) {
					$aIdProperties[]=(int)$iProperty;
				}
			}
		}
		
		$_REQUEST['product_attributes']=$aIdAttributes;
		$_REQUEST['product_properties']=$aIdProperties;
		$_REQUEST['product_attribut_and_property']=array_merge($aIdAttributes,$aIdProperties);
		
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
		 * Если уже существует продукт с таким URL
		 */
		if (false!==($oProductByURL=$this->PluginMinimarket_Product_getProductByURL($oProduct->getURL()))) {
			$this->Message_AddErrorSingle($this->Lang_Get('plugin.minimarket.product_create_url_double_error'),$this->Lang_Get('error'));
			return false;
		}
		/*
		 * Если есть прикрепленные фото
		 */
		if($sTargetTmp = $_COOKIE['ls_photoset_target_tmp']){
			if($aPhotos = $this->PluginMinimarket_Product_getPhotosByTargetTmp($sTargetTmp)){
				if (!($oPhotoMain=$this->PluginMinimarket_Product_getProductPhotoById(getRequestStr('product_main_photo')) and $oPhotoMain->getProductPhotoTargetTmp()==$sTargetTmp)) {
					$oPhotoMain=$aPhotos[0];
				}
				if($oPhotoMain){
					$oProduct->setMainPhotoId($oPhotoMain->getProductPhotoId());
				}
			}
		}
		/**
		 * Добавляем товар
		 */
		if ($this->PluginMinimarket_Product_addProduct($oProduct)) {
			/**
			 * Привязываем фото к id товара
			 * здесь нужно это делать одним запросом, а не перебором сущностей
			 */
			if (isset($aPhotos) && count($aPhotos)) {
				foreach($aPhotos as $oPhoto) {
					$oPhoto->setProductPhotoTargetTmp(null);
					$oPhoto->setProductId($oProduct->getId());
					$this->PluginMinimarket_Product_updateProductPhoto($oPhoto);
				}
			}
			/**
			 * Удаляем временную куку
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

		$bOk=true;
		/**
		 * Валидируем продукт
		 */
		if (!$oProduct->_Validate()) {
			$this->Message_AddError($oProduct->_getValidateError(),$this->Lang_Get('error'));
			$bOk=false;
		}

		return $bOk;
	}
}
?>