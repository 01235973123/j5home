<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2025 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

namespace Joomla\Plugin\Task\EBHouseKeeping\Extension;

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

final class EBHouseKeeping extends CMSPlugin implements SubscriberInterface
{
	use TaskPluginTrait;
	use DatabaseAwareTrait;

	/**
	 * @var string[]
	 *
	 * @since 4.1.0
	 */
	protected const TASKS_MAP = [
		'ebhoousekeeping.deleteOldInvoicesPDF'                  => [
			'langConstPrefix' => 'PLG_TASK_EBHOUSEKEEPING_TASK_DELETE_OLD_INVOICES_PDF',
			'method'          => 'deleteInvoicesPDF',
		],
		'ebhoousekeeping.deleteTicketsPDF'                      => [
			'langConstPrefix' => 'PLG_TASK_EBHOUSEKEEPING_TASK_DELETE_OLD_TICKETS_PDF',
			'method'          => 'deleteTicketsPDF',
		],
		'ebhoousekeeping.deleteOldCertificates'                 => [
			'langConstPrefix' => 'PLG_TASK_EBHOUSEKEEPING_TASK_DELETE_OLD_CERTIFICATES',
			'method'          => 'deleteCertificatesPDF',
		],
		'ebhoousekeeping.deleteIncompeletePaymentRegistrations' => [
			'langConstPrefix' => 'PLG_TASK_EBHOUSEKEEPING_TASK_DELETE_INCOMPLETE_PAYMENT_REGISTRATIONS',
			'form'            => 'delete_incomplete_payments_registrations',
			'method'          => 'deleteIncompletePaymentRegistrations',
		],
		'ebhoousekeeping.cleanEmailsLog'                        => [
			'langConstPrefix' => 'PLG_TASK_EBHOUSEKEEPING_TASK_CLEAN_EMAILS_LOG',
			'form'            => 'clean_emails_log',
			'method'          => 'cleanEmailsLog',
		],
	];

	/**
	 * Constructor.
	 *
	 * @param   DispatcherInterface  $dispatcher     The dispatcher
	 * @param   array                $config         An optional associative array of configuration settings
	 * @param   string               $rootDirectory  The root directory to look for images
	 *
	 */
	public function __construct(DispatcherInterface $dispatcher, array $config)
	{
		parent::__construct($dispatcher, $config);
	}

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
	 * Delete old invoices PDF to save storage spaces
	 *
	 * @param   ExecuteTaskEvent  $event  The onExecuteTask event
	 *
	 * @return integer  The exit code
	 */
	protected function deleteInvoicesPDF(ExecuteTaskEvent $event): int
	{
		$invoicesPath = JPATH_ROOT . '/media/com_eventbooking/invoices';

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
	 * Delete old invoices PDF to save storage spaces
	 *
	 * @param   ExecuteTaskEvent  $event  The onExecuteTask event
	 *
	 * @return integer  The exit code
	 */
	protected function deleteTicketsPDF(ExecuteTaskEvent $event): int
	{
		$ticketsPath = JPATH_ROOT . '/media/com_eventbooking/tickets';

		$files = Folder::files($ticketsPath, '.pdf', false, true);

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
	protected function deleteCertificatesPDF(ExecuteTaskEvent $event): int
	{
		$certificatesPath = JPATH_ROOT . '/media/com_eventbooking/certificates';

		$files = Folder::files($certificatesPath, '.pdf', false, true);

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
	protected function deleteIncompletePaymentRegistrations(ExecuteTaskEvent $event): int
	{
		$delay = (int) $event->getArgument('params')->delay ?? 10;

		$db = $this->getDatabase();

		$query = $db->getQuery(true)
			->select('id')
			->from('#__eb_registrants')
			->where('published = 0')
			->where('group_id = 0')
			->where('payment_method NOT LIKE "os_offline%"')
			->where('DATEDIFF(UTC_DATE(), register_date) >= ' . $delay)
			->order('id');
		$db->setQuery($query);
		$ids = $db->loadColumn();

		if (count($ids))
		{
			$query->clear()
				->select('id')
				->from('#__eb_registrants')
				->whereIn('group_id', $ids);
			$db->setQuery($query);

			$registrantIds = array_merge($ids, $db->loadColumn());

			$query->clear()
				->delete('#__eb_field_values')
				->whereIn('registrant_id', $registrantIds);
			$db->setQuery($query)
				->execute();

			$query->clear()
				->delete('#__eb_registrant_tickets')
				->whereIn('registrant_id', $registrantIds);
			$db->setQuery($query)
				->execute();

			$query->clear()
				->delete('#__eb_registrants')
				->whereIn('id', $registrantIds);
			$db->setQuery($query)
				->execute();
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
		$db    = $this->getDatabase();
		$query = $db->getQuery(true)
			->delete('#__eb_emails')
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
}