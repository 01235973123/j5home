<?php 
/*------------------------------------------------------------------------
# spam.php - Ossolution Property
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
	<legend><?php echo Text::_('OS_SPAM_DETECT')?></legend>
    <div class="<?php echo $controlGroupClass; ?>">
        <div class="<?php echo $controlLabelClass; ?>">
            <?php echo HelperOspropertyCommon::showLabel('integrate_stopspamforum', TextOs::_( 'Integrate with StopSpamForm' ), TextOs::_('Integrate with StopSpamForm explain')); ?>
        </div>
        <div class="<?php echo $controlsClass; ?>">
			<?php
            OspropertyConfiguration::showCheckboxfield('integrate_stopspamforum',$configs['integrate_stopspamforum']);
            ?>
        </div>
    </div>
</fieldset>
<fieldset class="form-horizontal options-form">
    <legend><?php echo Text::_('OS_REPORT')?></legend>
    <div class="<?php echo $controlGroupClass; ?>">
        <div class="<?php echo $controlLabelClass; ?>">
            <?php echo HelperOspropertyCommon::showLabel('integrate_stopspamforum',Text::_( 'OS_REPORT' ), Text::_('OS_REPORT_EXPLAIN')); ?>
        </div>
        <div class="<?php echo $controlsClass; ?>">
            <?php
            OspropertyConfiguration::showCheckboxfield('enable_report',$configs['enable_report']);
            ?>
        </div>
    </div>
</fieldset>