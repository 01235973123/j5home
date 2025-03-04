<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2025 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Pagination\Pagination;
use Joomla\Database\DatabaseQuery;

class EventbookingModelCategories extends RADModelList
{
	/**
	 * Instantiate the model.
	 *
	 * @param   array  $config  configuration data for the model
	 */
	public function __construct($config = [])
	{
		parent::__construct($config);

		$this->state->insert('filter_parent', 'int', 0);
	}

	/**
	 * Method to get categories data
	 *
	 * @access public
	 * @return array
	 */
	public function getData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->data))
		{
			$parent = $this->state->filter_parent;
			$db     = $this->getDbo();
			$query  = $this->buildListQuery();
			$db->setQuery($query);
			$rows = $db->loadObjectList();

			$children = [];

			// first pass - collect children
			foreach ($rows as $v)
			{
				$pt            = $v->parent;
				$list          = $children[$pt] ?? [];
				$list[]        = $v;
				$children[$pt] = $list;
			}

			$list             = HTMLHelper::_('menu.treerecurse', $parent, '', [], $children, 9999);
			$total            = count($list);
			$this->pagination = new Pagination($total, $this->state->limitstart, $this->state->limit);
			// slice out elements based on limits
			$list       = array_slice($list, $this->pagination->limitstart, $this->pagination->limit);
			$this->data = $list;
		}

		return $this->data;
	}

	/**
	 * Builds SELECT columns list for the query
	 */
	protected function buildQueryColumns(DatabaseQuery $query)
	{
		$query->select('tbl.*, tbl.parent AS parent_id, tbl.name AS title, vl.title AS access_level, COUNT(ec.id) AS total_events');

		return $this;
	}

	/**
	 * Builds LEFT JOINS clauses for the query
	 */
	protected function buildQueryJoins(DatabaseQuery $query)
	{
		$query->leftJoin('#__eb_event_categories AS ec ON tbl.id = ec.category_id')
			->leftJoin('#__viewlevels AS vl ON vl.id = tbl.access');

		return $this;
	}

	/**
	 * Builds a GROUP BY clause for the query
	 */
	protected function buildQueryGroup(DatabaseQuery $query)
	{
		$query->group('tbl.id');

		return $this;
	}
}
