{extends file='actions/ActionAdmin/info/index.tpl'}	{block name="content-bar"}	<div class="btn-group">		<a href="#" class="btn btn-primary disabled"><i class="icon-plus-sign"></i></a>	</div>{/block}{block name="content-body"}		<div class="b-wbox">		<div class="b-wbox-content nopadding">			<table class="table table-striped table-condensed pages-list">				<thead>				<tr>					<th>{$aLang.plugin.minimarket.admin_order_number}</th>					<th>{$aLang.plugin.minimarket.admin_order_time_order_init}</th>					<th>{$aLang.plugin.minimarket.admin_order_time_selected_pay_system}</th>					<th>{$aLang.plugin.minimarket.admin_order_time_payment_success}</th>					<th>{$aLang.plugin.minimarket.admin_order_status}</th>					<th>{$aLang.plugin.minimarket.action}</th>				</tr>				</thead>				<tbody class="content">					{foreach from=$aOrder item=oOrder}					<tr>						<td class="center">							{$oOrder->getId()}						</td>						<td class="center">							{$oOrder->getTimeOrderInit()|date_format:'%Y-%m-%d %H:%M:%S'}						</td>						<td class="center">							{$oOrder->getTimeSelectedPaySystem()|date_format:'%Y-%m-%d %H:%M:%S'}						</td>						<td class="center">							{$oOrder->getTimePaymentSuccess()|date_format:'%Y-%m-%d %H:%M:%S'}						</td>						<td class="center">							{if $oOrder->getStatus() == $ORDER_STATUS_DELIVERED}								{$aLang.plugin.minimarket.admin_order_status_ok}							{elseif $oOrder->getStatus() == $ORDER_STATUS_PAYD}								{$aLang.plugin.minimarket.admin_order_status_paid}							{elseif $oOrder->getStatus() == $ORDER_STATUS_PAY_SYSTEM_SELECTED}								{$aLang.plugin.minimarket.admin_order_status_adopted}							{else}								{$aLang.plugin.minimarket.admin_order_status_formation}							{/if}						</td>						<td class="center">							<a href="{router page='admin'}mm_order_edit/{$oOrder->getId()}/">								<i class="icon-edit tip-top" title="{$aLang.plugin.minimarket.edit}"></i>							</a>							<a href="{router page='admin'}mm_order_delete/{$oOrder->getId()}/?security_ls_key={$ALTO_SECURITY_KEY}"								onclick="return confirm('{$aLang.plugin.minimarket.admin_order_detele_confirm}');">									<i class="icon-remove tip-top" title="{$aLang.plugin.minimarket.admin_order_remove}"></i>							</a>						</td>					</tr>					{/foreach}				</tbody>			</table>		</div>	</div>		{include file="inc.paging.tpl"}	{/block}