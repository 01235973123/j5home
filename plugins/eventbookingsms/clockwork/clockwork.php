<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2024 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Plugin\CMSPlugin;
use mediaburst\ClockworkSMS\Clockwork;

class plgEventbookingSMSClockwork extends CMSPlugin
{
	public function onEBSendingSMSReminder($rows)
	{
		require_once JPATH_ROOT . '/plugins/eventbookingsms/clockwork/clockwork/vendor/autoload.php';

		$apiKey = $this->params->get('api_key');

		if (!$apiKey)
		{
			return;
		}

		$client = new Clockwork($apiKey);

		foreach ($rows as $row)
		{
			$message = ['to' => $this->sanitize($row->phone), 'message' => $row->sms_message];

			try
			{
				$result = $client->send($message);

				if (!$result['success'])
				{
					EventbookingHelper::logData(
						__DIR__ . '/clockwork_error.txt',
						['id' => $row->id, 'phone' => $row->phone, 'error' => $result['error_message']]
					);
				}
			}
			catch (Exception $e)
			{
				EventbookingHelper::logData(
					__DIR__ . '/clockwork_error.txt',
					['id' => $row->id, 'phone' => $row->phone, 'error' => $e->getMessage()]
				);
			}
		}

		// Return true to tell the system that SMS were successfully sent so that it could update sms sending status for registrants
		return true;
	}

	/**
	 * Helper method used to sanitize phone numbers.
	 *
	 * @param   string  $phone  The phone number to sanitize.
	 *
	 * @return    string    The cleansed number.
	 */
	protected function sanitize($phone)
	{
		$phone = trim(str_replace(' ', '', $phone));

		if (substr($phone, 0, 1) != '+')
		{
			if (substr($phone, 0, 2) == '00')
			{
				$phone = '+' . substr($phone, 2);
			}
			else
			{
				$phone = $this->params->get('prefix') . $phone;
			}
		}

		return $phone;
	}
}
