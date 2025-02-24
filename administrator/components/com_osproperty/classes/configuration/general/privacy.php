<?php 
/*------------------------------------------------------------------------
# privacy.php - Ossolution Property
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
?>
<fieldset class="form-horizontal options-form">
	<legend><?php echo Text::_('OS_PRIVACY_POLICY')?></legend>
    <div class="<?php echo $controlGroupClass; ?>">
        <div class="<?php echo $controlLabelClass; ?>">
            <?php echo HelperOspropertyCommon::showLabel('use_privacy_policy', Text::_( 'OS_SHOW_PRIVACY_POLICY' ), Text::_('OS_SHOW_PRIVACY_POLICY_EXPLAIN')); ?>
        </div>
        <div class="<?php echo $controlsClass; ?>">
            <?php
            OspropertyConfiguration::showCheckboxfield('use_privacy_policy',(int)$configs['use_privacy_policy']);
            ?>
        </div>
    </div>
    <div class="<?php echo $controlGroupClass; ?>" data-showon='<?php echo HelperOspropertyCommon::renderShowon(array('configuration[use_privacy_policy]' => '1')); ?>'>
        <div class="<?php echo $controlLabelClass; ?>">
            <?php echo HelperOspropertyCommon::showLabel('privacy_policy_article_id', Text::_( 'OS_PRIVACY_ARTICLE' ), Text::_('OS_PRIVACY_ARTICLE_EXPLAIN')); ?>
        </div>
        <div class="<?php echo $controlsClass; ?>">
            <?php echo OSPHelper::getArticleInput($configs['privacy_policy_article_id'], 'configuration[privacy_policy_article_id]'); ?>
        </div>
    </div>
    <div class="<?php echo $controlGroupClass; ?>" data-showon='<?php echo HelperOspropertyCommon::renderShowon(array('configuration[use_privacy_policy]' => '1')); ?>'>
        <div class="<?php echo $controlLabelClass; ?>">
            <?php echo HelperOspropertyCommon::showLabel('allow_user_profile_optin', Text::_( 'OS_TURNON_PROFILE_OPTIN' ), Text::_('OS_TURNON_PROFILE_OPTIN_EXPLAIN')); ?>
        </div>
        <div class="<?php echo $controlsClass; ?>">
            <?php
            OspropertyConfiguration::showCheckboxfield('allow_user_profile_optin',(int)$configs['allow_user_profile_optin']);
            ?>
        </div>
    </div>
	<!--
    <div class="<?php echo $controlGroupClass; ?>" data-showon='<?php echo HelperOspropertyCommon::renderShowon(array('configuration[use_privacy_policy]' => '1')); ?>'>
        <div class="<?php echo $controlLabelClass; ?>">
            <?php echo HelperOspropertyCommon::showLabel('delete_account', Text::_( 'OS_DELETE_PROFILE_WHEN_USER_DELETED' ), Text::_('OS_DELETE_PROFILE_WHEN_USER_DELETED_EXPLAIN')); ?>
        </div>
        <div class="<?php echo $controlsClass; ?>">
            <?php
            OspropertyConfiguration::showCheckboxfield('delete_account',$configs['delete_account']);
            ?>
        </div>
    </div>
	-->
</fieldset>