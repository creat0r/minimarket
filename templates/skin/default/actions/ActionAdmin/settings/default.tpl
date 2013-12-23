{extends file='actions/ActionAdmin/info/index.tpl'}

{block name="content-bar"}
    <div class="btn-group">
        <a href="#" class="btn btn-primary disabled"><i class="icon-plus-sign"></i></a>
    </div>
    <div class="btn-group">
        <a href="{router page='admin'}mm_settings/" class="btn {if $sEvent=='mm_settings'}active{/if}">
            {$aLang.plugin.minimarket.admin_settings_base}
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
					{$aLang.plugin.minimarket.admin_settings_base}
				</div>
			</div>
			<div class="b-wbox-content nopadding">
				<div class="control-group">
					<label class="control-label">{$aLang.plugin.minimarket.admin_settings_base_default_currency}:</label>
					<div class="controls">	
						<select name="default">
							{if $aCurrency}
								{foreach from=$aCurrency item=oCurrency}
									<option {if $_aRequest.default && $_aRequest.default == $oCurrency->getKey()}selected{/if} value="{$oCurrency->getKey()}">{$oCurrency->getKey()}</option>
								{/foreach}
							{else}
								<option value="0">{$aLang.plugin.minimarket.admin_settings_base_currency_null}</option>
							{/if}
						</select>
						<span class="help-block">{$aLang.plugin.minimarket.admin_settings_base_default_currency_example}</span>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">{$aLang.plugin.minimarket.admin_settings_base_cart_currency}:</label>
					<div class="controls">	
						<select name="cart">
							{if $aCurrency}
								{foreach from=$aCurrency item=oCurrency}
									<option {if $_aRequest.cart && $_aRequest.cart == $oCurrency->getKey()}selected{/if} value="{$oCurrency->getKey()}">{$oCurrency->getKey()}</option>
								{/foreach}
							{else}
								<option value="0">{$aLang.plugin.minimarket.admin_settings_base_currency_null}</option>
							{/if}
						</select>
						<span class="help-block">{$aLang.plugin.minimarket.admin_settings_base_cart_currency_example}</span>
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