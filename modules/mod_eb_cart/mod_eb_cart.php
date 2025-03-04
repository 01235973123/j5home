<?php
/**
 * @package        Joomla
 * @subpackage     Event Booking
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2010 - 2025 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;

/**
 * Variables
 *
 * @var \Joomla\Registry\Registry $params
 */

// Require library + register autoloader
require_once JPATH_ADMINISTRATOR . '/components/com_eventbooking/libraries/rad/bootstrap.php';

EventbookingHelper::loadLanguage();
EventbookingHelper::loadComponentCssForModules();

$Itemid = (int) $params->get('item_id');

$query = [
	'view'   => 'register',
	'layout' => 'cart',
];

$menuItem = EventbookingHelperRoute::findMenuItemByQuery($query);

if ($menuItem)
{
	$Itemid = $menuItem->id;
}

if (!$Itemid)
{
	$Itemid = EventbookingHelper::getItemid();
}

$config = EventbookingHelper::getConfig();
$cart   = new EventbookingHelperCart();
$rows   = $cart->getEvents();

if ($config->show_discounted_price)
{
	foreach ($rows as $row)
	{
		$row->rate = $row->discounted_rate;
	}
}

Factory::getApplication()
	->getDocument()
	->getWebAssetManager()
	->useScript('core')
	->registerAndUseScript('mod-eb-cart', 'media/com_eventbooking/js/mod-eb-cart.min.js', ['version' => EventbookingHelper::getInstalledVersion()]);

require ModuleHelper::getLayoutPath('mod_eb_cart');