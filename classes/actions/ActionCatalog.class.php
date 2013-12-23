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
		$aParams = array();
		$aParams[] = Router::GetActionEvent();
		foreach (Router::GetParams() as $val) {
			$aParams[] = $val;
		}
		/**
		 * Если это главная страница каталога
		 */
		if ($aParams[0] == '') {
			$this->SetTemplateAction('catalog_main');
			$aCategories = $this->PluginMinimarket_Taxonomy_GetTaxonomiesByType('category');
			$this->Viewer_Assign('aCategories', $aCategories);
			$this->Viewer_Assign('noSidebar', true);
			$this->Viewer_AddHtmlTitle($this->Lang_Get('plugin.minimarket.catalog'));
			/**
			 * Попытка получить товар по последнему параметру УРЛ
			 */
		} elseif (false !== ($oProduct = $this->PluginMinimarket_Product_GetProductByURL($aParams[count($aParams) - 1]))) {
			/**
			 * Получение последовательности категорий текущего товара
			 */
			$aCategoriesByProduct = $this->PluginMinimarket_Taxonomy_GetChainsTaxonomiesByTaxonomies(array($this->PluginMinimarket_Taxonomy_GetTaxonomyById($oProduct->getCategory())));
			$aURLCategoriesByProduct = array();
			if (isset($aCategoriesByProduct[0])) {
				foreach ($aCategoriesByProduct[0] as $oCategory) {
					$aURLCategoriesByProduct[] = $oCategory->getURL();
				}
				$this->Viewer_Assign('aCategoriesByProduct', $aCategoriesByProduct[0]);
			}
			$aParamsTmp = $aParams;
			unset ($aParamsTmp[count($aParamsTmp) - 1]);
			if ($aURLCategoriesByProduct === $aParamsTmp) {
				$this->SetTemplateAction('product');
				/**
				 * Получение валюты и форматирование цены по маске валюты
				 */
				$oCurrency = $this->PluginMinimarket_Currency_GetCurrencyById($oProduct->getCurrency());
				$oProduct->setPriceCurrency(
					$this->PluginMinimarket_Currency_GetSumByFormat(
						$oProduct->getPrice() / Config::Get('plugin.minimarket.settings.factor'),
						$oCurrency->getDecimalPlaces(),
						$oCurrency->getFormat()
					)
				);
				$this->Viewer_Assign('oProduct', $oProduct);
				$this->Viewer_Assign('aPhotos', $this->PluginMinimarket_Product_GetPhotosByProductId($oProduct->getId()));
				$aCharacteristics = $this->PluginMinimarket_Product_GetProductTaxonomiesByArrayProductIdAndType($oProduct->getId(), 'characteristics');
				$this->Viewer_Assign('aCharacteristics', $aCharacteristics[$oProduct->getId()]);
				$aFeatures = $this->PluginMinimarket_Product_GetProductTaxonomiesByArrayProductIdAndType($oProduct->getId(), 'features');
				$this->Viewer_Assign('aFeatures', $aFeatures[$oProduct->getId()]);
				/**
				 * Получение списка идентификаторов свойств, которые принадлежат данному товару
				 */
				$aIdProductPropertiesByProductId = $this->PluginMinimarket_Link_GetLinksByParentsAndType($oProduct->getId(), 'product_property');
				if(isset($aIdProductPropertiesByProductId[$oProduct->getId()])) {
					$aIdProductPropertiesByProductId = $aIdProductPropertiesByProductId[$oProduct->getId()];
					/**
					 * Получение свойств по списку идентификаторов
					 */
					$aPropertiesByProduct = $this->PluginMinimarket_Taxonomy_GetTaxonomiesByArrayId($aIdProductPropertiesByProductId);					
					/**
					 * Получение идентификаторов атрибутов
					 */
					$aIdAttributByProduct = array();
					foreach($aPropertiesByProduct as $oProperty) {
						$aIdAttributByProduct[] = $oProperty->getParentId();
					}
					/**
					 * Получение Атрибутов по списку идентификаторов
					 */
					$aAttributesByProduct = $this->PluginMinimarket_Taxonomy_GetTaxonomiesByArrayId($aIdAttributByProduct);					
					$aPropertiesByProduct = $this->PluginMinimarket_Product_GetListProductPropertiesByPropertiesAndAttributes(array_merge($aPropertiesByProduct, $aAttributesByProduct));
					$this->Viewer_Assign('aPropertiesByProduct', $aPropertiesByProduct);
					$this->Viewer_AddHtmlTitle($this->Lang_Get($oProduct->getName() . ' (' . $oProduct->getManufacturerCode() . ')'));
				}
			} else {
				return parent::EventNotFound();
			}			
		} else {
			/**
			 * В противном случае, попытка получить категорию
			 */
			$iPage = 1;
			if (preg_match('(page([1-9]\d{0,5}))', $aParams[count($aParams) - 1])) {
				$iPage = (int)str_replace('page', '', $aParams[count($aParams) - 1]);
				/**
				 * Попытка по предпоследнему параметру получить категорию, если в УРЛ есть пагинация
				 */
				$sURL = $aParams[count($aParams) - 2];
				unset($aParams[count($aParams) - 1]);
			} else {
				/**
				 * Иначе, попытка получить категорию по последнему параметру
				 */
				$sURL = $aParams[count($aParams) - 1];
			}
			$aFilter = array(
				'url' => $sURL,
				'type' => 'category',
			);
			$aCategories = $this->PluginMinimarket_Taxonomy_GetTaxonomiesByFilter($aFilter, '');
			/**
			 * Получение последовательностей категорий до родительской, если такие категории существуют. Сравнение с текущей последовательностью категорий
			 */
			if (!empty($aCategories)) {
				$aChains = $this->PluginMinimarket_Taxonomy_GetChainsTaxonomiesByTaxonomies($aCategories);
				$aChainsURL = array();
				foreach ($aChains as $key => $aCategoriesByChains) {
					foreach ($aCategoriesByChains as $oCategory) {
						$aChainsURL['url'][$key][] = $oCategory->getURL();
						$aChainsURL['id'][$key][] = $oCategory->getId();
						$aChainsURL['name'][$key][] = $oCategory->getName();
					}
				}
				$bOK = false;
				/**
				 * Проверка на существование такой последовательности категории
				 */
				foreach ($aChainsURL['url'] as $iCategoryURL => $aCategoryURL) {
					if ($aParams === $aCategoryURL) {
						$bOK = true;
						break;
					}
				}
				if ($bOK===true) {
					$aFilter = array();
					$aSortParams = $this->PluginMinimarket_Product_GetArraySortParams();
					if (isset($aSortParams['lover'])) {
						$aFilter['lover'] = explode('~', $aSortParams['lover']);
					} elseif (isset($aSortParams['pros'])) {
						$aFilter['pros'] = explode('~', $aSortParams['pros']);
					}
					/**
					 * Получение списка ID дочерних категорий, по которым необходимо получить товары
					 */
					$iIdCategory = array();
					$iIdCategory = $aChainsURL['id'][$iCategoryURL][count($aChainsURL['id'][$iCategoryURL]) - 1];
					$aFilter['in_category'] = $this->PluginMinimarket_Taxonomy_GetIdChildrenTaxonomiesByTypeAndIdParentTaxonomy($iIdCategory, 'category');
					/**
					 * Добавление ID текущей категории в полученный список
					 */
					$aFilter['in_category'][] = $iIdCategory;
					/**
					 * Получение товаров по фильтру
					 */
					$aResult = $this->PluginMinimarket_Product_GetProductsByFilter($aFilter, $iPage, Config::Get('plugin.minimarket.product.per_page'));
					/**
					 * Формирование постраничности
					 */
					$sFullURL = '';
					foreach ($aParams as $sParam) {
						$sFullURL .= $sParam . '/';
					}
					$this->Viewer_AddHtmlTitle($this->Lang_Get('plugin.minimarket.catalog'));
					foreach ($aChainsURL['name'][$iCategoryURL] as $sNameCategory) {
						$this->Viewer_AddHtmlTitle($this->Lang_Get($sNameCategory));
					}
					$sFullURL = Router::GetPath('catalog') . $sFullURL;
					$aPaging = $this->Viewer_MakePaging(
						$aResult['count'], $iPage, Config::Get('plugin.minimarket.product.per_page'), Config::Get('pagination.pages.count'),
						$sFullURL
					);
					if(isset($aSortParams['lover'])) {
						$aPaging['sGetParams'] = "?c[lover]=" . $aSortParams['lover'];
					} elseif(isset($aSortParams['pros'])) {
						$aPaging['sGetParams'] = "?c[pros]=" . $aSortParams['pros'];
					} else {
						$aPaging['sGetParams'] = "";
					}
					/**
					 * Добавление виджет "Фильтр"
					 */
					$this->Viewer_AddWidget('right', 'filter', array('plugin' => 'minimarket'));
					/**
					 * Установка шаблона
					 */
					$this->SetTemplateAction('catalog');
					/**
					 * Загрузка переменных в шаблон
					 */
					$this->Viewer_Assign('aPaging', $aPaging);
					$this->Viewer_Assign('aProducts', $aResult['collection']);
					$this->Viewer_Assign('aCategoriesChildren', $this->PluginMinimarket_Taxonomy_GetTaxonomiesByParentIdAndType($aChains[$iCategoryURL][count($aChains[$iCategoryURL]) - 1]->getId(), 'category'));
					$this->Viewer_Assign('aCategoriesByProduct', $aChains[$iCategoryURL]);
					$this->Viewer_Assign('sURL', $sURL);
					$this->Viewer_Assign('sFullURL', $sFullURL);
					$this->Viewer_Assign('aSortParams', $aSortParams);
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