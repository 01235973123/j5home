<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2025 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

use Joomla\CMS\Table\Table;
use Joomla\Utilities\ArrayHelper;

defined('_JEXEC') or die;

class EventbookingModelDiscount extends RADModelAdmin
{
	protected function initData()
	{
		parent::initData();

		$this->data->discount_type = 1;
	}

	/**
	 * Validate to make sure data entered for event is valid before saving
	 *
	 * @param   RADInput  $input
	 *
	 * @return array
	 */
	public function validateFormInput($input)
	{
		$config             = EventbookingHelper::getConfig();
		$dateFormat         = str_replace('%', '', $config->get('date_field_format', '%Y-%m-%d')) . ' H:i';
		$dateFormatFallback = $dateFormat . ':s';

		$dateFields = [
			'from_date',
			'to_date',
		];

		foreach ($dateFields as $field)
		{
			$dateValue = $input->getString($field);

			if ($dateValue)
			{
				try
				{
					$date = DateTime::createFromFormat($dateFormat, $dateValue);

					if ($date === false)
					{
						$date = DateTime::createFromFormat($dateFormatFallback, $dateValue);
					}

					if ($date !== false)
					{
						$input->set($field, $date->format('Y-m-d H:i:s'));
					}
				}
				catch (Exception $e)
				{
					// Do nothing
				}
			}
		}

		return parent::validateFormInput($input);
	}

	/**
	 * Pre-process data before custom field is being saved to database
	 *
	 * @param   Table     $row
	 * @param   RADInput  $input
	 * @param   bool      $isNew
	 */
	protected function beforeStore($row, $input, $isNew)
	{
		$row->event_ids = implode(',', $input->get('event_id', [], 'array'));
	}

	/**
	 * Post - process, Store discount rule mapping with events.
	 *
	 * @param   EventbookingTableDiscount  $row
	 * @param   RADInput                   $input
	 * @param   bool                       $isNew
	 */
	protected function afterStore($row, $input, $isNew)
	{
		$eventIds   = array_filter(ArrayHelper::toInteger($input->get('event_id', [], 'array')));
		$discountId = $row->id;
		$db         = $this->getDbo();
		$query      = $db->getQuery(true);

		if (!$isNew)
		{
			$query->delete('#__eb_discount_events')->where('discount_id = ' . $discountId);
			$db->setQuery($query);
			$db->execute();
		}

		if (count($eventIds))
		{
			$query->clear()
				->insert('#__eb_discount_events')
				->columns('discount_id, event_id');

			foreach ($eventIds as $eventId)
			{
				$query->values("$discountId, $eventId");
			}

			$db->setQuery($query);
			$db->execute();
		}
	}

	/**
	 * Delete the mapping between discount and events before the actual discounts are being deleted
	 *
	 * @param   array  $cid  Ids of deleted record
	 */
	protected function beforeDelete($cid)
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true)
			->delete('#__eb_discount_events')
			->whereIn('discount_id', $cid);
		$db->setQuery($query)
			->execute();
	}
}
