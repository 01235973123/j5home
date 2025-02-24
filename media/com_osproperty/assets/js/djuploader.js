/**
 * @package OS Property
 * @copyright Copyright (C) joomdonation.com, All rights reserved.
 * @license http://www.gnu.org/licenses GNU/GPL
 * @author url: https://joomdonation.com
 * @author email: services@joomdonation.com
 */


function startUpload(up,files) {
	
	//up.settings.buttons.start = false;
	up.start();
	//console.log(up);
}

function injectUploaded(up,file,info,site_url,label_generator) {
	
	try{
        var response = JSON.parse(info.response);
    }catch(e){
		injectUploadedFailLog(file,"File can't be uploaded due to the server-related issue - Check the site's .htaccess file or contact your web host provider");
        return false;
    }

	if(!response){
		injectUploadedFailLog(file,"File can't be uploaded");
		return false;
	}
	if(response.error){
		injectUploadedFailLog(file,response.error.message);
		return false;
	}
	
	var img_caption = '';
	if(label_generator==1){
		img_caption = stripExt(file.name);
	}
	var html = '<img src="'+site_url+'/tmp/osupload/'+file.target_name+'" alt="'+file.name+'" />';
	html += '	<div class="imgMask">';
	html += '	<input type="hidden" name="img_id[]" value="0">';
	html += '	<input type="hidden" name="img_image[]" value="'+file.target_name+';'+file.name+'">';
	html += '	<input type="text" class="itemInput editTitle" name="img_caption[]" value="'+img_caption+'">';
	html += '	<span class="delBtn"></span><span class="rotateBtn"></span></div>';
	
	var item = jQuery('<div class="itemImage">'+html+'</div>');

	initItemEvents(item, jQuery('.adminItemImages').length ? true : false);

	item.appendTo(jQuery('#itemImagesWrap #itemImages'));
	if(typeof this.djsortables !== 'undefined'){
		this.djsortables.append(item);
	}
	up.removeFile(file);
	return true;
}

function injectFrontUploaded(up,file,info,site_url,label_generator) {
	
	try{
        var response = JSON.parse(info.response);
    }catch(e){
		injectUploadedFailLog(file,"File can't be uploaded due to the server-related issue - Check the site's .htaccess file or contact your web host provider");
        return false;
    }

	if(!response){
		injectUploadedFailLog(file,"File can't be uploaded");
		return false;
	}
	if(response.error){
		injectUploadedFailLog(file,response.error.message);
		return false;
	}
	
	var img_caption = '';
	if(label_generator==1)
	{
		img_caption = stripExt(file.name);
	}	
	var html = '<img src="'+site_url+'/tmp/osupload/'+file.target_name+'" alt="'+file.name+'" />';
	html += '	<div class="imgMask">';
	html += '	<input type="hidden" name="img_id[]" value="0">';
	html += '	<input type="hidden" name="img_image[]" value="'+file.target_name+';'+file.name+'">';
	html += '	<input type="hidden" name="img_rotate[]" class="input_rotate" value="0">';
	html += '	<input type="text" class="itemInput editTitle" name="img_caption[]" value="'+img_caption+'">';
	html += '	<span class="delBtn"></span><span rel="/tmp/osupload/'+file.target_name+'" alt="'+file.name+'" class="rotateBtn"></span></div>';
	
	var item = jQuery('<div class="itemImage">'+html+'</div>');

	initItemEvents(item, jQuery('.adminItemImages').length ? true : false);

	item.appendTo(jQuery('#itemImagesWrap #itemImages'));
	if(typeof this.djsortables !== 'undefined'){
		this.djsortables.append(item);
	}
	up.removeFile(file);
	
	return true;
}

function initItemEvents(item, is_admin) {
	
	is_admin = (typeof is_admin !== 'undefined') ?  is_admin : false;
	item = jQuery(item);
	if(!item.length) return;
	item.find('.delBtn').on('click',function(){
		item.detach();
		if(typeof djImgReqHelper === 'function'){
			djImgReqHelper();
		}
		return false;
	});
	

	var img_rotate = 0;
	var image = item.find('img');
	var img_src_org = image.attr('src');

	if(item.find('.rotateBtn').length){
		item.find('.rotateBtn').on('click',function(btn){
			img_src = jQuery(this).attr('rel');			
				
				if(parseInt(jQuery(this).parent().find('[name="img_id[]"]').val())){
				img_rotate++;
				item.find('.input_rotate').val(img_rotate);	
				jQuery(this).closest('.itemImage').find('img').css('transform', 'rotate('+ 90*img_rotate +'deg)');
				return false;
			}

			var data_obj =  {
				'option': 'com_osproperty',
				'img_src': img_src	
			};

			
			data_obj.view = 'additem';
			data_obj.task = 'rotateImage';
			

			jQuery.ajax({
				url: typeof window.djRootUrl !== 'undefined' ? window.djRootUrl : 'index.php',
				type: 'post',
				data: data_obj
			}).done(function (response, textStatus, jqXHR){
				if(textStatus == 'success'){
					image.removeAttr('src'); 
					img_rotate++;
					image.attr('src', img_src_org+'?r='+img_rotate);
					item.find('.input_rotate').val(img_rotate);
				}
			});		
		});		
	}
	
	item.find('input').each(function(){
		var input = jQuery(this);
		input.on('focus',function(){
			item.addClass('active');
		});
		input.on('blur',function(){
			item.removeClass('active');
		});
	});
}

function stripExt(filename) {
	
	var pattern = /\.[^.]+$/;
	return filename.replace(pattern, "");	
}

jQuery(function(){
	if(typeof jQuery.fn.sortable != 'undefined'){
		window.djsortables = jQuery('#itemImages').sortable({
			cancel: '.delBtn,.rotateBtn,input,a',
			cursor: 'move',
			opacity: 0.3
		});
	}

	jQuery('.itemImage').each(function(){
		initItemEvents(this, jQuery('.adminItemImages').length ? true : false);
	});

	if(typeof djImgReqHelper === 'function'){
		djImgReqHelper();
	}
});
























function DJC2PlUploadStartUploadImage(up,files) {
	return DJC2PlUploadStartUpload(up, files, 'image');
}

function DJC2PlUploadStartUploadFile(up,files) {
	return DJC2PlUploadStartUpload(up, files, 'file');
}

function DJC2PlUploadStartUpload(up, files, prefix) {
	
	var wrapper = document.id('djc_uploader_'+prefix+'_items');
	var total = wrapper.getElements('.djc_uploader_item').length;
	var limit = parseInt(wrapper.getProperty('data-limit'));
	
	var limitreached = false;
	
	if (total + files.length >= limit && limit >= 0) {
		var remaining = limit - total;
		var toRemove = files.length - remaining;
		
		if (toRemove > 0 && files.length > 0){
			limitreached = true;
			for (var i = files.length-1; i >= 0; i--) {
				if (toRemove <= 0) {
					break;
				}
				up.removeFile(up.files[i]);					
				toRemove--;
			}		
		}					   				
	}
	
	if (limitreached) {
		alert(DJCatalog2UploaderVars.lang.limitreached);
	}
	
	up.start();
}

function DJC2PlUploadInjectUploadedImage(up,file,info) {
	var prefix = 'image';
	
	var response = jQuery.parseJSON(info.response); 
	if(response.error) {
		file.status = plupload.FAILED;
		file.name += ' - ' + response.error.message;
		document.id(file.id).addClass('ui-state-error');
		document.id(file.id).getElement('td.plupload_file_name').appendText(' - ' + response.error.message);
		return false;
	}
	
	var html = '<td class="center ordering_handle"><span class="sortable-handler" style="cursor: move;"><i class="icon-move"></i></span></td>';
	html += '<td class="center"><img src="'+DJCatalog2UploaderVars.url+'tmp/osupload/'+file.target_name+'" alt="'+file.name+'" />';
	html += '<input type="hidden" name="'+prefix+'_file_id[]" value="0" />';
	html += '<input type="hidden" name="'+prefix+'_file_name[]" value="'+file.target_name+'" />';
	html += '</td>';
	html += '<td><input type="text" class="djc_uploader_caption inputbox input input-medium" name="'+prefix+'_caption[]" value="'+DJCatalog2MUStripExt(file.name)+'" /></td>';
	html += '<td class="center"><input type="checkbox" onchange="DJCatalog2UPExcludeCheckbox(this);" /><input type="hidden" name="'+prefix+'_exclude[]" value="0" class="djc_hiddencheckbox" /></td>';
	html += '<td class="center"><button class="button btn djc_uploader_remove_btn">'+DJCatalog2UploaderVars.lang.remove+'</button></td>';
	
	var item = new Element('tr',{'class':'djc_uploader_item', html: html});
	DJCatalog2MUInitItemEvents(item);
	
	// add uploaded image to the list and make it sortable
	item.inject(document.id('djc_uploader_'+prefix+'_items'), 'bottom');
	this.DJCatalog2MUUploaders['djc_uploader_'+prefix].addItems(item);
	
	up.removeFile(file);
	
	return true;
}

function DJC2PlUploadInjectUploadedFile(up,file,info) {
	var prefix = 'file';
	
	var response = jQuery.parseJSON(info.response); 
	if(response.error) {
		file.status = plupload.FAILED;
		file.name += ' - ' + response.error.message;
		document.id(file.id).addClass('ui-state-error');
		document.id(file.id).getElement('td.plupload_file_name').appendText(' - ' + response.error.message);
		return false;
	}
	
	var html = '<td class="center ordering_handle"><span class="sortable-handler" style="cursor: move;"><i class="icon-move"></i></span></td>';
	html += '<td class="center">'+file.name;
	html += '<input type="hidden" name="'+prefix+'_file_id[]" value="0">';
	html += '<input type="hidden" name="'+prefix+'_file_name[]" value="'+file.target_name+'">';
	html += '</td>';
	html += '<td>';
	
	if (DJCatalog2UploaderVars.valid_captions.length > 0) {
		html += '<select class="djc_uploader_caption inputbox input input-medium" name="'+prefix+'_caption[]">';
		for (var i = 0; i < DJCatalog2UploaderVars.valid_captions.length; i++) {
			html += DJCatalog2UploaderVars.valid_captions[i];
		}
		html += '</select>';
	} else {
		html += '<input type="text" class="djc_uploader_caption inputbox input input-medium" name="'+prefix+'_caption[]" value="'+DJCatalog2MUStripExt(file.name)+'" />';
	}
	
	html += '</td>';
	html += '<td class="center">';
	
	if (DJCatalog2UploaderVars.client == 1) {
		html +='<input type="text" class="djc_uploader_hits inputbox input input-small" name="'+prefix+'_hits[]" value="0" readonly="readonly" />';	
	} else {
		html +='<span>0</span>';
	}
	
	html += '</td>';
	html += '<td class="center"><button class="button btn djc_uploader_remove_btn">'+DJCatalog2UploaderVars.lang.remove+'</button></td>';
	
	var item = new Element('tr',{'class':'djc_uploader_item', html: html});
	DJCatalog2MUInitItemEvents(item);
	
	// add uploaded image to the list and make it sortable
	item.inject(document.id('djc_uploader_'+prefix+'_items'), 'bottom');
	
	if (typeof jQuery != 'undefined') {
		if (typeof jQuery(document).chosen != 'undefined') {
			jQuery('select.djc_uploader_caption').chosen({"disable_search_threshold":10,"allow_single_deselect":true});
		}
	}
	
	this.DJCatalog2MUUploaders['djc_uploader_'+prefix].addItems(item);
	
	up.removeFile(file);
	
	return true;
}

function DJCatalog2MUInitItemEvents(item) {
	
	if(!item) return;
	item.getElement('.djc_uploader_remove_btn').addEvent('click',function(){
		(function(){item.dispose();}).delay(50);
		return false;
	});
	item.getElements('input').each(function(input){
		input.addEvent('focus',function(){
			item.addClass('active');
		});
		input.addEvent('blur',function(){
			item.removeClass('active');
		});
	});
}

function DJCatalog2MUStripExt(filename) {
	
	var pattern = /\.[^.]+$/;
	return filename.replace(pattern, "");	
}

function DJCatalog2UPExcludeCheckbox(element){
	var p = element.parentNode;
	var inputs = p.getElementsByClassName('djc_hiddencheckbox');
	if (inputs.length == 0) {
		return false;
	}

	for (var k in inputs) {
		if (inputs.hasOwnProperty(k) && typeof inputs[k].type != 'undefined' && typeof inputs[k].name != 'undefined') {
			if (typeof element.checked != 'undefined' && element.checked) {
				inputs[k].value = '1';
			} else {
				inputs[k].value = '0';
			}
		}
	}
	return false;
}

function DJCatalog2UPAddUploader(suffix, wrapper_id){
	
	var wrapper = document.id('djc_uploader_'+suffix+'_items');
	var total = wrapper.getElements('.djc_uploader_item').length + wrapper.getElements('.djc_uploader_item_simple').length;
	var limit = parseInt(wrapper.getProperty('data-limit'));
	
	if (total >= limit && limit >= 0) {
		return false;				   				
	}
	
    var copy = document.id('djc_uploader_simple_'+suffix).clone().inject(wrapper_id + '_items', 'bottom');
    
    copy.setStyle('display', '');
    
    return false;
}

jQuery(document).ready(function() {
	this.DJCatalog2MUUploaders = [];
	
	var uploaders = jQuery('.djc_uploader');
	uploaders.each(function(element){
		id = element.id;
		if (id) {
			instance = document.id(document.body).getElement('#'+id + ' .djc_uploader_items');
			this.DJCatalog2MUUploaders[id] = new Sortables(instance,{
				clone: false,
				revert: false,
				opacity: 0.5,
				handle: '.sortable-handler'
			}); 
		}
	});
	
	jQuery('.djc_uploader_item').each(function(item){
		DJCatalog2MUInitItemEvents(item);
	});
});


function injectUploadedFailLog(file,msg)
{
	file.status = plupload.FAILED;
	file.name += ' - ' + msg;
	jQuery('#'+file.id).addClass('ui-state-error');
	jQuery('#'+file.id).find('td.plupload_file_name').append(' - ' + msg);
}

function djImgReqHelper()
{
    jQuery('#img_req_helper').val(jQuery('#itemImages .itemImage').length ? jQuery('#itemImages .itemImage').length : '');
}