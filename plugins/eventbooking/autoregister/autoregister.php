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
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Event\Event;
use Joomla\Event\SubscriberInterface;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;
use OSSolution\EventBooking\Admin\Event\Registration\AfterPaymentSuccess;
use OSSolution\EventBooking\Admin\Event\Registration\AfterStoreRegistrant;

class plgEventbookingAutoregister extends CMSPlugin implements SubscriberInterface
{
	use RADEventResult;

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
			'onEditEvent'                 => 'onEditEvent',
			'onAfterSaveEvent'            => 'onAfterSaveEvent',
			'onAfterStoreRegistrant'      => 'onAfterStoreRegistrant',
			'onAfterPaymentSuccess'       => 'onAfterPaymentSuccess',
			'onEBCheckAcceptRegistration' => 'onEBCheckAcceptRegistration',
		];
	}

	/**
	 * Render setting form
	 *
	 * @param   Event  $eventObj
	 *
	 * @return void
	 */
	public function onEditEvent(Event $eventObj): void
	{
		/* @var EventbookingTableEvent $row */
		[$row] = array_values($eventObj->getArguments());

		if (!$this->canRun($row))
		{
			return;
		}

		ob_start();
		$this->drawSettingForm($row);

		$result = [
			'title' => Text::_('EB_AUTO_REGISTER'),
			'form'  => ob_get_clean(),
		];

		$this->addResult($eventObj, $result);
	}

	/**
	 * Store setting into database, in this case, use params field of plans table
	 *
	 * @param   Event  $eventObj
	 */
	public function onAfterSaveEvent(Event $eventObj): void
	{
		/**
		 * @var EventbookingTableEvent $row
		 * @var array                  $data
		 * @var bool                   $isNew
		 */
		[$row, $data, $isNew] = array_values($eventObj->getArguments());

		// The plugin will only be available in the backend
		if (!$this->canRun($row))
		{
			return;
		}

		$params = new Registry($row->params);
		$params->set('auto_register_event_ids', trim($data['auto_register_event_ids']));
		$params->set('auto_register_all_children_events', $data['auto_register_all_children_events']);
		$row->params = $params->toString();
		$row->store();
	}

	/**
	 * Check accept registration
	 *
	 * @param   EventbookingTableEvent  $event
	 *
	 * @return bool
	 */
	/**
	 * Check accept registration
	 *
	 * @param   Event  $eventObj
	 *
	 * @return void
	 */
	public function onEBCheckAcceptRegistration(Event $eventObj): void
	{
		static $checkedEventIds = [];

		/* @var EventbookingTableEvent $event */
		[$event] = array_values($eventObj->getArguments());

		if ($this->params->get('disable_registration_for_main_event_if_auto_register_event_full'))
		{
			$eventIds = $this->getAutoRegistrationEventIds($event);

			foreach ($eventIds as $eventId)
			{
				if (isset($checkedEventIds[$eventId]))
				{
					continue;
				}

				// Mark it as checked so that the system does not have to check it again, prevent recursive checking error for wrong configuration
				$checkedEventIds[$eventId] = true;

				$autoRegisterEvent = EventbookingHelperDatabase::getEvent($eventId);

				if (!EventbookingHelperRegistration::acceptRegistration($autoRegisterEvent))
				{
					$event->cannot_register_reason = 'auto_register_child_event_is_full';

					$this->addResult($eventObj, false);

					return;
				}
			}
		}
	}

	/**
	 * Get IDs of events which should be auto registered
	 *
	 * @param   EventbookingTableEvent  $event
	 *
	 * @return array
	 */
	private function getAutoRegistrationEventIds($event): array
	{
		$params = new Registry($event->params);

		$eventIds                      = $params->get('auto_register_event_ids', '');
		$autoRegisterAllChildrenEvents = $params->get('auto_register_all_children_events', 0);

		$eventIds = explode(',', $eventIds);

		if ($autoRegisterAllChildrenEvents)
		{
			$db    = $this->db;
			$query = $db->getQuery(true)
				->select('id')
				->from('#__eb_events')
				->where('parent_id = ' . $event->id);
			$db->setQuery($query);
			$eventIds = array_merge($eventIds, $db->loadColumn());
		}

		// Should not auto registered to itself
		$eventIds = array_diff($eventIds, [$event->id]);

		$eventIds = ArrayHelper::toInteger($eventIds);

		$eventIds = array_unique($eventIds);

		return array_filter($eventIds);
	}

	/**
	 * Generate invoice number after registrant complete payment for registration
	 *
	 * @param   Event  $eventObj
	 *
	 * @return void
	 */
	public function onAfterPaymentSuccess(Event $eventObj): void
	{
		/* @var EventbookingTableRegistrant $row */
		[$row] = array_values($eventObj->getArguments());

		if ($row->group_id == 0 && !str_contains($row->payment_method, 'os_offline'))
		{
			$this->registerToConfiguredEvents($row);
		}
	}

	/**
	 * Generate invoice number after registrant complete registration in case he uses offline payment
	 *
	 * @param   Event  $eventObj
	 *
	 * @return void
	 */
	public function onAfterStoreRegistrant(Event $eventObj): void
	{
		/* @var EventbookingTableRegistrant $row */
		[$row] = array_values($eventObj->getArguments());

		if ($row->group_id == 0 && (str_contains($row->payment_method, 'os_offline')))
		{
			$this->registerToConfiguredEvents($row);
		}
	}

	/**
	 * Process ticket types data after registration is completed:
	 *
	 * @param   EventbookingTableRegistrant  $row
	 *
	 * @return void
	 */
	private function registerToConfiguredEvents($row): void
	{
		static $autoRegisteredEventIds = [];

		$db       = $this->db;
		$query    = $db->getQuery(true);
		$config   = EventbookingHelper::getConfig();
		$event    = EventbookingHelperDatabase::getEvent($row->event_id);
		$eventIds = $this->getAutoRegistrationEventIds($event);

		if (count($eventIds))
		{
			if ($row->is_group_billing)
			{
				$query->clear()
					->select('id')
					->from('#__eb_registrants')
					->where('group_id = ' . $row->id)
					->order('id');
				$db->setQuery($query);
				$groupMemberIds = $db->loadColumn();
			}

			foreach ($eventIds as $eventId)
			{
				$event = EventbookingHelperDatabase::getEvent($eventId);

				// Invalid event, causes by wrong configuration
				if (!$event)
				{
					continue;
				}

				if ($this->params->get('check_accept_registration', 1) && !EventbookingHelperRegistration::acceptRegistration($event))
				{
					continue;
				}

				if (in_array($eventId, $autoRegisteredEventIds))
				{
					continue;
				}

				$autoRegisteredEventIds[] = $eventId;

				$groupId = $this->registerToNewEvent($row->id, $eventId);

				// Insert group members records
				if (!empty($groupMemberIds))
				{
					foreach ($groupMemberIds as $groupMemberId)
					{
						$this->registerToNewEvent($groupMemberId, $eventId, $groupId);
					}
				}

				if ($this->params->get('send_email', 0))
				{
					$rowRegistrant = new EventbookingTableRegistrant($this->db);
					$rowRegistrant->load($groupId);
					EventbookingHelper::callOverridableHelperMethod('Mail', 'sendEmails', [$rowRegistrant, $config]);
				}
			}
		}
	}

	/**
	 * Create a registration record for an event base on existing registration record data
	 *
	 * @param   int  $registrantId
	 * @param   int  $eventId
	 * @param   int  $groupId
	 *
	 * @return int
	 */
	private function registerToNewEvent($registrantId, $eventId, $groupId = 0)
	{
		$db  = $this->db;
		$now = Factory::getDate()->toSql();

		$rowRegistrant = new EventbookingTableRegistrant($this->db);

		// Store main record data
		$rowRegistrant->load($registrantId);
		$rowRegistrant->id                     = 0;
		$rowRegistrant->event_id               = $eventId;
		$rowRegistrant->group_id               = $groupId;
		$rowRegistrant->register_date          = $now;
		$rowRegistrant->total_amount           = 0;
		$rowRegistrant->discount_amount        = 0;
		$rowRegistrant->late_fee               = 0;
		$rowRegistrant->tax_amount             = 0;
		$rowRegistrant->amount                 = 0;
		$rowRegistrant->deposit_amount         = 0;
		$rowRegistrant->payment_processing_fee = 0;
		$rowRegistrant->coupon_discount_amount = 0;
		$rowRegistrant->registration_code      = EventbookingHelperRegistration::getRegistrationCode();
		$rowRegistrant->ticket_qrcode          = EventbookingHelperRegistration::getTicketCode();

		if ($rowRegistrant->published == 0)
		{
			if (str_contains($rowRegistrant->payment_method, 'os_offline'))
			{
				$method = EventbookingHelperPayments::loadPaymentMethod($rowRegistrant->payment_method);

				if ($method)
				{
					$params = new Registry($method->params);

					if ($params->get('published'))
					{
						$rowRegistrant->published = 1;
					}
				}
			}

			if ($rowRegistrant->published == 0)
			{
				$rowRegistrant->payment_method = 'os_offline';
			}
		}

		$rowRegistrant->store();

		// Store custom field data
		$newRegistrantId = $rowRegistrant->id;

		$sql = 'INSERT INTO #__eb_field_values (registrant_id, field_id, field_value)'
			. " SELECT $newRegistrantId, field_id, field_value FROM #__eb_field_values WHERE registrant_id = $registrantId";

		$db->setQuery($sql)
			->execute();

		// Trigger event
		$app = $this->app;

		$eventObj = new AfterStoreRegistrant(
			'onAfterStoreRegistrant',
			['row' => $rowRegistrant]
		);

		$app->triggerEvent('onAfterStoreRegistrant', $eventObj);

		if ($rowRegistrant->published == 1)
		{
			$eventObj = new AfterPaymentSuccess(
				'onAfterPaymentSuccess',
				['row' => $rowRegistrant]
			);

			$app->triggerEvent('onAfterPaymentSuccess', $eventObj);
		}

		return $newRegistrantId;
	}

	/**
	 * Display form allows users to change settings on subscription plan add/edit screen
	 *
	 * @param   EventbookingTableEvent  $row
	 *
	 * @return void
	 */
	private function drawSettingForm($row): void
	{
		$params                        = new Registry($row->params);
		$eventIds                      = $params->get('auto_register_event_ids');
		$autoRegisterAllChildrenEvents = $params->get('auto_register_all_children_events', 0);

		require PluginHelper::getLayoutPath($this->_type, $this->_name, 'form');
	}

	/**
	 * Method to check to see whether the plugin should run
	 *
	 * @param   EventbookingTableEvent  $row
	 *
	 * @return bool
	 */
	private function canRun($row): bool
	{
		if ($this->app->isClient('site') && !$this->params->get('show_on_frontend'))
		{
			return false;
		}

		return true;
	}
}
