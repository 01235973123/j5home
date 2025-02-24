<?php
/*------------------------------------------------------------------------
# comment.php - Ossolution Property
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


class OspropertyTableComment extends Table
{
	/**
	 * @var int
	 */
	var $id = null;
	/**
	 * @var int
	 */
	var $pro_id = null;
	/**
	 *
	 * @var int
	 */
	var $user_id = null;
	/**
	 *
	 * @var varchar
	 */
	var $name = null;
	/**
	 * @var varchar
	 */
	var $title = null;
	/**
	 * @var varchar
	 */
	var $content = null;
	/**
	 * @var datetime
	 */
	var $created_on = null;
	
	/**
	 *
	 * @var tinyint
	 */
	var $rate = null;
	/**
	 *
	 * @var int
	 */
	var $published = null;
	
	/**
	 * Constructor
	 *
	 * @since	1.5
	 */
	
	function __construct(&$_db)
	{
		parent::__construct('#__osrs_comments', 'id', $_db);
	}
}