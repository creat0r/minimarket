{extends file='actions/ActionAdmin/info/index.tpl'}

{block name="content-bar"}
	<div class="btn-group">
		<a href="{router page='admin'}mm_attribut_add/" class="btn btn-primary tip-top"
		   title="{$aLang.plugin.minimarket.admin_attribut_add}"><i class="icon-plus-sign"></i></a>
	</div>
    <div class="btn-group">
        <a class="btn {if $sEvent=='mm_attributes'}active{/if}" href="{router page='admin'}mm_attributes/">
            {$aLang.plugin.minimarket.admin_attributes}
        </a>
        <a class="btn {if $sEvent=='mm_attributes_categories'}active{/if}" href="{router page='admin'}mm_attributes_categories/">
            {$aLang.plugin.minimarket.admin_attributes_category}
        </a>
    </div>
{/block}

{block name="content-body"}

	{if count($aAttributes) > 0}
        <div class="b-wbox">
            <div class="b-wbox-content nopadding">
				<table class="table table-striped table-condensed pages-list" id="sortable">
                    <thead>
                    <tr>
                        <th>{$aLang.plugin.minimarket.admin_category_attribut}</th>
                        <th>{$aLang.plugin.minimarket.name}</th>
                        <th class="span2">{$aLang.plugin.minimarket.action}</th>
                    </tr>
                    </thead>
					<tbody class="content">
						{foreach from=$aAttributes item=oAttribut}
                        <tr id="{$oAttribut->getId()}" class="cursor-x">
                            <td class="center">
                                {foreach from=$aAttributesCategories item=oAttributesCategory}
									{if $aAttributesCategoryAttribut && isset($aAttributesCategoryAttribut[$oAttributesCategory->getId()]) && in_array($oAttribut->getId(), $aAttributesCategoryAttribut[$oAttributesCategory->getId()])}
										{$oAttributesCategory->getName()}
									{/if}
								{/foreach}
                            </td>
                            <td class="center">
                                {$oAttribut->getName()}
                            </td>
                            <td class="center">
                                <a href="{router page='admin'}mm_attribut_edit/{$oAttribut->getId()}/">
                                    <i class="icon-edit tip-top" title="{$aLang.plugin.minimarket.edit}"></i>
								</a>
                                <a href="{router page='admin'}mm_attribut_delete/{$oAttribut->getId()}/?security_ls_key={$ALTO_SECURITY_KEY}"
									onclick="return confirm('{$aLang.plugin.minimarket.admin_attribut_detele_confirm}');">
										<i class="icon-remove tip-top"title="{$aLang.plugin.minimarket.delete}"></i>
                                </a>
                            </td>
                        </tr>
						{/foreach}
					</tbody>
				</table>
			</div>
		</div>
	{/if}
{/block}