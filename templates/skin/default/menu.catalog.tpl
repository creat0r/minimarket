<ul class="nav nav-menu">
	{if $aCategoriesByProduct}
		<li>
			<a href="{cfg name='path.root.web'}catalog/">{$aLang.plugin.minimarket.catalog}</a>
		</li>
		{foreach from=$aCategoriesByProduct item=oCategory}
			{assign var="var" value="`$var``$oCategory->getURL()`/"} 
			<li {if $sURL && $sURL==$oCategory->getURL()}class="active"{/if}>
				<a href="{cfg name='path.root.web'}catalog/{$var}{if isset($aSortParams['lover'])}?c[lover]={$aSortParams['lover']}{else if isset($aSortParams['pros'])}?c[pros]={$aSortParams['pros']}{/if}">{$oCategory->getName()}</a>
			</li>
		{/foreach}
	{/if}
</ul>