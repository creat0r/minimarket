{extends file='actions/ActionAdmin/info/index.tpl'}

{block name="content-bar"}
    <div class="btn-group">
        <a href="{router page='admin'}attributes/" class="btn"><i class="icon-chevron-left"></i></a>
    </div>
    <div class="btn-group">
        <a class="btn active" href="{router page='admin'}attributes/">
            {$aLang.plugin.minimarket.attributes}
        </a>
        <a class="btn" href="{router page='admin'}attributescategories/">
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

<div class="span12">
	<form method="POST" name="attributadd" enctype="multipart/form-data" class="form-horizontal uniform">
		<input type="hidden" name="security_ls_key" value="{$ALTO_SECURITY_KEY}"/>
		<div class="b-wbox">
			<div class="b-wbox-header">
                {if $sEvent=='attributadd'}
                    <div class="b-wbox-header-title">{$aLang.plugin.minimarket.adding_attribut}</div>
                {elseif $sEvent=='attributedit'}
                    <div class="b-wbox-header-title">{$aLang.plugin.minimarket.attribut_edit_title}: {$oAttribut->getName()|escape:'html'}</div>
                {/if}
			</div>
			<div class="b-wbox-content nopadding">
				<div class="control-group">
					<label class="control-label" for="adding_attribut_name">{$aLang.plugin.minimarket.adding_attribut_name}:</label>
					<div class="controls">
						<input id="adding_attribut_name" class="input-text" type="text" value="{$_aRequest.adding_attribut_name}" name="adding_attribut_name">
						<span class="help-block">{$aLang.plugin.minimarket.adding_attribut_name_example}</span>
					</div>
				</div>
				<div class="control-group">
                    <label class="control-label" for="adding_attribut_description">{$aLang.plugin.minimarket.adding_attribut_description}:</label>
                    <div class="controls">
						<textarea name="adding_attribut_description" id="adding_attribut_description" class="input-text" rows="5">{$_aRequest.adding_attribut_description}</textarea>
                    </div>
				</div>
			</div>
			{if $sEvent=='attributedit'}
                <div class="b-wbox-header">
                    <div class="b-wbox-header-title">{$aLang.plugin.minimarket.attribut_properties_added}</div>
                </div>
				<div class="b-wbox-content">
                    <table class="table table-bordered" id="sortable">
                        <thead>
                        <tr>
                            <th>{$aLang.plugin.minimarket.attribut_properties_name}</th>
                            <th>{$aLang.plugin.minimarket.attribut_properties_description}</th>
                            <th class="span2">{$aLang.plugin.minimarket.attribut_properties_actions}</th>
                        </tr>
                        </thead>

                        <tbody class="content">
                        {foreach from=$aProperties item=oProperty}
                            <tr id="{$oProperty->getId()}" class="cursor-x">
                                <td class="center">
                                    {$oProperty->getName()}
                                </td>
                                <td class="center">
                                    {$oProperty->getDescription()}
                                </td>
                                <td class="center">
                                    <a href="{router page='admin'}propertyedit/{$oProperty->getId()}/">
										<i class="icon-edit tip-top" title="{$aLang.plugin.minimarket.attribut_edit}"></i></a>
                                    <a href="{router page='admin'}propertydelete/{$oProperty->getId()}/?security_ls_key={$ALTO_SECURITY_KEY}"
										onclick="return confirm('{$aLang.plugin.minimarket.property_detele_confirm}');">
											<i class="icon-remove tip-top"title="{$aLang.plugin.minimarket.attribut_remove}"></i>
									</a>
                                </td>
                            </tr>
                        {/foreach}
                        </tbody>
                    </table>
					<div class="control-group">
						<a class="btn fl-r" href="{router page="admin"}propertyadd/{$_aRequest.attribut_id}/">
							<i class="icon-plus-sign"></i> {$aLang.plugin.minimarket.attribut_add_properties}
						</a>
					</div>
				</div>
			{/if}
            <div class="b-wbox-content nopadding">
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary"
                            name="submit_attribut_add">{$aLang.plugin.minimarket.content_submit}</button>
                </div>
            </div>
		</div>
	</form>
</div>

{/block}