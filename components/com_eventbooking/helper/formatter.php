<?php

use Joomla\CMS\HTML\HTMLHelper;

/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2025 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */
class EventbookingHelperFormatter
{
	public static function getFormattedDatetime($date)
	{
		if ((int) $date)
		{
			$config = EventbookingHelper::getConfig();

			if (str_contains($date, '00:00:00'))
			{
				$format = $config->date_format;
			}
			else
			{
				$format = $config->event_date_format;
			}

			return HTMLHelper::_('date', $date, $format, null);
		}

		return '';
	}
}