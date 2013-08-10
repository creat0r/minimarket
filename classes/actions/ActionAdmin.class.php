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

class PluginMinimarket_ActionAdmin extends PluginMinimarket_Inherits_ActionAdmin {

	/**
	 * Регистрируем евенты
	 *
	 */
	protected function RegisterEvent() {
		$this->AddEvent('attributes','EventAttributes');
		$this->AddEvent('attributadd','EventAttributadd');
		$this->AddEvent('attributedit','EventAttributedit');
		$this->AddEvent('attributdelete','EventAttributdelete');
		
		$this->AddEvent('attributescategories','EventAttributescategories');
		$this->AddEvent('attributescategoryadd','EventAttributescategoryadd');
		$this->AddEvent('attributescategoryedit','EventAttributescategoryedit');
		$this->AddEvent('attributescategorydelete','EventAttributescategorydelete');
		
		$this->AddEvent('propertyadd','EventPropertyadd');
		$this->AddEvent('propertyedit','EventPropertyedit');
		$this->AddEvent('propertydelete','EventPropertydelete');
		
		$this->AddEvent('mm_categories','EventMmcategories');
		$this->AddEvent('mm_category_add','EventMmcategoryadd');
		$this->AddEvent('mm_category_edit','EventMmcategoryedit');
		$this->AddEvent('mm_category_delete','EventMmcategorydelete');
		
		$this->AddEvent('mm_brands','EventMmbrands');
		$this->AddEvent('mm_brand_add','EventMmbrandadd');
		$this->AddEvent('mm_brand_edit','EventMmcbrandedit');
		$this->AddEvent('mm_brand_delete','EventMmbranddelete');
		
		$this->AddEvent('ajaxchangeordertaxonomies', 'EventAjaxChangeOrderTaxonomies');
		
		parent::RegisterEvent();
	}

    public function EventAttributescategoryedit() {
        /**
         * Устанавливаем шаблон вывода
         */
        $this->_setTitle($this->Lang_Get('plugin.minimarket.attributes_category_edit_title'));
        $this->SetTemplateAction('attributescategoryadd');	
        /**
         * Получаем аттрибут
         */
        if (!($oAttributesCategory=$this->PluginMinimarket_Taxonomy_GetTaxonomyById($this->GetParam(0))) || $oAttributesCategory->getTaxonomyType()!='attributes_category') {
            return parent::EventNotFound();
        }
        $this->Viewer_Assign('oAttributesCategory', $oAttributesCategory);
        $this->Viewer_Assign('aAttributes', $this->PluginMinimarket_Taxonomy_GetTaxonomiesByType('attribut'));
		
        /**
         * Проверяем отправлена ли форма с данными
         */
        if (isset($_REQUEST['submit_attributes_category_add'])) {
            /**
             * Обрабатываем отправку формы
             */
            return $this->SubmitAttributesCategoryEdit($oAttributesCategory);
        } else {
            $_REQUEST['adding_attributes_category_name'] = $oAttributesCategory->getName();
        }
	}
	
	protected function SubmitAttributesCategoryEdit($oAttributesCategory) {
        /**
         * Проверка корректности полей формы
         */
        if (!$this->CheckAttributesCategoryFields('edit')) {
            return false;
        }
		
        $oAttributesCategory->setTaxonomyConfig(serialize(getRequest('attribut_sel')));
        $oAttributesCategory->setName(getRequest('adding_attributes_category_name'));

        if ($this->PluginMinimarket_Taxonomy_UpdateTaxonomy($oAttributesCategory)) {
            Router::Location('admin/attributescategories/?edit=success');
        }
	}
	
    public function EventAttributescategorydelete() {
        $this->Security_ValidateSendForm();
		
        if (false!==($oAttributesCategory = $this->PluginMinimarket_Taxonomy_GetTaxonomyById($this->GetParam(0))) && $oAttributesCategory->getTaxonomyType()=='attributes_category') {
			$this->PluginMinimarket_Taxonomy_DeleteAttribut($oAttributesCategory);
			Router::Location('admin/attributescategories/');
        } else {
			return parent::EventNotFound();
		}
	}
	
    public function EventAttributescategoryadd() {
		$this->_setTitle($this->Lang_Get('plugin.minimarket.adding_attributes_category'));
        /**
         * Обрабатываем отправку формы
         */
        return $this->SubmitAttributesCategoryAdd();
	}
	
	protected function SubmitAttributesCategoryAdd() {
        /**
         * Проверяем отправлена ли форма с данными
         */
        if (!isPost('submit_attributes_category_add')) {
            return false;
        }
		
        /**
         * Проверка корректности полей формы
         */
        if (!$this->CheckAttributesCategoryFields()) {
            return false;
        }
		
        $oAttributesCategory = Engine::GetEntity('PluginMinimarket_ModuleTaxonomy_EntityTaxonomy');
        $oAttributesCategory->setTaxonomyType('attributes_category');
        $oAttributesCategory->setName(getRequest('adding_attributes_category_name'));

        if ($sId = $this->PluginMinimarket_Taxonomy_AddTaxonomy($oAttributesCategory)) {
            Router::Location('admin/attributescategoryedit/'.$sId.'/');
        }
	}
	
    public function EventAttributescategories() {
		$this->_setTitle($this->Lang_Get('plugin.minimarket.attributes_category'));
		
        /*
         * Получаем список
         */
        $this->Viewer_Assign('aAttributesCategory', $this->PluginMinimarket_Taxonomy_getTaxonomiesByType('attributes_category'));
		
		if (getRequest('add')) $this->Message_AddNoticeSingle($this->Lang_Get('plugin.minimarket.adding_attribut_category_ok'));
		if (getRequest('edit')) $this->Message_AddNoticeSingle($this->Lang_Get('plugin.minimarket.adding_attribut_category_edit'));
	}
	
    public function EventAjaxChangeOrderTaxonomies() {
        /**
         * Устанавливаем формат ответа
         */
        $this->Viewer_SetResponseAjax('json');

        if (!$this->User_IsAuthorization()) {
            $this->Message_AddErrorSingle($this->Lang_Get('need_authorization'), $this->Lang_Get('error'));
            return;
        }
        if (!$this->oUserCurrent->isAdministrator()) {
            $this->Message_AddErrorSingle($this->Lang_Get('need_authorization'), $this->Lang_Get('error'));
            return;
        }
        if (!getRequest('order')) {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error'), $this->Lang_Get('error'));
            return;
        }


        if (is_array(getRequest('order'))) {

            foreach (getRequest('order') as $oOrder) {
                if (is_numeric($oOrder['order']) && is_numeric($oOrder['id']) && $oTaxonomy = $this->PluginMinimarket_Taxonomy_GetTaxonomyById($oOrder['id'])) {
                    $oTaxonomy->setTaxonomySort($oOrder['order']);
                    $this->PluginMinimarket_Taxonomy_UpdateTaxonomy($oTaxonomy);
                }
            }

            $this->Message_AddNoticeSingle($this->Lang_Get('action.admin.save_sort_success'));
            return;
        } else {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error'), $this->Lang_Get('error'));
            return;
        }

    }
	
	protected function EventMmbranddelete() {
        $this->Security_ValidateSendForm();
		
        if (!($oBrand = $this->PluginMinimarket_Taxonomy_GetTaxonomyById($this->GetParam(0))) || $oBrand->getTaxonomyType()!='brand') {
            return parent::EventNotFound();
        }

        $this->PluginMinimarket_Taxonomy_DeleteTaxonomy($oBrand);
        Router::Location('admin/mm_brands/');
	}
	
	protected function EventMmcbrandedit() {
        /**
         * Устанавливаем шаблон вывода
         */
        $this->_setTitle($this->Lang_Get('plugin.minimarket.brand_edit_title'));
        $this->SetTemplateAction('brandadd');
        /**
         * Получаем свойство аттрибута
         */
        if (!($oBrand = $this->PluginMinimarket_Taxonomy_GetTaxonomyById($this->GetParam(0))) || $oBrand->getTaxonomyType()!='brand') {
            return parent::EventNotFound();
        }
        $this->Viewer_Assign('oBrand', $oBrand);

        /**
         * Проверяем отправлена ли форма с данными
         */
        if (isset($_REQUEST['submit_brand_add'])) {
            /**
             * Обрабатываем отправку формы
             */
            return $this->SubmitBrandEdit($oBrand);
        } else {
            $_REQUEST['brand_name'] = $oBrand->getName();
            $_REQUEST['brand_url'] = $oBrand->getURL();
            $_REQUEST['brand_description'] = $oBrand->getDescription();
        }	
	}
	
	protected function SubmitBrandEdit($oBrand) {
        /**
         * Проверяем отправлена ли форма с данными
         */
        if (!isPost('submit_brand_add')) {
            return false;
        }
		
        /**
         * Проверка корректности полей формы
         */
		
        if (!$this->CheckBrandFields($oBrand->getURL())) {
            return false;
        }
        $oBrand->setName(getRequest('brand_name'));
        $oBrand->setURL(getRequest('brand_url'));
        $oBrand->setParent(0);
        $oBrand->setDescription(getRequest('brand_description'));
        if ($this->PluginMinimarket_Taxonomy_UpdateTaxonomy($oBrand)) {
            Router::Location('admin/mm_brands/?edit=success');
        }
	}

	protected function EventMmbrandadd() {
        /**
         * Устанавливаем шаблон вывода
         */
        $this->_setTitle($this->Lang_Get('plugin.minimarket.brand_add_title'));
        $this->SetTemplateAction('brandadd');

        /**
         * Обрабатываем отправку формы
         */
        return $this->SubmitBrandAdd();
	}

	protected function SubmitBrandAdd() {
        /**
         * Проверяем отправлена ли форма с данными
         */
        if (!isPost('submit_brand_add')) {
            return false;
        }
		
        /**
         * Проверка корректности полей формы
         */
        if (!$this->CheckBrandFields()) {
            return false;
        }
		
        $oBrand = Engine::GetEntity('PluginMinimarket_ModuleTaxonomy_EntityTaxonomy');
        $oBrand->setParent(0);
        $oBrand->setTaxonomyType('brand');
        $oBrand->setName(getRequest('brand_name'));
        $oBrand->setURL(getRequest('brand_url'));
        $oBrand->setDescription(getRequest('brand_description'));

        if ($this->PluginMinimarket_Taxonomy_AddTaxonomy($oBrand)) {
            Router::Location('admin/mm_brands/?add=success');
        }
	}
	
	protected function EventMmbrands() {
        /**
         * Устанавливаем шаблон вывода
         */
        $this->_setTitle($this->Lang_Get('plugin.minimarket.brands'));
        $this->SetTemplateAction('brands');
		
        /*
         * Получаем список
         */
        $this->Viewer_Assign('aBrands', $this->PluginMinimarket_Taxonomy_getTaxonomiesByType('brand'));
		
		if (getRequest('add')) $this->Message_AddNoticeSingle($this->Lang_Get('plugin.minimarket.brans_add_ok'));
		if (getRequest('edit')) $this->Message_AddNoticeSingle($this->Lang_Get('plugin.minimarket.brans_edit_ok'));
	}
	
	protected function EventMmcategorydelete() {
        $this->Security_ValidateSendForm();
		
        if (!($oCategory = $this->PluginMinimarket_Taxonomy_GetTaxonomyById($this->GetParam(0))) || $oCategory->getTaxonomyType()!='category') {
            return parent::EventNotFound();
        }
		
		$aChildrenId=$this->PluginMinimarket_Taxonomy_GetArrayIdChildrenCategoriesByIdCategory($this->GetParam(0));
		
        $this->PluginMinimarket_Taxonomy_DeleteTaxonomiesByArrayId($aChildrenId);
        Router::Location('admin/mm_categories/');
	}
	
	protected function EventMmcategoryedit() {
        /**
         * Устанавливаем шаблон вывода
         */
        $this->_setTitle($this->Lang_Get('plugin.minimarket.category_edit_title'));
        $this->SetTemplateAction('categoryadd');	
        /**
         * Получаем свойство аттрибута
         */
        if (!($oCategory = $this->PluginMinimarket_Taxonomy_GetTaxonomyById($this->GetParam(0))) || $oCategory->getTaxonomyType()!='category') {
            return parent::EventNotFound();
        }
        $this->Viewer_Assign('oCategory', $oCategory);
		$this->Viewer_Assign('aCategories', $this->PluginMinimarket_Taxonomy_GetTreeCategories());

        /**
         * Проверяем отправлена ли форма с данными
         */
        if (isset($_REQUEST['submit_category_add'])) {
            /**
             * Обрабатываем отправку формы
             */
            return $this->SubmitCategoryEdit($oCategory);
        } else {
            $_REQUEST['category_name'] = $oCategory->getName();
            $_REQUEST['category_url'] = $oCategory->getURL();
            $_REQUEST['category_select'] = $oCategory->getParent();
            $_REQUEST['category_description'] = $oCategory->getDescription();
        }	
	}
	
	protected function SubmitCategoryEdit($oCategory) {
        /**
         * Проверяем отправлена ли форма с данными
         */
        if (!isPost('submit_category_add')) {
            return false;
        }
		
        /**
         * Проверка корректности полей формы
         */
        if (!$this->CheckMMCategoryFields($oCategory->getURL(),$oCategory->getParent(),$oCategory->getId())) {
            return false;
        }
        $oCategory->setName(getRequest('category_name'));
        $oCategory->setURL(getRequest('category_url'));
        $oCategory->setParent(getRequest('category_select'));
        $oCategory->setDescription(getRequest('category_description'));
        if ($this->PluginMinimarket_Taxonomy_UpdateTaxonomy($oCategory)) {
            Router::Location('admin/mm_categories/?edit=success');
        }
	}
	
	protected function EventMmcategoryadd() {
        /**
         * Устанавливаем шаблон вывода
         */
        $this->_setTitle($this->Lang_Get('plugin.minimarket.category_add_title'));
        $this->SetTemplateAction('categoryadd');
		
		$this->Viewer_Assign('aCategories', $this->PluginMinimarket_Taxonomy_GetTreeCategories());
		
        /**
         * Обрабатываем отправку формы
         */
        return $this->SubmitCategoryAdd();
	}
	
	protected function EventMmcategories() {
        /**
         * Устанавливаем шаблон вывода
         */
        $this->_setTitle($this->Lang_Get('plugin.minimarket.categories'));
        $this->SetTemplateAction('categories');
		
        /*
         * Получаем список
         */
        $this->Viewer_Assign('aCategories', $this->PluginMinimarket_Taxonomy_GetTreeCategories());
		
		if (getRequest('add')) $this->Message_AddNoticeSingle($this->Lang_Get('plugin.minimarket.category_add_ok'));
		if (getRequest('edit')) $this->Message_AddNoticeSingle($this->Lang_Get('plugin.minimarket.category_edit_ok'));
	}
	
	protected function EventAttributdelete() {
        $this->Security_ValidateSendForm();
		
        if (!$oAttribut = $this->PluginMinimarket_Taxonomy_GetTaxonomyById($this->GetParam(0))) {
            return parent::EventNotFound();
        }

        $this->PluginMinimarket_Taxonomy_DeleteAttribut($oAttribut);
        Router::Location('admin/attributes/');
	}
	
	protected function EventPropertydelete() {
        $this->Security_ValidateSendForm();
		
        if (!$oProperty = $this->PluginMinimarket_Taxonomy_GetTaxonomyById($this->GetParam(0))) {
            return parent::EventNotFound();
        }

        $this->PluginMinimarket_Taxonomy_DeleteTaxonomy($oProperty);
        Router::Location('admin/attributedit/' . $oProperty->getParent() . '/?propertydelete=success');
	}
	
	protected function EventPropertyedit() {
        /**
         * Устанавливаем шаблон вывода
         */
        $this->_setTitle($this->Lang_Get('plugin.minimarket.property_edit_title'));
        $this->SetTemplateAction('propertyadd');	
        /**
         * Получаем свойство аттрибута
         */
        if (!($oProperty = $this->PluginMinimarket_Taxonomy_GetTaxonomyById($this->GetParam(0))) || $oProperty->getParent()==0) {
            return parent::EventNotFound();
        }
        $this->Viewer_Assign('oProperty', $oProperty);
		
		$oAttribut=$this->PluginMinimarket_Taxonomy_GetTaxonomyById($oProperty->getParent());
        $this->Viewer_Assign('oAttribut',$oAttribut);
		
        /**
         * Проверяем отправлена ли форма с данными
         */
        if (isset($_REQUEST['submit_property_add'])) {
            /**
             * Обрабатываем отправку формы
             */
            return $this->SubmitPropertyEdit($oProperty,$oAttribut);
        } else {
            $_REQUEST['property_id'] = $oProperty->getId();
            $_REQUEST['property_name'] = $oProperty->getName();
            $_REQUEST['property_url'] = $oProperty->getURL();
            $_REQUEST['property_description'] = $oProperty->getDescription();
        }
	}
	
	protected function EventPropertyadd() {
        /**
         * Получаем аттрибут
         */
        if (!$oAttribut = $this->PluginMinimarket_Taxonomy_GetTaxonomyById($this->GetParam(0))) {
            return parent::EventNotFound();
        }
		
		$this->_setTitle($this->Lang_Get('plugin.minimarket.property_add_title'));
		
		$this->Viewer_Assign('oAttribut', $oAttribut);
		
        /**
         * Обрабатываем отправку формы
         */
        return $this->SubmitPropertyAdd($oAttribut);
	}
	
	protected function SubmitPropertyAdd($oAttribut) {
        /**
         * Проверяем отправлена ли форма с данными
         */
        if (!isPost('submit_property_add')) {
            return false;
        }
		
        /**
         * Проверка корректности полей формы
         */
        if (!$this->CheckPropertyFields()) {
            return false;
        }
		
        $oProperty = Engine::GetEntity('PluginMinimarket_ModuleTaxonomy_EntityTaxonomy');
        $oProperty->setParent($oAttribut->getId());
        $oProperty->setTaxonomyType('property');
        $oProperty->setName(getRequest('property_name'));
        $oProperty->setURL(getRequest('property_url'));
        $oProperty->setDescription(getRequest('property_description'));

        if ($this->PluginMinimarket_Taxonomy_AddTaxonomy($oProperty)) {
            Router::Location('admin/attributedit/'.$oAttribut->getId().'/?add=success');
        }
	}
		
	protected function EventAttributedit() {
        /**
         * Устанавливаем шаблон вывода
         */
        $this->_setTitle($this->Lang_Get('plugin.minimarket.attribut_edit_title'));
        $this->SetTemplateAction('attributadd');	
        /**
         * Получаем аттрибут
         */
        if (!($oAttribut = $this->PluginMinimarket_Taxonomy_GetTaxonomyById($this->GetParam(0))) || $oAttribut->getParent()!=0) {
            return parent::EventNotFound();
        }
        $this->Viewer_Assign('oAttribut', $oAttribut);
        $this->Viewer_Assign('aProperties', $this->PluginMinimarket_Taxonomy_GetPropertiesByAttributId($oAttribut->getId()));
		
        /**
         * Проверяем отправлена ли форма с данными
         */
        if (isset($_REQUEST['submit_attribut_add'])) {
            /**
             * Обрабатываем отправку формы
             */
            return $this->SubmitAttributEdit($oAttribut);
        } else {
            $_REQUEST['attribut_id'] = $oAttribut->getId();
            $_REQUEST['adding_attribut_name'] = $oAttribut->getName();
            $_REQUEST['adding_attribut_url'] = $oAttribut->getURL();
            $_REQUEST['adding_attribut_description'] = $oAttribut->getDescription();
        }
	}
	
	protected function SubmitAttributEdit($oAttribut) {
        /**
         * Проверяем отправлена ли форма с данными
         */
        if (!isPost('submit_attribut_add')) {
            return false;
        }
		
        /**
         * Проверка корректности полей формы
         */
        if (!$this->CheckAttributFields()) {
            return false;
        }
		
        $oAttribut->setParent(0);
        $oAttribut->setName(getRequest('adding_attribut_name'));
        $oAttribut->setURL(getRequest('adding_attribut_url'));
        $oAttribut->setDescription(getRequest('adding_attribut_description'));

        if ($this->PluginMinimarket_Taxonomy_UpdateTaxonomy($oAttribut)) {
            Router::Location('admin/attributedit/'.$oAttribut->getId().'/');
        }
	}
	
	protected function SubmitPropertyEdit($oProperty,$oAttribut) {
        /**
         * Проверяем отправлена ли форма с данными
         */
        if (!isPost('submit_property_add')) {
            return false;
        }
		
        /**
         * Проверка корректности полей формы
         */
        if (!$this->CheckPropertyFields()) {
            return false;
        }
        $oProperty->setName(getRequest('property_name'));
        $oProperty->setURL(getRequest('property_url'));
        $oProperty->setDescription(getRequest('property_description'));

        if ($this->PluginMinimarket_Taxonomy_UpdateTaxonomy($oProperty)) {
            Router::Location('admin/attributedit/'.$oAttribut->getId().'/?edit=success');
        }
	}

	protected function EventAttributes() {
		$this->_setTitle($this->Lang_Get('plugin.minimarket.attributes'));
		
        /*
         * Получаем список
         */
        $this->Viewer_Assign('aAttributes', $this->PluginMinimarket_Taxonomy_getTaxonomiesByType('attribut'));
        $this->Viewer_Assign('aAttributesCategories', $this->PluginMinimarket_Taxonomy_getTaxonomiesByType('attributes_category'));
		
		if (getRequest('add')) $this->Message_AddNoticeSingle($this->Lang_Get('plugin.minimarket.adding_attribut_ok'));
		if (getRequest('edit')) $this->Message_AddNoticeSingle($this->Lang_Get('plugin.minimarket.adding_attribut_edit'));
	}

	protected function EventAttributadd() {
		$this->_setTitle($this->Lang_Get('plugin.minimarket.adding_attribut'));
        /**
         * Обрабатываем отправку формы
         */
        return $this->SubmitAttributAdd();
	}
	
	protected function SubmitAttributAdd() {
        /**
         * Проверяем отправлена ли форма с данными
         */
        if (!isPost('submit_attribut_add')) {
            return false;
        }
		
        /**
         * Проверка корректности полей формы
         */
        if (!$this->CheckAttributFields()) {
            return false;
        }
		
        $oAttribut = Engine::GetEntity('PluginMinimarket_ModuleTaxonomy_EntityTaxonomy');
        $oAttribut->setParent(0);
        $oAttribut->setTaxonomyType('attribut');
        $oAttribut->setName(getRequest('adding_attribut_name'));
        $oAttribut->setURL(getRequest('adding_attribut_url'));
        $oAttribut->setDescription(getRequest('adding_attribut_description'));

        if ($sId = $this->PluginMinimarket_Taxonomy_AddTaxonomy($oAttribut)) {
            Router::Location('admin/attributedit/'.$sId.'/');
        }
	}
	
	protected function SubmitCategoryAdd() {
        /**
         * Проверяем отправлена ли форма с данными
         */
        if (!isPost('submit_category_add')) {
            return false;
        }
		
        /**
         * Проверка корректности полей формы
         */
        if (!$this->CheckMMCategoryFields()) {
            return false;
        }
		
        $oCategory = Engine::GetEntity('PluginMinimarket_ModuleTaxonomy_EntityTaxonomy');
        $oCategory->setParent(getRequest('category_select'));
        $oCategory->setTaxonomyType('category');
        $oCategory->setName(getRequest('category_name'));
        $oCategory->setURL(getRequest('category_url'));
        $oCategory->setDescription(getRequest('category_description'));

        if ($this->PluginMinimarket_Taxonomy_AddTaxonomy($oCategory)) {
            Router::Location('admin/mm_categories/?add=success');
        }
	}
	
	protected function CheckAttributesCategoryFields($sEdit=null) {
        $this->Security_ValidateSendForm();

        $bOk = true;

        if (!func_check(getRequest('adding_attributes_category_name', null, 'post'), 'text', 2, 50)) {
            $this->Message_AddError($this->Lang_Get('plugin.minimarket.adding_attributes_category_name_error'), $this->Lang_Get('error'));
            $bOk = false;
        }
		if($sEdit) {
			if(is_array(getRequest('attribut_sel'))) {
				foreach(getRequest('attribut_sel') as $val) {
					if(!($oAttribut=$this->PluginMinimarket_Taxonomy_GetTaxonomyById($val)) || $oAttribut->getTaxonomyType()!='attribut') {
						$this->Message_AddError($this->Lang_Get('system_error'), $this->Lang_Get('error'));
						$bOk = false;
						break;
					}
				}
			}
		}
        return $bOk;
	}
	
	protected function CheckAttributFields() {
        $this->Security_ValidateSendForm();

        $bOk = true;

        if (!func_check(getRequest('adding_attribut_name', null, 'post'), 'text', 2, 50)) {
            $this->Message_AddError($this->Lang_Get('plugin.minimarket.adding_attribut_name_error'), $this->Lang_Get('error'));
            $bOk = false;
        }
        if (!func_check(getRequest('adding_attribut_description', null, 'post'), 'text', 0, 2000)) {
            $this->Message_AddError($this->Lang_Get('plugin.minimarket.adding_attribut_description_error'), $this->Lang_Get('error'));
            $bOk = false;
        }
        return $bOk;
	}
	
	protected function CheckMMCategoryFields($oldURL=null,$oldParent=null,$sId=null) {
        $this->Security_ValidateSendForm();
        $bOk = true;
		if($sId) {
			if(getRequest('category_select', null, 'post')==$sId) {
				$this->Message_AddError($this->Lang_Get('plugin.minimarket.category_select_parent_error'), $this->Lang_Get('error'));
				$bOk = false;
			}
		}
		if(!func_check(getRequest('category_select', null, 'post'), 'id') || (getRequest('category_select')!=0 && (!($oCategory=$this->PluginMinimarket_Taxonomy_GetTaxonomyById(getRequest('category_select'))) || $oCategory->getTaxonomyType()!='category'))) {
            $this->Message_AddError($this->Lang_Get('plugin.minimarket.category_select_error'), $this->Lang_Get('error'));
            $bOk = false;			
		}
		if($oTaxonomy=$this->PluginMinimarket_Taxonomy_GetTaxonomyByURLAndParentId(getRequest('category_url', null, 'post'),getRequest('category_select', null, 'post'),'category')) {
			if(!$oldURL || ($oldURL && $oldURL!=$oTaxonomy->getURL()) || ($oldParent!=getRequest('category_select'))) {
				$this->Message_AddError($this->Lang_Get('plugin.minimarket.category_select_double_error'), $this->Lang_Get('error'));
				$bOk = false;
			}
		}
        if (!func_check(getRequest('category_name', null, 'post'), 'text', 2, 50)) {
            $this->Message_AddError($this->Lang_Get('plugin.minimarket.category_name_error'), $this->Lang_Get('error'));
            $bOk = false;
        }
        if (!func_check(getRequest('category_url', null, 'post'), 'login', 1, 50) || in_array(getRequest('adding_attribut_url', null, 'post'), array_keys(Config::Get('router.page')))) {
            $this->Message_AddError($this->Lang_Get('plugin.minimarket.category_url_error'), $this->Lang_Get('error'));
            $bOk = false;
        }
        if (!func_check(getRequest('category_description', null, 'post'), 'text', 0, 2000)) {
            $this->Message_AddError($this->Lang_Get('plugin.minimarket.category_description_error'), $this->Lang_Get('error'));
            $bOk = false;
        }
        return $bOk;
	}
	
	protected function CheckPropertyFields() {
        $this->Security_ValidateSendForm();

        $bOk = true;

        if (!func_check(getRequest('property_name', null, 'post'), 'text', 2, 50)) {
            $this->Message_AddError($this->Lang_Get('plugin.minimarket.property_name_error'), $this->Lang_Get('error'));
            $bOk = false;
        }
        if (!func_check(getRequest('property_description', null, 'post'), 'text', 0, 2000)) {
            $this->Message_AddError($this->Lang_Get('plugin.minimarket.property_description_error'), $this->Lang_Get('error'));
            $bOk = false;
        }

        return $bOk;
	}
	
	protected function CheckBrandFields($oldURL=null) {
        $this->Security_ValidateSendForm();

        $bOk = true;
		
        if (!func_check(getRequest('brand_name', null, 'post'), 'text', 2, 50)) {
            $this->Message_AddError($this->Lang_Get('plugin.minimarket.brand_name_error'), $this->Lang_Get('error'));
            $bOk = false;
        }
        if (!func_check(getRequest('brand_url', null, 'post'), 'login', 1, 50) || in_array(getRequest('brand_url', null, 'post'), array_keys(Config::Get('router.page')))) {
            $this->Message_AddError($this->Lang_Get('plugin.minimarket.brand_url_error'), $this->Lang_Get('error'));
            $bOk = false;
        }
		if($oTaxonomy=$this->PluginMinimarket_Taxonomy_GetTaxonomyByURLAndParentId(getRequest('brand_url', null, 'post'),0,'brand')) {
			if(!$oldURL || ($oldURL && $oldURL!=$oTaxonomy->getURL())) {
				$this->Message_AddError($this->Lang_Get('plugin.minimarket.brand_double_error'), $this->Lang_Get('error'));
				$bOk = false;
			}
		}
        if (!func_check(getRequest('brand_description', null, 'post'), 'text', 0, 4000)) {
            $this->Message_AddError($this->Lang_Get('plugin.minimarket.brand_description_error'), $this->Lang_Get('error'));
            $bOk = false;
        }

        return $bOk;
	}
}
?>