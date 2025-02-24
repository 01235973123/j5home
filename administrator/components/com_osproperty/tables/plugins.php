<?php

/*------------------------------------------------------------------------
# plugins.php - OS Property
# ------------------------------------------------------------------------
# author    Ossolution team
# copyright Copyright (C) 2023 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// no direct access
defined('_JEXEC') or die;
use Joomla\CMS\Table\Table;

/**
 * Employee table
 *
 * @package		Joomla.Administrator
 * @subpackage	com_osproperty
 * @since		1.5
 */
class OspropertyTablePlugins extends Table
{
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $_db
	 */	
	function __construct(&$_db)
	{
		parent::__construct('#__osrs_plugins', 'id', $_db);
	}
}