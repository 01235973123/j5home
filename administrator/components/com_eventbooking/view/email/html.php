<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2024 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

class EventbookingViewEmailHtml extends RADViewItem
{
	/**
	 * Method to instantiate the view.
	 *
	 * @param   array  $config  A named configuration array for object construction
	 */
	public function __construct($config = [])
	{
		$config['hide_buttons'] = ['save', 'save2new', 'save2copy'];

		parent::__construct($config);
	}
}
