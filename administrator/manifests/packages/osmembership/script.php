<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2024 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Installer\Installer;

class Pkg_OsmembershipInstallerScript
{
	/**
	 * Minimum PHP version
	 */
	const MIN_PHP_VERSION = '7.4.0';

	/**
	 * Minimum Joomla version
	 */
	const MIN_JOOMLA_VERSION = '4.2.0';

	/**
	 * Minimum Membership Pro version to allow update
	 */
	const MIN_MEMBERSHIP_PRO_VERSION = '2.7.0';

	/**
	 * The original version, use for update process
	 *
	 * @var string
	 */
	protected $installedVersion = '2.7.0';

	/**
	 * Perform some check to see if the extension could be installed/updated
	 *
	 * @param   string                                        $type
	 * @param   \Joomla\CMS\Installer\Adapter\PackageAdapter  $parent
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function preflight($type, $parent)
	{
		if (!version_compare(JVERSION, self::MIN_JOOMLA_VERSION, 'ge'))
		{
			Factory::getApplication()->enqueueMessage(
				'Cannot install Membership Pro in a Joomla release prior to ' . self::MIN_JOOMLA_VERSION,
				'error'
			);

			return false;
		}

		if (version_compare(PHP_VERSION, self::MIN_PHP_VERSION, '<'))
		{
			Factory::getApplication()->enqueueMessage(
				'Membership Pro requires PHP ' . self::MIN_PHP_VERSION . '+ to work. Please contact your hosting provider, ask them to update PHP version for your hosting account.',
				'error'
			);

			return false;
		}

		$this->getInstalledVersion();

		if (version_compare($this->installedVersion, self::MIN_MEMBERSHIP_PRO_VERSION, '<'))
		{
			Factory::getApplication()->enqueueMessage(
				'Update from older version than ' . self::MIN_MEMBERSHIP_PRO_VERSION . ' is not supported! You need to update to version 2.26.0 first. Please contact support to get that old Membership Pro version',
				'error'
			);

			return false;
		}

		if (version_compare($this->installedVersion, '3.0.0', '<'))
		{
			$installer = new Installer;

			/* @var \Joomla\Database\DatabaseDriver $db */
			$db    = Factory::getContainer()->get('db');
			$query = $db->getQuery(true);

			$plugins = [
				['osmembership', 'spout'],
			];

			foreach ($plugins as $plugin)
			{
				$query->clear()
					->select('extension_id')
					->from('#__extensions')
					->where($db->quoteName('folder') . ' = ' . $db->quote($plugin[0]))
					->where($db->quoteName('element') . ' = ' . $db->quote($plugin[1]));
				$db->setQuery($query);
				$id = $db->loadResult();

				if ($id)
				{
					try
					{
						$installer->uninstall('plugin', $id, 0);
					}
					catch (\Exception $e)
					{
					}
				}
			}
		}
	}

	/**
	 * Get installed version of the component
	 *
	 * @return void
	 */
	/**
	 * Get installed version of the component
	 *
	 * @return void
	 */
	private function getInstalledVersion()
	{
		/* @var \Joomla\Database\DatabaseDriver $db */
		$db    = Factory::getContainer()->get('db');
		$query = $db->getQuery(true)
			->select('manifest_cache')
			->from('#__extensions')
			->where($db->quoteName('element') . ' = ' . $db->quote('com_osmembership'))
			->where($db->quoteName('type') . ' = ' . $db->quote('component'));
		$db->setQuery($query);
		$manifestCache = $db->loadResult();

		if ($manifestCache)
		{
			$manifest               = json_decode($manifestCache);
			$this->installedVersion = $manifest->version;
		}
	}
}