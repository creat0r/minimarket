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

class PluginMinimarket_ModuleProduct extends Module {

	protected $oMapper;
	
	/**
	 * Инициализация модуля
	 */
	public function Init() {
		$this->oMapper = Engine::GetMapper(__CLASS__);
	}
	
    /**
     * Добавление товара
     *
     * @param PluginMinimarket_ModuleProduct_EntityProduct $oProduct    Объект товара
     *
     * @return PluginMinimarket_ModuleProduct_EntityProduct|bool
     */
	public function AddProduct(PluginMinimarket_ModuleProduct_EntityProduct $oProduct) {
        if ($iId = $this->oMapper->AddProduct($oProduct)) {
			$oProduct->setId($iId);
			/**
			 * Добавление характеристик товара
			 */
			$aCharacteristics = explode(',', $oProduct->getCharacteristics());
			foreach($aCharacteristics as $sCharacteristics) {
				$oProductTaxonomy = Engine::GetEntity('PluginMinimarket_ModuleProduct_EntityProductTaxonomy');
				$oProductTaxonomy->setProductId($oProduct->getId());
				$oProductTaxonomy->setText($sCharacteristics);
				$oProductTaxonomy->setType('characteristics');
				$this->PluginMinimarket_Product_AddProductTaxonomy($oProductTaxonomy);
			}
			/**
			 * Добавление особенностей товара
			 */
			$aFeatures = explode(',', $oProduct->getFeatures());
			foreach($aFeatures as $sFeatures) {
				$oProductTaxonomy = Engine::GetEntity('PluginMinimarket_ModuleProduct_EntityProductTaxonomy');
				$oProductTaxonomy->setProductId($oProduct->getId());
				$oProductTaxonomy->setText($sFeatures);
				$oProductTaxonomy->setType('features');
				$this->PluginMinimarket_Product_AddProductTaxonomy($oProductTaxonomy);
			}
			/**
			 * Создание связей между товаром и свойствами
			 */
			$aPropertiesId = $oProduct->getProperties();
			$aLink = array();
			foreach($aPropertiesId as $iPropertyId) {
				$oLink = Engine::GetEntity('PluginMinimarket_ModuleLink_EntityLink');
				$oLink->setObjectId((int)$iPropertyId);
				$oLink->setParentId($oProduct->getId());
				$oLink->setObjectType('product_property');
				$aLink[] = $oLink;
			}
			$this->PluginMinimarket_Link_AddLinks($aLink);
            return $oProduct;
        }
        return false;
	}
	
    /**
     * Добавление таксономии товара
     *
     * @param PluginMinimarket_ModuleProduct_EntityProductTaxonomy $oProductTaxonomy    Объект таксономии товара
     *
     * @return PluginMinimarket_ModuleProduct_EntityProductTaxonomy|bool
     */
	public function AddProductTaxonomy(PluginMinimarket_ModuleProduct_EntityProductTaxonomy $oProductTaxonomy) {
        if ($sId = $this->oMapper->AddProductTaxonomy($oProductTaxonomy)) {
            $oProductTaxonomy->setId($sId);
            return $oProductTaxonomy;
        }
        return false;
	}

    /**
     * Удаляет товара
     * Удалятся всё параметры по товару (характеристики, особенности, краткие характеристики)
     *
     * @param PluginMinimarket_ModuleProduct_EntityProduct $oProduct    Объект товара
     */
	public function DeleteProduct(PluginMinimarket_ModuleProduct_EntityProduct $oProduct) {
        /**
         * Если товар успешно удален, производится удаление связанных данных
         */
        if ($bResult = $this->oMapper->DeleteProduct($oProduct->getId())) {
			/**
			 * Удаление характеристик и особенностей товара
			 */
			$this->PluginMinimarket_Product_DeleteProductTaxonomyByProductIdAndType($oProduct->getId(), 'characteristics');
			$this->PluginMinimarket_Product_DeleteProductTaxonomyByProductIdAndType($oProduct->getId(), 'features');
			/**
			 * Удаление связей товара с свойствами
			 */
			$this->PluginMinimarket_Link_DeleteLinkByParentAndType($oProduct->getId(), 'product_property');
			/**
			 * Удаление фото
			 */
			if ($aPhotos = $this->GetPhotosByProductId($oProduct->getId())) {
				foreach ($aPhotos as $oPhoto) {
					$this->DeleteProductPhoto($oPhoto);
				}
			}
        }
	}

    /**
     * Обновление товара
     *
     * @param PluginMinimarket_ModuleProduct_EntityProduct $oProduct    Объект товара
     * 
     * return  bool
     */
	public function UpdateProduct(PluginMinimarket_ModuleProduct_EntityProduct $oProduct) {
		if ($this->oMapper->UpdateProduct($oProduct)) {
			/**
			 * Удаление старых характеристик и особенностей товара
			 */
			$this->PluginMinimarket_Product_DeleteProductTaxonomyByProductIdAndType($oProduct->getId(), 'characteristics');
			$this->PluginMinimarket_Product_DeleteProductTaxonomyByProductIdAndType($oProduct->getId(), 'features');
			/**
			 * Удаление связей между товаром и свойствами
			 */
			$this->PluginMinimarket_Link_DeleteLinkByParentAndType($oProduct->getId(), 'product_property');
			/**
			 * Добавление характеристик товара
			 */
			$aCharacteristics = explode(',', $oProduct->getCharacteristics());
			foreach($aCharacteristics as $sCharacteristics) {
				$oProductTaxonomy = Engine::GetEntity('PluginMinimarket_ModuleProduct_EntityProductTaxonomy');
				$oProductTaxonomy->setProductId($oProduct->getId());
				$oProductTaxonomy->setText($sCharacteristics);
				$oProductTaxonomy->setType('characteristics');
				$this->PluginMinimarket_Product_AddProductTaxonomy($oProductTaxonomy);
			}
			/**
			 * Добавление особенностей товара
			 */
			$aFeatures = explode(',', $oProduct->getFeatures());
			foreach($aFeatures as $sFeatures) {
				$oProductTaxonomy = Engine::GetEntity('PluginMinimarket_ModuleProduct_EntityProductTaxonomy');
				$oProductTaxonomy->setProductId($oProduct->getId());
				$oProductTaxonomy->setText($sFeatures);
				$oProductTaxonomy->setType('features');
				$this->PluginMinimarket_Product_AddProductTaxonomy($oProductTaxonomy);
			}
			/**
			 * Создание связей между товаром и его свойствами
			 */
			$aPropertiesId = $oProduct->getProperties();
			if(is_array($aPropertiesId) && count($aPropertiesId) > 0) {
				$aLink = array();
				foreach($aPropertiesId as $iPropertyId) {
					$oLink = Engine::GetEntity('PluginMinimarket_ModuleLink_EntityLink');
					$oLink->setObjectId((int)$iPropertyId);
					$oLink->setParentId($oProduct->getId());
					$oLink->setObjectType('product_property');
					$aLink[] = $oLink;
				}
				$this->PluginMinimarket_Link_AddLinks($aLink);
			}
			return true;
		}
		return false;
	}

    /**
     * Удаление таксономии товара по ID товара и типу таксономии
     *
     * @param int    $iId      ID товара
     * @param string $sType    Тип таксономии
     * 
     * return  bool
     */
	public function DeleteProductTaxonomyByProductIdAndType($iId, $sType) {
        if ($bResult = $this->oMapper->DeleteProductTaxonomyByProductIdAndType($iId, $sType)) {
			return true;
        }
        return false;
	}

    /**
     * Возвращает "голый" товар по ID
     *
     * @param int $iId    ID товара
     * 
     * return  PluginMinimarket_Product_Product|bool
     */
	public function GetProductById($iId) {
        return $this->oMapper->GetProductById($iId);
	}

    /**
     * Возвращает "голый" товар по УРЛ
     *
     * @param int $sURL    УРЛ товара
     * 
     * return  PluginMinimarket_Product_Product|bool
     */
	public function GetProductByURL($sURL) {
        return $this->oMapper->GetProductByURL($sURL);
	}

    /**
     * Возвращает "голые" товары по списку ID товаров
     *
     * @param array $aProductId    Список ID товаров
     * 
     * return  array
     */
	public function GetProductsByArrayId($aProductId) {
        return $this->oMapper->GetProductsByArrayId($aProductId);
	}

    /**
     * Возвращает список таксономий продукта по типу таксономии
     *
     * @param string $sType    Тип таксономий
     *
     * @return array
     */
	public function GetProductTaxonomiesByType($sType) {
        return $this->oMapper->GetProductTaxonomiesByType($sType);
	}
	
    /**
     * Возвращает список таксономий продукта по списку ID продуктов и типу
     *
     * @param array  $aProductId    Список ID продуктов
     * @param string $sType         Тип таксономий
     *
     * @return array
     */
	public function GetProductTaxonomiesByArrayProductIdAndType($aProductId, $sType) {
		if(!is_array($aProductId)) $aProductId = array($aProductId);
        return $this->oMapper->GetProductTaxonomiesByArrayProductIdAndType($aProductId, $sType);
	}
	
    /**
     * Возвращает список товаров по фильтру
     *
     * @param array $aFilter    Фильтр
     * @param int   $iPage       Номер страницы
     * @param int   $iPerPage    Количество элементов на страницу
     *
     * @return array('collection'=>array,'count'=>int)
     */
    public function GetProductsByFilter($aFilter, $iPage = 1, $iPerPage = 10) {
        if (!is_numeric($iPage) || $iPage <= 0) {
            $iPage = 1;
        }
		$data = array(
			'collection' => $this->oMapper->GetProducts($aFilter, $iCount, $iPage, $iPerPage),
			'count'      => $iCount
		);
        $data['collection'] = $this->GetProductsAdditionalData($data['collection']);
        return $data;
    }

    /**
     * Возвращает дополнительные данные (объекты) для товаров по их ID
     *
     * @param array      $aProductId      Список ID товаров
     * @param array|null $aAllowData      Список типов дополнительных данных, которые нужно подключать к товарам
     * @param array|null $aCartObjects    Список количества товара в корзине, где ключ списка -- ID товара
     *
     * @return array
     */
	public function GetProductsAdditionalData($aProductId, $aAllowData = null, $aCartObjects = null) {
		if (!is_array($aAllowData)) {
			$aAllowData = array($aAllowData);
		}
		func_array_simpleflip($aAllowData);
		if (is_null($aCartObjects)) {
			$aCartObjects = array();
		}
        if (!is_array($aProductId)) {
            $aProductId = array($aProductId);
        }
        /**
         * Получение "голых" товаров
         */
        $aProduct = $this->GetProductsByArrayId($aProductId);
        /**
         * Получение превью
         */
		$aPhotosId=array();
		foreach($aProduct as $oProduct) {
			$aPhotosId[] = $oProduct->getMainPhotoId();
		}
		$aPhotos = $this->PluginMinimarket_Product_GetProductPhotosByArrayId($aPhotosId);
        /**
         * Получение кратких характеристик
         */
		$aProductTaxonomies = $this->PluginMinimarket_Product_GetProductTaxonomiesByArrayProductIdAndType($aProductId, 'characteristics');
        /**
         * Получение массива УРЛ-ов к товарам
         */
		$aWebPathToProduct = $this->PluginMinimarket_Product_GetArrayWebPathToProductByProducts($aProduct);		
        /**
         * Получение списка валют
         */
		$aCurrency = $this->PluginMinimarket_Currency_GetAllCurrency();
		/**
		 * Определение валюты, в которой будут отображаться товары в корзине
		 */
		if (isset($aAllowData['cart_price_currency'])) {
			$oCurrency = $this->PluginMinimarket_Currency_GetCurrencyBySettings('cart');
		}
        /**
         * Добавление дополнительных данных к товарам
         */
		foreach($aProduct as $oProduct) {
			if (isset($aPhotos[$oProduct->getMainPhotoId()])) {
				$oProduct->setMainPhotoWebPath($aPhotos[$oProduct->getMainPhotoId()]->getProductPhotoWebPath(375));
			}
			$oProduct->setWebPath($aWebPathToProduct[$oProduct->getId()]);
			if(isset($aProductTaxonomies[$oProduct->getId()])) {
				$oProduct->setProductCharacteristics($aProductTaxonomies[$oProduct->getId()]);
			}
			$oProduct->setPriceCurrency(
				$this->PluginMinimarket_Currency_GetSumByFormat(
					$oProduct->getPrice() / Config::Get('plugin.minimarket.settings.factor'),
					$aCurrency[$oProduct->getCurrency()]->getDecimalPlaces(),
					$aCurrency[$oProduct->getCurrency()]->getFormat()
				)
			);
			if (isset($aAllowData['cart_price_currency'])) {
				$nPriceByCurrency = $oProduct->getPrice() / (($oCurrency->getCourse() / $oCurrency->getNominal()) / ($aCurrency[$oProduct->getCurrency()]->getCourse() / $aCurrency[$oProduct->getCurrency()]->getNominal()));
				/**
				 * Перевод цены товара в валюту корзины, БЕЗ форматирования вывода
				 */
				$oProduct->setCartPrice($nPriceByCurrency / Config::Get('plugin.minimarket.settings.factor'));
				/**
				 * Перевод цены товара в валюту корзины, с применением форматирования вывода
				 */
				$oProduct->setCartPriceCurrency(
					$this->PluginMinimarket_Currency_GetSumByFormat(
						isset($aCartObjects[$oProduct->getId()]) 
							? ($nPriceByCurrency * $aCartObjects[$oProduct->getId()]) / Config::Get('plugin.minimarket.settings.factor')
							: $nPriceByCurrency / Config::Get('plugin.minimarket.settings.factor'),
						$oCurrency->getDecimalPlaces(),
						$oCurrency->getFormat()
					)
				);
			}
		}
		return $aProduct;
	}

    /**
     * Возвращает список УРЛ-ов к товарам по списку товаров
     *
     * @param array $aProduct    Список объектов товаров
     *
     * @return array
     */
	public function GetArrayWebPathToProductByProducts($aProduct) {
		if(!is_array($aProduct)) $aProduct = array($aProduct);
		$aWebPathToProduct = array();
		$aCategories = $this->PluginMinimarket_Taxonomy_GetTaxonomiesByType('category');
		foreach($aProduct as $oProduct) {
			$aWebPathToProduct[$oProduct->getId()] = Config::Get('path.root.url') . 'catalog/';
			$bOK = false;
			if (isset($aCategories[$oProduct->getCategory()])) {
				$oCategory = $aCategories[$oProduct->getCategory()];
				$aResult = array();
				for( ; ; ) {
					$aResult[] = $oCategory->getURL();
					if($oCategory->getParentId() == 0) {
						$bOK = true;
					} else {
						$oCategory = $aCategories[$oCategory->getParentId()];
					}
					if($bOK === true) {
						$aResult = array_reverse($aResult);
						foreach($aResult as $sResult) {
							$aWebPathToProduct[$oProduct->getId()] .= $sResult . '/';
						}
						$aWebPathToProduct[$oProduct->getId()] .= $oProduct->getURL() . '/';
						break;
					}
				}
			} else {
				$aWebPathToProduct[$oProduct->getId()] .= $oProduct->getURL() . '/';
			}
		}
		return $aWebPathToProduct;
	}

    /**
     * Возвращает список параметров для сортировки
     *
     * @return array
     */
	public function GetArraySortParams() {
		$aSortParams = array();
		if(isset($_GET['c']['lover'])) {
			$aSortParams['lover'] = $_GET['c']['lover'];
		}
		if(isset($_GET['c']['pros'])) {
			$aSortParams['pros'] = $_GET['c']['pros'];
		}
		return $aSortParams;
	}
	
    /**
     * Получает список таксономий товара по первым буквам и типу таксономии
     *
     * @param string $sProductTaxonomy    Таксономия
     * @param int    $iLimit              Количество
     * @param string $sType               Тип таксономии
     *
     * @return array
     */
    public function GetProductTaxonomiesByLike($sProductTaxonomy, $iLimit, $sType) {
        return $this->oMapper->GetProductTaxonomiesByLike($sProductTaxonomy, $iLimit, $sType);
    }
	
    /**
     * Получить список изображений по временному коду
     *
     * @param string $sTargetTmp    Временный ключ
     *
     * @return array
     */
    public function GetPhotosByTargetTmp($sTargetTmp) {
		return $this->oMapper->GetPhotosByTargetTmp($sTargetTmp);
    }
	
    /**
     * Возвращает изображение по его ID
     *
     * @param int $iId    ID фото
     *
     * @return PluginMinimarket_ModuleProduct_EntityProductPhoto|null
     */
    public function GetProductPhotoById($iId) {
        $aPhotos = $this->GetProductPhotosByArrayId($iId);
        if (isset($aPhotos[$iId])) {
            return $aPhotos[$iId];
        }
        return null;
    }
	
    /**
     * Возвращает список фотографий по списку ID фотографий
     *
     * @param array $aPhotoId    Список ID фото
     *
     * @return array
     */
    public function GetProductPhotosByArrayId($aPhotoId) {
        if (!$aPhotoId) {
            return array();
        }
        if (!is_array($aPhotoId)) {
            $aPhotoId = array($aPhotoId);
        }
        $aPhotoId = array_unique($aPhotoId);
        $aPhotos = array();
		$data = $this->oMapper->GetProductPhotosByArrayId($aPhotoId);
		foreach ($data as $oPhoto) {
			$aPhotos[$oPhoto->getId()] = $oPhoto;
		}
		return $aPhotos;
    }
	
    /**
     * Обновление данных по изображению
     *
     * @param PluginMinimarket_ModuleProduct_EntityProductPhoto $oPhoto    Объект фото
     * 
     * @return  int|bool
     */
    public function UpdateProductPhoto($oPhoto) {
        $this->oMapper->UpdateProductPhoto($oPhoto);
    }
	
    /**
     * Удаление изображения по объекту изображения
     *
     * @param PluginMinimarket_ModuleProduct_EntityProductPhoto $oPhoto    Объект фото
     */
    public function DeleteProductPhoto($oPhoto) {
        $this->oMapper->DeleteProductPhoto($oPhoto->getId());
        $this->Image_RemoveFile($this->Image_GetServerPath($oPhoto->getProductPhotoWebPath()));
        $aSizes = Config::Get('plugin.minimarket.product.photoset.size');
		/**
		 * Удаление всех сгенерированных миниатюр
		 */
        foreach ($aSizes as $aSize) {
            $sSize = $aSize['w'];
            if ($aSize['crop']) {
                $sSize .= 'crop';
            }
            $this->Image_RemoveFile($this->Image_GetServerPath($oPhoto->getProductPhotoWebPath($sSize)));
        }
    }
	
    /**
     * Возвращает список изображений по ID продукта
     *
     * @param int      $iProductId    ID продукта
     * @param int|null $iFromId       ID с которого начинать выборку
     * @param int|null $iCount        Количество
     *
     * @return array
     */
    public function GetPhotosByProductId($iProductId, $iFromId = null, $iCount = null) {
        return $this->oMapper->GetPhotosByProductId($iProductId, $iFromId, $iCount);
    }
	
    /**
     * Возвращает число изображений по временному ключу
     *
     * @param string $sTargetTmp    Временный ключ
     *
     * @return int
     */
    public function GetCountPhotosByTargetTmp($sTargetTmp) {
        return $this->oMapper->GetCountPhotosByTargetTmp($sTargetTmp);
    }
	
    /**
     * Возвращает число изображений по ID товара
     *
     * @param int $iProductId    ID товара
     *
     * @return int
     */
    public function GetCountPhotosByProductId($iProductId) {
        return $this->oMapper->GetCountPhotosByProductId($iProductId);
    }
	
	/**
	 * Загружает изображение
	 *
	 * @param array $aFile    Массив $_FILES
	 *
	 * @return string|bool
	 */
    public function UploadProductPhoto($aFile) {
        if (!is_array($aFile) || !isset($aFile['tmp_name'])) {
            return false;
        }

        $sFileName = func_generator(10);
        $sPath = Config::Get('path.uploads.images') . '/product/' . date('Y/m/d') . '/';

        if (!is_dir(Config::Get('path.root.server') . $sPath)) {
            mkdir(Config::Get('path.root.server') . $sPath, 0755, true);
        }

        $sFileTmp = Config::Get('path.root.server') . $sPath . $sFileName;
        if (!move_uploaded_file($aFile['tmp_name'], $sFileTmp)) {
            return false;
        }

        $aParams = $this->Image_BuildParams('photoset');

        $oImage = $this->Image_CreateImageObject($sFileTmp);
        /**
         * Если объект изображения не создан,
         * возвращаем ошибку
         */
        if ($sError = $oImage->get_last_error()) {
            // Вывод сообщения об ошибки, произошедшей при создании объекта изображения
            $this->Message_AddError($sError, $this->Lang_Get('error'));
            @unlink($sFileTmp);
            return false;
        }
        /**
         * Превышает максимальные размеры из конфига
         */
        if (($oImage->get_image_params('width') > Config::Get('plugin.minimarket.product.img_max_width'))
            || ($oImage->get_image_params('height') > Config::Get('plugin.minimarket.product.img_max_height'))
        ) {
            $this->Message_AddError($this->Lang_Get('plugin.minimarket.product_photoset_error_size'), $this->Lang_Get('error'));
            @unlink($sFileTmp);
            return false;
        }
        /**
         * Добавляем к загруженному файлу расширение
         */
        $sFile = $sFileTmp . '.' . $oImage->get_image_params('format');
        rename($sFileTmp, $sFile);

        $aSizes = Config::Get('plugin.minimarket.product.photoset.size');
        foreach ($aSizes as $aSize) {
            // * Для каждого указанного в конфиге размера генерируем картинку
            $sNewFileName = $sFileName . '_' . $aSize['w'];
            $oImage = $this->Image_CreateImageObject($sFile);
            if ($aSize['crop']) {
                $this->Image_CropProportion($oImage, $aSize['w'], $aSize['h'], true);
                $sNewFileName .= 'crop';
            }
            $this->Image_Resize(
                $sFile, $sPath, $sNewFileName, Config::Get('plugin.minimarket.product.img_max_width'), Config::Get('plugin.minimarket.product.img_max_height'),
                $aSize['w'], $aSize['h'], true, $aParams, $oImage
            );
        }
        return $this->Image_GetWebPath($sFile);
    }
	
    /**
     * Добавляет к продукту изображение
     *
     * @param PluginMinimarket_ModuleProduct_EntityProductPhoto $oPhoto    Объект фото
     *
     * @return PluginMinimarket_ModuleProduct_EntityProductPhoto|bool
     */
    public function AddProductPhoto($oPhoto) {
        if ($iId = $this->oMapper->AddProductPhoto($oPhoto)) {
            $oPhoto->setId($iId);
            return $oPhoto;
        }
        return false;
    }
	
	/**
 	 * Возвращает список Свойств с Заголовками атрибутов и Атрибутами по списку атрибутов и свойств
	 *
	 * @param array $aPropertiesAndAttributes    Список свойств
	 *
	 * @return array
	 */
	public function GetListProductPropertiesByPropertiesAndAttributes($aPropertiesAndAttributes) {
        /**
         * Получение списка категорий атрибутов
         */
		$aAttributesCategories = $this->PluginMinimarket_Taxonomy_GetTaxonomiesByType('attributes_category');
        /**
         * Добавление заголовков к атрибутам товара
         */
		$aPropertiesByProductTmp = array();
		foreach($aPropertiesAndAttributes as $key=>$oTaxonomy) {
			if($oTaxonomy->getType() == 'property') {
				$aPropertiesByProductTmp[] = $oTaxonomy;
				unset($aPropertiesAndAttributes[$key]);
			}
		}
		$aLinks = $this->PluginMinimarket_Link_GetLinksByType('attributes_category_attribut');
		foreach($aAttributesCategories as $oAttributesCategory) {
			/**
			 * Получение списка связей категорий атрибутов с атрибутами
			 */
			$aAttributesCategoryAttribut = array();
			if (isset($aLinks[$oAttributesCategory->getId()])) $aAttributesCategoryAttribut = $aLinks[$oAttributesCategory->getId()];
			$bCheck = false;
			foreach($aPropertiesAndAttributes as $key => $oTaxonomy) {
				if(
					$oTaxonomy->getType() == 'attribut'
					&& in_array($oTaxonomy->getId(), $aAttributesCategoryAttribut)
				) {
					if ($bCheck === false) $aPropertiesByProductTmp[] = $oAttributesCategory;
					$bCheck = true;
					$aPropertiesByProductTmp[] = $oTaxonomy;
					unset ($aPropertiesAndAttributes[$key]);
				}
				if($oTaxonomy->getType() != 'attribut') unset($aPropertiesAndAttributes[$key]);
			}
		}
		if(count($aPropertiesAndAttributes)) {
			$oTaxonomy = Engine::GetEntity('PluginMinimarket_ModuleTaxonomy_EntityTaxonomy');
			$oTaxonomy->setName($this->Lang_Get('plugin.minimarket.additionally'));
			$oTaxonomy->setType('attributes_category');
			$aPropertiesByProductTmp[] = $oTaxonomy;
		}
		return array_merge($aPropertiesByProductTmp, $aPropertiesAndAttributes);
	}

}
?>