{include file='header.tpl' menu='catalog'}
	{if $aCategoriesChildren}
	<ul class="nav nav-pills">
		{foreach from=$aCategoriesByProduct item=oCategory}
			{assign var="var" value="`$var``$oCategory->getURL()`/"} 
		{/foreach}
		{foreach from=$aCategoriesChildren item=oCategory}
			<li><a href="{cfg name='path.root.web'}catalog/{$var}{$oCategory->getURL()}/{if isset($aSortParams['lover'])}?c[lover]={$aSortParams['lover']}{else if isset($aSortParams['pros'])}?c[pros]={$aSortParams['pros']}{/if}">{$oCategory->getName()}</a></li>
		{/foreach}
	</ul>
	{/if}
	<table class="catalog-product-table">
		{if $aProducts}
			<tbody>
			{foreach from=$aProducts item=oProduct key=key}
				<tr{if $key==count($aProducts)-1 && $aPaging.iCount<=count($aProducts)} style="margin-bottom:0;"{/if}>
					<td>
						<a class="catalog-product-href-img" href="{$oProduct->getWebPath()}">
							{if $oProduct->getMainPhotoWebPath()}
								<img src="{$oProduct->getMainPhotoWebPath()}" alt="" />
							{else}
								<img src="{$oConfig->GetValue('path.root.url')}plugins/minimarket/templates/skin/default/img/placeholder.png" alt="" />
							{/if}
						</a>
					</td>
					<td>
						<ul>
							<li class="mb-10"><a class="catalog-product-href-title" href="{$oProduct->getWebPath()}">{$oProduct->getName()}{if $oProduct->getManufacturerCode()} ({$oProduct->getManufacturerCode()}){/if}</a></li>
							{if $oProduct->getPrice()}
								<li class="catalog-product-price mb-10">{number_format($oProduct->getPrice(),2,',',' ')}&nbsp;$<a class="catalog-product-buy-href" href="{cfg name='path.root.web'}cart/add/{$oProduct->getId()}/">{$aLang.plugin.minimarket.product_buy}</a></li>
							{/if}
							{if count($oProduct->getProductCharacteristics())}
								<li class="catalog-product-characteristics">{foreach from=$oProduct->getProductCharacteristics() item=oCharacteristics key=key_characteristics}{$oCharacteristics->getProductTaxonomyText()}{if count($oProduct->getProductCharacteristics())-1!=$key_characteristics}<span class="catalog-product-bull-characteristics">&bull;</span>{/if}{/foreach}</li>
							{/if}
						</ul>
					</td>
				</tr>
			{/foreach}
			</tbody>
		{else}
			{$aLang.plugin.minimarket.product_not}
		{/if}
	</table>
{include file='paging.tpl' aPaging=$aPaging}
{include file='footer.tpl'}