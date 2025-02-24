<?php 
/*------------------------------------------------------------------------
# currency.php - Ossolution Property
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

global $languages;
$controlGroupClass = $bootstrapHelper->getClassMapping('control-group');
$controlLabelClass = $bootstrapHelper->getClassMapping('control-label');
$controlsClass	   = $bootstrapHelper->getClassMapping('controls');
$inputLargeClass   = $bootstrapHelper->getClassMapping('input-large');
$inputSmallClass   = $bootstrapHelper->getClassMapping('input-small');
?>
<fieldset class="form-horizontal options-form">
	<legend><?php echo TextOs::_('Currency Setting')?></legend>
	<div class="<?php echo $controlGroupClass; ?>">
		<div class="<?php echo $controlLabelClass; ?>">
			<?php echo HelperOspropertyCommon::showLabel('general_currency_default', TextOs::_( 'Default currency' ), TextOs::_('DEFAULT_CURRENCY_EXPLAIN')); ?>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<?php
			$db = Factory::getDbo();
			$db->setQuery("Select id as value, concat(currency_name,' - ',currency_code,' - ',currency_symbol) as text from #__osrs_currencies where published = '1' order by currency_name");
			$currencies = $db->loadObjectList();
			if (!isset($configs['general_currency_default'])) $configs['general_currency_default'] = '';
			echo OSPHelper::getChoicesJsSelect(HTMLHelper::_('select.genericlist',$currencies,'configuration[general_currency_default]','class="inputbox  ilarge form-select"','value','text',$configs['general_currency_default']));
			?>
		</div>
	</div>
	<div class="<?php echo $controlGroupClass; ?>">
		<div class="<?php echo $controlLabelClass; ?>">
			<?php echo HelperOspropertyCommon::showLabel('show_convert_currency', TextOs::_( 'Show convert currencies' ), TextOs::_('Show convert currencies explain')." ".Text::_( 'OS_CONVERT_CURRENCY_NOTICE' )); ?>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<?php
			OspropertyConfiguration::showCheckboxfield('show_convert_currency',$configs['show_convert_currency']);
			?>
		</div>
	</div>
	<div class="<?php echo $controlGroupClass; ?>">
        <div class="<?php echo $controlLabelClass; ?>">
            <?php echo HelperOspropertyCommon::showLabel('convert_api', Text::_( 'Currency convert API' ), Text::_('Register API key through this page: https://free.currconv.com/login')); ?>
        </div>
        <div class="<?php echo $controlsClass; ?>">
            <input type="text" class="<?php echo $inputSmallClass; ?> ilarge" name="configuration[convert_api]" value="<?php echo isset($configs['convert_api'])?$configs['convert_api']:'' ?>" />
        </div>
    </div>
	<div class="<?php echo $controlGroupClass; ?>">
		<div class="<?php echo $controlLabelClass; ?>">
			<?php echo HelperOspropertyCommon::showLabel('general_currency_money_format', TextOs::_( 'Money format' ), TextOs::_('Show convert currencies explain')); ?>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<?php
			$option_moneyformat = array();
			$option_moneyformat[] = HTMLHelper::_('select.option',0,TextOs::_('Select Money format'));
			$option_moneyformat[] = HTMLHelper::_('select.option',1,'1.000.000,00');
			$option_moneyformat[] = HTMLHelper::_('select.option',2,'1 000 000,00');
			$option_moneyformat[] = HTMLHelper::_('select.option',3,'1,000,000.00');
			$option_moneyformat[] = HTMLHelper::_('select.option',4,'1.000.000');
			$option_moneyformat[] = HTMLHelper::_('select.option',5,'1 000 000');
			$option_moneyformat[] = HTMLHelper::_('select.option',6,'1,000,000');
			echo HTMLHelper::_('select.genericlist',$option_moneyformat,'configuration[general_currency_money_format]','class="form-select input-large ilarge"','value','text',isset($configs['general_currency_money_format'])? $configs['general_currency_money_format']:0);
			?>
		</div>
	</div>
	<div class="<?php echo $controlGroupClass; ?>">
		<div class="<?php echo $controlLabelClass; ?>">
			<?php echo HelperOspropertyCommon::showLabel('currency_position', Text::_( 'OS_CURRENCY_POSITION' ), Text::_('OS_CURRENCY_POSITION_EXPLAIN')); ?>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<?php
			$option_order = array();
			$option_order[] = HTMLHelper::_('select.option','0',Text::_('OS_BEFORE_PRICE'));
			$option_order[] = HTMLHelper::_('select.option','1',Text::_('OS_AFTER_PRICE'));
			echo HTMLHelper::_('select.genericlist',$option_order,'configuration[currency_position]','class="form-select input-large ilarge"','value','text',$configs['currency_position']);
			?>
		</div>
	</div>
</fieldset>