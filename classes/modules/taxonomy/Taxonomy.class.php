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

class PluginMinimarket_ModuleTaxonomy extends Module {

	protected $oMapper;
	
	/**
	 * Инициализация модуля
	 */
	public function Init() {
		$this->oMapper = Engine::GetMapper(__CLASS__);
	}

    /**
     * Создание таксономии
     *
     * @param PluginMinimarket_ModuleTaxonomy_EntityTaxonomy $oTaxonomy    Объект таксономии
     *
     * @return int
     */
	public function AddTaxonomy(PluginMinimarket_ModuleTaxonomy_EntityTaxonomy $oTaxonomy) {
		return $this->oMapper->AddTaxonomy($oTaxonomy);
	}

    /**
     * Возвращает список таксономий по типу
     *
     * @param string $sType    Тип таксономии
     *
     * @return array
     */
	public function GetTaxonomiesByType($sType) {
        return $this->oMapper->GetTaxonomiesByType($sType);
	}

    /**
     * Возвращает таксономию по ID
     *
     * @param int $iId    ID таксономии
     *
     * @return PluginMinimarket_Taxonomy_Taxonomy|bool
     */
	public function GetTaxonomyById($iId) {
        return $this->oMapper->GetTaxonomyById($iId);
	}

    /**
     * Возвращает список таксономий по списку ID родителей
     *
     * @param array $aId    Список ID родителей
     *
     * @return array
     */
	public function GetTaxonomiesByParents($aId) {
        return $this->oMapper->GetTaxonomiesByParents($aId);
	}
	
    /**
     * Возвращает список ID родителей по списку ID таксономий
     *
     * @param array $aTaxonomyId    Список ID таксономий
     *
     * @return array
     */
	public function GetIdParentsByIdTaxonomies($aTaxonomyId) {
        if (!$aTaxonomyId) {
            return array();
        }
		if (!is_array($aTaxonomyId)) $aTaxonomyId = array($aTaxonomyId);
        return $this->oMapper->GetIdParentsByIdTaxonomies($aTaxonomyId);
	}

    /**
     * Возвращает список таксономий по ID родителя
     *
     * @param int $iId    ID родителя таксономии
     *
     * @return array
     */
	public function GetTaxonomiesByParentId($iId) {
        return $this->oMapper->GetTaxonomiesByParentId($iId);
	}

    /**
     * Возвращает список таксономий по ID родителя и типу таксономии
     *
     * @param int    $iId      ID родителя
     * @param string $sType    Тип таксономии
     *
     * @return array
     */
	public function GetTaxonomiesByParentIdAndType($nId, $sType) {
        return $this->oMapper->GetTaxonomiesByParentIdAndType($nId, $sType);
	}

    /**
     * Обновляет таксономию
     *
     * @param PluginMinimarket_ModuleTaxonomy_EntityTaxonomy $oTaxonomy    Объект таксономии
     *
     * @return bool
     */
	public function UpdateTaxonomy(PluginMinimarket_ModuleTaxonomy_EntityTaxonomy $oTaxonomy) {
        if ($this->oMapper->UpdateTaxonomy($oTaxonomy)) {
            return true;
        }
        return false;
	}

    /**
     * Возвращает список таксономий по фильтру
     *
     * @param array $aFilter    Фильтр выборки
     * @param array $aOrder     Сортировка
     *
     * @return array|bool
     */
    public function GetTaxonomiesByFilter($aFilter, $aOrder) {
        if ($aTaxonomy = $this->oMapper->GetTaxonomiesByFilter($aFilter, $aOrder)) {
            return $aTaxonomy;
        }
        return false;
    }

    /**
     * Возвращает список ID дочерних таксономий (заданного типа) по ID родительской таксономии
     *
     * @param int    $iId      ID родительской таксономии
     * @param string $sType    Тип таксономий, по которым необходимо производить поиск
     *
     * @return array
     */
	public function GetIdChildrenTaxonomiesByTypeAndIdParentTaxonomy($iId, $sType) {
		$aTaxonomy = $this->GetTaxonomiesByType($sType);
		$aTree = array();
		foreach($aTaxonomy as $oTaxonomy) {
			$aTree[$oTaxonomy->getId()] = array(
				'parent' => $oTaxonomy->getParentId()
			);
		}
		$data = $this->GetIdChildrenTaxonomiesByIdTaxonomyAndTaxonomies($aTree, $iId);
		return $data;
	}

    /**
     * Возвращает списком результат рекурсивного поиска по дереву (ищет дочерние элементы относительно заданного ID)
     *
     * @param array      $aTree    Дерево, по которому производится поиск
     * @param int        $iId      ID родителя
     * @param array|null $data     Результирующий список
     *
     * @return array
     */
	public function GetIdChildrenTaxonomiesByIdTaxonomyAndTaxonomies($aTree, $iId, $data = null) {
		foreach($aTree as $key => $val) {
			if($val['parent'] == $iId) {
				$data[] = $key;
				$data = $this->GetIdChildrenTaxonomiesByIdTaxonomyAndTaxonomies($aTree, $key, $data);
			}
		}
		return $data;
	}

    /**
     * Путем хитрых манипуляций возвращает древовидный список таксономий
     *
	 * @param string $sType    Тип таксономий, по которым будет строиться дерево
	 *
     * @return array
     */
	public function GetTreeTaxonomiesByType($sType) {
        if($aTaxonomy = $this->PluginMinimarket_Taxonomy_GetTaxonomiesByType($sType)) {
			$aTree = array();
			foreach($aTaxonomy as $oTaxonomy) {
				$aTree[$oTaxonomy->getId()] = array(
					'parent' => $oTaxonomy->getParentId(),
					'name' => $oTaxonomy->getName(),
					'url' => $oTaxonomy->getURL(),
				);
			}
			foreach($aTree as $key => $val) {
				$aTree[$val['parent']]['child'][$key] = &$aTree[$key];
			}
			return $this->PluginMinimarket_Taxonomy_GetArrayByTree((array)$aTree[0]['child']);
		}
		return false;
	}

    /**
     * Возврашает список таксономий, преобразованный из древовидного вида в одномерный с указанием вложенности
     *
     * @param array      $aTree    Древовидный список
     * @param array|null $data     Результирующий список
     * @param int|null   $i        Счетчик вложенности
     *
     * @return array
     */
	public function GetArrayByTree($aTree, $data = null, $i = 0) {
		foreach ($aTree as $key => $val) {
			$data[$key] = array(
				'name' => $val['name'],
				'url' => $val['url'],
				'position' => $i,
			);
			if (isset($aTree[$key]['child'])) {
				$i++;
				$data = $this->PluginMinimarket_Taxonomy_GetArrayByTree($aTree[$key]['child'], $data, $i);
				$i--;
			}
		}
		return $data;
	}

    /**
     * Удаляет таксономию по объекту
     *
     * @param PluginMinimarket_ModuleTaxonomy_EntityTaxonomy $oTaxonomy    Объект таксономии
     *
     * @return bool
     */
	public function DeleteTaxonomy(PluginMinimarket_ModuleTaxonomy_EntityTaxonomy $oTaxonomy) {
        if ($bResult = $this->oMapper->DeleteTaxonomy($oTaxonomy)) {
            return true;
        }
        return false;
	}

    /**
     * Удаляет таксономии по списку ID
     *
     * @param array $aId    Список ID таксономий
     *
     * @return bool
     */
	public function DeleteTaxonomiesByArrayId($aId) {
        if ($bResult = $this->oMapper->DeleteTaxonomiesByArrayId($aId)) {
            return true;
        }
        return false;
	}

    /**
     * Возвращает количество таксономий по списку ID и типов таксономий
     *
     * @param array $aId      Список ID таксономий
     * @param array $aType    Список типов таксономий
     *
     * @return int|bool
     */
    public function GetCountTaxonomyByArrayIdAndArrayType($aId, $aType) {
        return $this->oMapper->GetCountTaxonomyByArrayIdAndArrayType($aId, $aType);
    }

    /**
     * Возвращает список таксономий списку ID таксономий
     *
     * @param array $aId    Список ID таксономий
     *
     * @return array
     */
    public function GetTaxonomiesByArrayId($aId) {
        return $this->oMapper->GetTaxonomiesByArrayId($aId);
    }

    /**
     * Возвращает список таксономий в виде цепочек: от заданной до корневой (корневая -- у которой parent_id равен нулю)
     *
     * @param array       $aTaxonomy    Список таксономий
     * @param string|null $sProperty    Имя свойства таксономии, значение которого нужно вернуть
     *
     * @return array
     */
	public function GetChainsTaxonomiesByTaxonomies($aTaxonomy, $sProperty = null) {
		$aResult = array();
		foreach ($aTaxonomy as $key => $oTaxonomy) {
			if ($oTaxonomy) {
				$bOK = false;
				while (true) {
					switch ($sProperty) {
						case 'url':
							$aResult[$key][] = $oTaxonomy->getURL();
							break;
						default:
							$aResult[$key][] = $oTaxonomy;
					}
					if ($oTaxonomy->getParentId() == 0) {
						$bOK = true;
					} else {
						$oTaxonomy = $this->PluginMinimarket_Taxonomy_GetTaxonomyById($oTaxonomy->getParentId());
					}
					if ($bOK === true) {
						$aResult[$key] = array_reverse($aResult[$key]);
						break;
					}
				}
			}
		}
		return $aResult;
	}

}
?>