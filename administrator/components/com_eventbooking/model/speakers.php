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

class EventbookingModelSpeakers extends RADModelList
{
	/**
	 * Constructor
	 *
	 * @param   array  $config
	 */
	public function __construct($config = [])
	{
		parent::__construct($config);

		$this->state->insert('filter_event_id', 'int', 0);
	}

	/**
	 * Filter speakers by event
	 *
	 * @param   DatabaseQuery  $query
	 *
	 * @return RADModelList
	 */
	protected function buildQueryWhere(DatabaseQuery $query)
	{
		if ($this->state->filter_event_id)
		{
			$query->where('tbl.id IN (SELECT speaker_id FROM #__eb_event_speakers WHERE event_id = ' . $this->state->filter_event_id . ')');
		}

		return parent::buildQueryWhere($query);
	}
}
