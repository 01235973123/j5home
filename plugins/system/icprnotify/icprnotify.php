<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2025 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\Event;
use Joomla\Event\SubscriberInterface;

class plgSystemIcprNotify extends CMSPlugin implements SubscriberInterface
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
	 * @return array
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			'onAfterRespond' => 'onAfterRespond',
		];
	}

	/**
	 * Send reminder to registrants
	 *
	 * @param   Event  $eventObj
	 *
	 * @return void
	 * @throws Exception
	 */
	public function onAfterRespond(Event $eventObj): void
	{
		if (!$this->canRun())
		{
			return;
		}

		// Require library + register autoloader
		require_once JPATH_ADMINISTRATOR . '/components/com_eventbooking/libraries/rad/bootstrap.php';

		$cacheTime = (int) $this->params->get('cache_time', 12) * 3600;

		// We only need to check and store last runtime if cron job is not configured
		if (!$this->params->get('trigger_code')
			&& !EventbookingHelperPlugin::checkAndStoreLastRuntime($this->params, $cacheTime, $this->_name))
		{
			return;
		}

		// Only send notification to registrations within the last 48 hours
		$db    = $this->db;
		$now   = $db->quote(Factory::getDate('now', $this->app->get('offset'))->toSql(true));
		$query = $db->getQuery(true)
			->select('*')
			->from('#__eb_registrants')
			->where('published = 0')
			->where('group_id = 0')
			->where('icpr_notified = 0')
			->where('payment_method NOT LIKE "os_offline%"')
			->where("TIMESTAMPDIFF(HOUR, register_date, $now) <= 48")
			->order('id');
		$db->setQuery($query, 0, 10);
		$rows = $db->loadObjectList();

		$registrants = [];
		$ids         = [];

		foreach ($rows as $row)
		{
			// Special case, without user_id and email, no way to check if he is registered again ir not
			if (!$row->user_id && !$row->email)
			{
				$registrants[] = $row;
				continue;
			}

			// Check to see if he has paid for the event
			$query->clear()
				->select('COUNT(*)')
				->from('#__eb_registrants')
				->where('event_id = ' . $row->event_id)
				->where('id != ' . $row->id)
				->where('(published = 1 OR payment_method LIKE "os_offline%")');

			if ($row->user_id)
			{
				$query->where('user_id = ' . $row->user_id);
			}
			else
			{
				$query->where('email = ' . $db->quote($row->email));
			}

			$db->setQuery($query);
			$total = $db->loadResult();

			if (!$total)
			{
				$registrants[] = $row;
				$ids[]         = $row->id;
			}
		}

		if (count($registrants) > 0)
		{
			EventbookingHelper::callOverridableHelperMethod('Mail', 'sendIncompletePaymentRegistrationsEmails', [$registrants, $this->params]);

			// Mark the notification as sent
			$query->clear()
				->update('#__eb_registrants')
				->set('icpr_notified = 1')
				->whereIn('id', $ids);
			$db->setQuery($query)
				->execute();
		}
	}

	/**
	 * Override registerListeners method to only register listeners if needed
	 *
	 * @return void
	 */
	public function registerListeners()
	{
		if (!ComponentHelper::isEnabled('com_eventbooking'))
		{
			return;
		}

		parent::registerListeners();
	}

	/**
	 * Method to check whether this plugin should be run
	 *
	 * @return bool
	 */
	private function canRun(): bool
	{
		// If trigger code is set, we will only process sending reminder from cron job
		if (trim($this->params->get('trigger_code', ''))
			&& trim($this->params->get('trigger_code', '')) != $this->app->getInput()->getString('trigger_code'))
		{
			return false;
		}

		$subject = $this->params->get('subject', '');
		$body    = $this->params->get('message', '');

		if (strlen(trim($subject)) === 0)
		{
			return false;
		}

		if (strlen(trim(strip_tags($body))) === 0)
		{
			return false;
		}

		return true;
	}
}
