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

class PluginMinimarket_ModuleProduct_EntityProductPhoto extends Entity {

	/**
	 * Возвращает полный веб путь до фото определенного размера
	 *
	 * @param string|null $sWidth    Размер фото. Например: '100', '150crop' и т.п.
	 * 
	 * @return null|string
	 */
	public function getProductPhotoWebPath($sWidth = null) {
		if ($this->getPath()) {
			if ($sWidth) {
				$aPathInfo = pathinfo ($this->getPath());
				return $aPathInfo['dirname'] . '/' . $aPathInfo['filename'] . '_' . $sWidth . '.' . $aPathInfo['extension'];
			} else {
				return $this->getPath();
			}
		} else {
			return null;
		}
	}
}