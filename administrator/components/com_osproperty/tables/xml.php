<?php
/*------------------------------------------------------------------------
# xml.php - Ossolution Property
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
 * XML details MySQL table
 * @package		OS Property
 * @since		2.7.5
 */

class OspropertyTableXml extends Table
{
	function __construct(&$_db)
	{
		parent::__construct('#__osrs_xml_details', 'id', $_db);
	}
}