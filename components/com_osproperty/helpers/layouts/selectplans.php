<?php
/*------------------------------------------------------------------------
# selectplans.php - Ossolution Property
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2023 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
?>
<h1 class="componentheading">
    <?php
    switch ($type){
        case "1":
            echo Text::_('OS_ACTIVE_LISTING');
            break;
        case "2":
            echo Text::_('OS_UPGRADE_PROPERTIES_TO_FEATURE');
            break;
        case "3":
            echo Text::_('OS_EXTEND_LISTING_SUBSCRIPTION');
            break;
    }
    echo " [".OSPHelper::getLanguageFieldValue($property,'pro_name')."]";
    ?>
</h1>
<form method="POST" action="<?php echo Route::_('index.php?option=com_osproperty&task=property_paymentprocess&Itemid='.$itemid); ?>" name="ftForm1" id="ftForm1">
    <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>" id="addpropertypanel6">
        <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
            <?php
            HelperOspropertyCommon::generateMembershipForm($userCredit,'property',$row->id);
            ?>
        </div>
    </div>
    <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>" id="addpropertypanel6">
        <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?> center padding10">
            <input type="submit" value="<?php echo Text::_('OS_SUBMIT');?>" class="btn btn-primary" />
            <a href="javascript:history.go(-1)" class="btn btn-warning" title="<?php echo Text::_('OS_CANCEL');?>"><?php echo Text::_('OS_CANCEL');?></a>
        </div>
    </div>
    <input type="hidden" name="option" value="com_osproperty" />
    <input type="hidden" name="task" value="property_membershipprocess" />
    <input type="hidden" name="Itemid" value="<?php echo $itemid;?>" />
    <input type="hidden" name="remove_value" id="remove_value" value="" />
    <input type="hidden" name="live_site" value="<?php echo Uri::root()?>" />
    <input type="hidden" name="cid[]" value="<?php echo $property->id?>" />
    <input type="hidden" name="type" id="type" value="<?php echo $type;?>" />
</form>