<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2025 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\Database\DatabaseQuery;

class EventbookingModelUpcomingevents extends EventbookingModelList
{
	/**
	 * Pre-process data before returning to the view for displaying
	 *
	 * @param   array  $rows
	 */
	protected function beforeReturnData($rows)
	{
		parent::beforeReturnData($rows);

		// Do not process if children events are configured to be displayed
		if (!$this->params->get('hide_children_events'))
		{
			return;
		}

		foreach ($rows as $row)
		{
			if ($row->event_type != 1 || $row->event_start_minutes < 0)
			{
				continue;
			}

			$rowNextUpcomingEvent = EventbookingHelper::getNextChildEvent($row->id);

			if ($rowNextUpcomingEvent)
			{
				foreach (['event_date', 'event_end_date'] as $field)
				{
					$row->{$field} = $rowNextUpcomingEvent->{$field};
				}
			}
		}
	}

	/**
	 * Builds SELECT columns list for the query
	 *
	 * @param   DatabaseQuery  $query
	 *
	 * @return $this
	 */
	protected function buildQueryColumns(DatabaseQuery $query)
	{
		if ($this->params->get('hide_children_events'))
		{
			$currentDate = $this->getDbo()->quote(EventbookingHelper::getServerTimeFromGMTTime());
			$query->select(
				"CASE WHEN (tbl.event_type = 1 AND TIMESTAMPDIFF(MINUTE, tbl.event_date, $currentDate) > 0) THEN (SELECT MIN(event_date) AS next_event_date FROM #__eb_events WHERE published = 1 AND event_date >= $currentDate AND (parent_id = tbl.id OR id = tbl.id)) ELSE tbl.event_date END AS next_event_date"
			);
		}

		return parent::buildQueryColumns($query);
	}

	/**
	 * Builds a WHERE clause for the query
	 *
	 * @param   DatabaseQuery  $query
	 *
	 * @return $this
	 */
	protected function buildQueryWhere(DatabaseQuery $query)
	{
		parent::buildQueryWhere($query);

		$this->applyHidePastEventsFilter($query);

		return $this;
	}

	/**
	 * Builds a generic ORDER BY clause. For upcoming event, event is already ordered by event_date ASC direction
	 *
	 * @param   DatabaseQuery  $query
	 *
	 * @return $this
	 */
	protected function buildQueryOrder(DatabaseQuery $query)
	{
		$config = EventbookingHelper::getConfig();

		// Display featured events at the top if configured
		if ($config->display_featured_events_on_top)
		{
			$query->order('tbl.featured DESC');
		}

		if ($this->params->get('menu_filter_order', 'tbl.event_date') === 'tbl.event_date')
		{
			if ($this->params->get('hide_children_events'))
			{
				$query->order('next_event_date');
			}
			else
			{
				$query->order('tbl.event_date');
			}
		}
		else
		{
			$query->order($this->params->get('menu_filter_order', 'tbl.event_date'));
		}

		return $this;
	}
}
