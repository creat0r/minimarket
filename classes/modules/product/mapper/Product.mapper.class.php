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
	
    /**
     * Добавление товара
     *
     * @param PluginMinimarket_ModuleProduct_EntityProduct $oProduct    Объект товара
     *
     * @return int|bool
     */
    public function AddProduct(PluginMinimarket_ModuleProduct_EntityProduct $oProduct) {
        $sql = "INSERT INTO " . Config::Get('db.table.minimarket_product') . "
			(`name`,
			`manufacturer_code`,
			`url`,
			`brand`,
			`category`,
			`price`,
			`currency`,
			`weight`,
			`show`,
			`in_stock`,
			`main_photo_id`,
			`text`
			)
			VALUES(?, ?, ?, ?d, ?d, ?, ?d, ?d, ?d, ?d, ?d, ?)
		";
        $iId = $this->oDb->query(
            $sql,
			$oProduct->getName(),
			$oProduct->getManufacturerCode(),
			$oProduct->getURL(),
			$oProduct->getBrand(),
			$oProduct->getCategory(),
			$oProduct->getPrice(),
			$oProduct->getCurrency(),
			$oProduct->getWeight(),
			$oProduct->getShow(),
			$oProduct->getInStock(),
			$oProduct->getMainPhotoId(),
			$oProduct->getText()
        );
        if ($iId) {
            return $iId;
        }
        return false;
    }
	
    /**
     * Обновление товара
     *
     * @param PluginMinimarket_ModuleProduct_EntityProduct $oProduct    Объект товара
     * 
     * return  bool
     */
    public function UpdateProduct(PluginMinimarket_ModuleProduct_EntityProduct $oProduct) {
		$sql = "UPDATE " . Config::Get('db.table.minimarket_product') . "
			SET 
				`name` = ?,
				`manufacturer_code` = ?,
				`url` = ?,
				`brand` = ?d,
				`category` = ?d,
				`price` = ?,
				`currency` = ?d,
				`weight` = ?d,
				`show` = ?d,
				`in_stock` = ?d,
				`main_photo_id` = ?d,
				`text` = ?
			WHERE
				`id` = ?d
		";
        $bResult = $this->oDb->query(
            $sql,
			$oProduct->getName(),
			$oProduct->getManufacturerCode(),
			$oProduct->getURL(),
			$oProduct->getBrand(),
			$oProduct->getCategory(),
			$oProduct->getPrice(),
			$oProduct->getCurrency(),
			$oProduct->getWeight(), 
			$oProduct->getShow(),
			$oProduct->getInStock(),
			$oProduct->getMainPhotoId(),
			$oProduct->getText(),
			$oProduct->getId()
        );
		return $bResult !== false;
	}

    /**
     * Возвращает "голый" товар по УРЛ
     *
     * @param int $sURL    УРЛ товара
     * 
     * return PluginMinimarket_Product_Product|bool
     */	
	public function GetProductByURL($sURL) {
        $sql = "SELECT
						*
					FROM
						" . Config::Get('db.table.minimarket_product') . "
					WHERE
						`url` = ?
					";
        if ($aRow = $this->oDb->selectRow($sql, $sURL)) {
            return Engine::GetEntity('PluginMinimarket_Product_Product', $aRow);
        }
        return false;
	}

    /**
     * Возвращает "голый" товар по ID
     *
     * @param int $iId    ID товара
     * 
     * return PluginMinimarket_Product_Product|bool
     */	
	public function GetProductById($iId) {
        $sql = "SELECT
						*
					FROM
						" . Config::Get('db.table.minimarket_product') . "
					WHERE
						`id` = ?d
					";
        if ($aRow = $this->oDb->selectRow($sql, $iId)) {
            return Engine::GetEntity('PluginMinimarket_Product_Product', $aRow);
        }
        return false;
	}

    /**
     * Возвращает "голые" товары по списку ID товаров
     *
     * @param array $aProductId    Список ID товаров
     * 
     * return array
     */	
	public function GetProductsByArrayId($aProductId) {
        if (!is_array($aProductId) || count($aProductId) == 0) return array();
        $sql = "SELECT
						*
					FROM
						" . Config::Get('db.table.minimarket_product') . "
					WHERE
						`id` IN (?a)
					ORDER BY `price` DESC
					";
        $aProduct = array();
        if ($aRows = $this->oDb->select($sql, $aProductId)) {
            foreach ($aRows as $aRow) {
                $aProduct[] = Engine::GetEntity('PluginMinimarket_Product_Product', $aRow);
            }
        }
        return $aProduct;
	}

    /**
     * Возвращает список таксономий продукта по типу таксономии
     *
     * @param string $sType    Тип таксономий
     *
     * @return array
     */
	public function GetProductTaxonomiesByType($sType) {
        $sql = "SELECT
						*
					FROM
						" . Config::Get('db.table.minimarket_product_taxonomy') . "
					WHERE
						`type` = ?
					";
        $aResult = array();
        if ($aRows = $this->oDb->select(
			$sql, 
			$sType
		)) {
            foreach ($aRows as $aRow) {
                $aResult[] = Engine::GetEntity('PluginMinimarket_Product_ProductTaxonomy', $aRow);
            }
        }
        return $aResult;
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
        if (!is_array($aProductId) || count($aProductId) == 0) {
            return array();
        }
        $sql = "SELECT
						*
					FROM
						" . Config::Get('db.table.minimarket_product_taxonomy') . "
					WHERE
						`product_id` IN (?a) 
						AND `type` = ?
					";
        $aResult = array();
        if ($aRows = $this->oDb->select(
				$sql, 
				$aProductId, 
				$sType
			)
		) {
            foreach ($aRows as $aRow) {
				$oProductTaxonomy = Engine::GetEntity('PluginMinimarket_Product_ProductTaxonomy', $aRow);
                $aResult[$oProductTaxonomy->getProductId()][] = $oProductTaxonomy;
            }
        }
        return $aResult;
	}

    /**
     * Список товаров по фильтру
     *
     * @param array $aFilter      Фильтр
     * @param int   $iCount       Возвращает общее число элементов
     * @param int   $iPage        Номер страницы
     * @param int   $iPerPage     Количество элементов на страницу
     *
     * @return array
     */
	public function GetProducts($aFilter, &$iCount, $iPage, $iPerPage) {
		$sWhere = $this->BuildFilter($aFilter);
		if(isset($aFilter['pros']) && !empty($aFilter['pros'])) {
			$sql = "SELECT
							p.`id`, COUNT(p.`id`) AS c
						FROM
							" . Config::Get('db.table.minimarket_product') . " as p,
							" . Config::Get('db.table.minimarket_link') . " as t
						WHERE
							1=1
							" . $sWhere . "
							AND p.`id` = t.`parent_id`
							AND t.`object_id` IN (?a)
						GROUP BY p.`id`
						HAVING c = ?
						LIMIT 
							?d, ?d
						";
			$aProducts=array();
			if ($aRows = $this->oDb->selectPage($iCount, $sql, $aFilter['pros'], count($aFilter['pros']), ($iPage - 1) * $iPerPage, $iPerPage)) {
				foreach ($aRows as $aProduct) {
					$aProducts[] = $aProduct['id'];
				}
			}
		} elseif(isset($aFilter['lover']) && !empty($aFilter['lover'])) {
			$sql = "SELECT
							p.`id`, COUNT(p.`id`) AS c
						FROM
							" . Config::Get('db.table.minimarket_product') . " as p,
							" . Config::Get('db.table.minimarket_product_taxonomy') . " as pt
						WHERE
							1=1
							" . $sWhere . "
							AND p.`id` = pt.`product_id`
							AND pt.`text` IN (?a)
							AND pt.`type` = ?
						GROUP BY p.`id`
						HAVING c = ?
						LIMIT 
							?d, ?d
						";
			$aProducts=array();
			if ($aRows = $this->oDb->selectPage(
					$iCount, 
					$sql, 
					$aFilter['lover'], 
					'features', 
					count($aFilter['lover']), 
					($iPage - 1) * $iPerPage, $iPerPage
				)
			) {
				foreach ($aRows as $aProduct) {
					$aProducts[] = $aProduct['id'];
				}
			}			
		} else {
			$sql = "SELECT
							`id`
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
					$aProducts[] = $aProduct['id'];
				}
			}
		}
		return $aProducts;
	}
	
    /**
     * Строит строку условий для SQL запроса товаров
     *
     * @param array $aFilter    Фильтр
     *
     * @return string
     */
	protected function BuildFilter($aFilter) {
		$sWhere = '';
        if (isset($aFilter['in_category'])) {
            if (!is_array($aFilter['in_category'])) {
                $aFilter['in_category'] = array($aFilter['in_category']);
            }
			$sPrefix = isset($aFilter['pros'])?"p.":"";
            $sWhere .= " AND ".$sPrefix."`category` IN ('" . join("','", $aFilter['in_category']) . "')";
        }
        if (isset($aFilter['currency'])) {
            if (!is_array($aFilter['currency'])) {
                $aFilter['currency'] = array($aFilter['currency']);
            }
            $sWhere .= " AND `currency` IN ('" . join("','", $aFilter['currency']) . "')";
        }
		return $sWhere;
	}
	
    /**
     * Добавление таксономии товара
     *
     * @param PluginMinimarket_ModuleProduct_EntityProductTaxonomy $oProductTaxonomy    Объект таксономии товара
     *
     * @return int|bool
     */
	public function AddProductTaxonomy(PluginMinimarket_ModuleProduct_EntityProductTaxonomy $oProductTaxonomy) {
        $sql = "INSERT INTO 
					" . Config::Get('db.table.minimarket_product_taxonomy') . "
					(`product_id`,
					`text`,
					`type`
					)
				VALUES (?d, ?, ?)
		";
        if ($iId = $this->oDb->query(
            $sql,
            $oProductTaxonomy->getProductId(),
            $oProductTaxonomy->getText(),
            $oProductTaxonomy->getType()
        )
        ) {
            $oProductTaxonomy->setId($iId);
            return $iId;
        }
        return false;
	}

    /**
     * Удаление таксономии товара по ID товара и типу таксономии
     *
     * @param int    $iId      ID товара
     * @param string $sType    Тип таксономии
     * 
     * @return bool
     */
	public function DeleteProductTaxonomyByProductIdAndType($iId, $sType) {
        $sql = "DELETE FROM 
					" . Config::Get('db.table.minimarket_product_taxonomy') . "
				WHERE
					`product_id` = ?d 
					AND `type` = ?
		";
        return $this->oDb->query(
			$sql, 
			$iId, 
			$sType
		) !== false;
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
		$sProductTaxonomy = mb_strtolower($sProductTaxonomy, 'UTF-8');
        $sql = "SELECT
				`text`
			FROM
				" . Config::Get('db.table.minimarket_product_taxonomy') . "
			WHERE
				`text` LIKE ? 
				AND `type` = ?
			GROUP BY 
				`text`
			LIMIT 0, ?d
			";
        $aReturn = array();
        if ($aRows = $this->oDb->select(
				$sql, 
				$sProductTaxonomy . '%', 
				$sType, 
				$iLimit
			)
		) {
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
        $sql = 'SELECT
					*
				FROM 
					' . Config::Get('db.table.minimarket_product_photo') . ' 
				WHERE 
					`target_tmp` = ?
				';
        $aRows = $this->oDb->select(
			$sql,
			$sTargetTmp
		);
        $aReturn = array();
        if (is_array($aRows) && count($aRows)) {
            foreach ($aRows as $aRow) {
                $aReturn[] = Engine::GetEntity('PluginMinimarket_Product_ProductPhoto', $aRow);
            }
        }
        return $aReturn;
    }
	
    /**
     * Обновление данных по изображению
     *
     * @param PluginMinimarket_ModuleProduct_EntityProductPhoto $oPhoto    Объект фото
     * 
     * @return  int|bool
     */
    public function UpdateProductPhoto($oPhoto) {
        if (!$oPhoto->getProductId() && !$oPhoto->getTargetTmp()) {
            return false;
        }
        if ($oPhoto->getProductId()) {
            $oPhoto->setTargetTmp = null;
        }
        $sql = 'UPDATE 
					' . Config::Get('db.table.minimarket_product_photo') . ' 
				SET
					`path` = ?, 
					`description` = ?, 
					`product_id` = ?d, 
					`target_tmp` = ?
				WHERE 
					`id` = ?d ';
        $bResult = $this->oDb->query(
            $sql,
			$oPhoto->getPath(),
			$oPhoto->getDescription(), 
			$oPhoto->getProductId(), 
			$oPhoto->getTargetTmp(),
            $oPhoto->getId()
        );
        return $bResult !== false;
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
    public function GetPhotosByProductId($iProductId, $iFromId, $iCount) {
        $sql = 'SELECT 
					* 
				FROM 
					' . Config::Get('db.table.minimarket_product_photo') . ' 
				WHERE 
					`product_id` = ?d 
					{AND `id` > ?d LIMIT 0, ?d}';
        $aPhotos = $this->oDb->select(
			$sql, 
			$iProductId, 
			($iFromId !== null) ? $iFromId : DBSIMPLE_SKIP,
			$iCount
		);
        $aReturn = array();
        if (is_array($aPhotos) && count($aPhotos)) {
            foreach ($aPhotos as $aPhoto) {
                $aReturn[] = Engine::GetEntity('PluginMinimarket_Product_ProductPhoto', $aPhoto);
            }
        }
        return $aReturn;
    }

    /**
     * Возвращает число изображений по временному ключу
     *
     * @param string $sTargetTmp    Временный ключ
     *
     * @return int
     */
    public function GetCountPhotosByTargetTmp($sTargetTmp) {
        $sql = 'SELECT 
					COUNT(`id`) 
				FROM 
					' . Config::Get('db.table.minimarket_product_photo') . ' 
				WHERE 
					`target_tmp` = ?';
        $aPhotosCount = $this->oDb->selectCol(
			$sql,
			$sTargetTmp
		);
        return $aPhotosCount[0];
    }
	
    /**
     * Возвращает число изображений по ID товара
     *
     * @param int $iProductId    ID товара
     *
     * @return int
     */
    public function GetCountPhotosByProductId($iProductId) {
        $sql = 'SELECT
					COUNT(`id`)
				FROM 
					' . Config::Get('db.table.minimarket_product_photo') . ' 
				WHERE 
					`product_id` = ?d';
        $aPhotosCount = $this->oDb->selectCol(
			$sql,
			$iProductId
		);
        return $aPhotosCount[0];
    }
	
    /**
     * Добавляет к продукту изображение
     *
     * @param PluginMinimarket_ModuleProduct_EntityProductPhoto $oPhoto    Объект фото
     *
     * @return bool|int
     */
    public function AddProductPhoto($oPhoto) {
        if (!$oPhoto->getProductId() && !$oPhoto->getTargetTmp()) {
            return false;
        }
        $sTargetType = $oPhoto->getProductId() ? 'product_id' : 'target_tmp';
        $iTargetId = ($sTargetType == 'product_id') ? $oPhoto->getProductId() : $oPhoto->getTargetTmp();
        $sql = 'INSERT INTO 
					' . Config::Get('db.table.minimarket_product_photo') . ' 
				SET
					`path` = ?, 
					`description` = ?, ?# = ?';
        return $this->oDb->query(
			$sql, 
			$oPhoto->getPath(), 
			$oPhoto->getDescription(), 
			$sTargetType, 
			$iTargetId
		);
    }
	
    /**
     * Возвращает список фотографий по списку ID фотографий
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
					`id` IN(?a)
				ORDER BY FIELD (`id`, ?a) ";
        $aReturn = array();
        if ($aRows = $this->oDb->select(
			$sql, 
			$aPhotoId, 
			$aPhotoId)
		) {
            foreach ($aRows as $aPhoto) {
                $aReturn[] = Engine::GetEntity('PluginMinimarket_Product_ProductPhoto', $aPhoto);
            }
        }
        return $aReturn;
    }
	
	/**
     * Удаление изображения по ID
     *
     * @param int $iPhotoId    ID фото
     */
    public function DeleteProductPhoto($iPhotoId) {
        $sql = "DELETE FROM 
					" . Config::Get('db.table.minimarket_product_photo') . 
				" WHERE  
					`id` = ?d";
        return $this->oDb->query(
			$sql, 
			$iPhotoId
		) !== false;
    }
	
    /**
     * Удаляет товар
     *
     * @param int $iId    ID товара
     *
     * @return bool
     */
	public function DeleteProduct($iId) {
        $sql = "
            DELETE FROM " . Config::Get('db.table.minimarket_product') . "
            WHERE 
				`id` = ?d
        ";
        return $this->oDb->query($sql, $iId) !== false;
	}
}
?>