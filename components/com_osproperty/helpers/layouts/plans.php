<?php
/*------------------------------------------------------------------------
# plans.php - Ossolution Property
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2023 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
use Joomla\CMS\Language\Text;
?>
<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
    <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
        <?php
            OSPHelper::generateHeading(2,Text::_('OS_SELECT_PLANS_FOR_PURCHASING'));
        ?>
    </div>
</div>

<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
    <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
        <?php
        echo $plans;
        ?>
    </div>
</div>
