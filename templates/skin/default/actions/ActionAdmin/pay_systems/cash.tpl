{extends file='actions/ActionAdmin/info/index.tpl'}

{block name="content-bar"}
    <div class="btn-group">
        <a href="{router page='admin'}mm_pay_systems/" class="btn"><i class="icon-chevron-left"></i></a>
    </div>
{/block}

{block name="content-body"}

<div class="span12">
	<form method="POST" enctype="multipart/form-data" class="form-horizontal uniform">
		<input type="hidden" name="security_ls_key" value="{$ALTO_SECURITY_KEY}"/>
		<div class="b-wbox">
			<div class="b-wbox-header">
				<div class="b-wbox-header-title">
					{$aLang.plugin.minimarket.admin_pay_system_cash_edit_title}
				</div>
			</div>
			<div class="b-wbox-content nopadding">
				<div class="control-group">
					<label class="control-label" for="name">{$aLang.plugin.minimarket.name}:</label>
					<div class="controls">
						<input id="name" class="input-text" type="text" value="{$_aRequest.name}" name="name" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="activation">{$aLang.plugin.minimarket.admin_pay_system_cash_activation}:</label>
					<div class="controls">
						<input type="checkbox" id="activation" name="activation" value="1" class="form_plugins_checkbox" {if $_aRequest.activation}checked {/if}/>
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