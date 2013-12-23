{extends file='actions/ActionAdmin/info/index.tpl'}{block name="content-bar"}    <div class="btn-group">        <a href="{router page='admin'}mm_categories/" class="btn"><i class="icon-chevron-left"></i></a>    </div>{/block}{block name="content-body"}	<form method="POST" class="form-horizontal uniform">		<input type="hidden" name="security_ls_key" value="{$ALTO_SECURITY_KEY}"/>		<div class="b-wbox">			<div class="b-wbox-header">                {if $sEvent=='mm_category_add'}                    <div class="b-wbox-header-title">{$aLang.plugin.minimarket.admin_category_add_title}</div>                {elseif $sEvent=='mm_category_edit'}                    <div class="b-wbox-header-title">{$aLang.plugin.minimarket.admin_category_edit_title}: "{$oCategory->getURL()|escape:'html'}"</div>                {/if}			</div>			<div class="b-wbox-content nopadding">				<div class="control-group">					<label class="control-label" for="name">{$aLang.plugin.minimarket.name}:</label>					<div class="controls">						<input id="name" class="input-text" type="text" value="{$_aRequest.name}" name="name">						<span class="help-block">{$aLang.plugin.minimarket.admin_category_adding_name_example}</span>					</div>				</div>				<div class="control-group">					<label class="control-label" for="url">{$aLang.plugin.minimarket.url}:</label>					<div class="controls">						<input id="url" class="input-text" type="text" value="{$_aRequest.url}" name="url">						<span class="help-block">{$aLang.plugin.minimarket.admin_category_adding_url_example}</span>					</div>				</div>				<div class="control-group">                    <label class="control-label" for="description">{$aLang.plugin.minimarket.description}:</label>                    <div class="controls">						<textarea name="description" id="description" class="input-text" rows="5">{$_aRequest.description}</textarea>                    </div>				</div>				<div class="control-group">					<label class="control-label" for="category_parent">{$aLang.plugin.minimarket.admin_category_adding_parent}:</label>                    <div class="controls">                        <select name="category_select" class="input-text">                            <option value="0"{if !$_aRequest.category_select} selected{/if}>{$aLang.plugin.minimarket.admin_category_adding_select_default}</option>							{if $aCategories && is_array($aCategories) && count($aCategories) > 0}								{foreach from=$aCategories item=Category key=key}										<option{if $_aRequest.category_select==$key} selected{/if} value="{$key}">{for $for=1 to $Category.position}&mdash;{/for}{if $for} {/if}{$Category.name}</option>								{/foreach}							{/if}                        </select>                        <span class="help-block">{$aLang.plugin.minimarket.admin_category_adding_select_example}</span>                    </div>				</div>			</div>            <div class="b-wbox-content nopadding">                <div class="form-actions">                    <button type="submit" class="btn btn-primary"                            name="button_submit">{$aLang.plugin.minimarket.save}</button>                </div>            </div>		</div>	</form>{/block}