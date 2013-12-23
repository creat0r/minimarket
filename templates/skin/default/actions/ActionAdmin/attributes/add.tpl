{extends file='actions/ActionAdmin/info/index.tpl'}

{block name="content-bar"}
    <div class="btn-group">
        <a href="{router page='admin'}mm_attributes/" class="btn"><i class="icon-chevron-left"></i></a>
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

<div class="span12">
	<form method="POST" class="form-horizontal uniform">
		<input type="hidden" name="security_ls_key" value="{$ALTO_SECURITY_KEY}"/>
		<div class="b-wbox">
			<div class="b-wbox-header">
                {if $sEvent=='mm_attribut_add'}
                    <div class="b-wbox-header-title">{$aLang.plugin.minimarket.admin_attribut_adding}</div>
                {elseif $sEvent=='mm_attribut_edit'}
                    <div class="b-wbox-header-title">{$aLang.plugin.minimarket.admin_attribut_edit_title}: {$oAttribut->getName()|escape:'html'}</div>
                {/if}
			</div>
			<div class="b-wbox-content nopadding">
				<div class="control-group">
					<label class="control-label" for="name">{$aLang.plugin.minimarket.name}:</label>
					<div class="controls">
						<input id="name" class="input-text" type="text" value="{$_aRequest.name}" name="name">
						<span class="help-block">{$aLang.plugin.minimarket.admin_attribut_adding_name_example}</span>
					</div>
				</div>
				<div class="control-group">
                    <label class="control-label" for="description">{$aLang.plugin.minimarket.description}:</label>
                    <div class="controls">
						<textarea name="description" id="description" class="input-text" rows="5">{$_aRequest.description}</textarea>
                    </div>
				</div>
			</div>
			{if $sEvent=='mm_attribut_edit'}
                <div class="b-wbox-header">
                    <div class="b-wbox-header-title">{$aLang.plugin.minimarket.admin_attribut_properties_added}</div>
                </div>
				<div class="b-wbox-content">
                    <table class="table table-bordered" id="sortable">
                        <thead>
                        <tr>
                            <th class="span4">{$aLang.plugin.minimarket.name}</th>
                            <th>{$aLang.plugin.minimarket.description}</th>
                            <th class="span2">{$aLang.plugin.minimarket.action}</th>
                        </tr>
                        </thead>
                        <tbody class="content">
						{if $aProperties}
							{foreach from=$aProperties item=oProperty}
								<tr id="{$oProperty->getId()}" class="cursor-x">
									<td class="center">
										{$oProperty->getName()}
									</td>
									<td class="center">
										{$oProperty->getDescription()}
									</td>
									<td class="center">
										<a href="{router page='admin'}mm_property_edit/{$oProperty->getId()}/">
											<i class="icon-edit tip-top" title="{$aLang.plugin.minimarket.edit}"></i></a>
										<a href="{router page='admin'}mm_property_delete/{$oProperty->getId()}/?security_ls_key={$ALTO_SECURITY_KEY}"
											onclick="return confirm('{$aLang.plugin.minimarket.admin_property_detele_confirm}');">
												<i class="icon-remove tip-top"title="{$aLang.plugin.minimarket.delete}"></i>
										</a>
									</td>
								</tr>
							{/foreach}
						{/if}
                        </tbody>
                    </table>					
					<a class="btn" href="{router page="admin"}mm_property_add/{$_aRequest.id}/">
						<i class="icon-plus-sign"></i> {$aLang.plugin.minimarket.admin_attribut_properties_add}
					</a>
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