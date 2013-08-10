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
		$this->oMapper=Engine::GetMapper(__CLASS__);
	}
	
	public function addProduct(PluginMinimarket_ModuleProduct_EntityProduct $oProduct) {
        if ($sId=$this->oMapper->addProduct($oProduct)) {
			$oProduct->setId($sId);
			// добавляем характеристики товара
			$aCharacteristics=explode(',',$oProduct->getCharacteristics());
			foreach($aCharacteristics as $sCharacteristics) {
				$oProductTaxonomy=Engine::GetEntity('PluginMinimarket_ModuleProduct_EntityProductTaxonomy');
				$oProductTaxonomy->setProductId($oProduct->getId());
				$oProductTaxonomy->setProductTaxonomyText($sCharacteristics);
				$oProductTaxonomy->setProductTaxonomyType('characteristics');
				$this->PluginMinimarket_Product_AddProductTaxonomy($oProductTaxonomy);
			}
			// добавляем особенности товара
			$aFeatures=explode(',',$oProduct->getFeatures());
			foreach($aFeatures as $sFeatures) {
				$oProductTaxonomy=Engine::GetEntity('PluginMinimarket_ModuleProduct_EntityProductTaxonomy');
				$oProductTaxonomy->setProductId($oProduct->getId());
				$oProductTaxonomy->setProductTaxonomyText($sFeatures);
				$oProductTaxonomy->setProductTaxonomyType('features');
				$this->PluginMinimarket_Product_AddProductTaxonomy($oProductTaxonomy);
			}
			// добавляем свойства товара
			foreach($oProduct->getProperties() as $iProperty) {
				$oProductProperty=Engine::GetEntity('PluginMinimarket_ModuleProduct_EntityProductProperty');
				$oProductProperty->setProductId($oProduct->getId());
				$oProductProperty->setPropertyId($iProperty);
				$this->PluginMinimarket_Product_AddProductProperty($oProductProperty);
			}
            return $oProduct;
        }
        return false;
	}
	
	public function AddProductTaxonomy(PluginMinimarket_ModuleProduct_EntityProductTaxonomy $oProductTaxonomy) {
        if ($sId = $this->oMapper->AddProductTaxonomy($oProductTaxonomy)) {
            $oProductTaxonomy->setProductTaxonomyId($sId);
            return $oProductTaxonomy;
        }
        return false;
	}
	
	public function AddProductProperty(PluginMinimarket_ModuleProduct_EntityProductProperty $oProductProperty) {
        if ($sId = $this->oMapper->AddProductProperty($oProductProperty)) {
            $oProductProperty->setProductPropertyId($sId);
            return $oProductProperty;
        }
        return false;
	}
	
    /**
     * Удаляет товар.
     * Удалятся всё параметры по товару (характеристики, особенности, краткие характеристики)
     *
     * @param PluginMinimarket_ModuleProduct_EntityProduct $oProduct Объект продукта
     *
     */
	public function deleteProduct(PluginMinimarket_ModuleProduct_EntityProduct $oProduct) {
        /**
         * Если товар успешно удален, удаляем связанные данные
         */
        if ($bResult = $this->oMapper->deleteProduct($oProduct->getId())) {
			/**
			 * удаляем характеристики и особенности товара
			 */
			$this->PluginMinimarket_Product_DeleteProductTaxonomyByProductIdAndType($oProduct->getId(),'characteristics');
			$this->PluginMinimarket_Product_DeleteProductTaxonomyByProductIdAndType($oProduct->getId(),'features');
			/**
			 * удаляем свойства товара
			 */
			$this->PluginMinimarket_Product_DeleteProductPropertyByProductId($oProduct->getId());
			/**
			 * Удаляем фото
			 */
			if ($aPhotos = $this->getPhotosByProductId($oProduct->getId())) {
				foreach ($aPhotos as $oPhoto) {
					$this->deleteProductPhoto($oPhoto);
				}
			}
        }
	}
	
	public function UpdateProduct(PluginMinimarket_ModuleProduct_EntityProduct $oProduct) {
		if ($this->oMapper->UpdateProduct($oProduct)) {
			// удаляем старые характеристики и особенности товара
			$this->PluginMinimarket_Product_DeleteProductTaxonomyByProductIdAndType($oProduct->getId(),'characteristics');
			$this->PluginMinimarket_Product_DeleteProductTaxonomyByProductIdAndType($oProduct->getId(),'features');
			// удаляем старые свойства
			$this->PluginMinimarket_Product_DeleteProductPropertyByProductId($oProduct->getId());
			// добавляем характеристики товара
			$aCharacteristics=explode(',',$oProduct->getCharacteristics());
			foreach($aCharacteristics as $sCharacteristics) {
				$oProductTaxonomy=Engine::GetEntity('PluginMinimarket_ModuleProduct_EntityProductTaxonomy');
				$oProductTaxonomy->setProductId($oProduct->getId());
				$oProductTaxonomy->setProductTaxonomyText($sCharacteristics);
				$oProductTaxonomy->setProductTaxonomyType('characteristics');
				$this->PluginMinimarket_Product_AddProductTaxonomy($oProductTaxonomy);
			}
			// добавляем особенности товара
			$aFeatures=explode(',',$oProduct->getFeatures());
			foreach($aFeatures as $sFeatures) {
				$oProductTaxonomy=Engine::GetEntity('PluginMinimarket_ModuleProduct_EntityProductTaxonomy');
				$oProductTaxonomy->setProductId($oProduct->getId());
				$oProductTaxonomy->setProductTaxonomyText($sFeatures);
				$oProductTaxonomy->setProductTaxonomyType('features');
				$this->PluginMinimarket_Product_AddProductTaxonomy($oProductTaxonomy);
			}
			// добавляем свойства товара
			$aProperties = $oProduct->getProperties();
			if(is_array($aProperties) && count($aProperties) > 0) {
				foreach($aProperties as $iProperty) {
					$oProductProperty = Engine::GetEntity('PluginMinimarket_ModuleProduct_EntityProductProperty');
					$oProductProperty->setProductId($oProduct->getId());
					$oProductProperty->setPropertyId($iProperty);
					$this->PluginMinimarket_Product_AddProductProperty($oProductProperty);
				}
			}
			return true;
		}
		return false;
	}
	
	public function DeleteProductTaxonomyByProductIdAndType($sId, $sType) {
        if ($bResult = $this->oMapper->DeleteProductTaxonomyByProductIdAndType($sId, $sType)) {
			return true;
        }
        return false;
	}
	
	public function DeleteProductPropertyByProductId($sId) {
        if ($bResult = $this->oMapper->DeleteProductPropertyByProductId($sId)) {
			return true;
        }
        return false;
	}
	
	public function getProductById($sId) {
        return $this->oMapper->getProductById($sId);
	}
	
	public function getProductByURL($sURL) {
        return $this->oMapper->getProductByURL($sURL);
	}
	
	public function GetProductsByArrayId($aProductId) {
        return $this->oMapper->GetProductsByArrayId($aProductId);
	}
	
	public function getArrayProductPropertyIdByArrayProductId($aProductId) {
		if(!is_array($aProductId)) $aProductId=array($aProductId);
        return $this->oMapper->getArrayProductPropertyIdByArrayProductId($aProductId);
	}
	
    /**
     * Возвращает список таксономий по типу таксономии
     *
     * @param string $sType    		Тип таксономий
     *
     * @return array
     */
	public function GetArrayProductTaxonomyByType($sType) {
        return $this->oMapper->GetArrayProductTaxonomyByType($sType);
	}
	
    /**
     * Возвращает список таксономий продукта по списку ID продуктов и типу
     *
     * @param array $aProductId		Список ID продуктов
     * @param string $sType    		Тип таксономий
     *
     * @return array
     */
	public function GetArrayProductTaxonomyByArrayProductIdAndType($aProductId, $sType) {
		if(!is_array($aProductId)) $aProductId=array($aProductId);
        return $this->oMapper->GetArrayProductTaxonomyByArrayProductIdAndType($aProductId, $sType);
	}
	
    /**
     * Список товаров по фильтру
     *
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
	
	public function GetProductsAdditionalData($aProductId) {
        if (!is_array($aProductId)) {
            $aProductId = array($aProductId);
        }
        /**
         * Получаем "голые" товары
         */
        $aProducts = $this->GetProductsByArrayId($aProductId);
		
		// получаем превью
		$aPhotosId=array();
		foreach($aProducts as $oProduct) {
			$aPhotosId[]=$oProduct->getMainPhotoId();
		}
		$aPhotos=$this->PluginMinimarket_Product_GetProductPhotosByArrayId($aPhotosId);
		
		// получаем краткие характеристики
		// $aProductTaxonomies=$this->PluginMinimarket_Taxonomy_GetTaxonomiesByParentArray($aProductId);
		$aProductTaxonomies = $this->PluginMinimarket_Product_GetArrayProductTaxonomyByArrayProductIdAndType($aProductId, 'characteristics');
		
		// получаем массив УРЛ-ов к товарам
		$aWebPathToProduct=$this->PluginMinimarket_Product_GetArrayWebPathToProductByProducts($aProducts);
		
		// добавляем дополнительные данные к товарам
		foreach($aProducts as $oProduct) {
			// добавляем превью
			if(isset($aPhotos[$oProduct->getMainPhotoId()])) {
				$oProduct->setMainPhotoWebPath($aPhotos[$oProduct->getMainPhotoId()]->getProductPhotoWebPath(375));
			}
			// добавляем УРЛ
			$oProduct->setWebPath($aWebPathToProduct[$oProduct->getId()]);
			// добавляем особенности
			// $oProduct->setProductFeatures($aTaxonomies[$oProduct->getId()]['features']);
			// добавляем характеристики
			if(isset($aProductTaxonomies[$oProduct->getId()])) {
				$oProduct->setProductCharacteristics($aProductTaxonomies[$oProduct->getId()]);
			}
		}
		
		return $aProducts;
	}
	
	// возвращает массив УРЛ-ов к товарам по массиву товаров
	public function GetArrayWebPathToProductByProducts($aProducts) {
		if(!is_array($aProducts)) $aProducts=array($aProducts);
		$aWebPathToProduct=array();
		$aCategories=$this->PluginMinimarket_Taxonomy_GetTaxonomiesByType('category');
		foreach($aProducts as $oProduct) {
			$aWebPathToProduct[$oProduct->getId()]=Config::Get('path.root.url').'catalog/';
			$bOK=false;
			$oCategory=$aCategories[$oProduct->getCategory()];
			$aResult=array();
			for( ; ; ) {
				$aResult[]=$oCategory->getURL();
				if($oCategory->getParent()==0) {
					$bOK=true;
				} else {
					$oCategory=$aCategories[$oCategory->getParent()];
				}
				if($bOK===true) {
					$aResult=array_reverse($aResult);
					foreach($aResult as $sResult) {
						$aWebPathToProduct[$oProduct->getId()].=$sResult.'/';
					}
					$aWebPathToProduct[$oProduct->getId()].=$oProduct->getURL().'/';
					break;
				}
			}
		}
		return $aWebPathToProduct;
	}
	
	// если есть параметры для сортировки, то формирует их в URL
	public function GetArraySortParams() {
		$aSortParams=array();
		if(isset($_GET['c']['lover'])) {
			$aSortParams['lover']=$_GET['c']['lover'];
		}
		if(isset($_GET['c']['pros'])) {
			$aSortParams['pros']=$_GET['c']['pros'];
		}
		return $aSortParams;
	}
	
    public function GetProductTaxonomiesByLike($sProductTaxonomy,$iLimit,$sType) {
        return $this->oMapper->GetProductTaxonomiesByLike($sProductTaxonomy,$iLimit,$sType);
    }
	
    /**
     * Получить список изображений по временному коду
     *
     * @param string $sTargetTmp    Временный ключ
     *
     * @return array
     */
    public function getPhotosByTargetTmp($sTargetTmp) {
		return $this->oMapper->getPhotosByTargetTmp($sTargetTmp);
    }
	
    /**
     * Получить изображение по его id
     *
     * @param int $sId    ID фото
     *
     * @return PluginMinimarket_ModuleProduct_EntityProductPhoto|null
     */
    public function getProductPhotoById($sId) {
        $aPhotos = $this->GetProductPhotosByArrayId($sId);
        if (isset($aPhotos[$sId])) {
            return $aPhotos[$sId];
        }
        return null;
    }
	
    /**
     * Возвращает список фотографий по списку id фоток
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
     * Обновить данные по изображению
     *
     * @param PluginMinimarket_ModuleProduct_EntityProductPhoto $oPhoto Объект фото
     */
    public function updateProductPhoto($oPhoto) {
        $this->oMapper->updateProductPhoto($oPhoto);
    }
	
    /**
     * Удаляет изображение
     *
     * @param PluginMinimarket_ModuleProduct_EntityProductPhoto $oPhoto Объект фото
     */
    public function deleteProductPhoto($oPhoto) {
        $this->oMapper->deleteProductPhoto($oPhoto->getProductPhotoId());
        $this->Image_RemoveFile($this->Image_GetServerPath($oPhoto->getProductPhotoWebPath()));
        $aSizes = Config::Get('minimarket.product.photoset.size');
        // Удаляем все сгенерированные миниатюры основываясь на данных из конфига.
        foreach ($aSizes as $aSize) {
            $sSize = $aSize['w'];
            if ($aSize['crop']) {
                $sSize .= 'crop';
            }
            $this->Image_RemoveFile($this->Image_GetServerPath($oPhoto->getProductPhotoWebPath($sSize)));
        }
    }
	
    /**
     * Получить список изображений по id продукта
     *
     * @param int      $iProductId	ID продукта
     * @param int|null $iFromId     ID с которого начинать выборку
     * @param int|null $iCount      Количество
     *
     * @return array
     */
    public function getPhotosByProductId($iProductId, $iFromId = null, $iCount = null) {
        return $this->oMapper->getPhotosByProductId($iProductId, $iFromId, $iCount);
    }
	
    /**
     * Получить число изображений по id продукта
     *
     * @param string $sTargetTmp    Временный ключ
     *
     * @return int
     */
    public function getCountPhotosByTargetTmp($sTargetTmp) {
        return $this->oMapper->getCountPhotosByTargetTmp($sTargetTmp);
    }
	
    /**
     * Получить число изображений по id продукта
     *
     * @param int $iProductId    ID продукта
     *
     * @return int
     */
    public function getCountPhotosByProductId($iProductId) {
        return $this->oMapper->getCountPhotosByProductId($iProductId);
    }
	
   /**
     * Загрузить изображение
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
        if (($oImage->get_image_params('width') > Config::Get('minimarket.img_max_width'))
            || ($oImage->get_image_params('height') > Config::Get('minimarket.img_max_height'))
        ) {
            $this->Message_AddError($this->Lang_Get('topic_photoset_error_size'), $this->Lang_Get('error'));
            @unlink($sFileTmp);
            return false;
        }
        /**
         * Добавляем к загруженному файлу расширение
         */
        $sFile = $sFileTmp . '.' . $oImage->get_image_params('format');
        rename($sFileTmp, $sFile);

        $aSizes = Config::Get('minimarket.product.photoset.size');
        foreach ($aSizes as $aSize) {
            // * Для каждого указанного в конфиге размера генерируем картинку
            $sNewFileName = $sFileName . '_' . $aSize['w'];
            $oImage = $this->Image_CreateImageObject($sFile);
            if ($aSize['crop']) {
                $this->Image_CropProportion($oImage, $aSize['w'], $aSize['h'], true);
                $sNewFileName .= 'crop';
            }
            $this->Image_Resize(
                $sFile, $sPath, $sNewFileName, Config::Get('minimarket.img_max_width'), Config::Get('minimarket.img_max_height'),
                $aSize['w'], $aSize['h'], true, $aParams, $oImage
            );
        }
        return $this->Image_GetWebPath($sFile);
    }
	
    /**
     * Добавить к продукту изображение
     *
     * @param PluginMinimarket_ModuleProduct_EntityProductPhoto $oPhoto    Объект фото
     *
     * @return PluginMinimarket_ModuleProduct_EntityProductPhoto|bool
     */
    public function addProductPhoto($oPhoto) {
        if ($sId = $this->oMapper->addProductPhoto($oPhoto)) {
            $oPhoto->setProductPhotoId($sId);
            return $oPhoto;
        }
        return false;
    }
	
   /**
     * Строит массив Свойств с Заголовками атрибутов и Атрибутами по массиву из атрибутов и свойств
     *
     * @param array $aPropertiesAndAttributes
     *
     * @return array
     */
	public function createArrayPropertiesByArrayPropertiesAndAttributes($aPropertiesAndAttributes) {
		$aAttributesCategories=$this->PluginMinimarket_Taxonomy_getTaxonomiesByType('attributes_category');
		
		// к атрибутам товара добавляем заголовки
		$aPropertiesByProductTmp=array();
		foreach($aPropertiesAndAttributes as $key=>$oTaxonomy) {
			if($oTaxonomy->getTaxonomyType()=='property') {
				$aPropertiesByProductTmp[]=$oTaxonomy;
				unset($aPropertiesAndAttributes[$key]);
			}
		}
		foreach($aAttributesCategories as $oAttributesCategory) {
			$bCheck = false;
			foreach($aPropertiesAndAttributes as $key=>$oTaxonomy) {
				if(
					$oTaxonomy->getTaxonomyType()=='attribut' && 
					unserialize($oAttributesCategory->getTaxonomyConfig()) &&
					in_array($oTaxonomy->getId(),unserialize($oAttributesCategory->getTaxonomyConfig()))
				) {
					if($bCheck === false) $aPropertiesByProductTmp[]=$oAttributesCategory;
					$bCheck = true;
					$aPropertiesByProductTmp[]=$oTaxonomy;
					unset($aPropertiesAndAttributes[$key]);
				}
				if($oTaxonomy->getTaxonomyType()!='attribut') unset($aPropertiesAndAttributes[$key]);
			}
		}
		if(count($aPropertiesAndAttributes)) {
			$oTaxonomy=Engine::GetEntity('PluginMinimarket_ModuleTaxonomy_EntityTaxonomy');
			$oTaxonomy->setName($this->Lang_Get('plugin.minimarket.attributes_category_additionally'));
			$oTaxonomy->setTaxonomyType('attributes_category');
			$aPropertiesByProductTmp[]=$oTaxonomy;
		}
		
		return array_merge($aPropertiesByProductTmp,$aPropertiesAndAttributes);
	}

}
?>