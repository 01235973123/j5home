<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2025 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

namespace OSSolution\EventBooking\Admin\Event\Data;

class BeforeGettingDataFromFile extends \RADEventBase
{
	protected $requiredArguments = ['file', 'filename'];
}