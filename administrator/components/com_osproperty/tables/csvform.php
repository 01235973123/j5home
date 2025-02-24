<?php
/*------------------------------------------------------------------------
# csvform.php - Ossolution Property
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
class OspropertyTableCsvform extends Table
{
	var $id = null;
	var $form_name = null;
	var $max_file_size = null;
	var $created_on = null;
	var $last_import = null;
	var $yes_value = null;
	var $no_value = null;
	var $ftype = null;
	var $type_id = null;
	var $fcategory = null;
	var $category_id = null;
	var $agent_id = null;
	var $country = null;
	var $fstate = null;
	var $state = null;
	var $fcity = null;
	var $city = null;
	var $published = 1;
	/**
	 * Constructor
	 *
	 * @since	1.5
	 */
	
	function __construct(&$_db)
	{
		parent::__construct('#__osrs_csv_forms', 'id', $_db);
	}
}