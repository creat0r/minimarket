{extends file='actions/ActionAdmin/info/index.tpl'}

{block name="content-bar"}
	<div class="btn-group">
		<a href="{router page='admin'}mm_brand_add/" class="btn btn-primary tip-top"
		   title="{$aLang.plugin.minimarket.brand_add}"><i class="icon-plus-sign"></i></a>
	</div>
{/block}

{block name="content-body"}
	{if count($aBrands)>0}
        <div class="b-wbox">
            <div class="b-wbox-content nopadding">
				<table class="table table-striped table-condensed pages-list" id="sortable">
                    <thead>
                    <tr>
                        <th class="span4">{$aLang.plugin.minimarket.brand_name}</th>
                        <th>{$aLang.plugin.minimarket.brand_url}</th>
                        <th class="span2">{$aLang.plugin.minimarket.brand_actions}</th>
                    </tr>
                    </thead>
					<tbody class="content">
						{foreach from=$aBrands item=oBrand}
                        <tr class="cursor-x">
                            <td class="center">
                                {$oBrand->getName()}
                            </td>
                            <td class="center">
                                {$oBrand->getURL()}
                            </td>
                            <td class="center">
                                <a href="{router page='admin'}mm_brand_edit/{$oBrand->getId()}/">
                                    <i class="icon-edit tip-top" title="{$aLang.plugin.minimarket.brand_edit}"></i>
								</a>
                                <a href="{router page='admin'}mm_brand_delete/{$oBrand->getId()}/?security_ls_key={$ALTO_SECURITY_KEY}"
									onclick="return confirm('{$aLang.plugin.minimarket.brand_detele_confirm}');">
										<i class="icon-remove tip-top"title="{$aLang.plugin.minimarket.brand_remove}"></i>
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