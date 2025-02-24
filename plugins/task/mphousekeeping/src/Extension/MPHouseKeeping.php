<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2024 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

namespace Joomla\Plugin\Task\MPHouseKeeping\Extension;

use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Component\Scheduler\Administrator\Event\ExecuteTaskEvent;
use Joomla\Component\Scheduler\Administrator\Task\Status as TaskStatus;
use Joomla\Component\Scheduler\Administrator\Traits\TaskPluginTrait;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Event\DispatcherInterface;
use Joomla\Event\SubscriberInterface;

defined('_JEXEC') or die;

final class MPHouseKeeping extends CMSPlugin implements SubscriberInterface
{
	use TaskPluginTrait;
	use DatabaseAwareTrait;

	/**
	 * @var string[]
	 *
	 * @since 4.1.0
	 */
	protected const TASKS_MAP = [
		'mphoousekeeping.deleteOldInvoicesPDF'                  => [
			'langConstPrefix' => 'PLG_TASK_HOUSEKEEPING_TASK_DELETE_OLD_INVOICES_PDF',
			'method'          => 'deleteOldInvoicesPDF',
		],
		'mphoousekeeping.deleteMemberCardsPDF'                  => [
			'langConstPrefix' => 'PLG_TASK_HOUSEKEEPING_TASK_DELETE_OLD_MEMBERS_CARD_PDF',
			'method'          => 'deleteOldMemberCardsPDF',
		],
		'mphoousekeeping.deleteOldSubscriptions'                => [
			'langConstPrefix' => 'PLG_TASK_HOUSEKEEPING_TASK_DELETE_OLD_SUBSCRIPTIONS',
			'form'            => 'delete_old_subscriptions',
			'method'          => 'deleteOldSubscriptions',
		],
		'mphoousekeeping.deleteIncompeletePaymentSubscriptions' => [
			'langConstPrefix' => 'PLG_TASK_HOUSEKEEPING_TASK_DELETE_INCOMPLETE_PAYMENT_SUBSCRIPTIONS',
			'form'            => 'delete_incomplete_payments_subscriptions',
			'method'          => 'deleteIncompletePaymentSubscriptions',
		],
		'mphoousekeeping.cleanEmailLogs'                        => [
			'langConstPrefix' => 'PLG_TASK_HOUSEKEEPING_TASK_CLEAN_EMAILS_LOG',
			'form'            => 'clean_emails_log',
			'method'          => 'cleanEmailsLog',
		],
	];

	/**
	 * @inheritDoc
	 *
	 * @return string[]
	 *
	 * @since 4.1.0
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			'onTaskOptionsList'    => 'advertiseRoutines',
			'onExecuteTask'        => 'standardRoutineHandler',
			'onContentPrepareForm' => 'enhanceTaskItemForm',
		];
	}

	/**
	 * @var boolean
	 * @since 4.1.0
	 */
	protected $autoloadLanguage = true;

	/**
	 * Constructor.
	 *
	 * @param   DispatcherInterface  $dispatcher     The dispatcher
	 * @param   array                $config         An optional associative array of configuration settings
	 * @param   string               $rootDirectory  The root directory to look for images
	 *
	 * @since   4.2.0
	 */
	public function __construct(DispatcherInterface $dispatcher, array $config)
	{
		parent::__construct($dispatcher, $config);
	}

	/**
	 * Delete old invoices PDF to save storage spaces
	 *
	 * @param   ExecuteTaskEvent  $event  The onExecuteTask event
	 *
	 * @return integer  The exit code
	 */
	protected function deleteOldInvoicesPDF(ExecuteTaskEvent $event): int
	{
		$invoicesPath = JPATH_ROOT . '/media/com_osmembership/invoices';

		$files = Folder::files($invoicesPath, '.pdf', false, true);

		foreach ($files as $file)
		{
			if ($this->isFileCreatedMoreThanOneDay($file))
			{
				File::delete($file);
			}
		}

		return TaskStatus::OK;
	}

	/**
	 * Delete old membercards PDF to save storage spaces
	 *
	 * @param   ExecuteTaskEvent  $event  The onExecuteTask event
	 *
	 * @return integer  The exit code
	 */
	protected function deleteOldMemberCardsPDF(ExecuteTaskEvent $event): int
	{
		$membercardsPath = JPATH_ROOT . '/media/com_osmembership/membercards';

		$files = Folder::files($membercardsPath, '.pdf', false, true);

		foreach ($files as $file)
		{
			if ($this->isFileCreatedMoreThanOneDay($file))
			{
				File::delete($file);
			}
		}

		return TaskStatus::OK;
	}

	/**
	 * Delete old subscription records which are older than certain number of days in database
	 *
	 * @param   ExecuteTaskEvent  $event  The onExecuteTask event
	 *
	 * @return integer  The exit code
	 */
	protected function deleteOldSubscriptions(ExecuteTaskEvent $event): int
	{
		$delay = (int) $event->getArgument('params')->delay ?? 8;

		if ($delay > 0)
		{
			$delayInDays = $delay * 365;

			$db    = $this->getDatabase();
			$query = $db->getQuery(true)
				->select('id')
				->from('#__osmembership_subscribers')
				->where('TIMESTAMPDIFF(DAY, created_date, UTC_DATE()) >= ' . $delayInDays)
				->order('id');
			$db->setQuery($query);
			$ids = $db->loadColumn();

			if (count($ids))
			{
				$this->deleteSubscriptions($ids);
			}
		}

		return TaskStatus::OK;
	}

	/**
	 * Delete old subscription records which are older than certain number of days in database
	 *
	 * @param   ExecuteTaskEvent  $event  The onExecuteTask event
	 *
	 * @return integer  The exit code
	 */
	protected function deleteIncompletePaymentSubscriptions(ExecuteTaskEvent $event): int
	{
		$delay = (int) $event->getArgument('params')->delay ?? 10;

		$db = $this->getDatabase();

		$query = $db->getQuery(true)
			->select('id')
			->from('#__osmembership_subscribers')
			->where('published = 0')
			->where('payment_method NOT LIKE "os_offline%"')
			->where('DATEDIFF(UTC_DATE(), created_date) >= ' . $delay)
			->order('id');
		$db->setQuery($query);
		$ids = $db->loadColumn();

		if (count($ids))
		{
			$this->deleteSubscriptions($ids);
		}

		return TaskStatus::OK;
	}

	/**
	 * Clean email logs which are older than pre-configured number of days
	 *
	 * @param   ExecuteTaskEvent  $event  The onExecuteTask event
	 *
	 * @return integer  The exit code
	 */
	protected function cleanEmailsLog(ExecuteTaskEvent $event): int
	{
		$delay = (int) $event->getArgument('params')->delay ?? 10;

		$db = $this->getDatabase();

		$query = $db->getQuery(true)
			->delete('#__osmembership_emails')
			->where('DATEDIFF(UTC_DATE(), sent_at) >= ' . $delay);
		$db->setQuery($query)
			->execute();

		return TaskStatus::OK;
	}

	/**
	 * Method to check if the file was created more than one day ago
	 *
	 * @param   string  $file  Path to the file
	 *
	 * @return bool
	 */
	private function isFileCreatedMoreThanOneDay($file): bool
	{
		$fileCreatedTime = filemtime($file);

		if ($fileCreatedTime === false)
		{
			return false;
		}

		$timeDifference = time() - $fileCreatedTime;

		return $timeDifference > 24 * 60 * 60;
	}

	/**
	 * Delete subscriptions from given IDs
	 *
	 * @param   array  $ids
	 *
	 * @return void
	 */
	private function deleteSubscriptions(array $ids): void
	{
		if (!count($ids))
		{
			return;
		}

		// Require library + register autoloader
		require_once JPATH_ADMINISTRATOR . '/components/com_osmembership/loader.php';

		\JLoader::register('OSMembershipModelSubscription', JPATH_ADMINISTRATOR . '/components/com_osmembership/model/subscription.php');

		/* @var \OSMembershipModelSubscription $model */
		$model = \MPFModel::getTempInstance('Subscription', 'OSMembershipModel');

		$model->delete($ids);
	}
}