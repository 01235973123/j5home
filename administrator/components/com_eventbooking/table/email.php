<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2024 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

use Joomla\CMS\Table\Table;

class EventbookingTableEmail extends Table
{
	/**
	 * Constructor
	 *
	 * @param   \Joomla\Database\DatabaseDriver  $db  Database connector object
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__eb_emails', 'id', $db);
	}
}
