{extends file='actions/ActionAdmin/info/index.tpl'}

{block name="content-bar"}
	<div class="btn-group">
		<a href="{router page='admin'}attributescategoryadd/" class="btn btn-primary tip-top"
		   title="{$aLang.plugin.minimarket.add_attribut_category}"><i class="icon-plus-sign"></i></a>
	</div>
    <div class="btn-group">
        <a class="btn {if $sEvent=='attributes'}active{/if}" href="{router page='admin'}attributes/">
            {$aLang.plugin.minimarket.attributes}
        </a>
        <a class="btn {if $sEvent=='attributescategories'}active{/if}" href="{router page='admin'}attributescategories/">
            {$aLang.plugin.minimarket.attributes_category}
        </a>
    </div>
{/block}

{block name="content-body"}
	<script>
        var fixHelper = function (e, ui) {
            ui.children().each(function () {
                $(this).width($(this).width());
            });
            return ui;
        };

        var sortSave = function (e, ui) {
            var notes = $('#sortable tbody.content tr');
            if (notes.length > 0) {
                var order = [];
                $.each(notes.get().reverse(), function (index, value) {
                    order.push({ 'id': $(value).attr('id'), 'order': index });
                });
                ls.ajax(aRouter['admin'] + 'ajaxchangeordertaxonomies/', { 'order': order }, function (response) {
                    if (!response.bStateError) {
                        ls.msg.notice(response.sMsgTitle, response.sMsg);
                    } else {
                        ls.msg.error(response.sMsgTitle, response.sMsg);
                    }
                });
            }
        };

        $(function () {
            $("#sortable tbody.content").sortable({
                helper: fixHelper
            });
            $("#sortable tbody.content").disableSelection();

            $("#sortable tbody.content").sortable({
                stop: sortSave
            });
        });
    </script>
	{if count($aAttributesCategory)>0}
        <div class="b-wbox">
            <div class="b-wbox-content nopadding">
				<table class="table table-striped table-condensed pages-list" id="sortable">
                    <thead>
                    <tr>
                        <th>{$aLang.plugin.minimarket.attribut_name}</th>
                        <th class="span2">{$aLang.plugin.minimarket.attribut_actions}</th>
                    </tr>
                    </thead>
					<tbody class="content">
						{foreach from=$aAttributesCategory item=oAttributCategory}
                        <tr id="{$oAttributCategory->getId()}" class="cursor-x">
                            <td class="center">
                                {$oAttributCategory->getName()}
                            </td>
                            <td class="center">
                                <a href="{router page='admin'}attributescategoryedit/{$oAttributCategory->getId()}/">
                                    <i class="icon-edit tip-top" title="{$aLang.plugin.minimarket.attribut_edit}"></i>
								</a>
                                <a href="{router page='admin'}attributescategorydelete/{$oAttributCategory->getId()}/?security_ls_key={$ALTO_SECURITY_KEY}"
									onclick="return confirm('{$aLang.plugin.minimarket.attributes_category_detele_confirm}');">
										<i class="icon-remove tip-top"title="{$aLang.plugin.minimarket.attribut_remove}"></i>
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