<?php 
/*------------------------------------------------------------------------
# offering_paid.php - Ossolution Property
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
$inputMiniClass	   = $bootstrapHelper->getClassMapping('input-mini'). ' smallSizeBox';
?>
<fieldset class="form-horizontal options-form">
	<legend><?php echo Text::_('Offering Paid Listing')?></legend>

    <div class="<?php echo $controlGroupClass; ?>">
        <div class="<?php echo $controlLabelClass; ?>">
            <?php echo HelperOspropertyCommon::showLabel('active_payment',Text::_( 'OS_ACTIVATE_PAYMENT' ),Text::_('OS_ACTIVE_PAYMENT_EXPLAIN')); ?>
        </div>
        <div class="<?php echo $controlsClass; ?>">
            <?php
            OspropertyConfiguration::showCheckboxfield('active_payment',$configs['active_payment']);
            ?>
        </div>
    </div>
    <div class="<?php echo $controlGroupClass; ?>" data-showon='<?php echo HelperOspropertyCommon::renderShowon(array('configuration[active_payment]' => '1')); ?>'>
        <div class="<?php echo $controlLabelClass; ?>">
            <?php echo HelperOspropertyCommon::showLabel('normal_cost',Text::_( 'OS_COST_PER_NORMAL_PROPERTIES' ),Text::_('OS_COST_PER_NORMAL_PROPERTIES_EXPLAIN')); ?>
        </div>
        <div class="<?php echo $controlsClass; ?>">
            <input type="text" class="<?php echo$inputMiniClass; ?>" size="10" name="configuration[normal_cost]" value="<?php echo isset($configs['normal_cost'])? $configs['normal_cost']:'0'; ?>" />
            <?php echo HelperOspropertyCommon::loadCurrency($configs['general_currency_default']);?>
        </div>
    </div>
    <div class="<?php echo $controlGroupClass; ?>" data-showon='<?php echo HelperOspropertyCommon::renderShowon(array('configuration[active_payment]' => '1')); ?>'>
        <div class="<?php echo $controlLabelClass; ?>">
            <?php echo HelperOspropertyCommon::showLabel('general_featured_upgrade_amount',Text::_( 'OS_COST_PER_FEATURED_PROPERTIES' ),Text::_('OS_COST_PER_FEATURED_PROPERTIES_EXPLAIN'));?>
        </div>
        <div class="<?php echo $controlsClass; ?>">
            <input type="text" class="<?php echo$inputMiniClass; ?>" size="10" name="configuration[general_featured_upgrade_amount]" value="<?php echo isset($configs['general_featured_upgrade_amount'])? $configs['general_featured_upgrade_amount']:''; ?>" />
            <?php echo HelperOspropertyCommon::loadCurrency($configs['general_currency_default']);?>
        </div>
    </div>
</fieldset>