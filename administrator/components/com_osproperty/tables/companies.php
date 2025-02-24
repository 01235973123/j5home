<?php
/*------------------------------------------------------------------------
# companies.php - Ossolution Property
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

class OspropertyTableCompanies extends Table
{
	/**
	 * @var int
	 */
	var $id = null;
	/**
	 * @var integer
	 *
	 */
	var $user_id = 0;
	/**
	 *
	 * @var varchar
	 */
	var $company_name = null;
	/**
	 *
	 * @var varchar
	 */
	var $company_alias = null;
	/**
	 * @var varchar
	 */
	var $address = null;
	/**
	 * @var varchar
	 */
	var $state	= null;
	/**
	 * @var varchar
	 */
	var $city	= null;
	/**
	 * @var varchar
	 */
	var $country	= null;
	/**
	 * @var varchar
	 */
	var $postcode	= null;
	/**
	 * @var varchar
	 */
	var $phone	= null;
	/**
	 * @var varchar
	 */
	var $fax	= null;
	/**
	 * @var varchar
	 */
	var $email	= null;
	/**
	 * @var varchar
	 */
	var $website	= null;
	/**
	 * @var varchar
	 */
	var $photo	= null;
	/**
	 * @var varchar
	 */
	var $company_description	= null;
	
	
	var $request_to_approval = 0;
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
		parent::__construct('#__osrs_companies', 'id', $_db);
	}
}