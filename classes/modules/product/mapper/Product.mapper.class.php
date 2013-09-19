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

class PluginMinimarket_ModuleProduct_MapperProduct extends Mapper {
	
    public function addProduct(PluginMinimarket_ModuleProduct_EntityProduct $oProduct) {
        $sql = "INSERT INTO " . Config::Get('db.table.minimarket_product') . "
			(product_name,
			product_manufacturer_code,
			product_url,
			product_brand,
			product_category,
			product_price,
			product_weight,
			product_show,
			product_in_stock,
			product_main_photo_id,
			product_text
			)
			VALUES(?,?,?,?d,?d,?,?,?,?,?d,?)
		";
        $nId = $this->oDb->query(
            $sql, $oProduct->getName(), $oProduct->getManufacturerCode(), 
			$oProduct->getURL(), $oProduct->getBrand(), $oProduct->getCategory(), 
			$oProduct->getPrice(), $oProduct->getWeight(), $oProduct->getShow(), 
			$oProduct->getInStock(), $oProduct->getMainPhotoId(), $oProduct->getText()
        );
        if ($nId) {
            return $nId;
        }
        return false;
    }
	
    public function UpdateProduct(PluginMinimarket_ModuleProduct_EntityProduct $oProduct) {
		$sql = "UPDATE " . Config::Get('db.table.minimarket_product') . "
			SET 
				product_name = ?,
				product_manufacturer_code = ?,
				product_url = ?,
				product_brand = ?d,
				product_category = ?d,
				product_price = ?,
				product_weight = ?,
				product_show = ?d,
				product_in_stock = ?d,
				product_main_photo_id = ?d,
				product_text = ?
			WHERE
				product_id = ?d
		";
        $bResult = $this->oDb->query(
            $sql, $oProduct->getName(), $oProduct->getManufacturerCode(), $oProduct->getURL(), $oProduct->getBrand(),
			$oProduct->getCategory(), $oProduct->getPrice(), $oProduct->getWeight(), 
			$oProduct->getShow(), $oProduct->getInStock(), $oProduct->getMainPhotoId(), $oProduct->getText(), $oProduct->getId()
        );
		return $bResult !== false;
	}
	
	public function getProductByURL($sURL) {
        $sql = "SELECT
						*
					FROM
						" . Config::Get('db.table.minimarket_product') . "
					WHERE
						product_url = ?
					";
        if ($aRow = $this->oDb->selectRow($sql, $sURL)) {
            return Engine::GetEntity('PluginMinimarket_Product_Product', $aRow);
        }
        return false;
	}
	
	public function getProductById($sId) {
        $sql = "SELECT
						*
					FROM
						" . Config::Get('db.table.minimarket_product') . "
					WHERE
						product_id = ?d
					";
        if ($aRow = $this->oDb->selectRow($sql, $sId)) {
            return Engine::GetEntity('PluginMinimarket_Product_Product', $aRow);
        }
        return false;
	}
	
	public function GetProductsByArrayId($aProductId) {
        if (!is_array($aProductId) || count($aProductId) == 0) {
            return array();
        }
        $sql = "SELECT
						*
					FROM
						" . Config::Get('db.table.minimarket_product') . "
					WHERE
						product_id IN(?a)
					ORDER BY product_price DESC
					";
        $aProducts = array();
        if ($aRows = $this->oDb->select($sql, $aProductId)) {
            foreach ($aRows as $aProduct) {
                $aProducts[] = Engine::GetEntity('PluginMinimarket_Product_Product', $aProduct);
            }
        }
        return $aProducts;
	}
	
	public function getArrayProductPropertyIdByArrayProductId($aProductId) {
        if (!is_array($aProductId) || count($aProductId) == 0) {
            return array();
        }
        $sql = "SELECT
						product_id,property_id
					FROM
						" . Config::Get('db.table.minimarket_product_property') . "
					WHERE
						product_id IN(?a)
					";
        $aProperties = array();
        if ($aRows = $this->oDb->select($sql, $aProductId)) {
            foreach ($aRows as $aProperty) {
                $aProperties[$aProperty['product_id']][] = $aProperty['property_id'];
            }
        }
        return $aProperties;
	}
	
	public function GetArrayProductTaxonomyByType($sType) {
        $sql = "SELECT
						*
					FROM
						" . Config::Get('db.table.minimarket_product_taxonomy') . "
					WHERE
						product_taxonomy_type = ?
					";
        $aResult = array();
        if ($aRows = $this->oDb->select($sql, $sType)) {
            foreach ($aRows as $aRow) {
                $aResult[] = Engine::GetEntity('PluginMinimarket_Product_ProductTaxonomy', $aRow);
            }
        }
        return $aResult;
	}
	
	public function GetArrayProductTaxonomyByArrayProductIdAndType($aProductId, $sType) {
        if (!is_array($aProductId) || count($aProductId) == 0) {
            return array();
        }
        $sql = "SELECT
						*
					FROM
						" . Config::Get('db.table.minimarket_product_taxonomy') . "
					WHERE
						product_id IN ( ?a ) AND product_taxonomy_type = ?
					";
        $aResult = array();
        if ($aRows = $this->oDb->select($sql, $aProductId, $sType)) {
            foreach ($aRows as $aRow) {
				$oProductTaxonomy = Engine::GetEntity('PluginMinimarket_Product_ProductTaxonomy', $aRow);
                $aResult[$oProductTaxonomy->getProductId()][] = $oProductTaxonomy;
            }
        }
        return $aResult;
	}
	
	public function GetProducts($aFilter, &$iCount, $iPage, $iPerPage) {
		$sWhere = $this->buildFilter($aFilter);
		if(isset($aFilter['pros']) && !empty($aFilter['pros'])) {
			$sql = "SELECT
							p.product_id, COUNT(p.product_id) AS c
						FROM
							" . Config::Get('db.table.minimarket_product') . " as p,
							" . Config::Get('db.table.minimarket_product_property') . " as pp
						WHERE
							1=1
							" . $sWhere . "
							AND p.product_id = pp.product_id
							AND pp.property_id IN (?a)
						GROUP BY p.product_id
						HAVING c = ?
						LIMIT 
							?d, ?d
						";
			$aProducts=array();
			if ($aRows = $this->oDb->selectPage($iCount, $sql, $aFilter['pros'], count($aFilter['pros']), ($iPage - 1) * $iPerPage, $iPerPage)) {
				foreach ($aRows as $aProduct) {
					$aProducts[] = $aProduct['product_id'];
				}
			}
		} elseif(isset($aFilter['lover']) && !empty($aFilter['lover'])) {
			$sql = "SELECT
							p.product_id, COUNT(p.product_id) AS c
						FROM
							" . Config::Get('db.table.minimarket_product') . " as p,
							" . Config::Get('db.table.minimarket_product_taxonomy') . " as pt
						WHERE
							1=1
							" . $sWhere . "
							AND p.product_id = pt.product_id
							AND pt.product_taxonomy_text IN (?a)
							AND pt.product_taxonomy_type = ?
						GROUP BY p.product_id
						HAVING c = ?
						LIMIT 
							?d, ?d
						";
			$aProducts=array();
			if ($aRows = $this->oDb->selectPage($iCount, $sql, $aFilter['lover'], 'features', count($aFilter['lover']), ($iPage - 1) * $iPerPage, $iPerPage)) {
				foreach ($aRows as $aProduct) {
					$aProducts[] = $aProduct['product_id'];
				}
			}			
		} else {
			$sql = "SELECT
							product_id
						FROM
							" . Config::Get('db.table.minimarket_product') . "
						WHERE
							1=1
							" . $sWhere . "
						LIMIT 
							?d, ?d
						";
			$aProducts=array();
			if ($aRows = $this->oDb->selectPage($iCount, $sql, ($iPage - 1) * $iPerPage, $iPerPage)) {
				foreach ($aRows as $aProduct) {
					$aProducts[] = $aProduct['product_id'];
				}
			}
		}
		return $aProducts;
	}
	
	protected function buildFilter($aFilter) {
		$sWhere = '';
        if (isset($aFilter['in_category'])) {
            if (!is_array($aFilter['in_category'])) {
                $aFilter['in_category'] = array($aFilter['in_category']);
            }
			$sPrefix = isset($aFilter['pros'])?"p.":"";
            $sWhere .= " AND ".$sPrefix."product_category IN ('" . join("','", $aFilter['in_category']) . "')";
        }
		return $sWhere;
	}
	
	public function AddProductTaxonomy(PluginMinimarket_ModuleProduct_EntityProductTaxonomy $oProductTaxonomy) {
        $sql = "INSERT INTO " . Config::Get('db.table.minimarket_product_taxonomy') . "
			(product_id,
			product_taxonomy_text,
			product_taxonomy_type
			)
			VALUES(	?d,	?,	?)
		";
        if ($iId = $this->oDb->query(
            $sql,
            $oProductTaxonomy->getProductId(),
            $oProductTaxonomy->getProductTaxonomyText(),
            $oProductTaxonomy->getProductTaxonomyType()
        )
        ) {
            $oProductTaxonomy->setProductTaxonomyId($iId);
            return $iId;
        }
        return false;
	}
	
	public function AddProductProperty(PluginMinimarket_ModuleProduct_EntityProductProperty $oProductProperty) {
        $sql = "INSERT INTO " . Config::Get('db.table.minimarket_product_property') . "
			(product_id,
			property_id
			)
			VALUES(	?d,	?d)
		";
        if ($iId = $this->oDb->query(
            $sql,
            $oProductProperty->getProductId(),
            $oProductProperty->getPropertyId()
        )
        ) {
            $oProductProperty->setProductPropertyId($iId);
            return $iId;
        }
        return false;
	}
	
	public function DeleteProductTaxonomyByProductIdAndType($sId, $sType) {
        $sql = "DELETE FROM " . Config::Get('db.table.minimarket_product_taxonomy') . "
			WHERE
				product_id = ?d AND product_taxonomy_type = ?
		";
        return $this->oDb->query($sql, $sId, $sType) !== false;
	}
	
	public function DeleteProductPropertyByProductId($sId) {
        $sql = "DELETE FROM " . Config::Get('db.table.minimarket_product_property') . "
			WHERE
				product_id = ?d
		";
        return $this->oDb->query($sql, $sId) !== false;
	}
	
	public function GetProductTaxonomiesByLike($sProductTaxonomy,$iLimit,$sType) {
		$sProductTaxonomy=mb_strtolower($sProductTaxonomy,'UTF-8');
        $sql = "SELECT
				product_taxonomy_text
			FROM
				" . Config::Get('db.table.minimarket_product_taxonomy') . "
			WHERE
				product_taxonomy_text LIKE ? AND product_taxonomy_type = ?
			GROUP BY 
				product_taxonomy_text
			LIMIT 0, ?d
			";
        $aReturn = array();
        if ($aRows = $this->oDb->select($sql,$sProductTaxonomy.'%',$sType,$iLimit)) {
            foreach ($aRows as $aRow) {
                $aReturn[] = Engine::GetEntity('PluginMinimarket_Product_ProductTaxonomy', $aRow);
            }
        }
        return $aReturn;
	}
	
    /**
     * Получить список изображений по временному коду
     *
     * @param string $sTargetTmp    Временный ключ
     *
     * @return array
     */
    public function getPhotosByTargetTmp($sTargetTmp) {
        $sql = 'SELECT * FROM ' . Config::Get('db.table.minimarket_product_photo') . ' WHERE product_photo_target_tmp = ?';
        $aPhotos = $this->oDb->select($sql, $sTargetTmp);
        $aReturn = array();
        if (is_array($aPhotos) && count($aPhotos)) {
            foreach ($aPhotos as $aPhoto) {
                $aReturn[] = Engine::GetEntity('PluginMinimarket_Product_ProductPhoto', $aPhoto);
            }
        }
        return $aReturn;
    }
	
    /**
     * Обновить данные по изображению
     *
     * @param PluginMinimarket_ModuleProduct_EntityProductPhoto $oPhoto Объект фото
     *
     * @return  bool
     */
    public function updateProductPhoto($oPhoto) {
        if (!$oPhoto->getProductId() && !$oPhoto->getProductPhotoTargetTmp()) {
            return false;
        }
        if ($oPhoto->getProductId()) {
            $oPhoto->setProductPhotoTargetTmp = null;
        }
        $sql = 'UPDATE ' . Config::Get('db.table.minimarket_product_photo') . ' SET
                        product_photo_path = ?, 
						product_photo_description = ?, 
						product_id = ?d, 
						product_photo_target_tmp=? 
					WHERE 
						product_photo_id = ?d';
        $bResult = $this->oDb->query(
            $sql, $oPhoto->getProductPhotoPath(), $oPhoto->getProductPhotoDescription(), 
			$oPhoto->getProductId(), $oPhoto->getProductPhotoTargetTmp(),
            $oPhoto->getProductPhotoId()
        );
        return $bResult !== false;
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
    public function getPhotosByProductId($iProductId, $iFromId, $iCount) {
        $sql = 'SELECT * FROM ' . Config::Get('db.table.minimarket_product_photo') . 
			' WHERE product_id = ?d {AND product_photo_id > ?d LIMIT 0, ?d}';
        $aPhotos = $this->oDb->select($sql, $iProductId, ($iFromId !== null) ? $iFromId : DBSIMPLE_SKIP, $iCount);
        $aReturn = array();
        if (is_array($aPhotos) && count($aPhotos)) {
            foreach ($aPhotos as $aPhoto) {
                $aReturn[] = Engine::GetEntity('PluginMinimarket_Product_ProductPhoto', $aPhoto);
            }
        }
        return $aReturn;
    }
	
    /**
     * Получить число изображений по id продукта
     *
     * @param string $sTargetTmp    Временный ключ
     *
     * @return int
     */
    public function getCountPhotosByTargetTmp($sTargetTmp) {
        $sql = 'SELECT COUNT(product_photo_id) FROM ' . Config::Get('db.table.minimarket_product_photo') . 
			' WHERE product_photo_target_tmp = ?';
        $aPhotosCount = $this->oDb->selectCol($sql, $sTargetTmp);
        return $aPhotosCount[0];
    }
	
    /**
     * Получить число изображений по id продукта
     *
     * @param int $iProductId    ID продукта
     *
     * @return int
     */
    public function getCountPhotosByProductId($iProductId) {
        $sql = 'SELECT COUNT(product_photo_id) FROM ' . Config::Get('db.table.minimarket_product_photo') . 
			' WHERE product_id = ?d';
        $aPhotosCount = $this->oDb->selectCol($sql, $iProductId);
        return $aPhotosCount[0];
    }
	
    /**
     * Добавить к продукту изображение
     *
     * @param PluginMinimarket_ModuleProduct_EntityProductPhoto $oPhoto    Объект фото
     *
     * @return bool
     */
    public function addProductPhoto($oPhoto) {
        if (!$oPhoto->getProductId() && !$oPhoto->getProductPhotoTargetTmp()) {
            return false;
        }
        $sTargetType = ($oPhoto->getProductId()) ? 'product_id' : 'product_photo_target_tmp';
        $iTargetId = ($sTargetType == 'product_id') ? $oPhoto->getProductId() : $oPhoto->getProductPhotoTargetTmp();
        $sql = 'INSERT INTO ' . Config::Get('db.table.minimarket_product_photo') . ' SET
                        product_photo_path = ?, 
						product_photo_description = ?, ?# = ?';
        return $this->oDb->query($sql, 
			$oPhoto->getProductPhotoPath(), 
			$oPhoto->getProductPhotoDescription(), 
			$sTargetType, 
			$iTargetId
		);
    }
	
    /**
     * Возвращает список фотографий по списку id фоток
     *
     * @param array $aPhotoId    Список ID фото
     *
     * @return array
     */
    public function GetProductPhotosByArrayId($aPhotoId) {
        if (!is_array($aPhotoId) || count($aPhotoId) == 0) {
            return array();
        }

        $sql = "SELECT
					*
				FROM 
					" . Config::Get('db.table.minimarket_product_photo') . "
				WHERE 
					product_photo_id IN(?a)
				ORDER BY FIELD(product_photo_id,?a) ";
        $aReturn = array();
        if ($aRows = $this->oDb->select($sql, $aPhotoId, $aPhotoId)) {
            foreach ($aRows as $aPhoto) {
                $aReturn[] = Engine::GetEntity('PluginMinimarket_Product_ProductPhoto', $aPhoto);
            }
        }
        return $aReturn;
    }
	
    /**
     * Удалить изображение
     *
     * @param int $iPhotoId    ID фото
     */
    public function deleteProductPhoto($iPhotoId) {
        $sql = "DELETE FROM " . Config::Get('db.table.minimarket_product_photo') . 
			" WHERE  product_photo_id = ?d";
        return $this->oDb->query($sql, $iPhotoId) !== false;
    }
	
    /**
     * Удаляет продукт
     *
     * @param   int   $nId - ID товара
     *
     * @return  bool
     */
	public function deleteProduct($nId) {
        $sql = "
            DELETE FROM " . Config::Get('db.table.minimarket_product') . "
            WHERE 
				product_id = ?d
        ";
        return $this->oDb->query($sql, $nId) !== false;
	}
	
}
?>