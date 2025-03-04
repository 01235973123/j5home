<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2025 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

use Joomla\CMS\Table\Table;

defined('_JEXEC') or die;

class EventbookingModelCountry extends RADModelAdmin
{
	/**
	 * Update country_id make it the same with id
	 *
	 * @param   Table     $row
	 * @param   RADInput  $input
	 * @param   bool      $isNew
	 */
	protected function afterStore($row, $input, $isNew)
	{
		if ($isNew)
		{
			$db    = $this->getDbo();
			$query = $db->getQuery(true);
			$query->update('#__eb_countries')
				->set('country_id = id')
				->where('id = ' . $input->getInt('id', 0));
			$db->setQuery($query);
			$db->execute();
		}
	}
}
