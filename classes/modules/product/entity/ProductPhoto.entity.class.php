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
	 * ���������� ������ ��� ���� �� ���� ������������� �������
	 *
	 * @param string|null $sWidth	������ ����, ��������, '100' ��� '150crop'
	 * @return null|string
	 */
	public function getProductPhotoWebPath($sWidth = null) {
		if ($this->getProductPhotoPath()) {
			if ($sWidth) {
				$aPathInfo=pathinfo($this->getProductPhotoPath());
				return $aPathInfo['dirname'].'/'.$aPathInfo['filename'].'_'.$sWidth.'.'.$aPathInfo['extension'];
			} else {
				return $this->getProductPhotoPath();
			}
		} else {
			return null;
		}
	}

}