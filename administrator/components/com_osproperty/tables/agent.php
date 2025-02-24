<?php
/*------------------------------------------------------------------------
# agent.php - Ossolution Property
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
class OspropertyTableAgent extends Table
{
	var $id = null;
	var $user_id = null;
	var $name = null;
	var $alias = null;
	var $company_id = null;
	var $email = null;
	var $phone =  null;
	var $mobile = null;
	var $fax = null;
	var $address = null;
	var $city = null;
	var $state = null;
	var $country = null;
	var $photo = null;
	var $yahoo = null;
	var $skype = null;
	var $aim = null;
	var $msn = null;
	var $gtalk = null;
	var $facebook = null;
	var $license = null;
	var $ordering = null;
	var $published = null;
	var $request_to_approval = null;
	var $bio = null;
	/**
	 * Constructor
	 *
	 * @since	1.5
	 */
	
	function __construct(&$_db)
	{
		parent::__construct('#__osrs_agents', 'id', $_db);
	}
}