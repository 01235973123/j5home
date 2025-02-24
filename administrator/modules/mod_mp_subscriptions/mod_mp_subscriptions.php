<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2024 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;

require_once JPATH_ADMINISTRATOR . '/components/com_osmembership/loader.php';

JLoader::register('OSmembershipModelSubscriptions', JPATH_ADMINISTRATOR . '/components/com_osmembership/model/subscriptions.php');

/* @var Joomla\Registry\Registry $params */
$count = (int) $params->get('count', 10);

/* @var OSMembershipModelSubscriptions $model */
$model = MPFModel::getTempInstance('Subscriptions', 'OSMembershipModel')
	->limitstart(0)
	->limit($count)
	->filter_order('tbl.created_date')
	->filter_order_Dir('DESC');

$rows = $model->getData();

if (count($rows))
{
	$config = OSMembershipHelper::getConfig();

	/* @var \Joomla\Database\DatabaseDriver $db */
	$db     = Factory::getContainer()->get('db');
	$query  = $db->getQuery(true)
		->select('COUNT(*)')
		->from('#__osmembership_fields')
		->where('name = ' . $db->quote('last_name'))
		->where('published = 1');
	$db->setQuery($query);
	$showLastName = $db->loadResult() > 0;

	Factory::getApplication()->getLanguage()->load('com_osmembershipcommon', JPATH_ADMINISTRATOR);
	Factory::getApplication()->getLanguage()->load('com_osmembership', JPATH_ADMINISTRATOR);

	require ModuleHelper::getLayoutPath('mod_mp_subscriptions');
}
