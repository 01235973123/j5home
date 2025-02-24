
/******************************************************************************
 *
 *	PROJECT: OSproperty
 *	VERSION: 1.0
 *	LISENSE: http://joomdonation.com
 *	PRODUCT: Ossolution property
 *
 *	This script is a commercial software and any kind of using it must be 
 *	coordinate with Ossolution and be agree to Ossolution License Agreement
 *
 *
 *	Copyrights Ossolution Property | 2015
 *	http://www.joomdonation.com/
 *
 ******************************************************************************/
function osConfirm(message,task,id,live_site){
	var answer = confirm(message);
	if(answer == 1){
		osAjax(task,id,live_site);
	}
}

function osConfirmExtend(message,task,id,live_site,element,theme,layout){
	var answer = confirm(message);
	if(answer == 1){
		if (typeof(element)==='undefined') element = '';
		var process_element = document.getElementById('process_element');
		if(process_element != null){
			process_element.value = element;
		}
		osAjaxExtend(task,id,live_site,theme,layout);
	}
}



function changeValue(id){
	var temp = document.getElementById(id);
	if(temp.value == 0){
		temp.value = 1;
	}else{
		temp.value = 0;
	}
}


function changeValueShowDiv(id,div_id){
	var temp = document.getElementById(id);
	var div  = document.getElementById(div_id);
	if(temp.value == 0){
		temp.value = 1;
		div.style.display = "block";
	}else{
		temp.value = 0;
		div.style.display = "none";
	}
}
/**
*
* hide and show dinamic blocks
*
* @param string id - block id
* 
**/
function action_block( id )
{
	if ( $( '#block_content_'+id ).css('display') == 'block' )
	{
		$( '#block_content_'+id ).slideUp('normal');
		$( '#block_arrow_'+id ).removeClass('arrow_block_up');
		$( '#block_arrow_'+id ).addClass('arrow_block_down');
	
	}
	else
	{
		$( '#block_content_'+id ).slideDown('slow');
		$( '#block_arrow_'+id ).removeClass('arrow_block_down');
		$( '#block_arrow_'+id ).addClass('arrow_block_up');
		
	}
}


function changeBackground(item,color){
	var temp = document.getElementById(item);
	if(temp != null){
		temp.style.backgroundColor = color;
	}
}


/**
	Check upload photo
	Avoid Vulnerable
	@element_id: Id of the file type tag
**/
function checkUploadPhotoFiles(element_id){
	var element = document.getElementById(element_id);
	var photo_name = element.value.toUpperCase();
	var elementspan = document.getElementById(element_id + 'div');
	var blnValid = false;
    var _validFileExtensions = [".jpg", ".jpeg",".png",".gif",".webp"];
	for (var j = 0; j < _validFileExtensions.length; j++) {
        var sCurExtension = _validFileExtensions[j];
        if (photo_name.substr(photo_name.length - sCurExtension.length, sCurExtension.length).toLowerCase() == sCurExtension.toLowerCase()) {
            blnValid = true;
            break;
        }
    }
	if(!blnValid){
    	alert('Alow file: *.jpg, *.jpeg, *.gif, *png, *webp');
        element.value='';
        if(elementspan != null){
        	elementspan.innerHTML = elementspan.innerHTML;
        }
    }
}

/**
	Check upload document
	Avoid Vulnerable
	@element_id: Id of the file type tag
**/
function checkUploadDocumentFiles(element_id){
    var element = document.getElementById(element_id);
    var elementspan = document.getElementById(element_id + 'div');
	var ar_ext = ['pdf', 'doc', 'docx'];        // array with allowed extensions
	// - www.coursesweb.net
	  // get the file name and split it to separe the extension
	var name = element.value;
	var ar_name = name.split('.');
	
	var ar_nm = ar_name[0].split('\\');
	for(var i=0; i<ar_nm.length; i++) var nm = ar_nm[i];
	
	// check the file extension
	var re = 0;
	for(var i=0; i<ar_ext.length; i++) {
         if(ar_ext[i] == ar_name[1]) {
            re = 1;
            break;
         }
    }
	
   // if re is 1, the extension is in the allowed list
   if(re==1) {
    // enable submit
   } else {
    // delete the file name, disable Submit, Alert message
        element.value = '';
        alert('".'+ ar_name[1]+ '" is not an file type allowed for upload.');
        if(elementspan != null){
        	elementspan.innerHTML = elementspan.innerHTML;
        }
   }
}

function loadPriceListOption(){
	var property_type = document.getElementById('property_type');
	var live_site = document.getElementById('live_site');
	live_site = live_site.value;
	loadPriceListOptionAjax(property_type.value,live_site);
}