<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2024 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Event\Event;
use Joomla\Event\SubscriberInterface;
use Joomla\Utilities\ArrayHelper;

class plgEventBookingSponsors extends CMSPlugin implements SubscriberInterface
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
			'onEventDisplay'   => 'onEventDisplay',
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

		$result = [
			'title' => Text::_('EB_SPONSORS'),
			'form'  => $this->drawSettingForm($row),
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

		$db    = $this->db;
		$query = $db->getQuery(true);

		$sponsors   = isset($data['sponsors']) && is_array($data['sponsors']) ? $data['sponsors'] : [];
		$sponsorIds = [];
		$ordering   = 1;

		foreach ($sponsors as $sponsor)
		{
			$rowSponsor = new EventbookingTableSponsor($this->db);
			$rowSponsor->bind($sponsor);

			// Prevent sponsor data being moved to new event on saveAsCopy
			if ($isNew)
			{
				$rowSponsor->id = 0;
			}

			$rowSponsor->event_id = $row->id;
			$rowSponsor->ordering = $ordering++;
			$rowSponsor->store();
			$sponsorIds[] = $rowSponsor->id;
		}

		if (!$isNew)
		{
			$query->delete('#__eb_sponsors')
				->where('event_id = ' . $row->id);

			if (count($sponsorIds))
			{
				$query->whereNotIn('id', $sponsorIds);
			}

			$db->setQuery($query)
				->execute();

			$query->clear()
				->delete('#__eb_event_sponsors')
				->where('event_id = ' . $row->id);
			$db->setQuery($query);
			$db->execute();
		}

		if (!empty($data['existing_sponsor_ids']))
		{
			$sponsorIds = array_filter(ArrayHelper::toInteger($data['existing_sponsor_ids']));

			if (count($sponsorIds))
			{
				$query->clear()
					->insert('#__eb_event_sponsors')
					->columns($db->quoteName(['event_id', 'sponsor_id']));

				foreach ($sponsorIds as $sponsorId)
				{
					$query->values(implode(',', [$row->id, $sponsorId]));
				}

				$db->setQuery($query)
					->execute();
			}
		}

		// Insert event speakers into #__eb_event_sponsors table
		$sql = 'INSERT INTO #__eb_event_sponsors(event_id, sponsor_id) SELECT event_id, id FROM #__eb_sponsors WHERE event_id = ' . $row->id . ' ORDER BY ordering';
		$db->setQuery($sql)
			->execute();
	}

	/**
	 * Display form allows users to change settings on subscription plan add/edit screen
	 *
	 * @param   EventbookingTableEvent  $row
	 *
	 * @return string
	 */
	private function drawSettingForm($row)
	{
		$xml                              = simplexml_load_file(JPATH_ROOT . '/plugins/eventbooking/sponsors/form/sponsor.xml');
		$xml->field->attributes()->layout = $this->params->get('subform_layout', 'joomla.form.field.subform.repeatable-table');

		$form                 = Form::getInstance('sponsors', $xml->asXML());
		$formData['sponsors'] = [];
		$selectedSponsorIds   = [];

		$db    = $this->db;
		$query = $db->getQuery(true);

		// Load existing sponsors for this event
		if ($row->id)
		{
			$query->select('*')
				->from('#__eb_sponsors')
				->where('event_id = ' . $row->id)
				->order('ordering');
			$db->setQuery($query);

			foreach ($db->loadObjectList() as $speaker)
			{
				$formData['sponsors'][] = [
					'id'      => $speaker->id,
					'name'    => $speaker->name,
					'logo'    => $speaker->logo,
					'website' => $speaker->website,
				];
			}

			$query->clear()
				->select('sponsor_id')
				->from('#__eb_event_sponsors')
				->where('event_id = ' . (int) $row->id);
			$db->setQuery($query);
			$selectedSponsorIds = $db->loadColumn();
		}

		// Get existing sponsors from other events for selection
		$query->clear()
			->select('id, name')
			->from('#__eb_sponsors')
			->order('ordering');

		if ($row->id)
		{
			$query->where('event_id != ' . $row->id);
		}

		$db->setQuery($query);
		$existingSponsors = $db->loadObjectList();

		// Trigger content plugin
		PluginHelper::importPlugin('content');
		$this->app->triggerEvent('onContentPrepareForm', [$form, $formData]);

		$form->bind($formData);

		$layoutData = [
			'existingSponsors'   => $existingSponsors,
			'selectedSponsorIds' => $selectedSponsorIds,
			'form'               => $form,
		];

		return EventbookingHelperHtml::loadCommonLayout('plugins/sponsors_form.php', $layoutData);
	}

	/**
	 * Display event sponsors
	 *
	 * @param   Event  $eventObj
	 *
	 * @return void
	 */
	public function onEventDisplay(Event $eventObj): void
	{
		/* @var EventbookingTableEvent $row */
		[$row] = array_values($eventObj->getArguments());

		$eventId = $row->parent_id ?: $row->id;
		$db      = $this->db;
		$query   = $db->getQuery(true)
			->select('*')
			->from('#__eb_sponsors AS a')
			->innerJoin('#__eb_event_sponsors AS b ON a.id = b.sponsor_id')
			->where('b.event_id = ' . $eventId);

		if ($this->params->get('order_sponsors_by_name'))
		{
			$query->order('a.name');
		}
		else
		{
			$query->order('b.id');
		}

		$db->setQuery($query);
		$sponsors = $db->loadObjectList();

		if (empty($sponsors))
		{
			return;
		}

		$layoutFile = $this->params->get('layout', 'sponsors') . '.php';

		$result = [
			'title'    => Text::_('EB_EVENT_SPONSORS'),
			'form'     => EventbookingHelperHtml::loadCommonLayout('plugins/' . $layoutFile, ['sponsors' => $sponsors, 'params' => $this->params]),
			'position' => $this->params->get('output_position', 'before_register_buttons'),
			'name'     => $this->_name,
		];

		$this->addResult($eventObj, $result);
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
