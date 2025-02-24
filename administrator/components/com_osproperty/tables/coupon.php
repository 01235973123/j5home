<?php
/*------------------------------------------------------------------------
# coupon.php - Ossolution Property
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

class OspropertyTableCoupon extends Table
{
	/**
	 * @var int
	 */
	var $id = null;
	/**
	 *
	 * @var varchar
	 */
	var $coupon_name = null;
	/**
	 *
	 * @var varchar
	 */
	var $coupon_code = null;
	/**
	 * @var date
	 */
	var $start_time = null;
	/**
	 * @var date
	 */
	var $end_time = null;
	/**
	 * @var int
	 */
	var $discount = null;
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
		parent::__construct('#__osrs_coupon', 'id', $_db);
	}
	
	function check()
	{
		$db = Factory::getDBO();
		$query = " SELECt id FROM #__osrs_coupon "
				." WHERE `published` > 0 " 
				." AND "
				." 	("
				."    	(`start_time` <= '$this->start_time' AND '$this->start_time' <= `end_time`) "
				."   OR (`start_time` <= '$this->end_time' AND '$this->end_time' <= `end_time`) "
				."	 OR	(`start_time` >= '$this->start_time' AND '$this->end_time' >= `end_time`)" 
				.")"
				;
		if ($this->id) $query .= "AND `id` != $this->id";
		
		$db->setQuery($query);
		return $db->loadResult();
	}
}