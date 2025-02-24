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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
?>
<table width="100%" class="table table-striped table-bordered membershiptable" id="membershiptable">
    <thead>
    <tr>
        <th width="30%" class="nowrap  paddingleft10 colorwhite">
            <?php echo Text::_('OS_PROPERTY_TYPE');?>
        </th>
        <th width="25%" class="nowrap  paddingleft10 colorwhite">
            <span class="hasTip" title="<?php echo Text::_('OS_ACCOUNT_REMAINING');?>::<?php echo Text::_('OS_ACCOUNT_REMAINING_EXPLAIN');?>">
                <?php echo Text::_('OS_ACCOUNT_REMAINING');?>
            </span>
        </th>
        <?php
        if($configClass['general_use_expiration_management'] == 1){
            ?>
            <th width="45%" class="nowrap paddingleft10 colorwhite">
                <span class="hasTip" title="<?php echo Text::_('OS_PROPERTY_WILL_EXPIRED_ON');?>::<?php echo Text::_('OS_PROPERTY_WILL_EXPIRED_ON_EXPLAIN');?>">
                    <?php echo Text::_('OS_PROPERTY_WILL_EXPIRED_ON');?>
                </span>
            </th>
        <?php } ?>
    </tr>
    </thead>
    <tbody>
    <tr class="row0">
        <td width="35%" class="paddingleft10" data-label="<?php echo Text::_('OS_PROPERTY_TYPE');?>">
            <?php
            echo Text::_('OS_STANDARD_PROPERTY');
            ?>
        </td>
        <td width="20%" class="paddingleft10 center" data-label="<?php echo Text::_('OS_ACCOUNT_REMAINING');?>">
            <?php echo (int)$agentAcc[0]; ?>
            <?php
            if((int)$agentAcc[0] == 0){
                $link = OspropertyMembership::generateLink($usertype,0,0);
                ?>
                <div class="clearfix"></div>
                <a href="<?php echo $link;?>" title="<?php echo Text::_('OS_PURCHASE_SUBSCRIPTION');?>"><?php echo Text::_('OS_PURCHASE_SUBSCRIPTION');?></a>
                <?php
            }
            ?>
        </td>
    </tr>
    <tr class="row1">
        <td width="35%" class="paddingleft10" data-label="<?php echo Text::_('OS_PROPERTY_TYPE');?>">
            <?php
            echo Text::_('OS_FEATURED_PROPERTY');
            ?>
        </td>
        <td width="20%" class="paddingleft10 center" data-label="<?php echo Text::_('OS_ACCOUNT_REMAINING');?>">
            <?php echo (int)$agentAcc[1]; ?>
            <?php
            if((int)$agentAcc[1] == 0){
                $link = OspropertyMembership::generateLink($usertype,1,0);
                ?>
                <div class="clearfix"></div>
                <a href="<?php echo $link;?>" title="<?php echo Text::_('OS_PURCHASE_SUBSCRIPTION');?>"><?php echo Text::_('OS_PURCHASE_SUBSCRIPTION');?></a>
                <?php
            }
            ?>
        </td>
    </tr>
    </tbody>
</table>