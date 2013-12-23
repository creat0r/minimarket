{extends file='actions/ActionAdmin/info/index.tpl'}

{block name="content-bar"}
    <div class="btn-group">
        <a href="{router page='admin'}mm_delivery_services/" class="btn"><i class="icon-chevron-left"></i></a>
    </div>
    <div class="btn-group">
		<a class="btn active" href="{router page='admin'}mm_delivery_services/">
			{$aLang.plugin.minimarket.admin_delivery_service_tunable}
		</a>
		<a class="btn" href="{router page='admin'}mm_delivery_services_automatic/">
			{$aLang.plugin.minimarket.admin_delivery_service_automatic}
		</a>
    </div>
{/block}

{block name="content-body"}

<div class="span12">
	<form method="POST" class="form-horizontal uniform">
		<input type="hidden" name="security_ls_key" value="{$ALTO_SECURITY_KEY}"/>
		<div class="b-wbox">
			<div class="b-wbox-header">
				<div class="b-wbox-header-title">
					{if $sEvent=='mm_delivery_service_add'}
						{$aLang.plugin.minimarket.admin_delivery_service_adding}
					{elseif $sEvent=='mm_delivery_service_edit'}
						{$aLang.plugin.minimarket.admin_delivery_service_editing} ({$oDeliveryService->getName()})
					{/if}
				</div>
			</div>
			<div class="b-wbox-content nopadding">
				<div class="control-group">
					<label class="control-label" for="name">{$aLang.plugin.minimarket.name}:</label>
					<div class="controls">
						<input id="name" class="input-text" type="text" value="{$_aRequest.name}" name="name">
						<span class="help-block">{$aLang.plugin.minimarket.admin_delivery_service_adding_name_example}</span>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="activation">{$aLang.plugin.minimarket.admin_delivery_service_adding_activation}:</label>
					<div class="controls">
						<input type="checkbox" id="activation" name="activation" value="1" class="form_plugins_checkbox" {if $_aRequest.activation}checked {/if}/>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">{$aLang.plugin.minimarket.admin_delivery_service_adding_delivery_time}:</label>
					<div class="controls">
						<div class="input-prepend">
							<span class="add-on">{$aLang.plugin.minimarket.admin_delivery_service_adding_from}</span>
							<input class="span1" type="text" placeholder="0" name="time_from" value="{$_aRequest.time_from}" />
						</div>
						<div class="input-prepend">
							<span class="add-on">{$aLang.plugin.minimarket.admin_delivery_service_adding_to}</span>
							<input class="span1" type="text" placeholder="0" name="time_to" value="{$_aRequest.time_to}" />
						</div>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">{$aLang.plugin.minimarket.admin_delivery_service_adding_cost_calculation}:</label>
					<div class="controls">
						<label>
							<input type="radio" name="cost_calculation" value="1" {if $_aRequest.cost_calculation && $_aRequest.cost_calculation == $DELIVERY_COST_CALCULATION_ENTIRE_ORDER}checked {/if}/>
							{$aLang.plugin.minimarket.admin_delivery_service_adding_cost_calculation_all_order}
						</label>
						<label>
							<input type="radio" name="cost_calculation" value="2" {if $_aRequest.cost_calculation && $_aRequest.cost_calculation == $DELIVERY_COST_CALCULATION_ONE_ITEM}checked {/if}/>
							{$aLang.plugin.minimarket.admin_delivery_service_adding_cost_calculation_each_item}
						</label>
					</div>
				</div>
				<div class="control-group">
					<label for="cost" class="control-label">{$aLang.plugin.minimarket.admin_delivery_service_adding_cost}:</label>
					<div class="controls">	
						<input type="text" name="cost" id="cost" value="{$_aRequest.cost}" />
						<span class="help-block">{$aLang.plugin.minimarket.admin_delivery_service_adding_cost_example}</span>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">{$aLang.plugin.minimarket.admin_delivery_service_adding_currency}:</label>
					<div class="controls">	
						<select name="currency">
							{if $aCurrency}
								{foreach from=$aCurrency item=oCurrency}
									<option value="{$oCurrency->getId()}" {if ($_aRequest.currency == $oCurrency->getId()) || (!$_aRequest.currency && $oCurrency->getId() == $oCurrencyDefault->getId())}selected {/if}>{$oCurrency->getKey()}{if $oCurrency->getId() == $oCurrencyDefault->getId()} ({$aLang.plugin.minimarket.admin_delivery_service_adding_currency_default}){/if}</option>
								{/foreach}
							{else}
								<option value="0">{$aLang.plugin.minimarket.admin_delivery_service_adding_currency_null}</option>
							{/if}
						</select>
					</div>
				</div>
				<div class="control-group">
					<label for="description" class="control-label">{$aLang.plugin.minimarket.description}:</label>
					<div class="controls">
						<textarea name="description" id="description">{$_aRequest.description}</textarea>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">{$aLang.plugin.minimarket.admin_delivery_service_adding_location_gropus}:</label>
					<div class="controls">
						<select class="admin-select-multiple" multiple="multiple" size="10" name="location_groups[]">
							{if $aLocationGroups}
								{foreach from=$aLocationGroups item=oLocationGroup}
								<option {if is_array($_aRequest.location_groups) && in_array($oLocationGroup->getId(), $_aRequest.location_groups)}selected {/if}value="{$oLocationGroup->getId()}">
									{$oLocationGroup->getName()}
								</option>
								{/foreach}
							{/if}
						</select>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">{$aLang.plugin.minimarket.admin_delivery_service_adding_pay_systems}:</label>
					<div class="controls">
						<select class="admin-select-multiple" multiple="multiple" size="10" name="pay_systems[]">
							{if $aPaySystems}
								{foreach from=$aPaySystems item=oPaySystem}
								<option {if is_array($_aRequest.pay_systems) && in_array($oPaySystem->getId(),$_aRequest.pay_systems)}selected {/if}value="{$oPaySystem->getId()}">
									{$oPaySystem->getName()}
								</option>
								{/foreach}
							{/if}
						</select>
					</div>
				</div>
				<div class="b-wbox-content nopadding">
					<div class="form-actions">
						<button type="submit" class="btn btn-primary"
								name="button_submit">{$aLang.plugin.minimarket.save}</button>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>

{/block}