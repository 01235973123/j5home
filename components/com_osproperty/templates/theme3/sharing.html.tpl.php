<?php

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
?>
<style>
.form-horizontal .<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>{
	width: 160px !important;
	padding-top: 5px;
}
.window .window-mainbody {
    padding: 0px !important;
}
</style>
<?php
$language = Factory::getLanguage();
$language = $language->getTag();
$language = explode("-",$language);
$language = $language[0];
?>
<form method="POST" action="<?php echo Uri::root()?>index.php?option=com_osproperty&task=property_submittellfriend&Itemid=<?php echo $itemid?>&tmpl=component&lang=<?php echo $language;?>" name="tellfriend_form" id="tellfriend_form" class="form-horizontal">
    <div class="_leadError ajax-error"></div>
    <div style="width:100%;text-align:center;">
    	<h2>
    		<?php echo $title;?>
    	</h2>
    </div>
    <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
	    <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>"><?php echo Text::_('OS_FRIEND_NAME');?></label>
	    <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
	     	<input class="input-large" type="text" id="friend_name" name="friend_name" maxlength="30" placeholder="<?php echo Text::_('OS_FRIEND_NAME');?>"/>
	    </div>
	</div>
	<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
	    <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>"><?php echo Text::_('OS_FRIEND_EMAIL');?></label>
	    <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
	     	<input class="input-large" type="text" id="friend_email" name="friend_email" maxlength="50" placeholder="<?php echo Text::_('OS_FRIEND_EMAIL');?>"/>
	    </div>
	</div>
	<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
	    <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>"><?php echo Text::_('OS_YOUR_NAME');?></label>
	    <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
	     	<input class="input-large" type="text" id="your_name" name="your_name" maxlength="30" placeholder="<?php echo Text::_('OS_YOUR_NAME');?>"/>
	    </div>
	</div>
	<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
	    <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>"><?php echo Text::_('OS_YOUR_EMAIL');?></label>
	    <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
	     	<input type="text" id="your_email" name="your_email" maxlength="50" class="input-large" placeholder="<?php echo Text::_('OS_YOUR_EMAIL');?>"/>
	    </div>
	</div>
	<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
	    <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>"><?php echo Text::_('OS_MESSAGE');?></label>
	    <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
	     	<textarea id="message" name="message" rows="3" cols="50" class="input-large" style="width:250px !important;height:150px !important;"></textarea>
	    </div>
	</div>
	<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
	    <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>"><?php echo Text::_('OS_SECURITY_CODE');?></label>
	    <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
			<span class="grey_small" style="line-height:16px;"><?php echo Text::_('OS_PLEASE_INSERT_THE_SYMBOL_FROM_THE_INAGE_TO_FIELD_BELOW')?></span>
			<BR />
	     	<img src="<?php echo Uri::root()?>index.php?option=com_osproperty&no_html=1&task=property_captcha&ResultStr=<?php echo $captcha_value;?>" />
	     	<input type="text" class="input-mini" id="sharing_security_code" name="sharing_security_code" maxlength="5" style="width: 50px; margin: 0;" />
	     	
	    </div>
	</div>
	<div style="width:100%;padding:5px;text-align:center;">
    	<input class="btn btn-primary" type="button" name="finish" value="<?php echo Text::_('OS_SEND');?>" onclick="javascript:submitForm('tellfriend_form');"/>
    </div>
	
	<span class="reg_loading" id="tf_loading">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
	<input type="hidden" name="captcha_str" id="captcha_str" value="<?php echo $captcha_value;?>" />	
	<input type="hidden" name="option" value="com_osproperty" />
	<input type="hidden" name="task" value="property_submittellfriend" />
	<input type="hidden" name="id" value="<?php echo $id?>" />
	<input type="hidden" name="require_field" id="require_field" value="friend_name,friend_email,sharing_security_code" />
	<input type="hidden" name="require_label" id="require_label" value="<?php echo Text::_('OS_FRIEND_NAME');?>,<?php echo Text::_('OS_FRIEND_EMAIL');?>,<?php echo Text::_('OS_SECURITY_CODE')?>" />
</form>
<script type="text/javascript">
function submitForm(form_id){
	var form = document.getElementById(form_id);
	var temp1,temp2;
	var cansubmit = 1;
	var require_field = form.require_field;
	require_field = require_field.value;
	var require_label = form.require_label;
	require_label = require_label.value;
	var require_fieldArr = require_field.split(",");
	var require_labelArr = require_label.split(",");
	for(i=0;i<require_fieldArr.length;i++){
		temp1 = require_fieldArr[i];
		temp2 = document.getElementById(temp1);
		if(temp2 != null){
			if(temp2.value == ""){
				alert(require_labelArr[i] + " <?php echo Text::_('OS_IS_MANDATORY_FIELD')?>");
				temp2.focus();
				cansubmit = 0;
				return false;
			}else if(temp1 == "comment_security_code"){
				var captcha_str = document.getElementById('captcha_str');
				captcha_str = captcha_str.value;
				if(captcha_str != temp2.value){
					alert(" <?php echo Text::_('OS_SECURITY_CODE_IS_WRONG')?>");
					temp2.focus();
					cansubmit = 0;
					return false;
				}
			}else if(temp1 == "request_security_code"){
				var captcha_str = document.getElementById('captcha_str');
				captcha_str = captcha_str.value;
				if(captcha_str != temp2.value){
					alert("<?php echo Text::_('OS_SECURITY_CODE_IS_WRONG')?>");
					temp2.focus();
					cansubmit = 0;
					return false;
				}
			}else if(temp1 == "sharing_security_code"){
				var captcha_str = document.getElementById('captcha_str');
				captcha_str = captcha_str.value;
				if(captcha_str != temp2.value){
					alert("<?php echo Text::_('OS_SECURITY_CODE_IS_WRONG')?>");
					temp2.focus();
					cansubmit = 0;
					return false;
				}
			}
		}
	}
	if(cansubmit == 1){
		form.submit();
	}
}
</script>