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
	
	this.number_format = function(number, decimals, dec_point, thousands_sep) {

		var i, j, kw, kd, km;

		// input sanitation & defaults
		if( isNaN(decimals = Math.abs(decimals)) ){
			decimals = 2;
		}
		if( dec_point == undefined ){
			dec_point = ",";
		}
		if( thousands_sep == undefined ){
			thousands_sep = ".";
		}

		i = parseInt(number = (+number || 0).toFixed(decimals)) + "";

		if( (j = i.length) > 3 ){
			j = j % 3;
		} else{
			j = 0;
		}

		km = (j ? i.substr(0, j) + thousands_sep : "");
		kw = i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands_sep);
		kd = (decimals ? dec_point + Math.abs(number - i).toFixed(decimals).replace(/-/, 0).slice(2) : "");
		
		return km + kw + kd;
	}
	
	this.checkCart = function() {
		var summ_products = 0;
		$('.cart-count-input').each(function() {
			var summ_product = parseFloat($(this).val()) * parseFloat($('[name="cart_price_hidden_'+$(this).attr('id').replace('cart_count_input_','')+'"]').val());
			if (isNaN(summ_product)) summ_product = 0;
			summ_products += summ_product;
			var html = 0;
			if (summ_product > 0) {
				html = ls.minimarket.number_format(summ_product , 2, ',', ' ') + '&nbsp;$';
			}
			$('#cart_price_'+$(this).attr('id').replace('cart_count_input_','')).html(html);
		});
		var html = 0;
		if (summ_products > 0) {
			html = ls.minimarket.number_format(summ_products , 2, ',', ' ') + '&nbsp;$';
		}
		$('.cart-final-price').html(html);
	}
	
	return this;

}).call(ls.minimarket || {},jQuery);

jQuery(document).ready(function($){

	// Автокомплит
	ls.autocomplete.add($(".autocomplete-characteristics-sep"), aRouter['mm_ajax']+'mm_autocompleter/characteristics/', true);
	ls.autocomplete.add($(".autocomplete-features-sep"), aRouter['mm_ajax']+'mm_autocompleter/features/', true);
	
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

	// Изменение количества товара в корзине
    $(".cart-count-input").keyup(function(event) {
		// если вверх
		if(event.keyCode==38) {
			if($(this).val()=='') $(this).attr('value',0);
			$(this).attr('value',parseInt($(this).val(),10)+1);
		}
		// если вниз
		if(event.keyCode==40 && parseInt($(this).val(),10)>0) {
			if($(this).val()=='') $(this).attr('value',0);
			$(this).attr('value',parseInt($(this).val(),10)-1);
		}
		ls.minimarket.checkCart();
	});
    $(".cart-count-input").keydown(function(event) {
        // Разрешаем: backspace, delete, tab и escape
        if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 || 
             // Разрешаем: Ctrl+A
            (event.keyCode == 65 && event.ctrlKey === true) || 
             // Разрешаем: home, end, влево, вправо
            (event.keyCode >= 35 && event.keyCode <= 39)) {
                 // Ничего не делаем
                 return;
        }
        else {
            // Убеждаемся, что это цифра, и останавливаем событие keypress
            if ((event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) {
                event.preventDefault();
            }
        }
    });
	
	// Посылаем Ajax запрос на обновление количества товара при потере фокуса
	$(".cart-count-input").focusout(function() {
		var idProduct = $(this).attr('id').replace('cart_count_input_','');
		var iCount = $(this).val();
		var url = aRouter['cart']+'update/';
		var params = {product: idProduct, count: iCount};
		ls.ajax(url, params, function(result) {});
	});
	
	// Подсчет стоимости с учетом доставки
	$(".order-delivery-radio").click(function() {
		var idDeliveryService = $(this).attr('id').replace('order_delivery_radio_','');
		var fCost = parseFloat($('[name="order_delivert_cost_hidden_'+idDeliveryService+'"]').val());
		var fCartSum = parseFloat($('[name="order_delivert_final_cost_hidden"]').val());
		var html = 0;
		if (fCartSum + fCost > 0) {
			html = ls.minimarket.number_format(fCartSum + fCost, 2, ',', ' ') + '&nbsp;$'
		}
		$('.order-delivert-final-cost').html(html);
	});
});