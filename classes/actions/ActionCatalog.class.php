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

class PluginMinimarket_ActionCatalog extends ActionPlugin {

    public function Init() {
		$this->Viewer_AppendScript(Plugin::GetTemplatePath(__CLASS__).'js/template.js');
    }

	protected function RegisterEvent() {
		$this->AddEventPreg('/^/i', 'EventCatalog');
	}
	
	protected function EventCatalog() {
		$this->SetTemplateAction('catalog');
		$aParams=array();
		$aParams[]=Router::GetActionEvent();
		foreach(Router::GetParams() as $val) {
			$aParams[]=$val;
		}
		
		// если находимся на главной каталога
		if($aParams[0]=='') {
			$this->SetTemplateAction('catalog_main');
			$aCategories = $this->PluginMinimarket_Taxonomy_GetTaxonomiesByType('category');
			$this->Viewer_Assign('aCategories',$aCategories);
			$this->Viewer_Assign('noSidebar',true);
			$this->Viewer_AddHtmlTitle($this->Lang_Get('plugin.minimarket.catalog'));
		// по последнему параметру пытаемся получить продукт
		} elseif(false!==($oProduct=$this->PluginMinimarket_Product_getProductByURL($aParams[count($aParams)-1]))) {
			// получаем последовательность категорий текущего продукта
			$aCategoriesByProduct=$this->PluginMinimarket_Taxonomy_GetChainsByArrayCategories(array($this->PluginMinimarket_Taxonomy_GetTaxonomyById($oProduct->getCategory())));
			$aURLCategoriesByProduct=array();
			if(isset($aCategoriesByProduct[0])) {
				foreach($aCategoriesByProduct[0] as $oCategory) {
					$aURLCategoriesByProduct[]=$oCategory->getURL();
				}
				$this->Viewer_Assign('aCategoriesByProduct',$aCategoriesByProduct[0]);
			}
			$aParamsTmp=$aParams;
			unset($aParamsTmp[count($aParamsTmp)-1]);
			if($aURLCategoriesByProduct===$aParamsTmp) {
				$this->SetTemplateAction('product');
				
				$this->Viewer_Assign('oProduct',$oProduct);
				$this->Viewer_Assign('aPhotos',$this->PluginMinimarket_Product_getPhotosByProductId($oProduct->getId()));
				
				$aCharacteristics = $this->PluginMinimarket_Product_GetArrayProductTaxonomyByArrayProductIdAndType($oProduct->getId(),'characteristics');
				$this->Viewer_Assign('aCharacteristics',$aCharacteristics[$oProduct->getId()]);
				
				$aFeatures = $this->PluginMinimarket_Product_GetArrayProductTaxonomyByArrayProductIdAndType($oProduct->getId(),'features');
				$this->Viewer_Assign('aFeatures',$aFeatures[$oProduct->getId()]);
				
				// получим список идентификаторов Свойств, которые принадлежат данному товару
				$aIdProductPropertiesByProductId = $this->PluginMinimarket_Product_getArrayProductPropertyIdByArrayProductId($oProduct->getId());
				if(isset($aIdProductPropertiesByProductId[$oProduct->getId()])) {
					$aIdProductPropertiesByProductId = $aIdProductPropertiesByProductId[$oProduct->getId()];
					// по списку идентификаторов получаем Свойства
					$aPropertiesByProduct = $this->PluginMinimarket_Taxonomy_GetTaxonomiesByArrayId($aIdProductPropertiesByProductId);
					
					// получим идентификаторы атрибутов (в нашем случае -- это будет родитель Свойства)
					$aIdAttributByProduct = array();
					foreach($aPropertiesByProduct as $oProperty) {
						$aIdAttributByProduct[] = $oProperty->getParent();
					}
					// по списку идентификаторов получаем Атрибуты
					$aAttributesByProduct = $this->PluginMinimarket_Taxonomy_GetTaxonomiesByArrayId($aIdAttributByProduct);
					
					$aPropertiesByProduct = $this->PluginMinimarket_Product_createArrayPropertiesByArrayPropertiesAndAttributes(array_merge($aPropertiesByProduct, $aAttributesByProduct));
					
					$this->Viewer_Assign('aPropertiesByProduct',$aPropertiesByProduct);
					
					$this->Viewer_AddHtmlTitle($this->Lang_Get($oProduct->getName().' ('.$oProduct->getManufacturerCode().')'));
				}
			} else {
				return parent::EventNotFound();
			}			
		} else {
			// иначе пытаемся получить категорию
			$iPage=1;
			if(preg_match('(page([1-9]\d{0,5}))', $aParams[count($aParams)-1])) {
				$iPage=(int)str_replace('page','',$aParams[count($aParams)-1]);
				// если в УРЛ есть пагинация, то по ПРЕДпоследнему параметру пытаемся получить категорию
				$sURL=$aParams[count($aParams)-2];
				unset($aParams[count($aParams)-1]);
			} else {
				// иначе по последнему параметру пытаемся получить категорию
				$sURL=$aParams[count($aParams)-1];
			}
			$aCategories = $this->PluginMinimarket_Taxonomy_GetTaxonomiesByURLAndType($sURL,'category');
			// если есть такие категории, то получим их последовательности до родительской категории и сравним с нашей последовательностью категорий
			if(!empty($aCategories)) {
				$aChains=$this->PluginMinimarket_Taxonomy_GetChainsByArrayCategories($aCategories);
				$aChainsURL=array();
				foreach($aChains as $key=>$aCategoriesByChains) {
					foreach($aCategoriesByChains as $oCategory) {
						$aChainsURL['url'][$key][]=$oCategory->getURL();
						$aChainsURL['id'][$key][]=$oCategory->getId();
						$aChainsURL['name'][$key][]=$oCategory->getName();
					}
				}
				$bOK=false;
				// проверим, существует ли такая последовательность категорий
				foreach($aChainsURL['url'] as $iCategoryURL=>$aCategoryURL) {
					if($aParams===$aCategoryURL) {
						$bOK=true;
						break;
					}
				}
				if($bOK===true) {
					$aFilter=array();
					$aSortParams = $this->PluginMinimarket_Product_GetArraySortParams();
					if(isset($aSortParams['lover'])) {
						$aFilter['lover'] = explode('~',$aSortParams['lover']);
					} elseif(isset($aSortParams['pros'])) {
						$aFilter['pros'] = explode('~',$aSortParams['pros']);
					}
					// отбираем товары, которые принадлежат данной и родительской категории, а так же всем категориям-наследникам
					$aFilter['in_category']=$this->PluginMinimarket_Taxonomy_GetArrayIdChildrenCategoriesByIdCategory($aChainsURL['id'][$iCategoryURL][count($aChainsURL['id'][$iCategoryURL])-1]);
					// получаем товары по фильтру
					$aResult=$this->PluginMinimarket_Product_GetProductsByFilter($aFilter,$iPage,Config::Get('minimarket.product.per_page'));
					/**
					 * Формируем постраничность
					 */
					$sFullURL='';
					foreach($aParams as $sParam) {
						$sFullURL.=$sParam.'/';
					}
					$this->Viewer_AddHtmlTitle($this->Lang_Get('plugin.minimarket.catalog'));
					foreach($aChainsURL['name'][$iCategoryURL] as $sNameCategory) {
						$this->Viewer_AddHtmlTitle($this->Lang_Get($sNameCategory));
					}
					$sFullURL = Router::GetPath('catalog') . $sFullURL;
					$aPaging = $this->Viewer_MakePaging(
						$aResult['count'], $iPage, Config::Get('minimarket.product.per_page'), Config::Get('pagination.pages.count'),
						$sFullURL
					);
					
					if(isset($aSortParams['lover'])) {
						$aPaging['sGetParams'] = "?c[lover]=".$aSortParams['lover'];
					} elseif(isset($aSortParams['pros'])) {
						$aPaging['sGetParams']= "?c[pros]=".$aSortParams['pros'];
					} else {
						$aPaging['sGetParams']= "";
					}
					
					// добавляем виджет Фильтр
					$this->Viewer_AddWidget('right','filter',array('plugin'=>'minimarket'));
					// устанавливаем шаблон
					$this->SetTemplateAction('catalog');
					// загружаем нужные переменные в шаблон
					$this->Viewer_Assign('aPaging', $aPaging);
					$this->Viewer_Assign('aProducts',$aResult['collection']);
					$this->Viewer_Assign('aCategoriesChildren',$this->PluginMinimarket_Taxonomy_GetTaxonomiesByParentIdAndType($aChains[$iCategoryURL][count($aChains[$iCategoryURL])-1]->getId(),'category'));
					$this->Viewer_Assign('aCategoriesByProduct',$aChains[$iCategoryURL]);
					$this->Viewer_Assign('sURL',$sURL);
					$this->Viewer_Assign('sFullURL',$sFullURL);
					$this->Viewer_Assign('aSortParams',$aSortParams);
				} else {
					return parent::EventNotFound();
				}
			} else {
				return parent::EventNotFound();
			}
		}
	}

}
?>