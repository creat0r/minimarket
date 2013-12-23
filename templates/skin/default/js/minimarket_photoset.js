var ls = ls || {};

ls.minimarket_photoset = (function ($) {

	this.idLast = 0;
	this.isLoading = false;
	this.swfu;
	
	this.initSwfUpload = function(opt) {
		opt=opt || {};
		opt.button_placeholder_id = 'photoset-start-upload';
		opt.post_params.ls_photoset_target_tmp = $.cookie('ls_photoset_target_tmp') ? $.cookie('ls_photoset_target_tmp') : 0;
		opt.upload_url = aRouter['mm_product'] + "upload";
		opt.button_text = '<span class="button">'+ls.lang.get('plugin.minimarket.product_photoset_upload_choose')+'</span>';
		opt.button_width = 140;
		opt.button_text_left_padding = 0;
		
		$(ls.swfupload).unbind('load').bind('load',function() {
			this.swfu = ls.swfupload.init(opt);
			
			$(this.swfu).bind('eUploadProgress',this.swfHandlerUploadProgress);
			$(this.swfu).bind('eFileDialogComplete',this.swfHandlerFileDialogComplete);
			$(this.swfu).bind('eUploadSuccess',this.swfHandlerUploadSuccess);
			$(this.swfu).bind('eUploadComplete',this.swfHandlerUploadComplete);
		}.bind(this));
		
		ls.swfupload.loadSwf();
	}
	
	this.upload = function() {
		ls.minimarket_photoset.addPhotoEmpty();
		ls.ajaxSubmit(aRouter['mm_product'] + 'upload/', $('#photoset-upload-form'), function(data){
			if (data.bStateError) {
				$('#photoset_photo_empty').remove();
				ls.msg.error(data.sMsgTitle,data.sMsg);
			} else {
				ls.minimarket_photoset.addPhoto(data);
			}
		});
		ls.minimarket_photoset.closeForm();
	}
	
	this.swfHandlerUploadProgress = function(e, file, bytesLoaded, percent) {
		$('#photoset_photo_empty_progress').text(file.name+': '+( percent==100 ? 'resize..' : percent +'%'));
	}
	
	this.swfHandlerFileDialogComplete = function(e, numFilesSelected, numFilesQueued) {
		if (numFilesQueued>0) {
			ls.minimarket_photoset.addPhotoEmpty();
		}
	}
	
	this.swfHandlerUploadSuccess = function(e, file, serverData) {
		ls.minimarket_photoset.addPhoto(jQuery.parseJSON(serverData));
	}
	
	this.swfHandlerUploadComplete = function(e, file, next) {
		if (next>0) {
			ls.minimarket_photoset.addPhotoEmpty();
		}
	}
	
	this.addPhotoEmpty = function() {
		template = '<li id="photoset_photo_empty"><img src="'+DIR_STATIC_SKIN + '/images/loader.gif'+'" alt="image" style="margin-left: 35px;margin-top: 20px;" />'
					+'<div id="photoset_photo_empty_progress" style="height: 60px;width: 350px;padding: 3px;border: 1px solid #DDDDDD;"></div><br /></li>';
		$('#swfu_images').append(template);
	}
	
	this.addPhoto = function(response) {
		$('#photoset_photo_empty').remove();
		if (!response.bStateError) {
			template = '<li id="photo_'+response.id+'"><img src="'+response.file+'" alt="image" />'
						+'<textarea onBlur="ls.minimarket_photoset.setPreviewDescription('+response.id+', this.value)"></textarea><br />'
						+'<a href="javascript:ls.minimarket_photoset.deletePhoto('+response.id+')" class="image-delete">'+ls.lang.get('plugin.minimarket.product_photoset_photo_delete')+'</a>'
						+'<span id="photo_preview_state_'+response.id+'" class="photo-preview-state"><a href="javascript:ls.minimarket_photoset.setPreview('+response.id+')" class="mark-as-preview">'+ls.lang.get('plugin.minimarket.product_photoset_mark_as_preview')+'</a></span></li>';
			$('#swfu_images').append(template);
			ls.msg.notice(response.sMsgTitle,response.sMsg);
		} else {
			ls.msg.error(response.sMsgTitle,response.sMsg);
		}
		ls.minimarket_photoset.closeForm();
	}
	
	this.closeForm = function() {
		$('#photoset-upload-form').jqmHide();
	}
	
	this.setPreview = function(id) {
		$('#product_main_photo').val(id);

		$('.marked-as-preview').each(function (index, el) {
			$(el).removeClass('marked-as-preview');
			tmpId = $(el).attr('id').slice($(el).attr('id').lastIndexOf('_')+1);
			$('#photo_preview_state_'+tmpId).html('<a href="javascript:ls.minimarket_photoset.setPreview('+tmpId+')" class="mark-as-preview">'+ls.lang.get('plugin.minimarket.product_photoset_mark_as_preview')+'</a>');
		});
		$('#photo_'+id).addClass('marked-as-preview');
		$('#photo_preview_state_'+id).html(ls.lang.get('plugin.minimarket.product_photoset_is_preview'));
	}
	
	this.setPreviewDescription = function(id, text) {
		ls.ajax(aRouter['mm_product']+'setimagedescription', {'id':id, 'text':text},  function(result) {
			if (!result.bStateError) {

			} else {
				ls.msg.error('Error','Please try again later');
			}
		}
		)
	}
	
	this.deletePhoto = function(id) {
		if (!confirm(ls.lang.get('plugin.minimarket.product_photoset_photo_delete_confirm'))) {return;}
		ls.ajax(aRouter['mm_product']+'deleteimage', {'id':id}, function(response){
			if (!response.bStateError) {
				$('#photo_'+id).remove();
				ls.msg.notice(response.sMsgTitle,response.sMsg);
			} else {
				ls.msg.error(response.sMsgTitle,response.sMsg);
			}
		});
	}
	
	this.showForm = function() {
		var $select = $('#photoset-start-upload');
		if ($select.length) {
			var pos = $select.offset();
			w = $select.outerWidth();
			h = $select.outerHeight();
			t = pos.top + h - 30  + 'px';
			l = pos.left - 15 + 'px';
			$('#photoset-upload-form').css({'top':t,'left':l});
		}
		$('#photoset-upload-form').show();
	}
	
	return this;

}).call(ls.minimarket_photoset || {},jQuery);