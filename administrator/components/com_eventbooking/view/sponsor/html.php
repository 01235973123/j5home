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

class EventbookingViewSponsorHtml extends RADViewItem
{
	protected function prepareView()
	{
		parent::prepareView();

		$config = EventbookingHelper::getConfig();

		$selectedEventIds = [];

		if ($this->item->id > 0)
		{
			/* @var \Joomla\Database\DatabaseDriver $db */
			$db    = Factory::getContainer()->get('db');
			$query = $db->getQuery(true)
				->select('event_id')
				->from('#__eb_event_sponsors')
				->where('sponsor_id = ' . $this->item->id);
			$db->setQuery($query);
			$selectedEventIds = $db->loadColumn();
		}

		$rows                    = EventbookingHelperDatabase::getAllEvents($config->sort_events_dropdown);
		$this->lists['event_id'] = EventbookingHelperHtml::getEventsDropdown(
			$rows,
			'event_id[]',
			'class="input-xlarge" multiple',
			$selectedEventIds,
			false
		);
	}
}
