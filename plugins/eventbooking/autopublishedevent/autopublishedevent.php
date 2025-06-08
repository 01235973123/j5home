<?php

/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2025 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\User\UserHelper;
use Joomla\Event\Event;
use Joomla\Event\SubscriberInterface;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;

class plgEventbookingAutoPublishedEvent extends CMSPlugin
{
	/**
	 * Application object.
	 *
	 * @var    \Joomla\CMS\Application\CMSApplication
	 */
	protected $app;

	/**
	 * Database object.
	 *
	 * @var    \Joomla\Database\DatabaseDriver
	 */
	protected $db;

	/**
	 * Generate invoice number after registrant complete payment for registration
	 *
	 * @param   Event  $eventObj
	 *
	 * @return void
	 */
	public function onAfterPaymentSuccess($row)
	{

		// Coupon code was generated for this registration before, don't generate again
		if ($row->auto_coupon_coupon_id > 0) {
			return;
		}

		$db    = $this->db;
		$query = $db->getQuery(true);

		$config = EventbookingHelper::getConfig();

		$query->clear()
			->select('SUM(a.number_registrants) AS total_registrants')
			->from('#__eb_registrants AS a')
			->innerJoin('#__eb_events AS b ON a.event_id = b.id')
			->where('b.id = ' . $row->event_id)
			->where('a.group_id = 0')
			->where('(a.published = 1 OR (a.payment_method LIKE "os_offline%" AND a.published = 0))');
		$db->setQuery($query);
		$total = $db->loadResult();

		$event = EventbookingHelperDatabase::getEvent($row->event_id);

		if ($total >= $event->event_capacity) {
			// 1. Lấy các sự kiện có chứa $event->id trong cột ids_event_published
			$query->clear()
				->select('id, ids_event_published')
				->from('#__eb_events')
				->where('ids_event_published LIKE ' . $db->quote('%' . $event->id . '%'));
			$db->setQuery($query);
			$relatedEvents = $db->loadObjectList();

			foreach ($relatedEvents as $relatedEvent) {
				$publishedIds = array_map('intval', explode(',', $relatedEvent->ids_event_published));

				$allFull = true;

				foreach ($publishedIds as $eventIdToCheck) {
					$query->clear()
						->select('SUM(a.number_registrants) AS total_registrants')
						->from('#__eb_registrants AS a')
						->innerJoin('#__eb_events AS b ON a.event_id = b.id')
						->where('b.id = ' . (int)$eventIdToCheck)
						->where('a.group_id = 0')
						->where('(a.published = 1 OR (a.payment_method LIKE "os_offline%" AND a.published = 0))');
					$db->setQuery($query);
					$registered = (int) $db->loadResult();

					$eventData = EventbookingHelperDatabase::getEvent($eventIdToCheck);

					if ($registered < $eventData->event_capacity) {
						$allFull = false;
						break;
					}
				}

				// 4. Nếu tất cả các event đã đầy → cập nhật published = 1
				if ($allFull) {
					$query->clear()
						->update('#__eb_events')
						->set('published = 1')
						->where('id = ' . (int)$relatedEvent->id);
					$db->setQuery($query);
					$db->execute();
				}
			}
		}
	}
}
