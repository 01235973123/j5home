<?php
/*------------------------------------------------------------------------
# theme.php - Ossolution Property
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
class OspropertyTableTheme extends Table
{
	var $id = null;
	var $name = null;
	var $title = null;
	var $author = null;
	var $creation_date = null;
	var $copyright = null;
	var $license =  null;
	var $author_email =  null;
	var $author_url =  null;
	var $version =  null;
	var $description =  null;
	var $params =  null;
	var $support_mobile_device =  null;
	var $published = 1;
	/**
	 * Constructor
	 *
	 * @since	1.5
	 */
	
	function __construct(&$_db)
	{
		parent::__construct('#__osrs_themes', 'id', $_db);
	}
}