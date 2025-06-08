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

if (!in_array('ids_event_published', $fields)) {
    $sql = "ALTER TABLE  `#__eb_events` ADD  `ids_event_published` VARCHAR(32) NULL DEFAULT  NULL;";
    $db->setQuery($sql);
    $db->execute();
}

$rows = EventbookingHelperDatabase::getAllEvents($this->config->sort_events_dropdown, $this->config->hide_past_events_from_events_dropdown, []);

$currentId = (int) $this->item->id;

$rows = array_filter($rows, function ($row) use ($currentId) {
    return (int) $row->id !== $currentId;
});


$lists['ids_event_published'] = EventbookingHelperHtml::getEventsDropdown(
    $rows,
    'ids_event_published[]',
    'class="input-xlarge form-select advancedSelect" multiple="multiple" ',
    explode(',', $this->item->ids_event_published),
);
?>
<div class="control-group">
    <div class="control-label">
        <?php echo Text::_('EB_EVENTS_PUBLISHED'); ?>
    </div>
    <div class="controls">
        <?php echo EventbookingHelperHtml::getChoicesJsSelect($lists['ids_event_published']); ?>
    </div>
</div>