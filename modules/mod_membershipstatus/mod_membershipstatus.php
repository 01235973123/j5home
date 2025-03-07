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

if (!file_exists(JPATH_ROOT . '/components/com_osmembership/osmembership.php'))
{
	return;
}

// Require library + register autoloader
require_once JPATH_ADMINISTRATOR . '/components/com_osmembership/loader.php';

OSMembershipHelper::loadLanguage();

$config = OSMembershipHelper::getConfig();
$userId = Factory::getApplication()->getIdentity()->id;

if ($userId > 0)
{
	/* @var \Joomla\Database\DatabaseDriver $db */
	$db    = Factory::getContainer()->get('db');
	$query = $db->getQuery(true);
	$query->select('*')
		->from('#__osmembership_subscribers')
		->where('is_profile = 1')
		->where('(published >= 1 OR payment_method LIKE "os_offline%")')
		->where('user_id = ' . $userId);
	$db->setQuery($query);
	$rowProfile = $db->loadObject();

	if ($rowProfile)
	{
		$rowSubscriptions = OSMembershipHelperSubscription::getSubscriptionsForMembershipStatusModule($rowProfile->id);

		for ($i = 0, $n = count($rowSubscriptions); $i < $n; $i++)
		{
			$rowSubscription = $rowSubscriptions[$i];

			if ($rowSubscription->subscription_status != 1)
			{
				unset($rowSubscriptions[$i]);
			}
		}
	}
}

require ModuleHelper::getLayoutPath('mod_membershipstatus', 'default');
