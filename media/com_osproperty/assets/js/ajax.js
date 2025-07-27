/*------------------------------------------------------------------------
# ajax.js - Ossolution Property
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2018 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/

function validateEmail(email) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}

function checkNumber(txtName,msg)
{			
	var num = txtName.value			
	if(isNaN(num))			
	{			
		alert(msg);			
		txtName.value = "";			
		txtName.focus();			
	}			
}

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
    var _validFileExtensions = [".jpg", ".jpeg", ".png", ".gif"];
	for (var j = 0; j < _validFileExtensions.length; j++) {
        var sCurExtension = _validFileExtensions[j];
        if (photo_name.substr(photo_name.length - sCurExtension.length, sCurExtension.length).toLowerCase() == sCurExtension.toLowerCase()) {
            blnValid = true;
            break;
        }
    }
	if(!blnValid){
    	alert('Alow file: *.jpg, *.jpeg, *.png, *.gif,');
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

function updateCompanyDropdown() {
	var agent_type = document.getElementById('agent_type');
	var company_id = document.getElementById('company_id');
	if (agent_type.value == 0) {
		company_id.disabled = false;
	} else {
		company_id.disabled = true;
	}
}

var xmlHttp;

function doGrabImageAjax(live_site,url){
	xmlHttp=GetXmlHttpObject();
    if (xmlHttp==null){
        alert ("Browser does not support HTTP Request")
        return
    }

    link = live_site + "index.php?option=com_osproperty&no_html=1&tmpl=component&format=raw&task=ajax_grabImagess&url=" + url;
    xmlHttp.onreadystatechange=ajaxreturngrab;
    xmlHttp.open("GET",link,true)
    xmlHttp.send(null)
}

function cancelGrab(live_site){
	xmlHttp=GetXmlHttpObject();
    if (xmlHttp==null){
        alert ("Browser does not support HTTP Request")
        return
    }
    link = live_site + "index.php?option=com_osproperty&no_html=1&tmpl=component&format=raw&task=ajax_cancelgrabImagess";
    xmlHttp.onreadystatechange=ajaxreturngrab;
    xmlHttp.open("GET",link,true)
    xmlHttp.send(null)
}

function updateSendEmailStatusAjax(list_id,send_status,live_site){
    xmlHttp=GetXmlHttpObject();
    if (xmlHttp==null){
        alert ("Browser does not support HTTP Request")
        return
    }

    url = live_site + "index.php?option=com_osproperty&no_html=1&tmpl=component&format=raw&task=ajax_updateSendEmailStatus&list_id=" + list_id + "&send_status=" + send_status;
    xmlHttp.onreadystatechange=ajax4j;
    xmlHttp.open("GET",url,true)
    xmlHttp.send(null)
}

function loadStateAjax(country_id,live_site){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	
	url = live_site + "index.php?option=com_osproperty&no_html=1&tmpl=component&task=property_loadState&country_id=" + country_id;
	xmlHttp.onreadystatechange=ajax1;
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)

}

function ajax1() { 
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
		document.getElementById("div_states").innerHTML = xmlHttp.responseText ;
		
	} 
}


function loadLocationInfoStateCity(country_id,state_id,city_id,country_name,state_name,live_site){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	url = live_site + "index.php?option=com_osproperty&no_html=1&tmpl=component&tmpl=component&format=raw&task=ajax_loadstatecity&country_id=" + country_id + "&country_name=" + country_name + "&state_name=" + state_name + "&state_id=" + state_id + "&city_id=" + city_id;
	xmlHttp.onreadystatechange=ajax4e;
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}

function loadLocationInfoStateCityAddProperty(country_id,state_id,city_id,country_name,state_name,live_site,lang=''){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	var langVar = "";
	if(lang != "")
	{
		langVar = "&lang=" + lang;
	}
	url = live_site + "index.php?option=com_osproperty&no_html=1&tmpl=component&tmpl=component&format=raw&task=ajax_loadstatecity&country_id=" + country_id + "&country_name=" + country_name + "&state_name=" + state_name + "&state_id=" + state_id + "&city_id=" + city_id + langVar;
	xmlHttp.onreadystatechange=ajax4e2;
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}

function loadLocationInfoStateCityBackend(country_id,state_id,city_id,country_name,state_name,live_site){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	url = live_site + "index.php?option=com_osproperty&no_html=1&tmpl=component&tmpl=component&format=raw&task=ajax_loadstatecityBackend&country_id=" + country_id + "&country_name=" + country_name + "&state_name=" + state_name + "&state_id=" + state_id + "&city_id=" + city_id;
	xmlHttp.onreadystatechange=ajax4e2;
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}

function updateCurrency(id,pid,curr){
	var currency_item = document.getElementById('currency_item');
	currency_item.value = id;
	updateCurrencyListing(pid,curr,0,id);
}

function updateCurrencyListing(pid,curr,show_label,item){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	var live_site = document.getElementById('live_site');
	live_site = live_site.value;
	var url = live_site + 'index.php?option=com_osproperty&no_html=1&tmpl=component&format=raw&task=ajax_updateCurrency&pid=' + pid + '&curr=' + curr + '&show_label=' + show_label + '&item=' + item;
	xmlHttp.onreadystatechange=ajax5c;
	xmlHttp.open("GET",url,true);
	xmlHttp.send(null);
}

function convertCurrency(pid,curr,show_label){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	var live_site = document.getElementById('live_site');
	live_site = live_site.value;
	var url = live_site + 'index.php?option=com_osproperty&no_html=1&tmpl=component&format=raw&task=ajax_convertCurrency&pid=' + pid + '&curr=' + curr + '&show_label=' + show_label;
	xmlHttp.onreadystatechange=ajax5c;
	xmlHttp.open("GET",url,true);
	xmlHttp.send(null);
}

function convertCurrencyDefault(pid,curr,show_label){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	var live_site = document.getElementById('live_site');
	live_site = live_site.value;
	var currency_item = document.getElementById('currency_item');
	if(currency_item != null){
		currency_item.value = pid;
	}
	var url = live_site + 'index.php?option=com_osproperty&no_html=1&tmpl=component&format=raw&task=ajax_convertCurrency&pid=' + pid + '&curr=' + curr + '&show_label=0&theme=default';
	xmlHttp.onreadystatechange=ajax5c;
	xmlHttp.open("GET",url,true);
	xmlHttp.send(null);
}


function convertCurrencyDefaultDetails(pid,curr,show_label){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	var live_site = document.getElementById('live_site');
	live_site = live_site.value;
	var url = live_site + 'index.php?option=com_osproperty&no_html=1&tmpl=component&format=raw&task=ajax_convertCurrency&pid=' + pid + '&curr=' + curr + '&show_label=0&theme=defaultdetails';
	xmlHttp.onreadystatechange=ajax5c;
	xmlHttp.open("GET",url,true);
	xmlHttp.send(null);
}

function loadLocationInfoStateCityLocator(country_id,state_id,city_id,country_name,state_name,live_site,lang='')
{
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	var langVar = "";
	if(lang != "")
	{
		langVar = "&lang=" + lang;
	}
	url = live_site + "index.php?option=com_osproperty&no_html=1&tmpl=component&format=raw&task=ajax_loadstatecitylocator&country_id=" + country_id + "&country_name=" + country_name + "&state_name=" + state_name + "&state_id=" + state_id + "&city_id=" + city_id + langVar;
	xmlHttp.onreadystatechange=ajax4e;
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}

function loadLocationInfoCity(state_id,city_id,state_name,live_site,lang=''){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	city_name = jQuery("#city_name").val();
	cityVar = "";
	if(city_name != "")
	{
		cityVar = "&city_name=" + city_name;
	}
	else
	{
		cityVar = "&city_name=city";
	}
	var langVar = "";
	if(lang != "")
	{
		langVar = "&lang=" + lang;
	}
	url = live_site + "index.php?option=com_osproperty&no_html=1&tmpl=component&format=raw&task=ajax_loadcity" + "&state_name=" + state_name + "&state_id=" + state_id + "&city_id=" + city_id + langVar + cityVar;
	//alert(url); 
	xmlHttp.onreadystatechange=ajax4g;
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}

function loadLocationInfoCityInstallSampleData(state_id,city_id,state_name,live_site){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	url = live_site + "index.php?option=com_osproperty&no_html=1&tmpl=component&format=raw&task=ajax_loadcity" + "&state_name=" + state_name + "&state_id=" + state_id + "&city_id=" + city_id + "&useConfig=0";
	xmlHttp.onreadystatechange=ajax4g;
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}

function loadLocationInfoCityAddProperty(state_id,city_id,state_name,live_site,lang=''){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	var langVar = "";
	if(lang != "")
	{
		langVar = "&lang=" + lang;
	}
	url = live_site + "index.php?option=com_osproperty&no_html=1&tmpl=component&format=raw&task=ajax_loadcityAddProperty" + "&state_name=" + state_name + "&state_id=" + state_id + "&city_id=" + city_id + langVar;
	xmlHttp.onreadystatechange=ajax4g2;
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}

function loadLocationInfoStateCityLocatorModule(country_id,state_id,city_id,country_name,state_name,live_site,random_id,lang=''){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	var langVar = "";
	if(lang != "")
	{
		langVar = "&lang=" + lang;
	}
	url = live_site + "index.php?option=com_osproperty&no_html=1&tmpl=component&format=raw&task=ajax_loadstatecitylocatorModule&country_id=" + country_id + "&country_name=" + country_name + "&state_name=" + state_name + "&state_id=" + state_id + "&city_id=" + city_id + "&random_id=" + random_id + langVar;
	xmlHttp.onreadystatechange=ajax4e1;
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}

function initLocation(){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	live_site = document.getElementById('live_site');
	live_site = live_site.value;
	url = live_site + "index.php?option=com_osproperty&no_html=1&tmpl=component&format=raw&task=ajax_loadLocationInformation";
	xmlHttp.onreadystatechange=ajax4e3;
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}

function checkingVersion(current_version){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	live_site = document.getElementById('live_site');
	live_site = live_site.value;
	url = live_site + "index.php?option=com_osproperty&no_html=1&tmpl=component&format=raw&task=ajax_checkingVersion&current_version=" + current_version;
	xmlHttp.onreadystatechange=ajax4e4;
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}

function ajax4e1() { 
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
		res = xmlHttp.responseText;
		pos = res.indexOf("|*|");
		var random = "";
		if(pos >= 0){
			random = res.substring(pos+3);
			random = trim(random);
			res = res.substring(0,pos);
		}

		pos = res.indexOf("@@@");
		document.getElementById("country_state_search_module" + random).innerHTML = res.substring(0,pos);
		document.getElementById("city_div_search_module" + random).innerHTML = res.substring(pos+3);
		
	} 
}


function loadLocationInfoCityModule(state_id,city_id,state_name,live_site,random_id,lang = ''){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	var langVar = "";
	if(lang != "")
	{
		langVar = "&lang=" + lang;
	}
	url = live_site + "index.php?option=com_osproperty&no_html=1&tmpl=component&format=raw&task=ajax_loadcityModule" + "&state_name=" + state_name + "&state_id=" + state_id + "&city_id=" + city_id + "&random_id=" + random_id + langVar;
	xmlHttp.onreadystatechange=ajax4g1;
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}

function ajax4g1() { 
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
		res = xmlHttp.responseText;
		pos = res.indexOf("|*|");
		if(pos >= 0){
			random = res.substring(pos+3);
			random = trim(random);
			res = res.substring(0,pos);
		}else{
			random = "";
		}
		
		document.getElementById("city_div_search_module" + random).innerHTML = res;
		
	} 
}

function ltrim(str) { 
	for(var k = 0; k < str.length && isWhitespace(str.charAt(k)); k++);
	return str.substring(k, str.length);
}
function rtrim(str) {
	for(var j=str.length-1; j>=0 && isWhitespace(str.charAt(j)) ; j--) ;
	return str.substring(0,j+1);
}
function trim(str) {
	return ltrim(rtrim(str));
}
function isWhitespace(charToCheck) {
	var whitespaceChars = " \t\n\r\f";
	return (whitespaceChars.indexOf(charToCheck) != -1);
}

function GetXmlHttpObject(){
	var xmlHttp = null;
	try{
		xmlHttp = new XMLHttpRequest();
	}
	catch (e)
	{
		try{
			xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch (e)
		{
			xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
		}
	}
	return xmlHttp;
}

function osAjax(task,id,live_site){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	
	url = live_site + "index.php?option=com_osproperty&no_html=1&tmpl=component&task=" + task + "&id=" + id;
	xmlHttp.onreadystatechange=ajax2;
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}
	
function ajax2() { 
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
		document.getElementById("notice").style.display = "block";
		var response = xmlHttp.responseText ;
		pos = response.indexOf("@@@");
		var inform = response.substring(0,pos);
		document.getElementById("notice").innerHTML = inform ;
		window.location.hash = '#notice';
		setTimeout('closeDiv(\'notice!\')', 5000);
	} 
}

function osAjaxExtend(task,id,live_site,theme,layout){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}	
	url = live_site + "index.php?option=com_osproperty&no_html=1&tmpl=component&task=" + task + "&id=" + id + "&theme=" + theme + "&layout=" + layout;
	xmlHttp.onreadystatechange=ajax2Extend;
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}

function ajax2Extend() { 
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
		var response = xmlHttp.responseText ;
		pos = response.indexOf("@@@");
		var inform = response.substring(0,pos);
		document.getElementById("notice").style.display = "block";
		document.getElementById("notice").innerHTML = inform ;
		window.location.hash = '#notice';
		setTimeout('closeDiv(\'notice!\')', 5000);

		var process_element = document.getElementById('process_element');
		if(process_element != null){
			process_element = process_element.value;
			var return_text = response.substring(pos+3);
			if(return_text != ""){
				document.getElementById(process_element).innerHTML = return_text ;
			}
		}
	} 
}

function closeDiv(div_id){
	document.getElementById("notice").style.display = "none";
}

function checkCouponcode(id,coupon_code,live_site){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	
	url = live_site + "index.php?option=com_osproperty&no_html=1&tmpl=component&format=raw&task=ajax_checkcouponcode&coupon_code=" + coupon_code + "&id=" + id;
	xmlHttp.onreadystatechange=ajax3;
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}

function ajax3() { 
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
		document.getElementById("coupon_code_div").innerHTML = xmlHttp.responseText ;
		
	} 
}

function loadStateInListPageAjax(country_id,live_site){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	
	url = live_site + "index.php?option=com_osproperty&no_html=1&tmpl=component&format=raw&task=ajax_loadStateInListPage&country_id=" + country_id;
	xmlHttp.onreadystatechange=ajax4a;
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}

function ajax4a() { 
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
		res = xmlHttp.responseText;
		var pos = res.indexOf("@@@@");
		str1 = res.substring(0,pos);
		str2 = res.substring(pos+4);
		document.getElementById("div_state").innerHTML = str1;
		document.getElementById("city_div").innerHTML  = str2;
	} 
}

function removeAgentAjax(id,live_site){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	
	var url = live_site + 'index.php?option=com_osproperty&no_html=1&tmpl=component&task=company_removeagent&agent_id=' + id;
	xmlHttp.onreadystatechange=ajax4b;
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}

function ajax4b() { 
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
		document.getElementById("agent_div").innerHTML = xmlHttp.responseText ;
		
	} 
}

function searchAgentajax(keyword,live_site){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	var url = live_site + 'index.php?option=com_osproperty&no_html=1&tmpl=component&task=company_searchagent&keyword=' + keyword;
	xmlHttp.onreadystatechange=ajax4c;
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}

function ajax4c() { 
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
		document.getElementById("div_search_agent_result").innerHTML = xmlHttp.responseText ;
		
	} 
}

function addAgentAjax(keyword,agent_id,live_site){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	
	var url = live_site + 'index.php?option=com_osproperty&no_html=1&tmpl=component&task=company_addagent&keyword=' + keyword + '&agent_id=' + agent_id;
	xmlHttp.onreadystatechange=ajax4d;
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}

function ajax4d() { 
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
		res = xmlHttp.responseText;
		pos = res.indexOf("@@@");
		document.getElementById("div_search_agent_result").innerHTML = res.substring(0,pos);
		document.getElementById("agent_div").innerHTML = res.substring(pos+3);
		
	} 
}

function ajax4e() { 
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
		res = xmlHttp.responseText;
		pos = res.indexOf("@@@");
		document.getElementById("country_state").innerHTML = res.substring(0,pos);
		document.getElementById("city_div").innerHTML = res.substring(pos+3);
		
	} 
}

function ajax4e2() { 
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	{ 
		res = xmlHttp.responseText;
		pos = res.indexOf("@@@");
		document.getElementById("country_state").innerHTML = res.substring(0,pos);
		document.getElementById("city_div").innerHTML = res.substring(pos+3);
	
		jQuery(function ($) {
			initChosen();
			$("body").on("subform-row-add", initChosen);

			function initChosen(event, container)
			{
				container = container || document;
				$(container).find(".chosen").chosen({"disable_search_threshold":10,"search_contains":true,"allow_single_deselect":true,"placeholder_text_multiple":"Type or select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"});
			}
		});
	
		jQuery(document).ready(function()
		{
			jQuery(".chosen").chosen();
		});
	} 
}

function ajax4g() { 
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
		res = xmlHttp.responseText;
		document.getElementById("city_div").innerHTML = res;
		
		jQuery(function ($) {
			initChosen();
			$("body").on("subform-row-add", initChosen);

			function initChosen(event, container)
			{
				container = container || document;
				$(container).find(".chosen").chosen({"disable_search_threshold":10,"search_contains":true,"allow_single_deselect":true,"placeholder_text_multiple":"Type or select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"});
			}
		});
	
		jQuery(document).ready(function()
		{
			jQuery(".chosen").chosen();
		});
	} 
}

function ajax4g2() { 
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
		res = xmlHttp.responseText;
		document.getElementById("city_div").innerHTML = res;
		
	} 
}

function saveSearchListAjax(param,live_site,itemid){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	var url = live_site + 'index.php?option=com_osproperty&no_html=1&tmpl=component&task=property_savesearchlist&param=' + param + '&Itemid=' + itemid;
	xmlHttp.onreadystatechange=ajax4h;
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}

function ajax4h() { 
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
		res = xmlHttp.responseText;
		document.getElementById("search_list_div").innerHTML = res;
	} 
}


function saveListNameAjax(id,list_id,list_name,live_site,itemid){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	var url = live_site + 'index.php?option=com_osproperty&no_html=1&tmpl=component&task=property_savelistname&list_id=' + list_id + '&list_name=' + list_name + "&id=" + id + "&Itemid=" + itemid;
	xmlHttp.onreadystatechange=ajax4i;
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}

function ajax4i() { 
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
		var current_item = document.getElementById('current_item');
		current_item = current_item.value;
		res = xmlHttp.responseText;
		document.getElementById("div_list_" + current_item).innerHTML = res;
	} 
}

function changeCountryAjax(pid,country,live_site){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	var url = live_site + 'index.php?option=com_osproperty&no_html=1&tmpl=component&format=raw&task=ajax_loadStateBackend&pid=' + pid + '&country=' + country;
	xmlHttp.onreadystatechange=ajax5a;
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}

function ajax5a() { 
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
		var current_item = document.getElementById('current_item');
		current_item = current_item.value;
		res = xmlHttp.responseText;
		document.getElementById("div_state_" + current_item).innerHTML = res;
	} 
}

function changeStateAjax(pid,state,live_site){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	var url = live_site + 'index.php?option=com_osproperty&no_html=1&tmpl=component&format=raw&task=ajax_loadCityBackend&pid=' + pid + '&state=' + state;
	xmlHttp.onreadystatechange=ajax5b;
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}

function ajax5b() { 
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
		var current_item = document.getElementById('current_item');
		current_item = current_item.value;
		res = xmlHttp.responseText;
		document.getElementById("div_city_" + current_item).innerHTML = res;
	} 
}

function ajax5c() { 
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
		res = xmlHttp.responseText;
		var currency_item = document.getElementById('currency_item');
		currency_item = currency_item.value;
		document.getElementById("currency_div" + currency_item).innerHTML = res;
	} 
}


/* backend */
function saveOptionAjax(fid,value,live_site,div_name,type){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	
	url = live_site + "administrator/index.php?option=com_osproperty&no_html=1&tmpl=component&task=extrafield_addfieldoption&fid=" + fid + "&value=" + value + "&div_name=" + div_name + "&type=" + type;
	xmlHttp.onreadystatechange=backendajax1;
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)

}

function backendajax1() { 
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
		var div = document.getElementById('div_name');
		if(div != null){
			div_name = div.value;
			if(div_name != ""){
				document.getElementById(div_name).innerHTML = xmlHttp.responseText ;	
			}
		}
	} 
}

function removeOptionAjax(oid,fid,live_site,div_name,type){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	
	url = live_site + "administrator/index.php?option=com_osproperty&no_html=1&tmpl=component&task=extrafield_removefieldoption&fid=" + fid + "&oid=" + oid + "&div_name=" + div_name + "&type=" + type;
	//alert(url);
	xmlHttp.onreadystatechange=backendajax1;
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}

function saveChangeOptionAjax(oid,value,ordering,fid,live_site,div_name,type){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	
	url = live_site + "administrator/index.php?option=com_osproperty&no_html=1&tmpl=component&task=extrafield_savechangeoption&fid=" + fid + "&oid=" + oid + "&value=" + value + "&ordering=" + ordering + "&div_name=" + div_name + "&type=" + type;
	xmlHttp.onreadystatechange=backendajax1;
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}

function loadPriceListOptionAjax(property_type,live_site){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	url = live_site + "index.php?option=com_osproperty&no_html=1&tmpl=component&format=raw&task=ajax_loadPriceListOption&property_type=" + property_type;
	xmlHttp.onreadystatechange=loadPriceAjaxReturn;
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}

function loadPriceAjaxReturn(){
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
		res = xmlHttp.responseText;
		document.getElementById("pricefilter").innerHTML = res;
	} 
}

function ajax4e3(){
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
		res = xmlHttp.responseText;
		pos = res.indexOf("@@@");
		res1 = res.substring(0,pos);
		res2 = res.substring(pos+3);
		var location_div = document.getElementById("location_div");
		if(location_div != null){
			location_div.innerHTML = res1;
		}
		//document.getElementById("ospversion").innerHTML = res2;
	}else{
		live_site = document.getElementById('live_site');
		live_site = live_site.value;
		var location_div = document.getElementById("location_div");
		if(location_div != null){
			location_div.innerHTML = "<center><img src='" + live_site + "media/com_osproperty/assets/images/loading.gif'/></center>";
		}
		//document.getElementById("ospversion").innerHTML = "<img src='" + live_site + "media/com_osproperty/assets/images/updated_failure.png'/>";
	}
}

function ajax4e4(){
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
		res = xmlHttp.responseText;
		document.getElementById("ospversion").innerHTML = res;
	}else{
		live_site = document.getElementById('live_site');
		live_site = live_site.value;
		document.getElementById("ospversion").innerHTML = "<img src='" + live_site + "media/com_osproperty/assets/images/updated_failure.png'/>";
	}
}

function ajaxreturngrab(){
    if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){
		res = xmlHttp.responseText;
        document.getElementById("grab_images_raw").innerHTML = res;
    }else{
        var live_site = document.getElementById('live_site');
        live_site = live_site.value;
        document.getElementById("grab_images_raw").innerHTML = "<center><img src='" + live_site + "media/com_osproperty/assets/images/loading_large.gif'/></center>";
    }
}

function ajax4j(){
    if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){
        var select_item = document.getElementById('select_item');
        select_item = select_item.value;
        res = xmlHttp.responseText;
        document.getElementById("div_send_status_" + select_item).innerHTML = res;
    }else{
        var live_site = document.getElementById('live_site');
        live_site = live_site.value;
        var select_item = document.getElementById('select_item');
        select_item = select_item.value;
        document.getElementById("div_send_status_" + select_item).innerHTML = "<center><img src='" + live_site + "media/com_osproperty/assets/images/loading.gif'/></center>";
    }
}