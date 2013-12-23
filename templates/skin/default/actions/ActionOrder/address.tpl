{include file='header.tpl'}

<script type="text/javascript">
	jQuery(document).ready(function($){
		ls.geo.initSelect();
	});
</script>
<h2 class="order-title">{$aLang.plugin.minimarket.order_address_address}</h2>
<form class="form-profile" enctype="multipart/form-data" method="post">
	<table class="order-address">
		<tbody>
			<tr>
				<td class="order-address-label pb-30">{$aLang.plugin.minimarket.order_address_name}</td>
				<td class="pb-30"><input class="input-text input-width-300" type="text" value="{$_aRequest.name}" name="name" /></td>
			</tr>
			<tr>
				<td class="order-address-label pb-30">{$aLang.plugin.minimarket.order_address_city}*</td>
				<td class="pb-30">
					<div class="js-geo-select">
						<select class="js-geo-country input-width-200" name="geo_country">
							<option value="">{$aLang.geo_select_country}</option>
							{if $aGeoCountries}
								{foreach from=$aGeoCountries item=oGeoCountry}
									<option value="{$oGeoCountry->getId()}" {if $oGeoTarget and $oGeoTarget->getCountryId()==$oGeoCountry->getId()}selected="selected"{/if}>{$oGeoCountry->getName()}</option>
								{/foreach}
							{/if}
						</select>
						<select class="js-geo-region input-width-200" name="geo_region" {if !$oGeoTarget or !$oGeoTarget->getCountryId()}style="display:none;"{/if}>
							<option value="">{$aLang.geo_select_region}</option>
							{if $aGeoRegions}
								{foreach from=$aGeoRegions item=oGeoRegion}
									<option value="{$oGeoRegion->getId()}" {if $oGeoTarget and $oGeoTarget->getRegionId()==$oGeoRegion->getId()}selected="selected"{/if}>{$oGeoRegion->getName()}</option>
								{/foreach}
							{/if}
						</select>
						<select class="js-geo-city input-width-200" name="geo_city" {if !$oGeoTarget or !$oGeoTarget->getRegionId()}style="display:none;"{/if}>
							<option value="">{$aLang.geo_select_city}</option>
							{if $aGeoCities}
								{foreach from=$aGeoCities item=oGeoCity}
									<option value="{$oGeoCity->getId()}" {if $oGeoTarget and $oGeoTarget->getCityId()==$oGeoCity->getId()}selected="selected"{/if}>{$oGeoCity->getName()}</option>
								{/foreach}
							{/if}
						</select>
					</div>
				</td>
			</tr>
			<tr>
				<td class="order-address-label pb-30">{$aLang.plugin.minimarket.order_address_index}</td>
				<td class="pb-30"><input class="input-text input-width-150" type="text" value="{$_aRequest.index}" name="index" /></td>
			</tr>
			<tr>
				<td class="order-address-label pb-30">{$aLang.plugin.minimarket.order_address_address}</td>
				<td class="pb-30"><input class="input-text input-width-300" type="text" value="{$_aRequest.address}" name="address" /></td>
			</tr>
			<tr>
				<td class="order-address-label pb-30">{$aLang.plugin.minimarket.order_address_phone}</td>
				<td class="pb-30"><input class="input-text input-width-150" type="text" value="{$_aRequest.phone}" name="phone" /></td>
			</tr>
			<tr>
				<td class="order-address-label pb-30">{$aLang.plugin.minimarket.order_address_comment}</td>
				<td class="pb-30"><textarea rows="7" value="" class="input-text input-width-300" name="comment">{$_aRequest.comment}</textarea></td>
			</tr>
		</tbody>
	</table>
	<a class="button" href="{cfg name='path.root.web'}order/nulled/?security_ls_key={$ALTO_SECURITY_KEY}">{$aLang.plugin.minimarket.order_nulled}</a>
	<button class="button button-primary" name="submit" type="submit">{$aLang.plugin.minimarket.order_next}</button>
</form>
{include file='footer.tpl'}