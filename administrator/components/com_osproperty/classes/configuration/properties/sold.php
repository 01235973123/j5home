<?php 
/*------------------------------------------------------------------------
# sold.php - Ossolution Property
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

$controlGroupClass = $bootstrapHelper->getClassMapping('control-group');
$controlLabelClass = $bootstrapHelper->getClassMapping('control-label');
$controlsClass	   = $bootstrapHelper->getClassMapping('controls');
$inputLargeClass   = $bootstrapHelper->getClassMapping('input-large');
?>

<fieldset class="form-horizontal options-form">
	<legend><?php echo Text::_('OS_MARKET_STATUS')?></legend>
	<div class="<?php echo $controlGroupClass; ?>">
		<div class="<?php echo $controlLabelClass; ?>">
			<?php echo HelperOspropertyCommon::showLabel('active_market_status', Text::_( 'OS_ACTIVATE_OS_MARKET_STATUS' ), Text::_('OS_ACTIVATE_OS_MARKET_STATUS_EXPLAIN')); ?>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<?php
			OspropertyConfiguration::showCheckboxfield('active_market_status',(int)$configs['active_market_status']);
			?>
		</div>
	</div>
	<div class="<?php echo $controlGroupClass; ?>" data-showon='<?php echo HelperOspropertyCommon::renderShowon(array('configuration[active_market_status]' => '1')); ?>'>
		<div class="<?php echo $controlLabelClass; ?>">
			<?php echo HelperOspropertyCommon::showLabel('market_statuses', Text::_( 'OS_SELECT_MARKET_STATUS' ), Text::_('OS_SELECT_MARKET_STATUS_EXPLAIN')); ?>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<?php
			$market_status = array();
			$tmp		   = new \stdClass();
			$tmp->value = 1;
			$tmp->text = Text::_('OS_SOLD');
			$market_status[0] = $tmp;

			$tmp		   = new \stdClass();
			$tmp->value = 2;
			$tmp->text = Text::_('OS_CURRENT');
			$market_status[1] = $tmp;


			$tmp		   = new \stdClass();
			$tmp->value = 3;
			$tmp->text = Text::_('OS_RENTED');
			$market_status[2] = $tmp;

			$tmp		   = new \stdClass();
			$tmp->value = 4;
			$tmp->text = Text::_('OS_OFF_MARKET');
			$market_status[3] = $tmp;

			$tmp		   = new \stdClass();
			$tmp->value = 5;
			$tmp->text = Text::_('OS_FOR_SALE');
			$market_status[4] = $tmp;

			$tmp		   = new \stdClass();
			$tmp->value = 6;
			$tmp->text = Text::_('OS_FOR_RENT');
			$market_status[5] = $tmp;

			$tmp		   = new \stdClass();
			$tmp->value = 7;
			$tmp->text = Text::_('OS_SALE_PENDING');
			$market_status[6] = $tmp;


			$checkbox_market_status = array();
			if (isset($configs['market_status'])){
				$checkbox_market_status = explode(',',$configs['market_status']);
			}
			if($configs['use_sold'] == "1"){
				$checkbox_market_status[] = 1;
			}
			echo HTMLHelper::_('select.genericlist',$market_status,'configuration[market_status][]','multiple class="chosen form-select"','value','text',$checkbox_market_status);
			?>
		</div>
	</div>
</fieldset>

