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

JLoader::register('EventbookingModelCommonRegistrants', JPATH_ADMINISTRATOR . '/components/com_eventbooking/model/common/registrants.php');

class EventbookingModelRegistrantlist extends EventbookingModelCommonRegistrants
{
	/**
	 * Instantiate the model.
	 *
	 * @param   array  $config  configuration data for the model
	 */
	public function __construct($config = [])
	{
		$config['remember_states'] = false;

		parent::__construct($config);

		$this->state->insert('id', 'int', 0)
			->insert('registrant_type', 0);

		$ebConfig = EventbookingHelper::getConfig();

		if ($registrantsPerPage = (int) $ebConfig->get('public_registrants_list_number_registrants'))
		{
			$this->state->setDefault('limit', $registrantsPerPage);
		}
	}

	/**
	 * Build where clause of the query
	 *
	 * @see RADModelList::buildQueryWhere()
	 */
	protected function buildQueryWhere(DatabaseQuery $query)
	{
		$config = EventbookingHelper::getConfig();

		$includeGroupBillingRecords = $config->get(
			'include_group_billing_in_registrants_list',
			$config->get('include_group_billing_in_registrants', 1)
		);
		$includeGroupMemberRecords  = $config->get(
			'include_group_members_in_registrants_list',
			$config->get('include_group_members_in_registrants', 0)
		);

		if (!$includeGroupBillingRecords && $includeGroupMemberRecords)
		{
			$query->where(' tbl.is_group_billing = 0 ');
		}

		if (!$includeGroupMemberRecords)
		{
			$query->where(' tbl.group_id = 0 ');
		}

		if ($this->params->get('registration_status', '') === '0')
		{
			$query->where('tbl.published = 0');
		}
		elseif ($this->params->get('registration_status', '') === '1')
		{
			$query->where('tbl.published = 1');
		}
		else
		{
			if ($this->state->registrant_type == 3)
			{
				$query->where('tbl.published = 3');
			}
			else
			{
				$query->where('(tbl.published = 1 OR (tbl.published = 0 AND tbl.payment_method LIKE "os_offline%"))');
			}
		}

		return parent::buildQueryWhere($query);
	}

	/**
	 * Change ordering of public registrants list base on values from Configuration
	 *
	 * @param   DatabaseQuery  $query
	 *
	 * @return EventbookingModelCommonRegistrants
	 */
	protected function buildQueryOrder(DatabaseQuery $query)
	{
		$config = EventbookingHelper::getConfig();

		// For waiting list, it makes sense to order by ID ASC so that the people first join is displayed first
		if ($this->state->registrant_type == 3)
		{
			$this->state->set('filter_order', 'tbl.id');
			$this->state->set('filter_order_Dir', 'ASC');
		}
		else
		{
			$this->state->set('filter_order', $config->get('public_registrants_list_order', 'tbl.id'));
			$this->state->set('filter_order_Dir', $config->get('public_registrants_list_order_dir', 'desc'));
		}

		return parent::buildQueryOrder($query);
	}
}
