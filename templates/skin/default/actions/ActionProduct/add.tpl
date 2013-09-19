{if $sEvent=='add'}
	{include file='header.tpl' menu_content='create'}
{else}
	{include file='header.tpl'}
	<h2 class="page-header">{$aLang.plugin.minimarket.product_product_edit}: <b>{$_aRequest.product_name|escape:'html'}</b></h2>
{/if}

{include file='editor.tpl'}

<script type="text/javascript">
	jQuery(function($){
		if (jQuery.browser.flash) {
			ls.minimarket_photoset.initSwfUpload({
				post_params: { 'product_id': {json var=$_aRequest.product_id} }
			});
		}
	});
</script>

<form id="photoset-upload-form" method="POST" enctype="multipart/form-data" onsubmit="return false;" class="modal modal-image-upload">
	<header class="modal-header">
		<h3>{$aLang.uploadimg}</h3>
		<a href="#" class="close jqmClose"></a>
	</header>

	<div id="topic-photo-upload-input" class="topic-photo-upload-input modal-content">
		<label for="photoset-upload-file">{$aLang.topic_photoset_choose_image}:</label>
		<input type="file" id="photoset-upload-file" name="Filedata" /><br><br>

		<button type="submit" class="button button-primary" onclick="ls.minimarket_photoset.upload();">{$aLang.topic_photoset_upload_choose}</button>
		<button type="submit" class="button" onclick="ls.minimarket_photoset.closeForm();">{$aLang.topic_photoset_upload_close}</button>

		<input type="hidden" name="is_iframe" value="true" />
		<input type="hidden" name="product_id" value="{$_aRequest.product_id}" />
	</div>
</form>

<form action="" method="POST" enctype="multipart/form-data" class="wrapper-content">
	<input type="hidden" name="product_id" value="{$_aRequest.product_id}" />
	<input type="hidden" name="security_ls_key" value="{$ALTO_SECURITY_KEY}" />

	<p><label for="product_name">{$aLang.plugin.minimarket.product_create_title}:</label>
	<input type="text" id="product_name" name="product_name" value="{$_aRequest.product_name}" class="input-text input-width-full" /></p>

	<p><label for="product_manufacturer_code">{$aLang.plugin.minimarket.product_create_manufacturer_code}:</label>
	<input type="text" id="product_manufacturer_code" name="product_manufacturer_code" value="{$_aRequest.product_manufacturer_code}" class="input-text input-width-full" /></p>

	<p><label for="product_url">{$aLang.plugin.minimarket.product_create_url}:</label>
	<input type="text" id="product_url" name="product_url" value="{$_aRequest.product_url}" class="input-text input-width-full" /></p>

	<p><label for="product_price">{$aLang.plugin.minimarket.product_create_price}:</label>
	<input type="text" id="product_price" name="product_price" value="{$_aRequest.product_price}" class="input-text input-width-full" /></p>

	<p><label for="product_weight">{$aLang.plugin.minimarket.product_create_weight}:</label>
	<input type="text" id="product_weight" name="product_weight" value="{$_aRequest.product_weight}" class="input-text input-width-full" />
	<small class="note">{$aLang.plugin.minimarket.product_create_weight_notice}</small></p>
	{*
	<p><label><input type="checkbox" id="product_show" name="product_show" class="input-checkbox" value="1" {if $_aRequest.product_show==1}checked{/if} />
	{$aLang.plugin.minimarket.product_create_show}</label>
	<small class="note">{$aLang.plugin.minimarket.product_create_show_notice}</small></p>
	
	<p><label><input type="checkbox" id="product_in_stock" name="product_in_stock" class="input-checkbox" value="1" {if $_aRequest.product_in_stock==1}checked{/if} />
	{$aLang.plugin.minimarket.product_create_in_stock}</label>
	<small class="note">{$aLang.plugin.minimarket.product_create_in_stock_notice}</small></p>
	*}
	<p><label for="product_brand">{$aLang.plugin.minimarket.product_create_brand}:</label>
	<select name="product_brand" id="product_brand" class="input-width-full">
		<option value="0"{if !$_aRequest.product_brand} selected{/if}>{$aLang.plugin.minimarket.product_create_brand_no}</option>
		{foreach from=$aBrands item=oBrand}
			<option value="{$oBrand->getId()}" {if $_aRequest.product_brand==$oBrand->getId()}selected{/if}>{$oBrand->getName()}</option>
		{/foreach}
	</select></p>
	
	<p><label for="product_category">{$aLang.plugin.minimarket.product_create_category}:</label>
	<select name="product_category" id="product_category" class="input-width-full">
		<option value="0"{if !$_aRequest.product_category} selected{/if}>{$aLang.plugin.minimarket.product_create_category_no}</option>
		{foreach from=$aCategories item=Category key=key}
			<option value="{$key}" {if $_aRequest.product_category==$key}selected{/if}>{for $for=1 to $Category.position}&mdash;{/for}{if $for} {/if}{$Category.name}</option>
		{/foreach}
	</select></p>
	
	<p><label for="attribut_id">{$aLang.plugin.minimarket.product_create_attribut}:</label>
	<select onChange="ls.minimarket.addAttribut(jQuery(this).val());" name="attribut_id" id="attribut_id" class="input-width-full">
		<option id="attribut_select" value="0" selected>{$aLang.plugin.minimarket.product_create_attribut_no}</option>
		{foreach from=$aAttributes item=oAttribut}
			<option value="{$oAttribut->getId()}">
				{foreach from=$aAttributesCategories item=oAttributesCategory}
					{if is_array($oAttributesCategory->getTaxonomyConfig()|unserialize) && in_array($oAttribut->getId(),$oAttributesCategory->getTaxonomyConfig()|unserialize)}
						{$oAttributesCategory->getName()} &mdash; 
					{/if}
				{/foreach}
				{$oAttribut->getName()}
			</option>
		{/foreach}
	</select>
	</p>
	{foreach from=$aAttributes item=oAttribut}
		<div id="attribut_block_{$oAttribut->getId()}" {if !$_aRequest.product_attributes || !in_array($oAttribut->getId(),$_aRequest.product_attributes)}class="product-create-attribut-container"{/if}>
			<div class="product-create-attribut-header">
				<span class="fl-l product-create-attribut-header-name">
					{foreach from=$aAttributesCategories item=oAttributesCategory}
						{if is_array($oAttributesCategory->getTaxonomyConfig()|unserialize) && in_array($oAttribut->getId(),$oAttributesCategory->getTaxonomyConfig()|unserialize)}
							{$oAttributesCategory->getName()} &mdash; 
						{/if}
					{/foreach}
					{$oAttribut->getName()}
				</span>
				<span class="fl-r"><button onclick="ls.minimarket.deleteAttribut({$oAttribut->getId()}); return false;" class="button" type="submit fl-r">{$aLang.plugin.minimarket.product_create_attribut_delete}</button></span>
				<div style="clear:both;"></div>
			</div>
			<div class="product-create-attribut-body mb-20">
				<div class="side-by-side clearfix">
					<select name="product_attribut_and_property[{$oAttribut->getId()}][]" data-placeholder="{$aLang.plugin.minimarket.product_create_attribut_select_property}..." id="select_property_{$oAttribut->getId()}" class="chzn-select" multiple tabindex="4">
						<option value=""></option> 
						{foreach from=$aProperties item=oProperty}
							{if $oProperty->getParent()==$oAttribut->getId()}
								<option {if $_aRequest.product_properties && in_array($oProperty->getId(),$_aRequest.product_properties)}selected{/if} value="{$oProperty->getId()}">{$oProperty->getName()}</option> 
							{/if}
						{/foreach}
					</select>
				</div>
			</div>
		</div>
	{/foreach}
	
	<p><label for="product_characteristics">{$aLang.plugin.minimarket.product_create_characteristics}:</label>
	<input type="text" id="product_characteristics" name="product_characteristics" value="{$_aRequest.product_characteristics}" class="input-text input-width-full autocomplete-characteristics-sep" />
	<small class="note">{$aLang.plugin.minimarket.product_create_characteristics_notice}</small></p>
	
	<p><label for="product_features">{$aLang.plugin.minimarket.product_create_features}:</label>
	<input type="text" id="product_features" name="product_features" value="{$_aRequest.product_features}" class="input-text input-width-full autocomplete-features-sep" />
	<small class="note">{$aLang.plugin.minimarket.product_create_features_notice}</small></p>
	
	<h2 class="page-header"><a class="link-dotted pointer" onclick="$('.topic-photo-upload').slideToggle();return false;">{$aLang.topic_toggle_images}</a></h2>
	<div class="topic-photo-upload" {if !count($aPhotos)}style="display:none;"{/if}>
		<h2>{$aLang.topic_photoset_upload_title}</h2>

		<div class="topic-photo-upload-rules">
			{$aLang.topic_photoset_upload_rules|ls_lang:"SIZE%%`$oConfig->get('module.topic.photoset.photo_max_size')`":"COUNT%%`$oConfig->get('module.topic.photoset.count_photos_max')`"}
		</div>

		<input type="hidden" name="product_main_photo" id="product_main_photo" value="{$_aRequest.product_main_photo}" />

		<ul id="swfu_images">
			{if count($aPhotos)}
				{foreach from=$aPhotos item=oPhoto}
					{if $_aRequest.product_main_photo && $_aRequest.product_main_photo == $oPhoto->getProductPhotoId()}
						{assign var=bIsMainPhoto value=true}
					{/if}

					<li id="photo_{$oPhoto->getProductPhotoId()}" {if $bIsMainPhoto}class="marked-as-preview"{/if}>
						<img src="{$oPhoto->getProductPhotoWebPath('100crop')}" alt="image" />
						<textarea onBlur="ls.minimarket_photoset.setPreviewDescription({$oPhoto->getProductPhotoId()}, this.value)">{$oPhoto->getProductPhotoDescription()}</textarea><br />
						<a href="javascript:ls.minimarket_photoset.deletePhoto('{$oPhoto->getProductPhotoId()}')" class="image-delete">{$aLang.topic_photoset_photo_delete}</a>
						<span id="photo_preview_state_{$oPhoto->getProductPhotoId()}" class="photo-preview-state">
							{if $bIsMainPhoto}
								{$aLang.plugin.minimarket.product_photoset_is_preview}
							{else}
								<a href="javascript:ls.minimarket_photoset.setPreview('{$oPhoto->getProductPhotoId()}')" class="mark-as-preview">{$aLang.topic_photoset_mark_as_preview}</a>
							{/if}
						</span>
					</li>

					{assign var=bIsMainPhoto value=false}
				{/foreach}
			{/if}
		</ul>

		<a href="javascript:ls.minimarket_photoset.showForm()" id="photoset-start-upload">{$aLang.topic_photoset_upload_choose}</a>
	</div>

    <label for="product_text">{$aLang.plugin.minimarket.product_create_text}:</label>
	<textarea name="product_text" id="product_text" class="mce-editor markitup-editor input-width-full" rows="20">{$_aRequest.product_text}</textarea>
	
	<button type="submit"  name="submit_product_publish" id="submit_product_publish" class="button button-primary fl-r">{$aLang.plugin.minimarket.product_create_submit_publish}</button>
	
	<div style="clear:both;"></div>
	
</form>
{include file='footer.tpl'}