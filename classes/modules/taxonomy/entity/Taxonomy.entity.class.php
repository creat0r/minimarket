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

class PluginMinimarket_ModuleTaxonomy_EntityTaxonomy extends Entity {

	public function setTaxonomyType($data){
		$this->_aData['taxonomy_type']=$data;
	}

	public function setParent($data){
		$this->_aData['parent_id']=$data;
	}

	public function setName($data){
		$this->_aData['taxonomy_name']=$data;
	}

	public function setURL($data){
		$this->_aData['taxonomy_url']=$data;
	}

	public function setDescription($data){
		$this->_aData['taxonomy_description']=$data;
	}
	
	public function getName() {
		return $this->_getDataOne('taxonomy_name');
	}
	
	public function getURL() {
		return $this->_getDataOne('taxonomy_url');
	}
	
	public function getDescription() {
		return $this->_getDataOne('taxonomy_description');
	}
	
	public function getId() {
		return $this->_getDataOne('taxonomy_id');
	}
	
	public function getParent() {
		return $this->_getDataOne('parent_id');
	}
	
	public function getTaxonomyType() {
		return $this->_getDataOne('taxonomy_type');
	}
	
}

// EOF