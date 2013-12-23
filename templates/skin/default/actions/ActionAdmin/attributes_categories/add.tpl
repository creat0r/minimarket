{extends file='actions/ActionAdmin/info/index.tpl'}

{block name="content-bar"}
    <div class="btn-group">
        <a href="{router page='admin'}mm_attributes_categories/" class="btn"><i class="icon-chevron-left"></i></a>
    </div>
    <div class="btn-group">
        <a class="btn" href="{router page='admin'}mm_attributes/">
            {$aLang.plugin.minimarket.admin_attributes}
        </a>
        <a class="btn active" href="{router page='admin'}mm_attributes_categories/">
            {$aLang.plugin.minimarket.admin_attributes_category}
        </a>
    </div>
{/block}

{block name="content-body"}

<div class="span12">
	<form method="POST" class="form-horizontal uniform">
		<input type="hidden" name="security_ls_key" value="{$ALTO_SECURITY_KEY}"/>
		<div class="b-wbox">
			<div class="b-wbox-header">
                {if $sEvent=='mm_attributes_category_add'}
                    <div class="b-wbox-header-title">{$aLang.plugin.minimarket.admin_attributes_category_adding}</div>
                {elseif $sEvent=='mm_attributes_category_edit'}
                    <div class="b-wbox-header-title">{$aLang.plugin.minimarket.admin_attributes_category_edit_title}: {$oAttributesCategory->getName()|escape:'html'}</div>
                {/if}
			</div>
			<div class="b-wbox-content nopadding">
				<div class="control-group">
					<label class="control-label" for="name">{$aLang.plugin.minimarket.name}:</label>
					<div class="controls">
						<input id="name" class="input-text" type="text" value="{$_aRequest.name}" name="name">
						<span class="help-block">{$aLang.plugin.minimarket.admin_attributes_category_adding_name_example}</span>
					</div>
				</div>
			</div>
			{if $sEvent=='mm_attributes_category_edit'}
                <div class="b-wbox-header">
                    <div class="b-wbox-header-title">{$aLang.plugin.minimarket.admin_attributes}</div>
                </div>
				<div class="b-wbox-content">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
							<th></th>
                            <th>{$aLang.plugin.minimarket.name}</th>
                            <th>{$aLang.plugin.minimarket.description}</th>
                        </tr>
                        </thead>

                        <tbody class="content">
                        {foreach from=$aAttributes item=oAttribut}
                            <tr class="selectable{if $aAttributesCategoryAttribut && in_array($oAttribut->getId(), $aAttributesCategoryAttribut)} info{/if}">
								<td class="check-row">
									<input type="checkbox" {if $aAttributesCategoryAttribut && in_array($oAttribut->getId(), $aAttributesCategoryAttribut)}checked{/if} name="attribut_sel[]" value="{$oAttribut->getId()}"
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
                            name="button_submit">{$aLang.plugin.minimarket.save}</button>
                </div>
            </div>
		</div>
	</form>
</div>

{/block}