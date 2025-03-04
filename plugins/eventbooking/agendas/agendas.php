<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2025 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\Event;
use Joomla\Event\SubscriberInterface;

class plgEventBookingAgendas extends CMSPlugin implements SubscriberInterface
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
			'onAfterSaveEvent' => 'onAfterSaveEvent',
			'onEditEvent'      => 'onEditEvent',
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
			'title' => Text::_('EB_AGENDAS'),
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

		$agendas   = isset($data['agendas']) && is_array($data['agendas']) ? $data['agendas'] : [];
		$agendaIds = [];
		$ordering  = 1;

		foreach ($agendas as $agenda)
		{
			$rowAgenda = new EventbookingTableAgenda($this->db);
			$rowAgenda->bind($agenda);

			// Prevent agendas data being moved to new event on saveAsCopy
			if ($isNew)
			{
				$rowAgenda->id = 0;
			}

			$rowAgenda->event_id = $row->id;
			$rowAgenda->ordering = $ordering++;
			$rowAgenda->store();
			$agendaIds[] = $rowAgenda->id;
		}

		if (!$isNew)
		{
			$db    = $this->db;
			$query = $db->getQuery(true);
			$query->delete('#__eb_agendas')
				->where('event_id = ' . $row->id);

			if (count($agendaIds))
			{
				$query->whereNotIn('id', $agendaIds);
			}

			$db->setQuery($query)
				->execute();
		}
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
		$xml = simplexml_load_file(JPATH_ROOT . '/plugins/eventbooking/agendas/form/agenda.xml');

		if ($this->params->get('use_editor_for_description', 0))
		{
			foreach ($xml->field->form->children() as $field)
			{
				if ($field->attributes()->name == 'description')
				{
					$field->attributes()->type = 'editor';
				}
			}
		}

		$xml->field->attributes()->layout = $this->params->get('subform_layout', 'joomla.form.field.subform.repeatable-table');

		$form                = Form::getInstance('agendas', $xml->asXML());
		$formData['agendas'] = [];

		// Load existing speakers for this event
		if ($row->id)
		{
			$db    = $this->db;
			$query = $db->getQuery(true)
				->select('*')
				->from('#__eb_agendas')
				->where('event_id = ' . $row->id)
				->order('ordering');
			$db->setQuery($query);

			foreach ($db->loadObjectList() as $agenda)
			{
				$formData['agendas'][] = [
					'id'          => $agenda->id,
					'time'        => $agenda->time,
					'title'       => $agenda->title,
					'description' => $agenda->description,
				];
			}
		}

		$form->bind($formData);

		return EventbookingHelperHtml::loadCommonLayout('plugins/agendas_form.php', ['form' => $form]);
	}

	/**
	 * Display event's agendas
	 *
	 * @param   Event  $eventObj
	 *
	 * @return void
	 */
	public function onEventDisplay(Event $eventObj): void
	{
		/* @var EventbookingTableEvent $row */
		[$row] = array_values($eventObj->getArguments());

		if ($this->params->get('enable_setup_agendas_for_child_event'))
		{
			$eventId = $row->id;
		}
		else
		{
			$eventId = $row->parent_id ?: $row->id;
		}

		$db    = $this->db;
		$query = $db->getQuery(true)
			->select('*')
			->from('#__eb_agendas')
			->where('event_id = ' . $eventId)
			->order('ordering');

		if ($fieldSuffix = EventbookingHelper::getFieldSuffix())
		{
			EventbookingHelperDatabase::getMultilingualFieldsUseDefaultLanguageData($query, ['title', 'description'], $fieldSuffix);
		}

		$db->setQuery($query);
		$agendas = $db->loadObjectList();

		if (empty($agendas))
		{
			return;
		}

		$result = [
			'title'    => Text::_('EB_EVENT_AGENDAS'),
			'form'     => EventbookingHelperHtml::loadCommonLayout('plugins/agendas.php', ['agendas' => $agendas]),
			'position' => $this->params->get('output_position', 'before_register_buttons'),
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

		if ($row->parent_id > 0 && !$this->params->get('enable_setup_agendas_for_child_event'))
		{
			return false;
		}

		return true;
	}
}
