<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2024 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Http\HttpFactory;
use Joomla\Database\DatabaseQuery;

class EventbookingModelSearch extends EventbookingModelList
{
	/**
	 * Instantiate the model.
	 *
	 * @param   array  $config  configuration data for the model
	 */
	public function __construct($config = [])
	{
		parent::__construct($config);

		$this->state->remove('id')
			->insert('created_by', 'int', 0)
			->insert('filter_from_date', 'string', '')
			->insert('filter_to_date', 'string', '')
			->insert('filter_address', 'string', '')
			->insert('filter_distance', 'int', 0);
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

		$dateFormat = str_replace('%', '', $config->get('date_field_format', '%Y-%m-%d'));

		$displayEventsType = $this->params->get('display_events_type', 0);

		if ($displayEventsType == 0)
		{
			if ($config->hide_past_events)
			{
				$displayEventsType = 2;
			}
			else
			{
				$displayEventsType = 1;
			}
		}

		// Display upcoming events
		if ($displayEventsType == 2)
		{
			$this->applyHidePastEventsFilter($query);
		}
		elseif ($displayEventsType == 3)
		{
			// Display past events
			$this->applyHideFutureEventsFilter($query);
		}

		if ($this->state->filter_from_date && !EventbookingHelper::isValidDate($this->state->filter_from_date))
		{
			$date = DateTime::createFromFormat($dateFormat, $this->state->filter_from_date);

			if ($date !== false)
			{
				$this->state->filter_from_date = $date->format('Y-m-d');
			}
			else
			{
				$this->state->filter_from_date = '';
			}
		}

		if ($this->state->filter_to_date && !EventbookingHelper::isValidDate($this->state->filter_to_date))
		{
			$date = DateTime::createFromFormat($dateFormat, $this->state->filter_to_date);

			if ($date !== false)
			{
				$this->state->filter_to_date = $date->format('Y-m-d');
			}
			else
			{
				$this->state->filter_to_date = '';
			}
		}

		if ($this->state->filter_from_date)
		{
			$query->where('DATE(tbl.event_date) >= ' . $this->getDbo()->quote($this->state->filter_from_date));
		}

		if ($this->state->filter_to_date)
		{
			$query->where('DATE(tbl.event_date) <= ' . $this->getDbo()->quote($this->state->filter_to_date));
		}

		if ($this->state->filter_address && $this->state->filter_distance)
		{
			$this->applyRadiusFilter($query, $this->state->filter_address, $this->state->filter_distance);
		}

		return parent::buildQueryWhere($query);
	}

	/**
	 * Get list of all locations within a distance from given address
	 *
	 * @param   DatabaseQuery  $query
	 * @param   string         $address
	 * @param   int            $distance
	 *
	 * @return void
	 */
	protected function applyRadiusFilter($query, $address, $distance)
	{
		$coordinates = $this->getCoordinatesFromAddress($address);

		if ($coordinates !== null)
		{
			$lat  = (float) $coordinates[0];
			$long = (float) $coordinates[1];

			$config = EventbookingHelper::getConfig();

			if ($config->get('radius_search_distance', 'KM') == 'KM')
			{
				$multipleWithValue = 6371;
			}
			else
			{
				$multipleWithValue = 3959;
			}

			$db            = $this->getDbo();
			$locationQuery = $db->getQuery(true)
				->select(
					"id, ($multipleWithValue * acos(cos(radians($lat))* cos(radians(c.lat)) * cos(radians(c.long) - radians($long)) + sin ( radians($lat) )* sin( radians(c.lat)))) AS distance"
				)
				->from('#__eb_locations AS c')
				->where('c.published = 1')
				->having('distance < ' . (int) $distance);
			$db->setQuery($locationQuery);
			$locationIds = $db->loadColumn();
		}

		if (empty($locationIds))
		{
			$locationIds = [0];
		}

		$query->whereIn('tbl.location_id', $locationIds);
	}

	/**
	 * Method to get coordinates from
	 *
	 * @param $address
	 *
	 * @return array
	 */
	protected function getCoordinatesFromAddress($address)
	{
		$coordinates = null;
		$config      = EventbookingHelper::getConfig();

		if ($config->get('map_provider') == 'openstreetmap')
		{
			JLoader::register('EventbookingModelLocations', JPATH_ROOT . '/components/com_eventbooking/model/locations.php');

			/* @var EventbookingModelLocations $model */
			$model = RADModel::getTempInstance('Locations', 'EventbookingModel', ['table' => '#__eb_locations']);
			$items = $model->searchInOpenStreetMap($address);

			if (count($items))
			{
				$coordinates = [$items[0]->lat, $items[0]->long];
			}
		}
		else
		{
			$mapApiKey = $config->get('map_api_key', '');

			$http     = HttpFactory::getHttp();
			$url      = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . str_replace(' ', '+', $address) . '&key=' . $mapApiKey;
			$response = $http->get($url);

			if ($response->code == 200)
			{
				$output_deals = json_decode($response->body);
				$latLng       = $output_deals->results[0]->geometry->location;
				$coordinates  = [$latLng->lat, $latLng->lng];
			}
		}

		return $coordinates;
	}
}
