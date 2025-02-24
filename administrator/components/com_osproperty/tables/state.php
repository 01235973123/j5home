<?php
/*------------------------------------------------------------------------
# state.php - Ossolution Property
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2023 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/

// No direct access.
defined('_JEXEC') or die;
use Joomla\CMS\Table\Table;

/**
 * Agents table
 *
 * @package		Joomla.Administrator
 * @subpackage	com_osproperty
 * @since		1.5
 */

class OspropertyTableState extends Table
{
	/**
	 * @var int
	 */
	var $id = null;
	/**
	 * @var int
	 */
	var $country_id = null;
	/**
	 *
	 * @var varchar
	 */
	var $state_name = null;
	/**
	 *
	 * @var int
	 */
	var $state_code = null;
	
	
	var $published = null;
	/**
	 * Constructor
	 *
	 * @since	1.5
	 */
	
	function __construct(&$_db)
	{
		parent::__construct('#__osrs_states', 'id', $_db);
	}
}