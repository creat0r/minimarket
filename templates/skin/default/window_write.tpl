{if $oUserCurrent && $oUserCurrent->isAdministrator()}
	<li class="write-item-type-topic">
		<a href="{router page='mm_product'}add/" class="write-item-image"></a>
		<a href="{router page='mm_product'}add/" class="write-item-link">{$aLang.plugin.minimarket.product}</a>
	</li>
{/if}