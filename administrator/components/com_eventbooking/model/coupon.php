<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2025 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Crypt\Crypt;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Utilities\ArrayHelper;

class EventbookingModelCoupon extends RADModelAdmin
{
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
			'valid_from',
			'valid_to',
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
	 * Post - process, Store coupon code mapping with events.
	 *
	 * @param   EventbookingTableCoupon  $row
	 * @param   RADInput                 $input
	 * @param   bool                     $isNew
	 */
	protected function afterStore($row, $input, $isNew)
	{
		$assignment  = $input->getInt('assignment', 0);
		$categoryIds = ArrayHelper::toInteger($input->get('category_id', [], 'array'));
		$eventIds    = array_filter(ArrayHelper::toInteger($input->get('event_id', [], 'array')));

		if ($assignment == 0)
		{
			$row->event_id = -1;
		}
		else
		{
			$row->event_id = 1;
		}

		if (in_array(-1, $categoryIds))
		{
			$row->category_id = -1;
		}
		else
		{
			$row->category_id = 1;
		}

		$categoryIds = array_filter($categoryIds, function ($value) {
			return $value > 0;
		});

		// If coupon is assigned to all except selected events and no categories are selected, categories should be set to All
		if ($assignment == -1 && count($categoryIds) === 0)
		{
			$row->category_id = -1;
		}

		// If coupon is valid for certain categories, it should not be assigned to all events
		if (count($categoryIds) > 0 && $row->event_id == -1)
		{
			$row->event_id = 1;
		}

		$row->store();

		$couponId = $row->id;
		$db       = $this->getDbo();
		$query    = $db->getQuery(true);

		if (!$isNew)
		{
			$query->delete('#__eb_coupon_events')->where('coupon_id = ' . $couponId);
			$config = EventbookingHelper::getConfig();

			if ($config->hide_past_events_from_events_dropdown)
			{
				$currentDate = $db->quote(HTMLHelper::_('date', 'Now', 'Y-m-d'));
				$query->where(
					'event_id IN (SELECT id FROM #__eb_events AS a WHERE a.published = 1 AND (DATE(a.event_date) >= ' . $currentDate . ' OR DATE(a.event_end_date) >= ' . $currentDate . '))'
				);
			}

			$db->setQuery($query);
			$db->execute();

			$query->clear()
				->delete('#__eb_coupon_categories')
				->where('coupon_id = ' . $couponId);
			$db->setQuery($query)
				->execute();
		}

		if ($row->event_id != -1 && count($eventIds) > 0)
		{
			$query->clear()
				->insert('#__eb_coupon_events')
				->columns('coupon_id, event_id');

			foreach ($eventIds as $eventId)
			{
				$eventId *= $assignment;
				$query->values("$couponId, $eventId");
			}

			$db->setQuery($query)
				->execute();
		}

		if ($row->category_id != -1 && count($categoryIds) > 0)
		{
			$query->clear()
				->insert('#__eb_coupon_categories')->columns('coupon_id, category_id');

			foreach ($categoryIds as $categoryId)
			{
				$query->values("$couponId, $categoryId");
			}

			$db->setQuery($query)
				->execute();
		}
	}

	/**
	 * Delete associated data before deleting coupons
	 *
	 * @param   array  $cid
	 *
	 * @return void
	 */
	protected function beforeDelete($cid)
	{
		parent::beforeDelete($cid);

		$db    = $this->getDbo();
		$query = $db->getQuery(true)
			->delete('#__eb_coupon_events')
			->whereIn('coupon_id', $cid);
		$db->setQuery($query)
			->execute();

		$query->clear()
			->delete('#__eb_coupon_categories')
			->whereIn('coupon_id', $cid);
		$db->setQuery($query)
			->execute();
	}

	/**
	 * @param $file
	 * @param $filename
	 *
	 * @return int
	 * @throws Exception
	 */
	public function import($file, $filename = '')
	{
		$coupons = EventbookingHelperData::getDataFromFile($file, $filename);

		if (count($coupons) === 0)
		{
			return 0;
		}

		$db       = $this->getDbo();
		$query    = $db->getQuery(true);
		$imported = 0;

		foreach ($coupons as $coupon)
		{
			if (empty($coupon['code']) || empty($coupon['discount']))
			{
				continue;
			}

			/* @var EventbookingTableCoupon $row */
			$row = $this->getTable();

			$eventIds = $coupon['event'];

			if (!$eventIds)
			{
				$coupon['event_id'] = -1;
			}
			else
			{
				$coupon['event_id'] = 1;
			}

			if ($coupon['valid_from'])
			{
				$coupon ['valid_from'] = HTMLHelper::date($coupon['valid_from'], 'Y-m-d', null);
			}
			else
			{
				$coupon ['valid_from'] = '';
			}

			if ($coupon['valid_to'])
			{
				$coupon ['valid_to'] = HTMLHelper::date($coupon['valid_to'], 'Y-m-d', null);
			}
			else
			{
				$coupon ['valid_to'] = '';
			}

			$row->bind($coupon);
			$row->store();
			$couponId = $row->id;

			$eventIds = array_filter(ArrayHelper::toInteger(explode(',', $eventIds)));

			if (count($eventIds) > 0)
			{
				$query->clear()
					->insert('#__eb_coupon_events')->columns('coupon_id, event_id');

				foreach ($eventIds as $eventId)
				{
					$query->values("$couponId, $eventId");
				}

				$db->setQuery($query);
				$db->execute();
			}

			$imported++;
		}

		return $imported;
	}

	/**
	 * Generate batch coupon
	 *
	 * @param   RADInput  $input
	 */
	public function batch($input)
	{
		$db                  = $this->getDbo();
		$query               = $db->getQuery(true);
		$numberCoupon        = $input->getInt('number_coupon', 50);
		$charactersSet       = $input->getString('characters_set');
		$prefix              = $input->getString('prefix');
		$length              = $input->getInt('length', 20) ?: 10;
		$data                = [];
		$data['discount']    = $input->getFloat('discount', 0);
		$data['coupon_type'] = $input->getInt('coupon_type', 0);
		$data['times']       = $input->getInt('times');
		$assignment          = $input->getInt('assignment', 0);
		$eventIds            = array_filter(ArrayHelper::toInteger($input->get('event_id', [], 'array')));
		$categoryIds         = array_filter(ArrayHelper::toInteger($input->get('category_id', [], 'array')));

		if ($assignment == 0)
		{
			$data['event_id'] = -1;
		}
		else
		{
			$data['event_id'] = 1;
		}

		if (in_array(-1, $categoryIds))
		{
			$data['category_id'] = -1;
		}
		else
		{
			$data['category_id'] = 1;
		}

		// Remove All categories option
		$categoryIds = array_filter($categoryIds, function ($value) {
			return $value > 0;
		});

		// If coupon is assigned to all except selected events and no categories are selected, categories should be set to All
		if ($assignment == -1 && count($categoryIds) === 0)
		{
			$data['category_id'] = -1;
		}

		// If coupon is valid for certain categories, it should not be assigned to all events
		if (count($categoryIds) > 0 && $data['event_id'] == -1)
		{
			$data['event_id'] = 1;
		}

		if ($input->getString('valid_from'))
		{
			$data ['valid_from'] = HTMLHelper::date($input->getString('valid_from'), 'Y-m-d', null);
		}
		else
		{
			$data ['valid_from'] = '';
		}

		if ($input->getString('valid_to'))
		{
			$data ['valid_to'] = HTMLHelper::date($input->getString('valid_to'), 'Y-m-d', null);
		}
		else
		{
			$data ['valid_to'] = '';
		}

		$data['used']                   = 0;
		$data ['published']             = $input->getInt('published', 1);
		$data['apply_to']               = $input->getInt('apply_to', 0);
		$data['enable_for']             = $input->getInt('enable_for', 0);
		$data['min_number_registrants'] = $input->getInt('min_number_registrants', 0);
		$data['max_number_registrants'] = $input->getInt('max_number_registrants', 0);
		$data['min_payment_amount']     = $input->getFloat('min_payment_amount', 0);
		$data['max_payment_amount']     = $input->getFloat('max_payment_amount', 0);
		$data['note']                   = $input->getString('note');

		for ($i = 0; $i < $numberCoupon; $i++)
		{
			$salt       = $this->genRandomCoupon($length, $charactersSet);
			$couponCode = $prefix . $salt;

			/* @var EventbookingTableCoupon $row */
			$row          = $this->getTable();
			$data['code'] = $couponCode;

			$row->bind($data);
			$row->store();
			$couponId = $row->id;

			if ($row->event_id != -1 && count($eventIds))
			{
				$query->clear()
					->insert('#__eb_coupon_events')->columns('coupon_id, event_id');

				foreach ($eventIds as $eventId)
				{
					$eventId *= $assignment;
					$query->values("$couponId, $eventId");
				}

				$db->setQuery($query)
					->execute();
			}

			if ($row->category_id != -1 && count($categoryIds))
			{
				$query->clear()
					->insert('#__eb_coupon_categories')->columns('coupon_id, category_id');

				foreach ($categoryIds as $categoryId)
				{
					$query->values("$couponId, $categoryId");
				}

				$db->setQuery($query)
					->execute();
			}
		}
	}

	/**
	 * Get list of registration records which use the current coupon code
	 *
	 * @return array
	 */
	public function getRegistrants()
	{
		if (!$this->state->id)
		{
			return [];
		}

		$db    = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('id, first_name, last_name, email, register_date, total_amount')
			->from('#__eb_registrants')
			->where('coupon_id = ' . $this->state->id)
			->where('group_id = 0')
			->where('(published = 1 OR (published = 0 AND payment_method LIKE "os_offline%"))')
			->order('id');
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	/**
	 * Generate random Coupon
	 *
	 * @param   int     $length
	 * @param   string  $charactersSet
	 *
	 * @return string
	 */
	public static function genRandomCoupon($length = 8, $charactersSet = null)
	{
		$salt = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

		if ($charactersSet)
		{
			$salt = $charactersSet;
		}

		$base     = strlen($salt);
		$makePass = '';

		/*
		 * Start with a cryptographic strength random string, then convert it to
		 * a string with the numeric base of the salt.
		 * Shift the base conversion on each character so the character
		 * distribution is even, and randomize the start shift so it's not
		 * predictable.
		 */
		$random = Crypt::genRandomBytes($length + 1);
		$shift  = ord($random[0]);

		for ($i = 1; $i <= $length; ++$i)
		{
			$makePass .= $salt[($shift + ord($random[$i])) % $base];
			$shift    += ord($random[$i]);
		}

		return $makePass;
	}
}
