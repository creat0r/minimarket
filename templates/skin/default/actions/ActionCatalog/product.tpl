{include file='header.tpl' menu='catalog'}
<article class="topic topic-type-topic js-topic">
	<header class="topic-header">
		<h1 class="topic-title word-wrap">{$oProduct->getName()}{if $oProduct->getManufacturerCode()} ({$oProduct->getManufacturerCode()}){/if}</h1>
		{if $oUserCurrent && $oUserCurrent->isAdministrator()}
		<ul class="topic-actions">
			<li class="edit"><i class="icon-synio-actions-edit"></i><a href="{router page='mm_product'}edit/{$oProduct->getURL()}/" title="{$aLang.topic_edit}" class="actions-edit">{$aLang.topic_edit}</a></li>
			<li class="delete"><i class="icon-synio-actions-delete"></i><a href="{router page='mm_product'}delete/{$oProduct->getId()}/?security_ls_key={$ALTO_SECURITY_KEY}" title="{$aLang.topic_delete}" onclick="return confirm('{$aLang.plugin.minimarket.product_delete_confirm}');" class="actions-delete">{$aLang.topic_delete}</a></li>
		</ul>
		{/if}
	</header>
	<div class="product-img-container mb-30">
		<div class="product-price"><div class="mb-30"><span>{if $oProduct->getPrice()}{number_format($oProduct->getPrice(),2,',',' ')} ${/if}</span></div></div>
		{if count($aPhotos)}
			<ul>
				{foreach from=$aPhotos item=oPhoto}
					<li onclick="ls.minimarket.imgUpdate('{$oPhoto->getProductPhotoId()}','{$oPhoto->getProductPhotoWebPath('375')}','{$oPhoto->getProductPhotoPath()}');">
						<span id="product_img_preview_{$oPhoto->getProductPhotoId()}" class="{if $oProduct->getMainPhotoId()==$oPhoto->getProductPhotoId()}product-img-preview-active {/if}product-img-preview"><img src="{$oPhoto->getProductPhotoWebPath('36')}" alt="" /></span>
					</li>
				{/foreach}
			</ul>
			<div class="product-img-main">
				{foreach from=$aPhotos item=oPhoto}{if $oProduct->getMainPhotoId()==$oPhoto->getProductPhotoId()}<a id="product_img_main" href="{$oPhoto->getPath()}" target="_blank"><img src="{$oPhoto->getProductPhotoWebPath('375')}" alt="" /></a>{/if}{/foreach}
			</div>
		{else}
			<div class="product-img-main-no-photo">
				<img src="{$oConfig->GetValue('path.root.url')}plugins/minimarket/templates/skin/default/img/placeholder.png" alt="" />
			</div>
		{/if}
		<div style="clear:both;"></div>
	</div>
	{if count($aCharacteristics)}
	<div class="product-container mb-30">
		<h2 class="product-text-title">{$aLang.plugin.minimarket.product_characteristics}</h2>
		{assign var=var value=0}
		{foreach from=$aCharacteristics item=oCharacteristics}{if $var}<span class="product-bull">&bull;</span>{/if}{$oCharacteristics->getProductTaxonomyText()}{assign var=var value=1}{/foreach}
	</div>
	{/if}
	{if count($aFeatures)}
	<div class="product-container{if $oProduct->getText()!='' || $aPropertiesByProduct} mb-30{/if}">
		<h2 class="product-text-title">{$aLang.plugin.minimarket.product_features}</h2>
		{assign var=var value=0}{foreach from=$aFeatures item=oFeatures}{if $var}<span class="product-bull">&bull;</span>{/if}{$oFeatures->getProductTaxonomyText()}{assign var=var value=1}{/foreach}
	</div>
	{/if}
	{if $oProduct->getText()!=''}
	<div class="product-container{if $aPropertiesByProduct} mb-30{/if}">
		<h2 class="product-text-title">{$aLang.plugin.minimarket.product_description}</h2>
		{$oProduct->getText()}
	</div>
	{/if}
	{if $aPropertiesByProduct}
	<div class="product-container">
		<h2 class="product-text-title">{$aLang.plugin.minimarket.product_technical_characteristics} {$oProduct->getName()}</h2>
		<table cellspacing="0" class="product-table-characteristics">
		{assign var=attributes_category value=0}
		{foreach from=$aPropertiesByProduct item=oAttribut}
			{assign var=var value=0}
			{if $oAttribut->getTaxonomyType()=='attributes_category'}
				<tr>
					<td colspan="2" class="{if $attributes_category==1}pt-15 {/if}attributes-category-additionally-title">{$oAttribut->getName()}</td>
				</tr>
				{assign var=attributes_category value=1}
			{/if}
			{if $oAttribut->getTaxonomyType()=='attribut'}
				<tr>
					<td class="product-table-attribut-td"><div class="product-table-attribut-container"><span>{$oAttribut->getName()}</span></div></td>
					<td>{foreach from=$aPropertiesByProduct item=oProperty}{if $oProperty->getParent()==$oAttribut->getId()}{if $var==1}, {/if}{assign var=var value=1}{$oProperty->getName()}{/if}{/foreach}</td>
				</tr>
			{/if}
		{/foreach}
		</table>
	</div>
	{/if}
</article>
{include file='footer.tpl'}