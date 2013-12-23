{include file='header.tpl' menu='catalog'}
	{if $aCategories}
	<ul>
	{foreach from=$aCategories item=oCategory}
		{if $oCategory->getParentId()==0}
		<li class="catalog-main-parent-li"><a class="catalog-main-parent-href" href="{cfg name='path.root.web'}catalog/{$oCategory->getURL()}/">{$oCategory->getName()}</a></li>
			<ul class="catalog-main-child-ul">
			{foreach from=$aCategories item=oCategoryChildren}
				{if $oCategory->getId()==$oCategoryChildren->getParentId()}
				<li class="catalog-main-child-li"><a class="catalog-main-child-href" href="{cfg name='path.root.web'}catalog/{$oCategory->getURL()}/{$oCategoryChildren->getURL()}/">{$oCategoryChildren->getName()}</a></li>
				{/if}
			{/foreach}
			</ul>
		{/if}
	{/foreach}
	</ul>
	{else}
		{$aLang.plugin.minimarket.category_not}
	{/if}
{include file='footer.tpl'}