{include file='header.tpl'}
<h2 class="order-title">{$aLang.plugin.minimarket.cart}</h2>
{if $aProducts}
	<table class="cart">
		<thead>
			<tr>
				<th colspan="2">{$aLang.plugin.minimarket.cart_purchase}</th>
				<th>{$aLang.plugin.minimarket.cart_count}</th>
				<th>{$aLang.plugin.minimarket.cart_price}</th>
			</tr>
		</thead>
		<tbody>
			{foreach from=$aProducts item=oProduct}
				{assign var=cart_summ value=$cart_summ+$oProduct->getPrice()*$aCartObjects[$oProduct->getId()]} 
				<tr>
					<td class="cart-action">
						<a href="{cfg name='path.root.web'}cart/delete/{$oProduct->getId()}/?security_ls_key={$ALTO_SECURITY_KEY}" class="cart-action-href">{$aLang.plugin.minimarket.cart_delete}</a>
					</td>
					<td class="cart-product">
						<div class="cart-product-photo">
							<div class="cart-product-photo-content">
								{if $oProduct->getMainPhotoWebPath()}
									<img src="{$oProduct->getMainPhotoWebPath()}" alt="" />
								{else}
									<img src="{$oConfig->GetValue('path.root.url')}plugins/minimarket/templates/skin/default/img/placeholder.png" alt="" />
								{/if}
							</div>
						</div>
						<div class="cart-product-name">
							<a href="{$oProduct->getWebPath()}" class="cart-product-name-href">{$oProduct->getName()}</a>
						</div>
					</td>
					<td class="cart-count"><input class="input-text cart-count-input" id="cart_count_input_{$oProduct->getId()}" type="text" value="{$aCartObjects[$oProduct->getId()]}" name="count" maxlength="2" /></td>
					<td class="cart-price">
						<span id="cart_price_{$oProduct->getId()}">
						{if $aCartObjects[$oProduct->getId()]}
							{number_format($oProduct->getPrice()*$aCartObjects[$oProduct->getId()],2,',',' ')}&nbsp;$
						{else}
						0
						{/if}
						</span><input type="hidden" name="cart_price_hidden_{$oProduct->getId()}" value="{number_format($oProduct->getPrice(),2,'.','')}" /></td>
				</tr>
			{/foreach}
			<tr>
				<td class="cart-final" colspan="4">
					{$aLang.plugin.minimarket.cart_summ}: <span class="cart-final-price">{if $cart_summ > 0}{number_format($cart_summ,2,',',' ')}&nbsp;${else}0{/if}</span>
				</td>
			</tr>
		</tbody>
	</table>
	<form enctype="multipart/form-data" method="POST" action="">
		<button class="button button-primary" name="submit" type="submit">{$aLang.plugin.minimarket.cart_next}</button>
	</form>
{else}
	{$aLang.plugin.minimarket.cart_no}
{/if}
{include file='footer.tpl'}