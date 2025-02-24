<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2024 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

namespace Joomla\Plugin\Task\MPOfflinePaymentReminder\Extension;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Component\Scheduler\Administrator\Event\ExecuteTaskEvent;
use Joomla\Component\Scheduler\Administrator\Task\Status as TaskStatus;
use Joomla\Component\Scheduler\Administrator\Traits\TaskPluginTrait;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Event\DispatcherInterface;
use Joomla\Event\SubscriberInterface;

defined('_JEXEC') or die;

final class MPOfflinePaymentReminder extends CMSPlugin implements SubscriberInterface
{
	use TaskPluginTrait;
	use DatabaseAwareTrait;

	/**
	 * @var string[]
	 *
	 * @since 4.1.0
	 */
	protected const TASKS_MAP = [
		'mpofflinepaymentreminder.sendOfflinePaymentReminder' => [
			'langConstPrefix' => 'PLG_TASK_MP_OFFLINE_PAYMENT_REMINDER_TASK_SEND_REMINDER',
			'method'          => 'sendOfflinePaymentReminder',
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
	protected function sendOfflinePaymentReminder(ExecuteTaskEvent $event): int
	{
		$bccEmails           = $event->getArgument('params')->bcc_emails ?? '';
		$numberDays          = (int) $event->getArgument('params')->number_days ?? 7;
		$numberSubscriptions = (int) $event->getArgument('params')->number_subscribers ?? 20;

		$db    = $this->getDatabase();
		$query = $db->getQuery(true)
			->select('*')
			->from('#__osmembership_subscribers')
			->where('published = 0')
			->where('payment_method LIKE "os_offline%"')
			->where('offline_payment_reminder_email_sent = 0')
			->where('DATEDIFF(created_date, UTC_DATE()) >= ' . $numberDays);
		$db->setQuery($query, 0, $numberSubscriptions);
		$rows = $db->loadObjectList();
		
		if (count($rows) > 0)
		{
			$ids = [];

			foreach ($rows as $row)
			{
				$ids[] = $row->id;
			}

			\OSMembershipHelper::callOverridableHelperMethod(
				'Mail',
				'sendOfflinePaymentReminder',
				[$rows, $bccEmails]
			);

			$query->clear()
				->update('#__osmembership_subscribers')
				->set('offline_payment_reminder_email_sent = 1')
				->whereIn('id', $ids);
			$db->setQuery($query)
				->execute();
		}

		return TaskStatus::OK;
	}
}