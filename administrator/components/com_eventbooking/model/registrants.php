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
use Joomla\Database\DatabaseQuery;

class EventbookingModelRegistrants extends EventbookingModelCommonRegistrants
{
	protected $choicesStates = ['filter_category_id', 'filter_event_id', 'filter_ticket_type_id'];

	protected function buildQueryWhere(DatabaseQuery $query)
	{
		$config = EventbookingHelper::getConfig();

		if (!$config->show_pending_registrants)
		{
			$query->where('(tbl.published >= 1 OR tbl.payment_method LIKE "os_offline%")');
		}

		switch ($this->state->filter_registrants_type)
		{
			case 0:
				// All registration records
				break;
			case 1:
				// Only billing record
				$query->where(' tbl.group_id = 0 ');
				break;
			case 2:
				// Only group members record
				$query->where('tbl.is_group_billing = 0');
				break;
			default:
				// Depend on configuration
				if (!$config->get('include_group_billing_in_registrants', 1)
					&& $config->include_group_members_in_registrants)
				{
					$query->where(' tbl.is_group_billing = 0 ');
				}

				if (!$config->include_group_members_in_registrants)
				{
					$query->where(' tbl.group_id = 0 ');
				}
		}

		return parent::buildQueryWhere($query);
	}

	/**
	 * Get statistic data
	 *
	 * @return array
	 */
	public static function getStatisticsData()
	{
		$data = [];

		/* @var \Joomla\Database\DatabaseDriver $db */
		$db    = Factory::getContainer()->get('db');
		$query = $db->getQuery(true)
			->select('SUM(number_registrants) AS total_registrants, SUM(amount) AS total_amount')
			->from('#__eb_registrants');

		// Today
		[$fromDate, $toDate] = EventbookingHelper::getDateDuration('today');

		$query->where('group_id = 0')
			->where('(published = 1 OR (payment_method LIKE "os_offline%" AND published = 0))')
			->where('register_date >= ' . $db->quote($fromDate))
			->where('register_date <=' . $db->quote($toDate));
		$db->setQuery($query);
		$row = $db->loadObject();

		$data['today'] = [
			'total_registrants' => (int) $row->total_registrants,
			'total_amount'      => floatval($row->total_amount),
		];

		// Yesterday
		[$fromDate, $toDate] = EventbookingHelper::getDateDuration('yesterday');

		$query->clear('where')
			->where('group_id = 0')
			->where('(published = 1 OR (payment_method LIKE "os_offline%" AND published = 0))')
			->where('register_date >= ' . $db->quote($fromDate))
			->where('register_date <=' . $db->quote($toDate));
		$db->setQuery($query);
		$row = $db->loadObject();

		$data['yesterday'] = [
			'total_registrants' => (int) $row->total_registrants,
			'total_amount'      => floatval($row->total_amount),
		];

		// This week
		[$fromDate, $toDate] = EventbookingHelper::getDateDuration('this_week');

		$query->clear('where')
			->where('group_id = 0')
			->where('(published = 1 OR (payment_method LIKE "os_offline%" AND published = 0))')
			->where('register_date >= ' . $db->quote($fromDate))
			->where('register_date <=' . $db->quote($toDate));
		$db->setQuery($query);
		$row = $db->loadObject();

		$data['this_week'] = [
			'total_registrants' => (int) $row->total_registrants,
			'total_amount'      => floatval($row->total_amount),
		];

		// Last week
		[$fromDate, $toDate] = EventbookingHelper::getDateDuration('last_week');

		$query->clear('where')
			->where('group_id = 0')
			->where('(published = 1 OR (payment_method LIKE "os_offline%" AND published = 0))')
			->where('register_date >= ' . $db->quote($fromDate))
			->where('register_date <=' . $db->quote($toDate));
		$db->setQuery($query);
		$row = $db->loadObject();

		$data['last_week'] = [
			'total_registrants' => (int) $row->total_registrants,
			'total_amount'      => floatval($row->total_amount),
		];

		// This month
		[$fromDate, $toDate] = EventbookingHelper::getDateDuration('this_month');

		$query->clear('where')
			->where('group_id = 0')
			->where('(published = 1 OR (payment_method LIKE "os_offline%" AND published = 0))')
			->where('register_date >= ' . $db->quote($fromDate))
			->where('register_date <=' . $db->quote($toDate));
		$db->setQuery($query);
		$row = $db->loadObject();

		$data['this_month'] = [
			'total_registrants' => (int) $row->total_registrants,
			'total_amount'      => floatval($row->total_amount),
		];

		// Last month
		[$fromDate, $toDate] = EventbookingHelper::getDateDuration('last_month');

		$query->clear('where')
			->where('group_id = 0')
			->where('(published = 1 OR (payment_method LIKE "os_offline%" AND published = 0))')
			->where('register_date >= ' . $db->quote($fromDate))
			->where('register_date <=' . $db->quote($toDate));
		$db->setQuery($query);
		$row = $db->loadObject();

		$data['last_month'] = [
			'total_registrants' => (int) $row->total_registrants,
			'total_amount'      => floatval($row->total_amount),
		];

		// This year
		[$fromDate, $toDate] = EventbookingHelper::getDateDuration('this_year');

		$query->clear('where')
			->where('group_id = 0')
			->where('(published = 1 OR (payment_method LIKE "os_offline%" AND published = 0))')
			->where('register_date >= ' . $db->quote($fromDate))
			->where('register_date <=' . $db->quote($toDate));
		$db->setQuery($query);
		$row = $db->loadObject();

		$data['this_year'] = [
			'total_registrants' => (int) $row->total_registrants,
			'total_amount'      => floatval($row->total_amount),
		];

		// Last year
		[$fromDate, $toDate] = EventbookingHelper::getDateDuration('last_year');

		$query->clear('where')
			->where('group_id = 0')
			->where('(published = 1 OR (payment_method LIKE "os_offline%" AND published = 0))')
			->where('register_date >= ' . $db->quote($fromDate))
			->where('register_date <=' . $db->quote($toDate));
		$db->setQuery($query);
		$row = $db->loadObject();

		$data['last_year'] = [
			'total_registrants' => (int) $row->total_registrants,
			'total_amount'      => floatval($row->total_amount),
		];

		// Total registration
		$query->clear()
			->select('SUM(number_registrants) AS total_registrants, SUM(amount) AS total_amount')
			->from('#__eb_registrants')
			->where('group_id = 0')
			->where('(published = 1 OR (payment_method LIKE "os_offline%" AND published = 0))');
		$db->setQuery($query);
		$row = $db->loadObject();

		$data['total_registration'] = [
			'total_registrants' => (int) $row->total_registrants,
			'total_amount'      => floatval($row->total_amount),
		];

		// Total Cancellation
		$query->clear()
			->select('SUM(number_registrants) AS total_registrants, SUM(amount) AS total_amount')
			->from('#__eb_registrants')
			->where('group_id = 0')
			->where('published = 2');
		$db->setQuery($query);
		$row = $db->loadObject();

		$data['total_cancellation'] = [
			'total_registrants' => (int) $row->total_registrants,
			'total_amount'      => floatval($row->total_amount),
		];

		return $data;
	}
}
