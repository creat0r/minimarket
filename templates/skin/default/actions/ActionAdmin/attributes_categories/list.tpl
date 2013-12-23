{extends file='actions/ActionAdmin/info/index.tpl'}

{block name="content-bar"}
	<div class="btn-group">
		<a href="{router page='admin'}mm_attributes_category_add/" class="btn btn-primary tip-top"
		   title="{$aLang.plugin.minimarket.admin_attributes_category_add}"><i class="icon-plus-sign"></i></a>
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
	{if count($aAttributesCategory) > 0}
        <div class="b-wbox">
            <div class="b-wbox-content nopadding">
				<table class="table table-striped table-condensed pages-list" id="sortable">
                    <thead>
                    <tr>
                        <th>{$aLang.plugin.minimarket.name}</th>
                        <th class="span2">{$aLang.plugin.minimarket.action}</th>
                    </tr>
                    </thead>
					<tbody class="content">
						{foreach from=$aAttributesCategory item=oAttributCategory}
                        <tr id="{$oAttributCategory->getId()}" class="cursor-x">
                            <td class="center">
                                {$oAttributCategory->getName()}
                            </td>
                            <td class="center">
                                <a href="{router page='admin'}mm_attributes_category_edit/{$oAttributCategory->getId()}/">
                                    <i class="icon-edit tip-top" title="{$aLang.plugin.minimarket.edit}"></i>
								</a>
                                <a href="{router page='admin'}mm_attributes_category_delete/{$oAttributCategory->getId()}/?security_ls_key={$ALTO_SECURITY_KEY}"
									onclick="return confirm('{$aLang.plugin.minimarket.admin_attributes_category_detele_confirm}');">
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