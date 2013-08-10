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
		$this->oMapper=Engine::GetMapper(__CLASS__);
	}
	
	public function AddTaxonomy(PluginMinimarket_ModuleTaxonomy_EntityTaxonomy $oTaxonomy) {
		return $this->oMapper->AddTaxonomy($oTaxonomy);
	}

	public function getTaxonomiesByType($sType) {
        return $this->oMapper->getTaxonomiesByType($sType);
	}
	
	public function GetTaxonomyById($nId) {
        return $this->oMapper->GetTaxonomyById($nId);
	}
	
	public function GetTaxonomiesByParentArray($aId) {
        return $this->oMapper->GetTaxonomiesByParentArray($aId);
	}
	
    /**
     * Возвращает список ID родителя по списку ID таксономий
     *
     * @param array $aTaxonomyId    Список ID таксономий
     *
     * @return array
     */
	public function GetArrayIdParentByArrayIdTaxonomy($aTaxonomyId) {
        if (!$aTaxonomyId) {
            return array();
        }
		if(!is_array($aTaxonomyId)) $aTaxonomyId=array($aTaxonomyId);
        return $this->oMapper->GetArrayIdParentByArrayIdTaxonomy($aTaxonomyId);
	}
	
	public function GetTaxonomiesByParentId($nId) {
        return $this->oMapper->GetTaxonomiesByParentId($nId);
	}
	
	public function GetTaxonomiesByParentIdAndType($nId,$sType) {
        return $this->oMapper->GetTaxonomiesByParentIdAndType($nId,$sType);
	}
	
	public function UpdateTaxonomy(PluginMinimarket_ModuleTaxonomy_EntityTaxonomy $oTaxonomy) {
        if ($this->oMapper->UpdateTaxonomy($oTaxonomy)) {
            return true;
        }
        return false;
	}
	
	public function GetAttributByURL($nURL) {
        return $this->oMapper->GetAttributByURL($nURL);
	}
	
	public function GetPropertyByURLAndParentId($nURL,$nId) {
        return $this->oMapper->GetPropertyByURLAndParentId($nURL,$nId);
	}
	
	public function GetTaxonomiesByURLAndType($nURL,$nType) {
        return $this->oMapper->GetTaxonomiesByURLAndType($nURL,$nType);
	}
	
	public function GetTaxonomyByURLAndParentId($nURL,$nId,$nType) {
        return $this->oMapper->GetTaxonomyByURLAndParentId($nURL,$nId,$nType);
	}
	
	public function GetPropertiesByAttributId($nId) {
        return $this->oMapper->GetPropertiesByAttributId($nId);
	}
	
	// получаем массив Id дочерних категорий по Id категории
	public function GetArrayIdChildrenCategoriesByIdCategory($nId) {
		$aCategories=$this->PluginMinimarket_Taxonomy_getTaxonomiesByType('category');
		$aTree=array();
		foreach($aCategories as $oCategory) {
			$aTree[$oCategory->getId()]=array(
				'parent'=>$oCategory->getParent()
			);
		}
		$data=$this->PluginMinimarket_Taxonomy_GetArrayIdChildrenCategoriesByIdCategoryAndArrayCategories($aTree,$nId);
		$data[]=$nId;
		return $data;
	}
	
	public function GetArrayIdChildrenCategoriesByIdCategoryAndArrayCategories($aTree,$nId,$data=null) {
		foreach($aTree as $key=>$val) {
			if($val['parent']==$nId) {
				$data[]=$key;
				$data=$this->PluginMinimarket_Taxonomy_GetArrayIdChildrenCategoriesByIdCategoryAndArrayCategories($aTree,$key,$data);
			}
		}
		return $data;
	}
	
	// получаем древовидный массив
	public function GetTreeCategories() {
        if($data=$this->PluginMinimarket_Taxonomy_getTaxonomiesByType('category')) {
			$aTree=array();
			foreach($data as $oData) {
				$aTree[$oData->getId()]=array(
					'parent'=>$oData->getParent(),
					'name'=>$oData->getName(),
					'url'=>$oData->getURL(),
				);
			}

			foreach($aTree as $menu_id=>$data) {
				$aTree[$data['parent']]['child'][$menu_id]=&$aTree[$menu_id];
			}
			$sorted=(array)$aTree[0]['child'];
			
			$sorted=$this->PluginMinimarket_Taxonomy_GetArrayByTree($sorted);
			
			return $sorted;
		}
	}
	
	// убираем вложенность у древовидного массива, но с указанием степени вложенности
	public function GetArrayByTree($tree,$data=null,$i=0) {
		foreach($tree as $key=>$val) {
			$data[$key]=array(
				'name'=>$val['name'],
				'url'=>$val['url'],
				'position'=>$i,
			);
			if(isset($tree[$key]['child'])) {
				$i++;
				$data=$this->PluginMinimarket_Taxonomy_GetArrayByTree($tree[$key]['child'],$data,$i);
				$i--;
			}
		}
		return $data;
	}
	
	public function DeleteTaxonomy(PluginMinimarket_ModuleTaxonomy_EntityTaxonomy $oTaxonomy) {
        if ($bResult = $this->oMapper->DeleteTaxonomy($oTaxonomy)) {
            return true;
        }
        return false;
	}
	
	public function DeleteTaxonomiesByArrayId($aId) {
        if ($bResult = $this->oMapper->DeleteTaxonomiesByArrayId($aId)) {
            return true;
        }
        return false;
	}
	
	public function DeleteAttribut(PluginMinimarket_ModuleTaxonomy_EntityTaxonomy $oAttribut) {
        if ($bResult = $this->oMapper->DeleteAttribut($oAttribut)) {
            return true;
        }
        return false;
	}
	
    public function GetCountTaxonomyByArrayIdAndArrayType($aId,$aType) {
        return $this->oMapper->GetCountTaxonomyByArrayIdAndArrayType($aId,$aType);
    }
	
    public function GetTaxonomiesByArrayId($aId) {
        return $this->oMapper->GetTaxonomiesByArrayId($aId);
    }
	
	public function GetChainsByArrayCategories($aCategories,$sType=null) {
		$aResult=array();
		foreach($aCategories as $key=>$oCategory) {
			if($oCategory) {
				$bOK=false;
				for( ; ; ) {
					if($sType=='url') {
						$aResult[$key][]=$oCategory->getURL();
					} else {
						$aResult[$key][]=$oCategory;
					}
					if($oCategory->getParent()==0) {
						$bOK=true;
					} else {
						$oCategory=$this->PluginMinimarket_Taxonomy_GetTaxonomyById($oCategory->getParent());
					}
					if($bOK===true) {
						$aResult[$key]=array_reverse($aResult[$key]);
						break;
					}
				}
			}
		}
		return $aResult;
	}

}
?>