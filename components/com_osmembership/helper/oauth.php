<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2025 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseDriver;

defined('_JEXEC') or die;

abstract class OSMembershipHelperOauth
{
	/**
	 * Get the valid access token
	 *
	 * @param   string  $vendor
	 *
	 * @return stdClass
	 */
	public static function getAccessToken($vendor)
	{
		/* @var DatabaseDriver $db */
		$db    = Factory::getContainer()->get('db');
		$query = $db->getQuery(true)
			->select('*')
			->from('#__osmembership_accesstokens')
			->where('vendor = ' . $db->quote($vendor))
			->order('id DESC');
		$db->setQuery($query);

		return $db->loadObject();
	}

	/**
	 * Store the token into database
	 *
	 * @param   string  $vendor
	 * @param   string  $token
	 * @param   int     $expireAt
	 *
	 * @return void
	 */
	public static function storeToken($vendor, $token, $expireAt)
	{
		$rowToken = self::getAccessToken($vendor);

		/* @var DatabaseDriver $db */
		$db    = Factory::getContainer()->get('db');
		$query = $db->getQuery(true);

		// Token record already exist
		if ($rowToken)
		{
			// We just need to update the record
			$query->update('#__osmembership_accesstokens')
				->set('token = ' . $db->quote($token))
				->set('expire_at = ' . $expireAt)
				->where('id = ' . $rowToken->id);
			$db->setQuery($query)
				->execute();
		}
		else
		{
			// Insert the record
			$query->insert('#__osmembership_accesstokens')
				->columns(['vendor', 'token', 'expire_at'])
				->values(implode(',', $db->quote([$vendor, $token, $expireAt])));
			$db->setQuery($query)
				->execute();
		}
	}
}