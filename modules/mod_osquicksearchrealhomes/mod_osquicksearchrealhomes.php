<?php
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
/**
 * @subpackage  mod_osquicksearchrealhomes
 * @author      Dang Thuc Dam
 * @copyright   Copyright (C) 2007 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

require_once __DIR__ . '/helper.php';
require_once JPATH_ROOT.'/components/com_osproperty/helpers/helper.php';
require_once JPATH_ROOT.'/components/com_osproperty/helpers/route.php';
$doc						= Factory::getDocument();
$needs						= array();
$needs[]					= "property_advsearch";
$needs[]					= "ladvsearch";
$itemid						= OSPRoute::getItemid($needs);
$widthsize					= $params->get('widthsize','715');
$show_category				= $params->get('show_category', 0);
$category_id				= Factory::getApplication()->input->getInt('category_id', 0);
$module_name				= basename(dirname(__FILE__));
$url						= JURI::base(true) . '/modules/' . $module_name . '/asset/';
$doc->addStyleSheet($url . 'css.css');
$show_advancesearchform		= $params->get('show_advancesearchform',1);
$osp_type = $params->get('osp_type',array());
require ModuleHelper::getLayoutPath('mod_osquicksearchrealhomes');
?>
