{extends file='actions/ActionAdmin/info/index.tpl'}{block name="content-bar"}	<div class="btn-group">		<a href="{router page='admin'}mm_delivery_service_add/" class="btn btn-primary tip-top"			title="{$aLang.plugin.minimarket.admin_delivery_service_add}"><i class="icon-plus-sign"></i></a>	</div>    <div class="btn-group">		<a class="btn {if $sEvent=='mm_delivery_services'}active{/if}" href="{router page='admin'}mm_delivery_services/">			{$aLang.plugin.minimarket.admin_delivery_service_tunable}		</a>		<a class="btn {if $sEvent=='mm_delivery_services_automatic'}active{/if}" href="{router page='admin'}mm_delivery_services_automatic/">			{$aLang.plugin.minimarket.admin_delivery_service_automatic}		</a>    </div>{/block}{block name="content-body"}	{if $aDeliveryServices}        <div class="b-wbox">            <div class="b-wbox-content nopadding">				<table class="table table-striped table-condensed pages-list">                    <thead>                    <tr>                        <th>{$aLang.plugin.minimarket.name}</th>                        <th class="span2">{$aLang.plugin.minimarket.action}</th>                    </tr>                    </thead>					<tbody>						{foreach from=$aDeliveryServices item=oDeliveryService}                        <tr>                            <td class="center">                                {$oDeliveryService->getName()}                            </td>                            <td class="center">                                <a href="{router page='admin'}mm_delivery_service_edit/{$oDeliveryService->getId()}/">                                    <i class="icon-edit tip-top" title="{$aLang.plugin.minimarket.edit}"></i>								</a>                                <a href="{router page='admin'}mm_delivery_service_delete/{$oDeliveryService->getId()}/?security_ls_key={$ALTO_SECURITY_KEY}"									onclick="return confirm('{$aLang.plugin.minimarket.admin_delivery_service_detele_confirm}');">										<i class="icon-remove tip-top"title="{$aLang.plugin.minimarket.delete}"></i>                                </a>                            </td>                        </tr>						{/foreach}					</tbody>				</table>			</div>		</div>	{/if}{/block}