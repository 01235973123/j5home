<?php

/*------------------------------------------------------------------------
# mod_ospropertymap.php - OS Property Map
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2018 joomdonation.com. All Rights Reserved.
# @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Helper\ModuleHelper;
error_reporting(E_ERROR | E_PARSE | E_COMPILE_ERROR);
$document = Factory::getDocument();
$document->addStyleSheet(Uri::root().'modules/mod_ospropertymap/asset/style.css');
require_once dirname(__FILE__).'/helper.php';

include_once(JPATH_ROOT.'/components/com_osproperty/helpers/helper.php');
include_once(JPATH_ROOT.'/components/com_osproperty/helpers/common.php');
include_once(JPATH_ROOT.'/components/com_osproperty/helpers/route.php');
include_once(JPATH_ROOT.'/components/com_osproperty/helpers/bootstrap.php');
$configClass = OSPHelper::loadConfig();
if (version_compare(JVERSION, '3.0', 'lt')) {
	OSPHelper::loadBootstrapStylesheet();
}else{
	if($configClass['load_bootstrap'] == 1){
		OSPHelper::loadBootstrap();	
	}
}
global $bootstrapHelper;
OSPHelper::generateBoostrapVariables();
modOspropertyGoogleMapHelper::loadLanguage();
// params
$width          = $params->get('width','100%');
$height         = $params->get('height',300);
$zoom           = $params->get('zoom',10);
$map_type       = 'google';
$bing_api       = $params->get('bing_api','');
$google_maptype = $params->get('google_maptype','ROADMAP');
$osp_category   = $params->get('osp_category');
$osp_type       = $params->get('osp_type');
$module_id      = $module->id;
$configClass    = OSPHelper::loadConfig();
require( ModuleHelper::getLayoutPath( 'mod_ospropertymap' ) );
?>
