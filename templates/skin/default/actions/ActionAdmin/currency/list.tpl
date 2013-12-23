{extends file='actions/ActionAdmin/info/index.tpl'}

{block name="content-bar"}
	<div class="btn-group">
		<a href="{router page='admin'}mm_currency_add/" class="btn btn-primary tip-top"
		   title="{$aLang.plugin.minimarket.admin_currency_add}"><i class="icon-plus-sign"></i></a>
	</div>
{/block}

{block name="content-body"}

	{if count($aCurrency) > 0}
        <div class="b-wbox">
            <div class="b-wbox-content nopadding">
				<table class="table table-striped table-condensed pages-list">
                    <thead>
                    <tr>
						<th>{$aLang.plugin.minimarket.name}</th>
                        <th>{$aLang.plugin.minimarket.admin_currency_nominal}</th>
                        <th>{$aLang.plugin.minimarket.admin_currency_course}</th>
                        <th class="span2">{$aLang.plugin.minimarket.action}</th>
                    </tr>
                    </thead>
					<tbody class="content">
						{foreach from=$aCurrency item=oCurrency}
                        <tr>
                            <td class="center">
								{$oCurrency->getKey()}
                            </td>
                            <td class="center">
                                {$oCurrency->getNominal()}
                            </td>
                            <td class="center">
                                {$oCurrency->getCourse() / {cfg name='plugin.minimarket.settings.factor'}}
                            </td>
                            <td class="center">
                                <a href="{router page='admin'}mm_currency_edit/{$oCurrency->getId()}/">
                                    <i class="icon-edit tip-top" title="{$aLang.plugin.minimarket.edit}"></i>
								</a>
                                <a href="{router page='admin'}mm_currency_delete/{$oCurrency->getId()}/?security_ls_key={$ALTO_SECURITY_KEY}"
									onclick="return confirm('{$aLang.plugin.minimarket.admin_currency_detele_confirm}');">
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