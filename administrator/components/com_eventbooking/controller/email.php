<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2024 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;

class EventbookingControllerEmail extends EventbookingController
{
	public function delete_all()
	{
		Factory::getContainer()->get('db')->truncateTable('#__eb_emails');

		$this->setRedirect('index.php?option=com_eventbooking&view=emails');
	}
}
