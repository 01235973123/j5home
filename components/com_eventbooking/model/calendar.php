<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2024 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;
use OSSolution\EventBooking\Admin\Event\Events\AfterReturnEventsFromDatabase;

class EventbookingModelCalendar extends EventbookingModelCommoncalendar
{
	protected $currentDate;

	/**
	 * Instantiate the model.
	 *
	 * @param   array  $config  configuration data for the model
	 */

	public function __construct($config = [])
	{
		parent::__construct($config);

		$date = Factory::getDate('now', Factory::getApplication()->get('offset'));

		$this->state->insert('year', 'int', $this->params->get('default_year', $date->format('Y')))
			->insert('month', 'int', $this->params->get('default_month', $date->format('m')))
			->insert('date', 'string', '')
			->insert('day', 'string', '')
			->insert('id', 'int', 0)
			->insert('mini_calendar', 'int', 0)
			->insert('mini_calendar_item_id', 'int', 0);

		if (File::exists(__DIR__ . '/custom_event_fields.php'))
		{
			$fields = require __DIR__ . '/custom_event_fields.php';

			foreach ($fields as $field)
			{
				static::$fields[] = 'a.' . $field;
			}
		}
	}

	/**
	 * Get monthly events
	 *
	 * @return array|mixed
	 */
	public function getData()
	{
		PluginHelper::importPlugin('eventbooking');

		$app     = Factory::getApplication();
		$db      = $this->getDbo();
		$config  = EventbookingHelper::getConfig();
		$date    = Factory::getDate('now', $app->get('offset'));
		$nowDate = $date->format('d');
		$year    = $this->state->get('year') ?: $date->format('Y');
		$month   = $this->state->get('month') ?: $date->format('m');

		$this->state->set('month', $month)
			->set('year', $year);

		$this->currentDate = static::getCurrentDateData($year . '-' . $month . '-' . $nowDate);

		// Calculate start date and end date of the given month
		$date->setDate($year, $month, 1);
		$date->setTime(0, 0, 0);
		$startDate = $db->quote($date->toSql(true));

		$date->setDate($year, $month, $date->daysinmonth);
		$date->setTime(23, 59, 59);
		$endDate = $db->quote($date->toSql(true));

		$query = $this->buildQuery();

		if ($config->show_multiple_days_event_in_calendar && !$this->state->mini_calendar)
		{
			$query->where(
				"((a.event_date BETWEEN $startDate AND $endDate) OR (a.event_end_date BETWEEN $startDate AND $endDate) OR (a.event_date <= $startDate AND a.event_end_date >= $endDate))"
			);
		}
		else
		{
			$query->where("a.event_date BETWEEN $startDate AND $endDate");
		}

		$db->setQuery($query);

		if ($config->show_multiple_days_event_in_calendar && !$this->state->mini_calendar)
		{
			$rows = $db->loadObjectList();

			$eventObj = new AfterReturnEventsFromDatabase(['rows' => $rows, 'context' => 'calendar']);

			$app->triggerEvent($eventObj->getName(), $eventObj);

			$rowEvents = [];

			foreach ($rows as $row)
			{
				$arrDates = explode('-', $row->event_date);

				if ($arrDates[0] == $year && $arrDates[1] == $month)
				{
					$rowEvents[] = $row;
				}

				$startDateParts = explode(' ', $row->event_date);
				$startTime      = strtotime($startDateParts[0]);
				$startDateTime  = strtotime($row->event_date);
				$endDateParts   = explode(' ', $row->event_end_date);
				$endTime        = strtotime($endDateParts[0]);
				$count          = 0;

				while ($startTime < $endTime)
				{
					$count++;
					$rowNew             = clone $row;
					$rowNew->event_date = date('Y-m-d H:i:s', $startDateTime + $count * 24 * 3600);
					$arrDates           = explode('-', $rowNew->event_date);

					if ($arrDates[0] == $year && $arrDates[1] == $month)
					{
						$rowEvents[]            = $rowNew;
						$rowNew->original_event = $row;
					}

					$startTime += 24 * 3600;
				}
			}

			return $rowEvents;
		}

		$rows = $db->loadObjectList();

		$eventObj = new AfterReturnEventsFromDatabase(['rows' => $rows, 'context' => 'calendar']);

		$app->triggerEvent($eventObj->getName(), $eventObj);

		return $rows;
	}

	/**
	 * Get events of the given week
	 *
	 * @return array
	 */
	public function getEventsByWeek()
	{
		$db       = $this->getDbo();
		$query    = $this->buildQuery();
		$config   = EventbookingHelper::getConfig();
		$startDay = (int) $config->calendar_start_date;

		$locationIds = $this->params->get('location_ids', []);
		$locationIds = array_filter(ArrayHelper::toInteger($locationIds));

		// get first day of week of today
		$startWeekDate = $this->state->date ?: $this->params->get('default_start_date', '');

		if (!EventbookingHelper::isValidDate($startWeekDate))
		{
			$startWeekDate = '';
		}

		if ($startWeekDate)
		{
			$date = Factory::getDate($startWeekDate, Factory::getApplication()->get('offset'));
		}
		else
		{
			$currentDateData = self::getCurrentDateData();
			$date            = Factory::getDate($currentDateData['start_week_date'], Factory::getApplication()->get('offset'));
		}

		$this->state->set('date', $date->format('Y-m-d', true));

		$weekStartDate = Factory::getDate($this->state->date, Factory::getApplication()->get('offset'));
		$activeDate    = Factory::getDate('now', Factory::getApplication()->get('offset'));
		$activeDate->setDate($weekStartDate->format('Y'), $weekStartDate->format('m'), $activeDate->format('d'));

		$this->currentDate = static::getCurrentDateData($activeDate->format('Y-m-d'));

		$date->setTime(0, 0, 0);
		$startDate = $db->quote($date->toSql(true));
		$date->modify('+6 day');
		$date->setTime(23, 59, 59);
		$endDate = $db->quote($date->toSql(true));
		$query->where("(a.event_date BETWEEN $startDate AND $endDate)")
			->order('a.event_date ASC, a.ordering ASC');

		if ($locationIds)
		{
			$query->whereIn('a.location_id', $locationIds);
		}

		$db->setQuery($query);
		$rows = $db->loadObjectList();

		$this->replaceEventCustomFields($rows);

		$eventsGroupedByWeekDay = [];

		foreach ($rows as $row)
		{
			$row->short_description             = HTMLHelper::_('content.prepare', $row->short_description);
			$weekDay                            = (date('w', strtotime($row->event_date)) - $startDay + 7) % 7;
			$eventsGroupedByWeekDay[$weekDay][] = $row;
		}

		return $eventsGroupedByWeekDay;
	}

	/**
	 * Get events of the given date
	 *
	 * @return mixed
	 */
	public function getEventsByDaily()
	{
		$db    = $this->getDbo();
		$query = $this->buildQuery();

		$day = $this->state->day;

		if (!EventbookingHelper::isValidDate($day))
		{
			$day = '';
		}

		if (!$day)
		{
			if (!$this->params->get('default_daily_start_date'))
			{
				$currentDateData = self::getCurrentDateData();
				$day             = $currentDateData['current_date'];
			}
			else
			{
				$day = $this->params->get('default_daily_start_date');
			}

			$this->state->set('day', $day);
		}

		$this->currentDate = static::getCurrentDateData($this->state->day);

		$startDate = $db->quote($day . ' 00:00:00');
		$endDate   = $db->quote($day . ' 23:59:59');

		$query->where("(a.event_date BETWEEN $startDate AND $endDate)")
			->order('a.event_date ASC, a.ordering ASC');

		$db->setQuery($query);
		$rows = $db->loadObjectList();

		foreach ($rows as $row)
		{
			$row->short_description = HTMLHelper::_('content.prepare', $row->short_description);
		}

		$this->replaceEventCustomFields($rows);

		return $rows;
	}

	/**
	 * Get calculated data for current date
	 *
	 * @return mixed
	 */
	public function getCurrentDate()
	{
		return $this->currentDate;
	}

	/**
	 * Get data of current date
	 *
	 * @return array
	 */
	public static function getCurrentDateData($currentDate = 'now')
	{
		$config               = EventbookingHelper::getConfig();
		$startDay             = (int) $config->calendar_start_date;
		$data                 = [];
		$date                 = new DateTime($currentDate, new DateTimeZone(Factory::getApplication()->get('offset')));
		$data['year']         = $date->format('Y');
		$data['month']        = $date->format('m');
		$data['current_date'] = $date->format('Y-m-d');

		if ($startDay == 0)
		{
			$date->modify('Sunday last week');
		}
		else
		{
			$date->modify(('Sunday' == $date->format('l')) ? 'Monday last week' : 'Monday this week');
		}

		$data['start_week_date'] = $date->format('Y-m-d');
		$data['end_week_date']   = $date->modify('+6 day')->format('Y-m-d');

		return $data;
	}

	/**
	 * Replace event custom fields tag in short_description of each event
	 *
	 * @param   array  $rows
	 */
	protected function replaceEventCustomFields($rows)
	{
		if (!file_exists(JPATH_ROOT . '/components/com_eventbooking/fields.xml'))
		{
			return;
		}

		$fields = EventbookingHelper::getEventCustomFields();

		foreach ($rows as $row)
		{
			$fieldsValue = new Registry($row->custom_fields);

			foreach ($fields as $field)
			{
				$fieldValue = $fieldsValue->get($field, '');

				if (!is_scalar($fieldValue))
				{
					continue;
				}

				$row->short_description = str_replace('[' . strtoupper($field) . ']', (string) $fieldValue, $row->short_description);
			}
		}
	}
}
