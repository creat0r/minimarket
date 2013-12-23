{extends file='actions/ActionAdmin/info/index.tpl'}

{block name="content-bar"}
    <div class="btn-group">
        <a href="{router page='admin'}mm_attribut_edit/{$oAttribut->getId()}/" class="btn"><i class="icon-chevron-left"></i></a>
    </div>
    <div class="btn-group">
        <a class="btn active" href="{router page='admin'}mm_attributes/">
            {$aLang.plugin.minimarket.admin_attributes}
        </a>
        <a class="btn" href="{router page='admin'}mm_attributes_categories/">
            {$aLang.plugin.minimarket.admin_attributes_category}
        </a>
    </div>
{/block}

{block name="content-body"}
	<form method="POST" class="form-horizontal uniform">
		<input type="hidden" name="security_ls_key" value="{$ALTO_SECURITY_KEY}"/>
		<div class="b-wbox">
			<div class="b-wbox-header">
                {if $sEvent=='mm_property_add'}
                    <div class="b-wbox-header-title">{$aLang.plugin.minimarket.admin_property_adding_title} ({$aLang.plugin.minimarket.admin_attribut_for} "{$oAttribut->getName()|escape:'html'}")</div>
                {elseif $sEvent=='mm_property_edit'}
                    <div class="b-wbox-header-title">{$aLang.plugin.minimarket.admin_property_edit_title}: {$oProperty->getName()|escape:'html'}</div>
                {/if}
			</div>
			<div class="b-wbox-content nopadding">
				<div class="control-group">
					<label class="control-label" for="name">{$aLang.plugin.minimarket.name}:</label>
					<div class="controls">
						<input id="name" class="input-text" type="text" value="{$_aRequest.name}" name="name">
						<span class="help-block">{$aLang.plugin.minimarket.admin_property_adding_name_example}</span>
					</div>
				</div>
				<div class="control-group">
                    <label class="control-label" for="description">{$aLang.plugin.minimarket.description}:</label>
                    <div class="controls">
						<textarea name="description" id="description" class="input-text" rows="5">{$_aRequest.description}</textarea>
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
{/block}