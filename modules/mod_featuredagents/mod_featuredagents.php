<?php

/**
 * @package 	mod_featuredagents - Featured agents
 * @version		1.0
 * @created		July 2013

 * @author		Dang Thuc Dam
 * @email		damdt@joomservices.com
 * @website		http://joomdonation.com
 * @copyright	Copyright (C) 2023 Joomdonation. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 */

// no direct access
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;

include_once(JPATH_ROOT."/components/com_osproperty/helpers/helper.php");
include_once(JPATH_ROOT."/components/com_osproperty/helpers/route.php");
$document = Factory::getDocument();
$document->addStyleSheet(JURI::root().'modules/mod_featuredagents/style/style.css');
require_once JPATH_SITE.'/modules/mod_featuredagents/helper.php';
$number_agents = $params->get('number_agents',4);
$sort_by = $params->get('sort_by','name');
$items = modFeaturedAgentsHelper::getList($params);
require ModuleHelper::getLayoutPath('mod_featuredagents');
