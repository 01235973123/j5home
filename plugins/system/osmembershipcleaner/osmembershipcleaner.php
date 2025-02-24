<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2024 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Cache\Cache;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\Event;
use Joomla\Event\SubscriberInterface;

/**
 * OS Membership Accounts cleaner Plugin
 *
 * @package        Joomla
 * @subpackage     OS Membership
 */
class plgSystemOSMembershipCleaner extends CMSPlugin implements SubscriberInterface
{
	/**
	 * Application object.
	 *
	 * @var    \Joomla\CMS\Application\CMSApplication
	 */
	protected $app;
	/**
	 * Database object
	 *
	 * @var \Joomla\Database\DatabaseDriver
	 */
	protected $db;

	public static function getSubscribedEvents(): array
	{
		return [
			'onAfterRespond' => 'onAfterRespond',
		];
	}

	/**
	 * Register listeners
	 *
	 * @return void
	 */
	public function registerListeners()
	{
		if (!ComponentHelper::isEnabled('com_osmembership'))
		{
			return;
		}

		parent::registerListeners();
	}

	/**
	 * Clean up incomplete payment subscriptions
	 *
	 * @return void
	 */
	public function onAfterRespond(Event $event): void
	{
		$secretCode = trim($this->params->get('secret_code', ''));

		if ($secretCode && ($this->app->input->getString('secret_code') != $secretCode))
		{
			return;
		}

		$lastRun    = (int) $this->params->get('last_run', 0);
		$numberDays = (int) $this->params->get('number_days', 30) ?: 30;
		$now        = time();
		$cacheTime  = 3600 * (int) $this->params->get('cache_time', 24); // The cleaner process will be run every 1 days

		if (($now - $lastRun) < $cacheTime)
		{
			return;
		}

		//Store last run time
		$db    = $this->db;
		$query = $db->getQuery(true);
		$this->params->set('last_run', $now);
		$params = $this->params->toString();
		$query->clear();
		$query->update('#__extensions')
			->set('params=' . $db->quote($params))
			->where('`element`="osmembershipcleaner"')
			->where('`folder`="system"');

		try
		{
			// Lock the tables to prevent multiple plugin executions causing a race condition
			$db->lockTable('#__extensions');
		}
		catch (Exception $e)
		{
			// If we can't lock the tables it's too risk continuing execution
			return;
		}

		try
		{
			// Update the plugin parameters
			$result = $db->setQuery($query)->execute();
			$this->clearCacheGroups(['com_plugins'], [0, 1]);
		}
		catch (Exception $exc)
		{
			// If we failed to execite
			$db->unlockTables();
			$result = false;
		}

		try
		{
			// Unlock the tables after writing
			$db->unlockTables();
		}
		catch (Exception $e)
		{
			// If we can't lock the tables assume we have somehow failed
			$result = false;
		}

		// Abort on failure
		if (!$result)
		{
			return;
		}

		$query->clear();
		$now = Factory::getDate()->toSql();
		$query->select('id, user_id')
			->from('#__osmembership_subscribers')
			->where('published = 0')
			->where('payment_method NOT LIKE "os_offline%"')
			->where("DATEDIFF('$now', created_date) >= $numberDays");
		$db->setQuery($query);
		$rowPendingSubscribers = $db->loadObjectList();

		if (count($rowPendingSubscribers))
		{
			$subscriberIds = [];

			foreach ($rowPendingSubscribers as $subscriber)
			{
				if ($subscriber->user_id > 0)
				{
					$user = Factory::getUser($subscriber->user_id);

					if ($user->id && $user->block && !$user->authorise('core.admin'))
					{
						$user->delete();
					}
				}

				$subscriberIds[] = $subscriber->id;
			}

			$query->clear()
				->delete('#__osmembership_subscribers')
				->whereIn('id', $subscriberIds);
			$db->setQuery($query);
			$db->execute();
		}
	}

	/**
	 * Clears cache groups. We use it to clear the plugins cache after we update the last run timestamp.
	 *
	 * @param   array  $clearGroups   The cache groups to clean
	 * @param   array  $cacheClients  The cache clients (site, admin) to clean
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	private function clearCacheGroups(array $clearGroups, array $cacheClients = [0, 1])
	{
		foreach ($clearGroups as $group)
		{
			foreach ($cacheClients as $clientId)
			{
				try
				{
					$options = [
						'defaultgroup' => $group,
						'cachebase'    => ($clientId) ? JPATH_ADMINISTRATOR . '/cache' :
							$this->app->get('cache_path', JPATH_SITE . '/cache'),
					];
					$cache   = Cache::getInstance('callback', $options);
					$cache->clean();
				}
				catch (Exception $e)
				{
					// Ignore it
				}
			}
		}
	}
}
