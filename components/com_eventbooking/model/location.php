<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2024 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Factory;
use Joomla\Database\DatabaseQuery;

class EventbookingModelLocation extends EventbookingModelList
{
	/**
	 * Instantiate the model.
	 *
	 * @param   array  $config  configuration data for the model
	 */
	public function __construct($config = [])
	{
		parent::__construct($config);
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
		$config = EventbookingHelper::getConfig();

		$hidePastEventsParam = $this->params->get('hide_past_events', 2);

		if ($hidePastEventsParam == 1 || ($hidePastEventsParam == 2 && $config->hide_past_events))
		{
			$this->applyHidePastEventsFilter($query);
		}

		return parent::buildQueryWhere($query);
	}

	/**
	 * Get location information from database, using for add/edit page
	 *
	 * @return \Joomla\CMS\Table\Table|mixed
	 */
	public function getLocationData()
	{
		if ($this->state->id)
		{
			return EventbookingHelperDatabase::getLocation($this->state->id);
		}
		$row          = $this->getTable();
		$config       = EventbookingHelper::getConfig();
		$row->country = $config->default_country;

		return $row;
	}

	/**
	 * Method to store a location
	 *
	 * @access    public
	 * @return    boolean    True on success
	 */
	public function store(&$data)
	{
		$row         = $this->getTable();
		$user        = Factory::getApplication()->getIdentity();
		$coordinates = explode(',', $data['coordinates']);

		if (!empty($data['id']))
		{
			$row->load($data['id']);
		}

		if (count($coordinates) == 2)
		{
			$row->lat  = $coordinates[0];
			$row->long = $coordinates[1];
		}

		$row->user_id = $user->id;
		$row->bind($data);

		if (empty($row->alias))
		{
			$row->alias = ApplicationHelper::stringURLSafe($row->name);
		}

		$row->store();

		// Check and make sure this alias is valid
		$db    = $this->getDbo();
		$query = $db->getQuery(true)
			->select('COUNT(*)')
			->from('#__eb_locations')
			->where('id != ' . $row->id)
			->where($db->quoteName('alias') . ' = ' . $db->quote($row->alias));
		$db->setQuery($query);
		$count = $db->loadResult();

		if ($count)
		{
			$row->alias = $row->id . '-' . $row->alias;
			$row->store();
		}

		$data['id'] = $row->id;

		return $row->id;
	}

	/**
	 * Delete the selected location
	 *
	 * @param   array  $cid
	 *
	 * @return boolean
	 */
	public function delete($cid = [])
	{
		if (count($cid))
		{
			$db    = $this->getDbo();
			$query = $db->getQuery(true)
				->delete('#__eb_locations')
				->whereIn('id', $cid);

			$user = Factory::getApplication()->getIdentity();

			if (!$user->authorise('core.admin', 'com_eventbooking'))
			{
				$query->where('user_id = ' . (int) $user->id);
			}

			$db->setQuery($query);

			if (!$db->execute())
			{
				return false;
			}
		}

		return true;
	}
}
