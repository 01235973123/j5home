<?php
/**
 * @package            Joomla
 * @subpackage         Membership Pro
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2012 - 2024 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Cache\Cache;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Database\DatabaseDriver;
use Joomla\Event\Event;
use Joomla\Event\SubscriberInterface;

class plgSystemIcpsNotify extends CMSPlugin implements SubscriberInterface
{
	/**
	 * Application object.
	 *
	 * @var    CMSApplication
	 */
	protected $app;

	/**
	 * Database object.
	 *
	 * @var DatabaseDriver
	 */
	protected $db;

	public static function getSubscribedEvents(): array
	{
		return [
			'onAfterRespond' => 'onAfterRespond',
		];
	}

	/**
	 * Send reminder to registrants
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	public function onAfterRespond(Event $event): void
	{
		if (!$this->canRun())
		{
			return;
		}

		$db    = $this->db;
		$query = $db->getQuery(true);

		if (!trim($this->params->get('trigger_code', '')))
		{
			$now = time();

			//Store last run time
			$this->params->set('last_run', time());
			$params = $this->params->toString();
			$query->update('#__extensions')
				->set('params=' . $db->quote($params))
				->where('`element`="icpsnotify"')
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
		}

		// Require library + register autoloader
		require_once JPATH_ADMINISTRATOR . '/components/com_osmembership/loader.php';

		// Only send notification to subscriptions within the last 48 hours
		$db    = $this->db;
		$now   = $db->quote(Factory::getDate('now', $this->app->get('offset'))->toSql(true));
		$query = $db->getQuery(true)
			->select('*')
			->from('#__osmembership_subscribers')
			->where('published = 0')
			->where('icps_notified = 0')
			->where('payment_method NOT LIKE "os_offline%"')
			->where("TIMESTAMPDIFF(HOUR, created_date, $now) <= 48")
			->where("TIMESTAMPDIFF(MINUTE, created_date, $now) >= 20")
			->order('id');
		$db->setQuery($query, 0, 10);
		$rows = $db->loadObjectList();

		$subscriptions = [];
		$ids           = [];

		foreach ($rows as $row)
		{
			// Special case, without user_id and email, no way to check if he is registered again ir not
			if (!$row->user_id && !$row->email)
			{
				$subscriptions[] = $row;
				continue;
			}

			// Check to see if he has paid for the subscription in a different record
			$query->clear()
				->select('COUNT(*)')
				->from('#__osmembership_subscribers')
				->where('plan_id = ' . (int) $row->plan_id)
				->where('id > ' . $row->id)
				->where('(published = 1 OR payment_method LIKE "os_offline%")');

			if ($row->user_id)
			{
				$query->where('user_id = ' . $row->user_id);
			}
			else
			{
				$query->where('email = ' . $db->quote($row->email));
			}

			$db->setQuery($query);
			$total = $db->loadResult();

			if (!$total)
			{
				$subscriptions[] = $row;
				$ids[]           = $row->id;
			}
		}

		if (count($subscriptions) > 0)
		{
			OSMembershipHelper::callOverridableHelperMethod('Mail', 'sendIncompletePaymentSubscriptionsEmails', [$subscriptions, $this->params]);

			// Mark the notification as sent
			$query->clear()
				->update('#__osmembership_subscribers')
				->set('icps_notified = 1')
				->whereIn('id', $ids);
			$db->setQuery($query)
				->execute();
		}
	}

	/**
	 * Method to check whether this plugin should be run
	 *
	 * @return bool
	 */
	private function canRun()
	{
		// If trigger code is set, we will only process sending reminder from cron job
		if (trim($this->params->get('trigger_code', ''))
			&& trim($this->params->get('trigger_code', '')) != $this->app->input->getString('trigger_code'))
		{
			return false;
		}

		$subject = $this->params->get('subject', '');
		$body    = $this->params->get('message', '');

		if (strlen(trim($subject)) === 0)
		{
			return false;
		}

		if (strlen(trim(strip_tags($body))) === 0)
		{
			return false;
		}

		return true;
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
