<?php
/*------------------------------------------------------------------------
# extrafield.php - Ossolution Property
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
 * Banner table
 *
 * @package		Joomla.Administrator
 * @subpackage	com_osproperty
 * @since		1.5
 */
class OspropertyTableExtrafield extends Table
{
	var $id = null;
	var $group_id = null;
	var $field_type = null;
	var $field_name = null;
	var $field_label = null;
	var $field_description = null;
	var $ordering = null;
	var $required = null;
	var $options = null;
	var $default_value = null;
	var $show_description = null;
	var $size = null;
	var $ncols = null;
	var $nrows = null;
	var $readonly = null;
	var $searchable = null;
	var $maxlength = null;
	var $value_type = 0;
	var $displaytitle = null;
	var $access = null;
	var $published = null;
	/**
	 * Constructor
	 *
	 * @since	1.5
	 */
	
	function __construct(&$_db)
	{
		parent::__construct('#__osrs_extra_fields', 'id', $_db);
	}
}