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
					<td class="order-delivery-product-price">{if $aCartObjects[$oProduct->getId()]}{number_format($oProduct->getPrice()*$aCartObjects[$oProduct->getId()],2,',',' ')}&nbsp;${else}0{/if}</td>
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
						{if $oDeliveryService->getCost()}{number_format($oDeliveryService->getCost(),2,',',' ')}&nbsp;${else}0{/if}
						<input type="hidden" name="order_delivert_cost_hidden_{$oDeliveryService->getId()}" value="{number_format($oDeliveryService->getCost(),2,'.','')}" />
					</td>
				</tr>
			{/foreach}
			<tr>
				<td class="order-delivert-final" colspan="4">
					{$aLang.plugin.minimarket.order_delivery_sum}: <span class="order-delivert-final-cost">{if $oOrder->getCartSum() > 0}{number_format($oOrder->getCartSum(),2,',',' ')}&nbsp;${else}0{/if}</span>
					<input type="hidden" name="order_delivert_final_cost_hidden" value="{$oOrder->getCartSum()}" />
				</td>
			</tr>
		</tbody>
	</table>
	<button class="button button-primary" name="submit" type="submit">{$aLang.plugin.minimarket.order_next}</button>
	{else}
		{$aLang.plugin.minimarket.order_delivery_access_error}
	{/if}
</form>
{include file='footer.tpl'}