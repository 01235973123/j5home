<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2021 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\User\UserFactoryInterface;
use Joomla\Event\Event;
use Joomla\Event\SubscriberInterface;

class plgEventbookingNENotification extends CMSPlugin implements SubscriberInterface
{
	/**
	 * Application object.
	 *
	 * @var    \Joomla\CMS\Application\CMSApplication
	 */
	protected $app;

	/**
	 * Database object.
	 *
	 * @var    \Joomla\Database\DatabaseDriver
	 */
	protected $db;

	/**
	 * @return array
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			'onAfterSaveEvent' => 'onAfterSaveEvent',
		];
	}

	/**
	 * Store setting into database
	 *
	 * @param   Event  $eventObj
	 *
	 * @return void
	 */
	public function onAfterSaveEvent(Event $eventObj): void
	{
		/**
		 * @var EventbookingTableEvent $row
		 * @var array                  $data
		 * @var bool                   $isNew
		 */
		[$row, $data, $isNew] = array_values($eventObj->getArguments());

		// Only send notification for newly created event
		if (!$isNew)
		{
			return;
		}

		// Should we send notification
		if (!$this->needToSendNotification())
		{
			return;
		}

		$userGroups = $this->params->get('user_groups');
		$subject    = $this->params->get('subject');
		$message    = $this->params->get('message');

		// Get list of emails which will receive notification
		$db    = $this->db;
		$query = $db->getQuery(true)
			->select('u.*')
			->from('#__users AS u')
			->where('block = 0')
			->where(
				'u.id IN (SELECT user_id FROM #__user_usergroup_map WHERE group_id  IN (' . implode(',', $userGroups) . '))'
			);
		$db->setQuery($query);
		$users = $db->loadObjectList();

		if ($row->created_by > 0)
		{
			/* @var \Joomla\CMS\User\User $creator */
			$creator = Factory::getContainer()->get(UserFactoryInterface::class)->loadUserById((int) $row->created_by);
		}
		else
		{
			$creator = $this->app->getIdentity();
		}

		$config = EventbookingHelper::getConfig();

		// Build tags which can be used in the email which contains event information
		$replaces = EventbookingHelper::buildEventTags($row, $config);

		$replaces['event_creator_name']     = $creator->name;
		$replaces['event_creator_username'] = $creator->username;
		$replaces['event_creator_email']    = $creator->email;
		$replaces['event_creator_id']       = $creator->id;

		foreach ($replaces as $key => $value)
		{
			$subject = str_ireplace("[$key]", $value, $subject);
			$message = str_ireplace("[$key]", $value, $message);
		}

		$mailer    = EventbookingHelperMail::getMailer($config);
		$logEmails = EventbookingHelperMail::loggingEnabled('new_event_notification_emails', $config);

		foreach ($users as $user)
		{
			$userSubject = $subject;
			$userMessage = $message;

			$userReplaces = [
				'user_id'  => $user->id,
				'username' => $user->username,
				'name'     => $user->name,
				'email'    => $user->email,
			];

			foreach ($userReplaces as $key => $value)
			{
				$userSubject = str_ireplace("[$key]", $value, $userSubject);
				$userMessage = str_ireplace("[$key]", $value, $userMessage);
			}

			EventbookingHelperMail::send(
				$mailer,
				[$user->email],
				$userSubject,
				$userMessage,
				$logEmails,
				1,
				'new_event_notification_emails'
			);

			$mailer->clearAllRecipients();
		}
	}

	/**
	 * Method to check if we should send notification when event is created
	 * depends on app parameter configured in the plugin
	 *
	 * @return bool
	 */
	private function needToSendNotification(): bool
	{
		// If required parameters are not entered, do not send emails
		if (!$this->params->get('subject') || !$this->params->get('user_groups') || !$this->params->get('message'))
		{
			return false;
		}

		if ($this->params->get('app', 'both') == 'both')
		{
			return true;
		}

		if ($this->app->isClient($this->params->get('app')))
		{
			return true;
		}

		return false;
	}
}
