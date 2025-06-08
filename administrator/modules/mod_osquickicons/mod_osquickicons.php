<?php
;/**
; * @version	$Id: mod_osquickicons.php $
; * @package	OS Property
; * @author		Dang Thuc Dam http://www.joomdonation.com
; * @copyright	Copyright (c) 2007 - 2024 Joomdonation. All rights reserved.
; * @license	GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
; */


// no direct access
defined('_JEXEC') or die ;

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;

$user = Factory::getUser();
$document = Factory::getDbo();

error_reporting(E_CORE_ERROR|E_ERROR|E_PARSE|E_USER_ERROR | E_COMPILE_ERROR);

include JPATH_ROOT.'/components/com_osproperty/helpers/helper.php';
include JPATH_ADMINISTRATOR.'/components/com_osproperty/classes/cpanel.php';
include JPATH_ADMINISTRATOR.'/components/com_osproperty/classes/cpanel.html.php';

// OS Property reference parameters
$mod_name = "mod_osquickicons";
$mod_copyrights_start = "\n\n<!-- Ossolution \"OS Property QuickIcons\" Module starts here -->\n";
$mod_copyrights_end = "\n<!-- Ossolution \"Os Property QuickIcons\" Module ends here -->\n\n";

// API
$mainframe = Factory::getApplication();
$document = Factory::getDocument();
$user = Factory::getUser();

// Module parameters
$moduleclass_sfx = $params->get('moduleclass_sfx', '');
$modCSSStyling = (int)$params->get('modCSSStyling', 1);
$modLogo = (int)$params->get('modLogo', 1);

// Component parameters
$componentParams = ComponentHelper::getParams('com_osproperty');

// Call the modal and add some needed JS
//JHTML::_('behavior.modal');

// Append CSS to the document's head
if ($modCSSStyling)
	$document->addStyleSheet(JURI::base(true).'/modules/'.$mod_name.'/tmpl/css/style.css');


$configClass = OSPHelper::loadConfig();
include JPATH_ADMINISTRATOR.'/modules/mod_osquickicons/helper.php';
// Output content with template
echo $mod_copyrights_start;
require (JModuleHelper::getLayoutPath($mod_name, 'default'));
echo $mod_copyrights_end;
