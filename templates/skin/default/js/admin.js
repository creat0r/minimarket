jQuery(document).ready(function($){
	
	/*
	 * »нициализаци€ JS дл€ сортировки строк в таблице
	 */
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

});