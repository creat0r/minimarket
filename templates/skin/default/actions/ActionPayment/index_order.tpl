{include file='header.tpl'}
<h2 class="payment-title mb-30">{$aLang.plugin.minimarket.payment_available_pay_systems}</h2>
<form method="post" action="">
	{if $aPaySystem}
	<table class="mb-30">
		<tbody>
			{foreach from=$aPaySystem item=oPaySystem}
				<tr>
					<td>
						<label><input class="mr-5" type="radio" name="pay_system" value="{$oPaySystem->getId()}" />{$oPaySystem->getName()}</label>
					</td>
				</tr>
			{/foreach}
		</tbody>
	</table>
	<a class="button" href="{cfg name='path.root.web'}order/nulled/?security_ls_key={$ALTO_SECURITY_KEY}">{$aLang.plugin.minimarket.order_nulled}</a>
	<button class="button button-primary" name="submit" type="submit">{$aLang.plugin.minimarket.action_payment_init_button_next}</button>
	{/if}
</form>
{include file='footer.tpl'}