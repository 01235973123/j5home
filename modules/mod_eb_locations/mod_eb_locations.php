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
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\HTML\HTMLHelper;

/**
 * Variables
 *
 * @var \Joomla\Registry\Registry $params
 */

// Require library + register autoloader
require_once JPATH_ADMINISTRATOR . '/components/com_eventbooking/libraries/rad/bootstrap.php';

if (!EventbookingHelper::needToShowModule($params->get('show_on_pages', '')))
{
	return;
}

// Load css
EventbookingHelper::loadComponentCssForModules();

$user = Factory::getApplication()->getIdentity();
EventbookingHelper::loadLanguage();
$config           = EventbookingHelper::getConfig();
$fieldSuffix      = EventbookingHelper::getFieldSuffix();
$app              = Factory::getApplication();

/* @var \Joomla\Database\DatabaseDriver $db */
$db               = Factory::getContainer()->get('db');
$query            = $db->getQuery(true);
$numberLocations  = (int) $params->get('number_locations', 0);
$showNumberEvents = (int) $params->get('show_number_events', 1);

$query->select('a.id, COUNT(b.id) AS total_events')
	->select($db->quoteName('a.name' . $fieldSuffix, 'name'))
	->from('#__eb_locations AS a')
	->innerJoin('#__eb_events AS b ON a.id = b.location_id')
	->where('a.published = 1')
	->where('b.hidden = 0')
	->where('b.published = 1')
	->whereIn('b.access', $user->getAuthorisedViewLevels())
	->group('a.id')
	->order($db->quoteName('a.name' . $fieldSuffix));

if ($config->hide_past_events)
{
	$currentDate = $db->quote(HTMLHelper::_('date', 'Now', 'Y-m-d'));

	if ($config->show_children_events_under_parent_event)
	{
		$query->where(
			'(DATE(b.event_date) >= ' . $currentDate . ' OR DATE(b.cut_off_date) >= ' . $currentDate . ' OR DATE(b.max_end_date) >= ' . $currentDate . ')'
		);
	}
	else
	{
		if ($config->show_until_end_date)
		{
			$query->where('(DATE(b.event_date) >= ' . $currentDate . ' OR DATE(b.event_end_date) >= ' . $currentDate . ')');
		}
		else
		{
			$query->where('(DATE(b.event_date) >= ' . $currentDate . ' OR DATE(b.cut_off_date) >= ' . $currentDate . ')');
		}
	}
}

if ($numberLocations)
{
	$db->setQuery($query, 0, $numberLocations);
}
else
{
	$db->setQuery($query);
}

$rows   = $db->loadObjectList();
$itemId = (int) $params->get('item_id');

if (!$itemId)
{
	$itemId = EventbookingHelper::getItemid();
}

require ModuleHelper::getLayoutPath('mod_eb_locations', 'default');
