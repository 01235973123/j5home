<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2025 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Form\Form;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Event\Event;
use Joomla\Event\SubscriberInterface;
use Joomla\Registry\Registry;

class plgEventBookingDates extends CMSPlugin implements SubscriberInterface
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
			'onEditEvent'      => 'onEditEvent',
			'onAfterSaveEvent' => 'onAfterSaveEvent',
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
			'title' => Text::_('EB_ADDITIONAL_DATES'),
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

		if (!$this->canRun($row))
		{
			return;
		}

		/* @var EventbookingModelEvent $eventModel */
		$eventModel = RADModel::getTempInstance('Event', 'EventbookingModel');

		$db    = $this->db;
		$query = $db->getQuery(true);

		$config     = EventbookingHelper::getConfig();
		$dateFormat = str_replace('%', '', $config->get('date_field_format', '%Y-%m-%d'));
		$dates      = isset($data['dates']) && is_array($data['dates']) ? $data['dates'] : [];

		$additionalEventIds   = [];
		$numberChildrenEvents = 0;

		foreach ($dates as $date)
		{
			if (empty($date['event_date']) || str_contains($date['event_date'], '0000'))
			{
				continue;
			}

			// Convert date data to Y-m-d H:i:s format
			$dateFields = [
				'event_date',
				'event_end_date',
				'registration_start_date',
				'cut_off_date',
			];

			foreach ($dateFields as $field)
			{
				if ($date[$field] && !str_contains($date[$field], '0000'))
				{
					$datetime = DateTime::createFromFormat($dateFormat . ' H:i', $date[$field]);

					if ($datetime !== false)
					{
						$date[$field] = $datetime->format('Y-m-d H:i:s');
					}
				}
			}

			$id = $date['id'] ?? 0;

			if ($isNew)
			{
				$id         = 0;
				$date['id'] = 0;
			}

			if ($id > 0)
			{
				$rowEvent = new EventbookingTableEvent($this->db);
				$rowEvent->load($id);

				if ($rowEvent->id)
				{
					$query->clear()
						->select('COUNT(*)')
						->from('#__eb_events')
						->where('`alias`  = ' . $db->quote($rowEvent->alias))
						->where('id != ' . $rowEvent->id);
					$db->setQuery($query);
					$total = $db->loadResult();

					if ($total)
					{
						$rowEvent->alias = ApplicationHelper::stringURLSafe(
							$rowEvent->id . '-' . $rowEvent->title . '-' . HTMLHelper::_('date', $rowEvent->event_date, $config->date_format, null)
						);
					}
				}
			}
			else
			{
				$rowEvent     = clone $row;
				$rowEvent->id = 0;
			}

			$rowEvent->bind($date);
			$rowEvent->parent_id          = $row->id;
			$rowEvent->event_type         = 2;
			$rowEvent->is_additional_date = 1;

			if (!$rowEvent->id)
			{
				$rowEvent->alias = ApplicationHelper::stringURLSafe(
					$rowEvent->title . '-' . HTMLHelper::_('date', $rowEvent->event_date, $config->date_format, null)
				);
				$rowEvent->hits  = 0;

				if ($row->early_bird_discount_amount > 0 && (int) $row->early_bird_discount_date > 0)
				{
					// Calculate early bird discount date for child event base on parent event data
					$earlyBirdDuration                  = abs(strtotime($row->early_bird_discount_date) - strtotime($row->event_date));
					$rowEvent->early_bird_discount_date = strftime('%Y-%m-%d %H:%M:%S', strtotime($rowEvent->event_date) - $earlyBirdDuration);
				}
			}
			elseif (isset($data['update_data_from_main_event']))
			{
				$fieldsToUpdate = EventbookingHelper::callOverridableHelperMethod('Helper', 'getEventFieldsToUpdate');

				foreach ($fieldsToUpdate as $field)
				{
					$rowEvent->$field = $row->$field;
				}

				if ($row->early_bird_discount_amount > 0 && (int) $row->early_bird_discount_date > 0)
				{
					// Calculate early bird discount date for child event base on parent event data
					$earlyBirdDuration                  = abs(strtotime($row->early_bird_discount_date) - strtotime($row->event_date));
					$rowEvent->early_bird_discount_date = strftime('%Y-%m-%d %H:%M:%S', strtotime($rowEvent->event_date) - $earlyBirdDuration);
				}
			}

			$rowEvent->store();

			$numberChildrenEvents++;

			if ($id == 0)
			{
				$isChildEventNew = true;
			}
			else
			{
				$isChildEventNew = false;
			}

			if ($isChildEventNew || isset($data['update_data_from_main_event']))
			{
				// Store categories
				$eventModel->storeEventCategories($rowEvent->id, $data, $isChildEventNew);

				// Store group registration rate
				$eventModel->storeEventGroupRegistrationRates($rowEvent->id, $data, $isChildEventNew);
			}

			$additionalEventIds[] = $rowEvent->id;
		}

		if ($numberChildrenEvents)
		{
			$row->event_type = 1;
		}
		elseif (!$isNew)
		{
			$db    = $this->db;
			$query = $db->getQuery(true)
				->select('COUNT(*)')
				->from('#__eb_events')
				->where('parent_id = ' . (int) $row->id);
			$db->setQuery($query);
			$total = (int) $db->loadResult();

			if ($total > 0)
			{
				$row->event_type = 1;
			}
			else
			{
				$row->event_type   = 0;
				$row->max_end_date = $db->getNullDate();
			}
		}

		$row->store();

		// Remove the events which are removed by users
		if (!$isNew)
		{
			$db    = $this->db;
			$query = $db->getQuery(true)
				->select('id')
				->from('#__eb_events')
				->where('parent_id = ' . $row->id)
				->where('is_additional_date = 1');
			$db->setQuery($query);
			$allChildrenEventIds = $db->loadColumn();

			if (count($allChildrenEventIds))
			{
				$deletedEventIds = array_diff($allChildrenEventIds, $additionalEventIds);

				if (count($deletedEventIds))
				{
					$eventModel->delete($deletedEventIds);
				}
			}
		}

		if ($numberChildrenEvents)
		{
			$row->max_end_date = EventbookingHelper::updateParentMaxEventDate($row->id);
		}

		// Store status of update data from main event checkbox
		if ($isNew)
		{
			$updateDataFromMainEvent = $this->params->get('default_update_data_from_main_event_checkbox_status', 1);
		}
		elseif (isset($data['update_data_from_main_event']))
		{
			$updateDataFromMainEvent = 1;
		}
		else
		{
			$updateDataFromMainEvent = 0;
		}

		$params = new Registry($row->params);
		$params->set('update_data_from_main_event', $updateDataFromMainEvent);
		$row->params = $params->toString();
		$row->store();
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
		$form              = Form::getInstance('dates', $this->getFormXML($row));
		$db                = $this->db;
		$query             = $db->getQuery(true);
		$rowEvents         = [];
		$formData['dates'] = [];

		if ($row->id > 0)
		{
			$query->select('id, event_date, event_end_date, cut_off_date, registration_start_date, location_id, event_capacity')
				->from('#__eb_events')
				->where('parent_id = ' . (int) $row->id)
				->where('is_additional_date = 1')
				->order('id');
			$db->setQuery($query);
			$rowEvents = $db->loadObjectList();
		}
		else
		{
			for ($i = 0; $i < $this->params->get('max_number_dates', 3); $i++)
			{
				$rowEvent                          = new stdClass();
				$rowEvent->id                      = 0;
				$rowEvent->event_date              = null;
				$rowEvent->event_end_date          = null;
				$rowEvent->cut_off_date            = null;
				$rowEvent->registration_start_date = null;
				$rowEvent->location_id             = $row->location_id;
				$rowEvent->event_capacity          = $row->event_capacity;
				$rowEvents[]                       = $rowEvent;
			}
		}

		foreach ($rowEvents as $rowEvent)
		{
			$formData['dates'][] = [
				'id'                      => $rowEvent->id,
				'event_date'              => $rowEvent->event_date,
				'event_end_date'          => $rowEvent->event_end_date,
				'cut_off_date'            => $rowEvent->cut_off_date,
				'registration_start_date' => $rowEvent->registration_start_date,
				'location_id'             => $rowEvent->location_id,
				'event_capacity'          => $rowEvent->event_capacity,
			];
		}

		$form->bind($formData);

		require PluginHelper::getLayoutPath($this->_type, $this->_name, 'form');
	}

	/**
	 * Method to get form xml definition. Change some field attributes base on Events Booking config and the event
	 * is being edited
	 *
	 * @param   EventbookingTableEvent  $row
	 *
	 * @return string
	 */
	private function getFormXML($row)
	{
		$config = EventbookingHelper::getConfig();
		// Set some default value for form xml base on component settings
		$xml = simplexml_load_file(JPATH_ROOT . '/plugins/eventbooking/dates/form/dates.xml');

		$xml->field->attributes()->layout = $this->params->get('subform_layout', 'joomla.form.field.subform.repeatable-table');

		if ($this->app->isClient('site'))
		{
			// Remove fields which are disabled on submit event form
			$removeFields = [];

			if (!$config->get('fes_show_event_end_date', 1))
			{
				$removeFields[] = 'event_end_date';
			}

			if (!$config->get('fes_show_registration_start_date', 1))
			{
				$removeFields[] = 'registration_start_date';
			}

			if (!$config->get('fes_show_cut_off_date', 1))
			{
				$removeFields[] = 'cut_off_date';
			}

			if (!$config->get('fes_show_capacity', 1))
			{
				$removeFields[] = 'event_capacity';
			}

			foreach ($removeFields as $fieldName)
			{
				$xpathQuery = "//field[@name='$fieldName']";
				$nodes      = $xml->xpath($xpathQuery);

				foreach ($nodes as $node)
				{
					$dom = dom_import_simplexml($node);
					$dom->parentNode->removeChild($dom);
				}
			}
		}

		$datePickerFormat = $config->get('date_field_format', '%Y-%m-%d') . ' %H:%M';
		$filterFormat     = $config->date_field_format ?: '%Y-%m-%d';
		$filterFormat     = str_replace('%', '', $filterFormat) . ' H:i';

		foreach ($xml->field->form->children() as $field)
		{
			if ($field->getName() != 'field')
			{
				continue;
			}

			if ($field['type'] == 'calendar')
			{
				$field['format']       = $datePickerFormat;
				$field['filterformat'] = $filterFormat;
			}

			if ($row->id > 0)
			{
				if ($field['name'] == 'location_id')
				{
					$field['default'] = $row->location_id;
				}

				if ($field['name'] == 'event_capacity')
				{
					$field['default'] = $row->event_capacity;
				}
			}
		}

		return $xml->asXML();
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

		if ($row->parent_id > 0)
		{
			return false;
		}

		return true;
	}
}
