{extends file='actions/ActionAdmin/info/index.tpl'}

{block name="content-bar"}
    <div class="btn-group">
        <a href="{router page='admin'}attributescategories/" class="btn"><i class="icon-chevron-left"></i></a>
    </div>
    <div class="btn-group">
        <a class="btn" href="{router page='admin'}attributes/">
            {$aLang.plugin.minimarket.attributes}
        </a>
        <a class="btn active" href="{router page='admin'}attributescategories/">
            {$aLang.plugin.minimarket.attributes_category}
        </a>
    </div>
{/block}

{block name="content-body"}

<div class="span12">
	<form method="POST" class="form-horizontal uniform">
		<input type="hidden" name="security_ls_key" value="{$ALTO_SECURITY_KEY}"/>
		<div class="b-wbox">
			<div class="b-wbox-header">
                {if $sEvent=='attributescategoryadd'}
                    <div class="b-wbox-header-title">{$aLang.plugin.minimarket.adding_attributes_category}</div>
                {elseif $sEvent=='attributescategoryedit'}
                    <div class="b-wbox-header-title">{$aLang.plugin.minimarket.attributes_category_edit_title}: {$oAttributesCategory->getName()|escape:'html'}</div>
                {/if}
			</div>
			<div class="b-wbox-content nopadding">
				<div class="control-group">
					<label class="control-label" for="adding_attributes_category_name">{$aLang.plugin.minimarket.adding_attributes_category_name}:</label>
					<div class="controls">
						<input id="adding_attributes_category_name" class="input-text" type="text" value="{$_aRequest.adding_attributes_category_name}" name="adding_attributes_category_name">
						<span class="help-block">{$aLang.plugin.minimarket.adding_attributes_category_name_example}</span>
					</div>
				</div>
			</div>
			{if $sEvent=='attributescategoryedit'}
                <div class="b-wbox-header">
                    <div class="b-wbox-header-title">{$aLang.plugin.minimarket.attributes}</div>
                </div>
				<div class="b-wbox-content">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
							<th></th>
                            <th>{$aLang.plugin.minimarket.attribut_properties_name}</th>
                            <th>{$aLang.plugin.minimarket.attribut_properties_description}</th>
                        </tr>
                        </thead>

                        <tbody class="content">
                        {foreach from=$aAttributes item=oAttribut}
                            <tr class="selectable{if is_array($oAttributesCategory->getTaxonomyConfig()|unserialize) && in_array($oAttribut->getId(),$oAttributesCategory->getTaxonomyConfig()|unserialize)} info{/if}">
								<td class="check-row">
									<input type="checkbox" {if is_array($oAttributesCategory->getTaxonomyConfig()|unserialize) && in_array($oAttribut->getId(),$oAttributesCategory->getTaxonomyConfig()|unserialize)}checked{/if} name="attribut_sel[]" value="{$oAttribut->getId()}"
										   class="form_plugins_checkbox"/>
								</td>
                                <td class="center">
                                    {$oAttribut->getName()}
                                </td>
                                <td class="center">
                                    {$oAttribut->getDescription()}
                                </td>
                            </tr>
                        {/foreach}
                        </tbody>
                    </table>
				</div>
			{/if}
            <div class="b-wbox-content nopadding">
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary"
                            name="submit_attributes_category_add">{$aLang.plugin.minimarket.content_submit}</button>
                </div>
            </div>
		</div>
	</form>
</div>

{/block}