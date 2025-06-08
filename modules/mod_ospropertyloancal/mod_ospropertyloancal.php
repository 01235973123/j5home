<?php
use Joomla\CMS\Helper\ModuleHelper;
/*------------------------------------------------------------------------
# mod_ospropertyloancal.php - mod_oscategoryloancal
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2010 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
global $configClass, $bootstrapHelper;
include_once(JPATH_ROOT."/components/com_osproperty/helpers/helper.php");
include_once(JPATH_ROOT."/components/com_osproperty/helpers/bootstrap.php");
OSPHelper::loadBootstrap();
OSPHelper::loadMedia();
OSPHelper::loadLanguage();
OSPHelper::generateBoostrapVariables();
$configClass			= OSPHelper::loadConfig();
require(ModuleHelper::getLayoutPath('mod_ospropertyloancal'));
