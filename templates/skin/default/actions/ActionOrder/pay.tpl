{include file='header.tpl'}
<h2 class="order-title">{$aLang.plugin.minimarket.order_pay_systems}</h2>
<form enctype="multipart/form-data" method="post">
	{if $aPaySystems}
	<table class="order-pay-systems">
		<tbody>
			{foreach from=$aPaySystems item=oPaySystem}
				<tr>
					<td>
						<label><input class="mr-5" type="radio" name="pay_system" value="{$oPaySystem->getId()}" />{$oPaySystem->getName()}</label>
					</td>
				</tr>
			{/foreach}
		</tbody>
	</table>
	<button class="button button-primary" name="submit" type="submit">{$aLang.plugin.minimarket.order_next}</button>
	{else}
		{$aLang.plugin.minimarket.order_pay_systems_access_error}
	{/if}
</form>
{include file='footer.tpl'}