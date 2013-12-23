{extends file='actions/ActionAdmin/info/index.tpl'}

{block name="content-bar"}
	<div class="btn-group">
		<a href="{router page='admin'}mm_category_add/" class="btn btn-primary tip-top"
		   title="{$aLang.plugin.minimarket.admin_category_add}"><i class="icon-plus-sign"></i></a>
	</div>
{/block}

{block name="content-body"}
	{if $aCategories && is_array($aCategories) && count($aCategories) > 0}
        <div class="b-wbox">
            <div class="b-wbox-content nopadding">
				<table class="table table-striped table-condensed pages-list" id="sortable">
                    <thead>
                    <tr>
                        <th class="span4">{$aLang.plugin.minimarket.name}</th>
                        <th>{$aLang.plugin.minimarket.url}</th>
                        <th class="span2">{$aLang.plugin.minimarket.action}</th>
                    </tr>
                    </thead>
					<tbody>
						{foreach from=$aCategories item=Category key=key}
                        <tr>
                            <td>
                                {for $for=1 to $Category.position}&mdash;{/for}{if !$for}<b>{$Category.name}</b>{else}&nbsp;{$Category.name}{/if}
                            </td>
                            <td class="center">
                                {$Category.url}
                            </td>
                            <td class="center">
                                <a href="{router page='admin'}mm_category_edit/{$key}/">
                                    <i class="icon-edit tip-top" title="{$aLang.plugin.minimarket.edit}"></i>
								</a>
                                <a href="{router page='admin'}mm_category_delete/{$key}/?security_ls_key={$ALTO_SECURITY_KEY}"
									onclick="return confirm('{$aLang.plugin.minimarket.admin_category_detele_confirm}');">
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