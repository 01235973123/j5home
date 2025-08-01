<?php

/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2025 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

$db     = Factory::getContainer()->get('db');
$query  = $db->getQuery(true);

$fields = array_keys($db->getTableColumns('#__eb_events'));

if (!in_array('auto_unpublish_timeslot', $fields)) {
    $sql = "ALTER TABLE  `#__eb_events` ADD `auto_unpublish_timeslot` TINYINT NOT NULL DEFAULT 1;";
    $db->setQuery($sql);
    $db->execute();
}

?>
<div class="control-group">
    <div class="control-label">
        <?php echo Text::_('EB_AUTO_UNPUBLISH_TIME_SLOT'); ?>
    </div>
    <div class="controls">
        <?php echo EventbookingHelperHtml::getBooleanInput('auto_unpublish_timeslot', $this->item->auto_unpublish_timeslot); ?>
    </div>
</div>