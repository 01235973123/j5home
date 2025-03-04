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
use Joomla\CMS\Language\Text;
use Joomla\CMS\Mail\MailHelper;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Database\ParameterType;
use Joomla\Event\Event;
use Joomla\Event\SubscriberInterface;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;

class plgEventBookingAcym extends CMSPlugin implements SubscriberInterface
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
	 * @return string[]
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			'onAfterPaymentSuccess'        => 'onAfterPaymentSuccess',
			'onAfterSaveEvent'             => 'onAfterSaveEvent',
			'onAfterStoreRegistrant'       => 'onAfterStoreRegistrant',
			'onEditEvent'                  => 'onEditEvent',
			'onGetNewsletterFields'        => 'onGetNewsletterFields',
			'onRegistrantMovingToNewEvent' => 'onRegistrantMovingToNewEvent',
			'onRegistrationCancelled'      => 'onRegistrationCancelled',
		];
	}

	/**
	 * Constructor.
	 *
	 * @param $subject
	 * @param $config
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		$this->app->getLanguage()->load('plg_eventbooking_acym', JPATH_ADMINISTRATOR);
	}

	/**
	 * Return list of custom fields in ACYMailing which will be used to map with fields in Events Booking
	 *
	 * @param   Event  $eventObj
	 *
	 * @return void
	 */
	public function onGetNewsletterFields(Event $eventObj): void
	{
		$db    = $this->db;
		$query = $db->getQuery(true)
			->select($db->quoteName(['name', 'name'], ['value', 'text']))
			->from('#__acym_field')
			->whereNotIn('name', ['ACYM_NAME', 'ACYM_EMAIL'], ParameterType::STRING);
		$db->setQuery($query);

		$this->addResult($eventObj, $db->loadObjectList());
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
			'title' => Text::_('PLG_EB_ACYM_LIST_SETTINGS'),
			'form'  => ob_get_clean(),
		];

		$this->addResult($eventObj, $result);
	}

	/**
	 * Store setting into database, in this case, use params field of plans table
	 *
	 * @param   Event  $eventObj
	 *
	 * @return void
	 */
	public function onAfterSaveEvent(Event $eventObj): void
	{
		/**
		 * @var EventbookingTableEvent $row
		 * @var array                  $data
		 * @var bool                   $isNew
		 */
		[$row, $data, $isNew] = array_values($eventObj->getArguments());

		if (!$this->canRun($row))
		{
			return;
		}

		$params = new Registry($row->params);

		$params->set('acymailing_list_ids', implode(',', $data['acymailing_list_ids'] ?? []));
		$row->params = $params->toString();

		$row->store();
	}

	/**
	 * Add registrants to ACYMailing when they register for event using offline payment
	 *
	 * @param   Event  $eventObj
	 *
	 * @return void
	 */
	public function onAfterStoreRegistrant(Event $eventObj): void
	{
		/* @var EventbookingTableRegistrant $row */
		[$row] = array_values($eventObj->getArguments());

		if (str_contains($row->payment_method, 'os_offline') && $row->published != 3)
		{
			$this->addRegistrantToAcyMailing($row);
		}
	}

	/**
	 * Add registrants to ACYMailing when payment for registration completed or registration is approved
	 *
	 * @param   Event  $eventObj
	 *
	 * @return void
	 */
	public function onAfterPaymentSuccess(Event $eventObj): void
	{
		/* @var EventbookingTableRegistrant $row */
		[$row] = array_values($eventObj->getArguments());

		// Exclude waiting list registrants from 
		if (!str_contains($row->payment_method, 'os_offline'))
		{
			$this->addRegistrantToAcyMailing($row);
		}
	}

	/**
	 * Move registrant out of mailing list when they are moving to new event
	 *
	 * @param   Event  $eventObj
	 *
	 * @return void
	 */
	public function onRegistrantMovingToNewEvent(Event $eventObj): void
	{
		$this->onRegistrationCancelled($eventObj);
	}

	/**
	 * Remove users from mailing lists if configured
	 *
	 * @param   Event  $eventObj
	 *
	 * @return void
	 */
	public function onRegistrationCancelled(Event $eventObj): void
	{
		/**
		 * @var EventbookingTableRegistrant $row
		 * @var int                         $published
		 */

		[$row, $published] = array_values($eventObj->getArguments());

		if (!$this->params->get('remove_from_lists_when_registration_cancelled'))
		{
			return;
		}

		// Get list ID
		$event = new EventbookingTableEvent($this->db);
		$event->load($row->event_id);
		$params  = new Registry($event->params);
		$listIds = $params->get('acymailing_list_ids', '');

		if (empty($listIds))
		{
			$listIds = $this->params->get('default_list_ids', '');
		}

		if (empty($listIds))
		{
			return;
		}

		$listIds = explode(',', $listIds);

		$listIds = array_filter(ArrayHelper::toInteger($listIds));

		if (empty($listIds))
		{
			return;
		}

		$this->removeFromMailingLists($row, $listIds);

		if ($row->is_group_billing && $this->params->get('add_group_members_to_newsletter'))
		{
			$db    = $this->db;
			$query = $db->getQuery(true)
				->select('*')
				->from('#__eb_registrants')
				->where('group_id = ' . (int) $row->id);
			$db->setQuery($query);
			$groupMembers = $db->loadObjectList();

			foreach ($groupMembers as $groupMember)
			{
				$this->removeFromMailingLists($groupMember, $listIds);
			}
		}
	}

	/**
	 * Add registrant to AcyMailing
	 *
	 * @param   EventbookingTableRegistrant  $row
	 *
	 * @return void
	 */
	private function addRegistrantToAcyMailing($row)
	{
		$config = EventbookingHelper::getConfig();

		// In case subscriber doesn't want to subscribe to newsleter, stop
		if ($config->show_subscribe_newsletter_checkbox && empty($row->subscribe_newsletter))
		{
			return;
		}

		$db    = $this->db;
		$query = $db->getQuery(true);

		// Only add subscribers to newsletter if they agree.
		if ($subscribeNewsletterField = $this->params->get('subscribe_newsletter_field'))
		{
			$query->select('name, fieldtype')
				->from('#__eb_fields')
				->where('id = ' . $db->quote((int) $subscribeNewsletterField));
			$db->setQuery($query);
			$field     = $db->loadObject();
			$fieldType = $field->fieldtype;
			$fieldName = $field->name;

			if ($fieldType == 'Checkboxes')
			{
				if (!isset($_POST[$fieldName]))
				{
					return;
				}
			}
			else
			{
				$fieldValue = strtolower($this->app->getInput()->getString($fieldName));

				if (empty($fieldValue) || $fieldValue == 'no' || $fieldValue == '0')
				{
					return;
				}
			}
		}

		$event = new EventbookingTableEvent($this->db);
		$event->load($row->event_id);
		$params  = new Registry($event->params);
		$listIds = $params->get('acymailing_list_ids', '');

		if (empty($listIds))
		{
			$listIds = $this->params->get('default_list_ids', '');
		}

		if ($listIds != '')
		{
			$listIds = explode(',', $listIds);

			$this->subscribeToAcyMailingLists($row, $listIds);

			if ($row->is_group_billing && $this->params->get('add_group_members_to_newsletter'))
			{
				$query->clear()
					->select('*')
					->from('#__eb_registrants')
					->where('group_id = ' . (int) $row->id);
				$db->setQuery($query);
				$groupMembers = $db->loadObjectList();

				foreach ($groupMembers as $groupMember)
				{
					$this->subscribeToAcyMailingLists($groupMember, $listIds);
				}
			}
		}
	}

	/**
	 * @param   EventbookingTableRegistrant  $row
	 * @param   array                        $listIds
	 */
	private function subscribeToAcyMailingLists($row, $listIds)
	{
		if (!MailHelper::isEmailAddress($row->email))
		{
			return;
		}

		require_once JPATH_ADMINISTRATOR . '/components/com_acym/helpers/helper.php';

		$db    = $this->db;
		$query = $db->getQuery(true);

		if (class_exists(\AcyMailing\Classes\UserClass::class))
		{
			$userClass = new \AcyMailing\Classes\UserClass();
		}
		else
		{
			/* @var acymUserClass $userClass */
			$userClass = acym_get('class.user');
		}

		$userClass->checkVisitor = false;

		if (method_exists($userClass, 'getOneByEmail'))
		{
			$subId = $userClass->getOneByEmail($row->email);
		}
		else
		{
			$subId = $userClass->getUserIdByEmail($row->email);
		}

		if (!$subId)
		{
			$myUser        = new stdClass();
			$myUser->email = $row->email;
			$myUser->name  = trim($row->first_name . ' ' . $row->last_name);

			if ($row->group_id == 0 || $this->params->get('connect_subscriber_to_joomla_user_account', '0') == 0)
			{
				$myUser->cms_id = $row->user_id;
			}

			$subId = $userClass->save($myUser);

			$config = EventbookingHelper::getConfig();

			if ($config->multiple_booking)
			{
				$rowFields = EventbookingHelperRegistration::getFormFields($row->id, 4);
			}
			elseif ($row->is_group_billing)
			{
				$rowFields = EventbookingHelperRegistration::getFormFields($row->event_id, 1);
			}
			else
			{
				$rowFields = EventbookingHelperRegistration::getFormFields($row->event_id, 0);
			}

			$data = EventbookingHelperRegistration::getRegistrantData($row, $rowFields);

			foreach ($rowFields as $rowField)
			{
				if (!$rowField->newsletter_field_mapping)
				{
					continue;
				}

				// Get ID of field
				$query->clear()
					->select('id')
					->from('#__acym_field')
					->where('name = ' . $db->quote($rowField->newsletter_field_mapping));
				$db->setQuery($query);
				$fieldId = $db->loadResult();

				if (!$fieldId)
				{
					continue;
				}

				$fieldValue = $data[$rowField->name] ?? '';

				$query->clear()
					->insert('#__acym_user_has_field')
					->columns($db->quoteName(['user_id', 'field_id', 'value']))
					->values(implode(',', $db->quote([$subId, $fieldId, $fieldValue])));

				try
				{
					$db->setQuery($query)
						->execute();
				}
				catch (Exception $e)
				{
					// Ignore the error for now
				}
			}
		}

		if (is_object($subId))
		{
			$subId = $subId->id;
		}

		try
		{
			$userClass->subscribe($subId, $listIds);
		}
		catch (Exception $e)
		{
			// Ignore error
		}
	}

	/**
	 * Method to remove registrants from the mailing lists
	 *
	 * @param   EventbookingTableRegistrant  $row
	 * @param   array                        $listIds
	 *
	 * @return void
	 */
	private function removeFromMailingLists($row, $listIds)
	{
		if (!MailHelper::isEmailAddress($row->email))
		{
			return;
		}

		require_once JPATH_ADMINISTRATOR . '/components/com_acym/helpers/helper.php';

		if (class_exists(\AcyMailing\Classes\UserClass::class))
		{
			$userClass = new \AcyMailing\Classes\UserClass();
		}
		else
		{
			/* @var acymUserClass $userClass */
			$userClass = acym_get('class.user');
		}

		$userClass->checkVisitor = false;

		if (method_exists($userClass, 'getOneByEmail'))
		{
			$subId = $userClass->getOneByEmail($row->email);
		}
		else
		{
			$subId = $userClass->getUserIdByEmail($row->email);
		}

		if (is_object($subId))
		{
			$subId = $subId->id;
		}

		if (count($listIds) > 0)
		{
			try
			{
				$userClass->unsubscribe($subId, $listIds);
			}
			catch (Exception $e)
			{
			}
		}
	}

	/**
	 * Display form allows users to change settings on subscription plan add/edit screen
	 *
	 * @param   object  $row
	 */
	private function drawSettingForm($row): void
	{
		require_once JPATH_ADMINISTRATOR . '/components/com_acym/helpers/helper.php';

		if ($row->id)
		{
			$params  = new Registry($row->params);
			$listIds = explode(',', $params->get('acymailing_list_ids', ''));
		}
		else
		{
			$listIds = [];
		}

		if (class_exists(\AcyMailing\Classes\ListClass::class))
		{
			$listClass = new \AcyMailing\Classes\ListClass();
		}
		else
		{
			/* @var acymlistClass $listClass */
			$listClass = acym_get('class.list');
		}

		$allLists = $listClass->getAllWithIdName();

		require PluginHelper::getLayoutPath($this->_type, $this->_name, 'form');
	}

	/**
	 * Override registerListeners method to only register listeners if needed
	 *
	 * @return void
	 */
	public function registerListeners()
	{
		if (!ComponentHelper::isEnabled('com_acym'))
		{
			return;
		}

		parent::registerListeners();
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
