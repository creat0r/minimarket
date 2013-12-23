{extends file='actions/ActionAdmin/info/index.tpl'}

{block name="content-bar"}
    <div class="btn-group">
        <a href="{router page='admin'}mm_currency/" class="btn"><i class="icon-chevron-left"></i></a>
    </div>
{/block}

{block name="content-body"}

<div class="span12">
	<form method="POST" class="form-horizontal uniform">
		<input type="hidden" name="security_ls_key" value="{$ALTO_SECURITY_KEY}"/>
		<div class="b-wbox">
			<div class="b-wbox-header">
                {if $sEvent=='mm_currency_add'}
                    <div class="b-wbox-header-title">{$aLang.plugin.minimarket.admin_currency_adding}</div>
                {elseif $sEvent=='mm_currency_edit'}
                    <div class="b-wbox-header-title">{$aLang.plugin.minimarket.admin_currency_editing}: {$oCurrency->getKey()}</div>
                {/if}
			</div>
			<div class="b-wbox-content nopadding">
				<div class="control-group">
					<label class="control-label" for="key">{$aLang.plugin.minimarket.admin_currency}:</label>
					<div class="controls">
						<input id="key" class="input-text" type="text" value="{$_aRequest.key}" name="key">
						<span class="help-block">{$aLang.plugin.minimarket.admin_currency_adding_key_example}</span>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="nominal">{$aLang.plugin.minimarket.admin_currency_adding_nominal}:</label>
					<div class="controls">
						<input id="nominal" class="input-text" type="text" value="{$_aRequest.nominal}" name="nominal">
						<span class="help-block">{$aLang.plugin.minimarket.admin_currency_adding_nominal_example}</span>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="course">{$aLang.plugin.minimarket.admin_currency_adding_course}:</label>
					<div class="controls">
						<input id="course" class="input-text" type="text" value="{$_aRequest.course}" name="course">
						<span class="help-block">{$aLang.plugin.minimarket.admin_currency_adding_course_example}</span>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="format">{$aLang.plugin.minimarket.admin_currency_adding_format}:</label>
					<div class="controls">
						<input id="format" class="input-text" type="text" value="{$_aRequest.format}" name="format">
						<span class="help-block">{$aLang.plugin.minimarket.admin_currency_adding_format_example}</span>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="decimal_places">{$aLang.plugin.minimarket.admin_currency_adding_decimal_places}:</label>
					<div class="controls">
						<input id="decimal_places" class="input-text" type="text" value="{$_aRequest.decimal_places}" name="decimal_places">
					</div>
				</div>
			</div>
            <div class="b-wbox-content nopadding">
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary"
						name="button_submit">{$aLang.plugin.minimarket.save}</button>
                </div>
            </div>
		</div>
	</form>
</div>

{/block}