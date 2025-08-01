<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2024 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\Database\DatabaseQuery;

class EventbookingModelCoupons extends RADModelList
{
	protected $choicesStates = ['filter_event_id'];

	/**
	 * Instantiate the model.
	 *
	 * @param   array  $config  configuration data for the model
	 */
	public function __construct($config = [])
	{
		$config['search_fields'] = ['tbl.code', 'tbl.note'];

		parent::__construct($config);

		$this->state->insert('filter_event_id', 'int', 0)
			->setDefault('filter_order_Dir', 'DESC');
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
		if ($this->state->filter_event_id)
		{
			$query->where(
				'(tbl.event_id = -1 OR tbl.id IN (SELECT coupon_id FROM #__eb_coupon_events WHERE event_id=' . $this->state->filter_event_id . '))'
			);
		}

		return parent::buildQueryWhere($query);
	}
}
