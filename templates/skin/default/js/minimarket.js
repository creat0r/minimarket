var ls = ls || {};

ls.minimarket = (function ($) {

	this.addAttribut = function(block_id) {
		$('#attribut_block_'+block_id).css('display','block');
		$('#attribut_select').attr('selected','true');
	}

	this.deleteAttribut = function(block_id) {
		$('#attribut_block_'+block_id).css('display','none');
		$('#select_property_'+block_id+'_chzn ul li').each(function() {
			$(this).hasClass('search-choice')?$(this).find('a').click():'';
		});
	}

	this.imgUpdate = function(id,url,href) {
		$('#product_img_main img').attr('src',url);
		$('#product_img_main').attr('href',href);
		$('.product-img-container ul li').each(function() {
			$(this).find('span').removeClass('product-img-preview-active');
		});
		$('#product_img_preview_'+id).addClass('product-img-preview-active');
	}
	
	return this;

}).call(ls.minimarket || {},jQuery);

jQuery(document).ready(function($){

	// Автокомплит
	ls.autocomplete.add($(".autocomplete-characteristics-sep"), aRouter['mm_ajax']+'mm_autocompleter/characteristics/', true);
	ls.autocomplete.add($(".autocomplete-features-sep"), aRouter['mm_ajax']+'mm_autocompleter/features/', true);
	
	// $('#photoset-upload-form').jqm({trigger: '#photoset-start-upload'});
	
	// Транслитерация
	$(document).ready(function(){
		// $('.wrapper-content').liTranslit({
			// elName: '#product_manufacturer_code',    //Класс елемента с именем
			// elAlias: '#product_url'   //Класс елемента с алиасом
		// });
	})
	
	$(".block-filter-pros-attribut-href").click(function () {
		var value = $(this).attr('id');
		value = value.replace("block_filter_pros_attribut_href_", "block_filter_pros_features_");
		if($('#'+value).css('display')=='none') {
			$('#'+value).css('display','block');
		} else {
			$('#'+value).css('display','none');
		}
		return false;
	});
	
	var config = {
		'.chzn-select'           : {width:"100%"}
	}
	for (var selector in config) {
		$(selector).chosen(config[selector]);
	}

});