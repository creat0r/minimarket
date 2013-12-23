{include file='header.tpl'}
<h2 class="order-title">{$aLang.plugin.minimarket.order_delivery_methods}</h2>
<form enctype="multipart/form-data" method="post">
	{if $aProducts && $aDeliveryServices}
	<table class="order-delivery">
		<thead>
			<tr>
				<th class="pr-30">{$aLang.plugin.minimarket.order_delivery_table_name}</th>
				<th class="pr-30">{$aLang.plugin.minimarket.order_delivery_table_count}</th>
				<th>{$aLang.plugin.minimarket.order_delivery_table_cost}</th>
			</tr>
		</thead>
		<tbody>
			{foreach from=$aProducts item=oProduct}
				<tr>
					<td class="pr-30"><a href="{$oProduct->getWebPath()}" class="order-delivery-product-name-href">{$oProduct->getName()}</a></td>
					<td class="pr-30">{$aCartObjects[$oProduct->getId()]}</td>
					<td class="order-delivery-product-price">{if $aCartObjects[$oProduct->getId()]}{$oProduct->getCartPriceCurrency()}{else}0{/if}</td>
				</tr>
			{/foreach}
			<tr>
				<td class="pr-30 pt-30">{$aLang.plugin.minimarket.order_delivery}</td>
				<td class="pr-30 pt-30">{$aLang.plugin.minimarket.order_delivery_days_count}</td>
				<td class="pt-30">{$aLang.plugin.minimarket.order_delivery_cost}</td>
			</tr>
			{foreach from=$aDeliveryServices item=oDeliveryService}
				<tr>
					<td class="pr-30">
						<label class="order-delivery-label">
							<input class="mr-5 order-delivery-radio" id="order_delivery_radio_{$oDeliveryService->getId()}" type="radio" value="{$oDeliveryService->getId()}" name="delivery" />
							{$oDeliveryService->getName()}
						</label>
						<div class="order-delivery-comment">
							{if $oDeliveryService->getDescription()}
							({$oDeliveryService->getDescription()})
							{/if}
						</div>
					</td>
					<td class="pr-30">
						{if $oDeliveryService->getTimeFrom() && $oDeliveryService->getTimeTo()}
							{$aLang.plugin.minimarket.order_delivery_from}&nbsp;{$oDeliveryService->getTimeFrom()}&nbsp;{$aLang.plugin.minimarket.order_delivery_to}&nbsp;{$oDeliveryService->getTimeTo()}
						{/if}
					</td>
					<td class="order-delivery-price">
						{if $oDeliveryService->getCartCostCurrency()}{$oDeliveryService->getCartCostCurrency()}{else}0{/if}
						<input type="hidden" name="order_delivery_cost_hidden_{$oDeliveryService->getId()}" value="{$oDeliveryService->getCartCost() / {cfg name='plugin.minimarket.settings.factor'}}" />
					</td>
				</tr>
			{/foreach}
			<tr>
				<td class="order-delivery-final" colspan="4">
					{$aLang.plugin.minimarket.order_delivery_sum}: <span class="order-delivery-final-cost">{if $aCartSumData.cart_sum_currency}{$aCartSumData.cart_sum_currency}{else}0{/if}</span>
					<input type="hidden" name="cart_sum" value="{$aCartSumData.cart_sum / {cfg name='plugin.minimarket.settings.factor'}}" />
					<input type="hidden" name="currency_format" value="{$aCartSumData.format}" />
					<input type="hidden" name="currency_decimal_places" value="{$aCartSumData.decimal_places}" />
				</td>
			</tr>
		</tbody>
	</table>
	<a class="button" href="{cfg name='path.root.web'}order/nulled/?security_ls_key={$ALTO_SECURITY_KEY}">{$aLang.plugin.minimarket.order_nulled}</a>
	<button class="button button-primary" name="submit" type="submit">{$aLang.plugin.minimarket.order_next}</button>
	{else}
		{$aLang.plugin.minimarket.order_delivery_access_error}<br /><br />
		<a class="button" href="{cfg name='path.root.web'}order/nulled/?security_ls_key={$ALTO_SECURITY_KEY}">{$aLang.plugin.minimarket.order_nulled}</a>
	{/if}
</form>
{include file='footer.tpl'}