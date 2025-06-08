<?php
use Joomla\CMS\Helper\ModuleHelper;
/**
 * @version 1.5.0 2011-11-11
 * @package Joomla
 * @subpackage OS-Property Category menus
 * @copyright (C)  2011 the Ossolution
 * @license see LICENSE.php
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Include helper functions only once
require_once( dirname(__FILE__).DIRECTORY_SEPARATOR.'helper.php' );
require_once(JPATH_ROOT.'/components/com_osproperty/helpers/route.php');
require_once(JPATH_ROOT.'/components/com_osproperty/helpers/helper.php');
$moduleclass_sfx = $params->get('moduleclass_sfx','');
$num_cols = $params->get('num_cols',1);
$list_type = $params->get('list_type',0);
$itemid	 = $params->get('itemid',0);
if((int) $itemid == 0)
{
	$needs[] = "property_listing";
	$needs[] = "property_type";
	$needs[] = "ltype";
	$itemid = OSPRoute::getItemid($needs);
}

OSPHelper::loadBootstrap();
$module_id = $module->id;
require( ModuleHelper::getLayoutPath( 'mod_ospropertystates' ) );
?>
