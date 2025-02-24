<?php 
/*------------------------------------------------------------------------
# business.php - Ossolution Property
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2023 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// no direct access
defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

global $languages;
$controlGroupClass = $bootstrapHelper->getClassMapping('control-group');
$controlLabelClass = $bootstrapHelper->getClassMapping('control-label');
$controlsClass	   = $bootstrapHelper->getClassMapping('controls');
$inputLargeClass   = $bootstrapHelper->getClassMapping('input-large');
?>
<fieldset class="form-horizontal options-form">
	<legend><?php echo TextOs::_('Business setting')?></legend>
	<div class="<?php echo $controlGroupClass; ?>">
		<div class="<?php echo $controlLabelClass; ?>">
			<?php echo HelperOspropertyCommon::showLabel('general_bussiness_name', TextOs::_( 'System bussiness name' ), TextOs::_('The name of your real estate business shown on the component header and elsewhere, eg. the print page and email pages.')); ?>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<input type="text" class="<?php echo $inputLargeClass;?> ilarge" size="40" name="configuration[general_bussiness_name]" value="<?php echo isset($configs['general_bussiness_name'])? $configs['general_bussiness_name']:''; ?>">
		</div>
	</div>
	<div class="<?php echo $controlGroupClass; ?>">
		<div class="<?php echo $controlLabelClass; ?>">
			<?php echo HelperOspropertyCommon::showLabel('general_bussiness_address', TextOs::_( 'Business Address' ), Text::_('Your business address. This appears in the header of the print page.')); ?>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<input type="text" class="<?php echo $inputLargeClass;?> ilarge" size="40" name="configuration[general_bussiness_address]" value="<?php echo isset($configs['general_bussiness_address'])? $configs['general_bussiness_address']:''; ?>">
		</div>
	</div>
	<div class="<?php echo $controlGroupClass; ?>">
		<div class="<?php echo $controlLabelClass; ?>">
			<?php echo HelperOspropertyCommon::showLabel('general_bussiness_phone', Text::_( 'OS_BUSINESS_PHONE_NUMBER' ), Text::_('OS_BUSINESS_PHONE_NUMBER_EXPLAIN')); ?>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<input type="text" class="<?php echo $inputLargeClass;?> ilarge" size="40" name="configuration[general_bussiness_phone]" value="<?php echo isset($configs['general_bussiness_phone'])? $configs['general_bussiness_phone']:''; ?>">
		</div>
	</div>
	<div class="<?php echo $controlGroupClass; ?>">
		<div class="<?php echo $controlLabelClass; ?>">
			<?php echo HelperOspropertyCommon::showLabel('general_bussiness_email', TextOs::_( 'Business Email' ), TextOs::_('Email address to use with the property inspection and mailing list request forms.')); ?>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<input type="text" class="<?php echo $inputLargeClass;?> ilarge" size="40" name="configuration[general_bussiness_email]" value="<?php echo isset($configs['general_bussiness_email'])? $configs['general_bussiness_email']:''; ?>">
		</div>
	</div>
	<div class="<?php echo $controlGroupClass; ?>">
		<div class="<?php echo $controlLabelClass; ?>">
			<?php echo HelperOspropertyCommon::showLabel('notify_email', TextOs::_( 'Notify Email' ), TextOs::_('NOTIFY_EMAIL_EXPLAIN')); ?>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<input type="text" class="<?php echo $inputLargeClass;?> ilarge" size="40" name="configuration[notify_email]" value="<?php echo isset($configs['notify_email'])? $configs['notify_email']:''; ?>" />
		</div>
	</div>
	<div class="<?php echo $controlGroupClass; ?>">
		<div class="<?php echo $controlLabelClass; ?>">
			<?php echo HelperOspropertyCommon::showLabel('show_footer', Text::_( 'OS_SHOW_COPYRIGHT' ), TextOs::_('SHOW_FOOTER_EXPLAIN')); ?>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<?php
			OspropertyConfiguration::showCheckboxfield('show_footer',$configs['show_footer']);
			?>
		</div>
	</div>
</fieldset>
<input type="hidden" name="configuration[live_site]" id="live_site" value="<?php echo Uri::root();?>" />