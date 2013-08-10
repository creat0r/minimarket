{extends file='actions/ActionAdmin/info/index.tpl'}

{block name="content-bar"}
    <div class="btn-group">
        <a href="{router page='admin'}attributedit/{$oAttribut->getId()}/" class="btn"><i class="icon-chevron-left"></i></a>
    </div>
    <div class="btn-group">
        <a class="btn active" href="{router page='admin'}attributes/">
            {$aLang.plugin.minimarket.attributes}
        </a>
        <a class="btn" href="{router page='admin'}attributescategories/">
            {$aLang.plugin.minimarket.attributes_category}
        </a>
    </div>
{/block}

{block name="content-body"}
	<form method="POST" name="attributadd" enctype="multipart/form-data" class="form-horizontal uniform">
		<input type="hidden" name="security_ls_key" value="{$ALTO_SECURITY_KEY}"/>
		<div class="b-wbox">
			<div class="b-wbox-header">
                {if $sEvent=='propertyadd'}
                    <div class="b-wbox-header-title">{$aLang.plugin.minimarket.property_add_title} ({$aLang.plugin.minimarket.attribut_for} "{$oAttribut->getName()|escape:'html'}")</div>
                {elseif $sEvent=='propertyedit'}
                    <div class="b-wbox-header-title">{$aLang.plugin.minimarket.property_edit_title}: {$oProperty->getName()|escape:'html'}</div>
                {/if}
			</div>
			<div class="b-wbox-content nopadding">
				<div class="control-group">
					<label class="control-label" for="property_name">{$aLang.plugin.minimarket.property_name}:</label>
					<div class="controls">
						<input id="property_name" class="input-text" type="text" value="{$_aRequest.property_name}" name="property_name">
						<span class="help-block">{$aLang.plugin.minimarket.property_name_example}</span>
					</div>
				</div>
				<div class="control-group">
                    <label class="control-label" for="property_description">{$aLang.plugin.minimarket.property_description}:</label>
                    <div class="controls">
						<textarea name="property_description" id="property_description" class="input-text" rows="5">{$_aRequest.property_description}</textarea>
                    </div>
				</div>
			</div>
            <div class="b-wbox-content nopadding">
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary"
                            name="submit_property_add">{$aLang.plugin.minimarket.property_submit}</button>
                </div>
            </div>
		</div>
	</form>
{/block}