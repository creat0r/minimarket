<section class="block">
	<header class="block-header sep">
		<h3>{$aLang.plugin.minimarket.filter}</h3>

		<ul class="nav nav-pills">
			<li class="{if !$sPros}active {/if}js-block-filter-item" data-type="lover"><a href="#">{$aLang.plugin.minimarket.lover}</a></li>
			<li class="{if $sPros}active {/if}js-block-filter-item" data-type="pros"><a href="#">{$aLang.plugin.minimarket.pros}</a></li>
		</ul>
	</header>
	
	<div class="block-content">
		{assign var=aLoverSortParams value=array()}
		{assign var=aProsSortParams value=array()}
		{if isset($aSortParams['lover'])}
			{assign var=aLoverSortParams value="~"|explode:$aSortParams['lover']}
		{/if}
		{if isset($aSortParams['pros'])}
			{assign var=aProsSortParams value="~"|explode:$aSortParams['pros']}
		{/if}
		<div class="js-block-filter-content block-filter-lover" data-type="lover"{if $sPros} style="display: none;"{/if}>
			{foreach from=$aFeatures item=FeaturesName key=key_features}<a{if in_array($FeaturesName,$aLoverSortParams)} class="block-filter-lover-href-active"{/if} href="{$sFullURL}{if in_array($FeaturesName,$aLoverSortParams)}{if count($aLoverSortParams)==1}{else}{assign var=var_slofer value=0}?c[lover]={foreach from=$aLoverSortParams item=sLover}{if $sLover!=$FeaturesName && $sLover!=''}{if $var_slofer}~{/if}{$sLover}{assign var=var_slofer value=1}{/if}{/foreach}{/if}{elseif isset($aSortParams['lover'])}?c[lover]={$aSortParams['lover']}~{$FeaturesName}{else}?c[lover]={$FeaturesName}{/if}">{$FeaturesName}</a>{if count($aFeatures)-1!=$key_features}&nbsp;<span class="filter-bull-features">&bull;</span> {/if}{/foreach}
		</div>
		<div class="js-block-filter-content block-filter-pros" data-type="pros"{if !$sPros} style="display: none;"{/if}>
			<ul class="block-filter-pros-attribut">
			{if $aProperties}
				{assign var=attributes_category value=0}
				{foreach from=$aProperties item=oTaxonomy}
					{if $oTaxonomy->getType() == 'attributes_category'}
						<li class="block-filter-pros-attributes-category{if $attributes_category} pt-15{/if}">{$oTaxonomy->getName()}</li>
						{assign var=attributes_category value=1}
					{/if}
					{if $oTaxonomy->getType() == 'attribut'}
						<li>
							<a class="block-filter-pros-attribut-href" id="block_filter_pros_attribut_href_{$oTaxonomy->getId()}" href="#">{$oTaxonomy->getName()}</a>
							<div style="clear:both;"></div>
							<ul {if in_array($oTaxonomy->getId(),$aIdAttributesActive)}style="display:block;"{/if}class="block-filter-pros-properties" id="block_filter_pros_features_{$oTaxonomy->getId()}">
								{foreach from=$aProperties item=oProperty}
									{if $oProperty->getParentId()==$oTaxonomy->getId()}
										<li>
											<a class="block-filter-pros-properties-href{if in_array($oProperty->getId(),$aProsSortParams)} block-filter-pros-properties-href-active{/if}" 
													href="
													{$sFullURL}{if in_array($oProperty->getId(),$aProsSortParams)}{if count($aProsSortParams)==1}{else}{assign var=var_spros value=0}?c[pros]={foreach from=$aProsSortParams item=sPros}{if $sPros!=$oProperty->getId() && $sPros!=''}{if $var_spros}~{/if}{$sPros}{assign var=var_spros value=1}{/if}{/foreach}{/if}{elseif isset($aSortParams['pros'])}?c[pros]={$aSortParams['pros']}~{$oProperty->getId()}{else}?c[pros]={$oProperty->getId()}{/if}
													">
													{$oProperty->getName()}
											</a>
										</li>
									{/if}
								{/foreach}
								<div style="clear:both;"></div>
							</ul>
						</li>
					{/if}
				{/foreach}
			{/if}
			</ul>
		</div>
	</div>
</section>