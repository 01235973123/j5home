<?php
/*------------------------------------------------------------------------
# company.php - Ossolution Property
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
class OspropertyTableCompany extends Table
{
	var $id = null;
	var $company_name = null;
	var $company_alias = null;
	var $address = null;
	var $state = null;
	var $city = null;
	var $country = null;
	var $postcode = null;
	var $phone = null;
	var $fax = null;
	var $email = null;
	var $website = null;
	var $photo = null;
	var $company_description = null;
	var $published = null;
	var $request_to_approval = 0;
	var $alreadyPublished = null;
	/**
	 * Constructor
	 *
	 * @since	1.5
	 */
	
	function __construct(&$_db)
	{
		parent::__construct('#__osrs_companies', 'id', $_db);
	}
}