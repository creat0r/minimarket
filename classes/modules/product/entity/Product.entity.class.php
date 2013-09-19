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

class PluginMinimarket_ModuleProduct_EntityProduct extends Entity {

    /**
     * Определяем правила валидации
     */
    public function Init() {
        parent::Init();
        $this->aValidateRules[] = array(
            'product_name', 'string', 'max' => 200, 'min' => 2,
            'allowEmpty' => false,
            'label' => $this->Lang_Get('plugin.minimarket.product_create_title'),
            'on' => array('product')
        );
        $this->aValidateRules[] = array(
            'product_url', 'product_url',
            'on' => array('product')
        );
        $this->aValidateRules[] = array(
            'product_brand', 'product_brand',
            'on' => array('product')
        );
        $this->aValidateRules[] = array(
            'product_category', 'product_category',
            'on' => array('product')
        );
        $this->aValidateRules[] = array(
            'product_attribut_and_property', 'product_attribut_and_property',
            'on' => array('product')
        );
        $this->aValidateRules[] = array(
            'product_manufacturer_code', 'string', 'max' => 200,
			'label' => $this->Lang_Get('plugin.minimarket.product_create_manufacturer_code'),
            'on' => array('product')
        );
        $this->aValidateRules[] = array(
            'product_price', 'number', 'max' => 10000, 'min' => 0,
			'allowEmpty' => false,
			'label' => $this->Lang_Get('plugin.minimarket.product_create_price'),
            'on' => array('product')
        );
        $this->aValidateRules[] = array(
            'product_weight', 'number', 'max' => 1000, 'min' => 0,
			'allowEmpty' => false,
			'label' => $this->Lang_Get('plugin.minimarket.product_create_weight'),
            'on' => array('product')
        );
        $this->aValidateRules[] = array(
            'product_show', 'boolean',
			'label' => $this->Lang_Get('plugin.minimarket.product_create_show'),
            'on' => array('product')
        );
        $this->aValidateRules[] = array(
            'product_in_stock', 'boolean',
			'label' => $this->Lang_Get('plugin.minimarket.product_create_in_stock'),
            'on' => array('product')
        );
        $this->aValidateRules[] = array(
            'product_characteristics', 'tags', 'count' => 50,
            'allowEmpty' => false,
            'label' => $this->Lang_Get('plugin.minimarket.product_create_characteristics'),
            'on' => array('product')
        );
        $this->aValidateRules[] = array(
            'product_features', 'tags', 'count' => 50,
            'allowEmpty' => false,
            'label' => $this->Lang_Get('plugin.minimarket.product_create_features'),
            'on' => array('product')
        );
        $this->aValidateRules[] = array(
            'product_text', 'string', 'max' => 5000,
            'label' => $this->Lang_Get('plugin.minimarket.product_create_text'),
            'on' => array('product')
        );
	}
	
    public function ValidateProductAttributAndProperty($aValue, $aParams) {
		// все ли пришедшие к нам значения являются атрибутами и свойствами
        if(empty($aValue) || count($aValue)==$this->PluginMinimarket_Taxonomy_GetCountTaxonomyByArrayIdAndArrayType($aValue,array('attribut','property'))) {
            return true;
        }
        return $this->Lang_Get('system_error');
    }
	
    public function ValidateProductCategory($sValue, $aParams) {
        if ($sValue==0 || (false!==($oCategory=$this->PluginMinimarket_Taxonomy_GetTaxonomyById((int)$sValue)) && $oCategory->getType()=='category')) {
            return true;
        }
        return $this->Lang_Get('plugin.minimarket.product_create_category_error');
    }
	
    public function ValidateProductBrand($sValue, $aParams) {
        if ($sValue==0 || (false!==($oBrand=$this->PluginMinimarket_Taxonomy_GetTaxonomyById((int)$sValue)) && $oBrand->getType()=='brand')) {
            return true;
        }
        return $this->Lang_Get('plugin.minimarket.product_create_brand_error');
    }
	
    public function ValidateProductUrl($sValue, $aParams) {
        if (func_check($sValue, 'login', 2, 200)) {
            return true;
        }
        return $this->Lang_Get('plugin.minimarket.product_create_url_error');
    }

	public function setId($data){
		$this->_aData['product_id']=$data;
	}

	public function setMainPhotoId($data){
		$this->_aData['product_main_photo_id']=$data;
	}

	public function setProductProperties($data){
		$this->_aData['product_properties']=$data;
	}

	public function setName($data){
		$this->_aData['product_name']=$data;
	}

	public function setURL($data){
		$this->_aData['product_url']=$data;
	}

	public function setBrand($data){
		$this->_aData['product_brand']=$data;
	}

	public function setCategory($data){
		$this->_aData['product_category']=$data;
	}

	public function setCharacteristics($data){
		$this->_aData['product_characteristics']=$data;
	}

	public function setFeatures($data){
		$this->_aData['product_features']=$data;
	}

	public function setText($data){
		$this->_aData['product_text']=$data;
	}

	public function setAttributAndProperty($data){
		$this->_aData['product_attribut_and_property']=$data;
	}

	public function setPrice($data){
		$this->_aData['product_price']=$data;
	}

	public function setWeight($data){
		$this->_aData['product_weight']=$data;
	}

	public function setShow($data){
		$this->_aData['product_show']=$data;
	}

	public function setInStock($data){
		$this->_aData['product_in_stock']=$data;
	}

	public function setManufacturerCode($data){
		$this->_aData['product_manufacturer_code']=$data;
	}
	
	public function getName() {
		return $this->_getDataOne('product_name');
	}
	
	public function getURL() {
		return $this->_getDataOne('product_url');
	}
	
	public function getBrand() {
		return $this->_getDataOne('product_brand');
	}
	
	public function getCategory() {
		return $this->_getDataOne('product_category');
	}
	
	public function getCharacteristics() {
		return $this->_getDataOne('product_characteristics');
	}
	
	public function getFeatures() {
		return $this->_getDataOne('product_features');
	}
	
	public function getText() {
		return $this->_getDataOne('product_text');
	}
	
	public function getId() {
		return $this->_getDataOne('product_id');
	}
	
	public function getMainPhotoId(){
		return $this->_getDataOne('product_main_photo_id');
	}
	
	public function getProductProperties(){
		return $this->_getDataOne('product_properties');
	}
	
	public function getAttributAndProperty(){
		return $this->_getDataOne('product_attribut_and_property');
	}
	
	public function getPrice(){
		return $this->_getDataOne('product_price');
	}
	
	public function getWeight(){
		return $this->_getDataOne('product_weight');
	}
	
	public function getShow(){
		return $this->_getDataOne('product_show');
	}
	
	public function getInStock(){
		return $this->_getDataOne('product_in_stock');
	}
	
	public function getManufacturerCode(){
		return $this->_getDataOne('product_manufacturer_code');
	}
		
	// этот метод нужно удалить, и заменить его (везде где он используется) на PluginMinimarket_Product_GetArrayWebPathToProductByProducts
	public function getWebPathBuilder(){
		$aCategoriesByProduct=$this->PluginMinimarket_Taxonomy_GetChainsByArrayCategories(array($this->PluginMinimarket_Taxonomy_GetTaxonomyById($this->getCategory())));
		$sWebPathProduct=Config::Get('path.root.url').'catalog/';
		if(isset($aCategoriesByProduct[0])) {
			foreach($aCategoriesByProduct[0] as $oCategory) {
				$sWebPathProduct.=$oCategory->getURL().'/';
			}
		}
		return $sWebPathProduct.$this->getURL().'/';
	}
	
}
?>