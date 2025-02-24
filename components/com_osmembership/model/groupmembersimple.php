<?php

/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Filter\InputFilter;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\User\UserHelper;
use Joomla\CMS\Table\Table;
use OSSolution\MembershipPro\Admin\Event\GroupMember\GroupMemberAfterSave;
use OSSolution\MembershipPro\Admin\Event\Subscription\AfterStoreSubscription;
use OSSolution\MembershipPro\Admin\Event\Subscription\MembershipActive;
use OSSolution\MembershipPro\Admin\Event\Subscription\MembershipExpire;

class OSMembershipModelGroupmembersimple extends MPFModel
{
	use OSMembershipModelValidationtrait;

	/**
	 * Instantiate the model.
	 *
	 * @param   array  $config  configuration data for the model
	 */
	public function __construct($config = [])
	{
		$config['table'] = '#__osmembership_subscribers';

		parent::__construct($config);

		$this->state->insert('id', 'int', 0);
	}

	/**
	 * Initialize data for group member
	 *
	 *
	 * @return Table
	 */
	public function getData()
	{
		/* @var OSMembershipTableSubscriber $row */
		$row = $this->getTable('Subscriber');

		if ($this->state->id) {
			$row->load($this->state->id);
		}

		return $row;
	}

	/**
	 * Override store function to perform specific saving
	 * @see OSModel::store()
	 */
	public function store(&$data)
	{
		PluginHelper::importPlugin('osmembership');
		$app = Factory::getApplication();
		// Calculate to_date
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		/* @var OSMembershipTableSubscriber $row */
		$row   = $this->getTable('Subscriber');
		$isNew = true;

		$query = $db->getQuery(true)
			->select($db->quoteName('id')) // Chọn cột 'id'
			->from($db->quoteName('#__users')) // Bảng users
			->where($db->quoteName('email') . ' = ' . $db->quote($data['email'])); // Điều kiện email

		// Thực thi query
		$db->setQuery($query);
		$userId = $db->loadResult();

		if ($userId > 0) {
			$data['user_id'] = $userId;
		}

		$data['username'] = $data['email'];

		if (!$data['id'] && $data['username'] && $data['password'] && empty($data['user_id'])) {
			$data['user_id'] = OSMembershipHelperOverrideHelper::saveRegistrationSimple($data);
		}

		if ($data['id']) {
			$isNew = false;
			$row->load($data['id']);
		}

		if (!$isNew && $row->group_admin_id != Factory::getApplication()->getIdentity()->id) {
			throw new Exception(sprintf('You are not allowed to save group member from different group'));
		}

		// Password should not be stored in database
		$row->bind($data, ['password']);

		if ($isNew) {
			$row->user_id           = (int) $row->user_id;
			$row->published         = 1;
			$row->group_admin_id    = Factory::getApplication()->getIdentity()->id;
			$row->created_date      = gmdate('Y-m-d H:i:s');
			$row->from_date         = gmdate('Y-m-d H:i:s');
			$row->is_profile        = 1;
			$row->subscription_code = OSMembershipHelper::getUniqueCodeForField('subscription_code', '#__osmembership_subscribers');

			$query->clear()
				->select('MAX(to_date)')
				->from('#__osmembership_subscribers')
				->where('user_id=' . $row->group_admin_id . ' AND plan_id=' . $row->plan_id . ' AND published = 1');
			$db->setQuery($query);
			$row->to_date = $db->loadResult();
		}

		if (!$row->store()) {
			throw new Exception($row->getError());
		}

		if ($isNew) {
			$row->profile_id = $row->id;
			$row->store();
		}

		$rowFields = OSMembershipHelper::getProfileFields($row->plan_id, false);
		$form      = new MPFForm($rowFields);
		$form->setData($data)
			->bindData()
			->buildFieldsDependency();
		$form->storeData($row->id, $data);

		if ($isNew && $row->user_id) {
			$event = new AfterStoreSubscription(['row' => $row]);
			$app->triggerEvent($event->getName(), $event);

			$event = new MembershipActive(['row' => $row]);
			$app->triggerEvent($event->getName(), $event);

			// Workaround to support [PASSWORD] tag in new group member email
			if (!empty($data['password'])) {
				$row->password = $data['password'];
			}

			OSMembershipHelperMail::sendNewGroupMemberEmail($row);
		}

		$data['id'] = $row->id;

		$event = new GroupMemberAfterSave([
			'row'   => $row,
			'data'  => $data,
			'isNew' => $isNew,
		]);

		$app->triggerEvent($event->getName(), $event);

		return true;
	}

	/**
	 * Delete group member record
	 *
	 * @param   array  $id
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	public function deleteMember($id)
	{
		/* @var OSMembershipTableSubscriber $row */
		$row = $this->getTable('Subscriber');

		if (!$row->load($id)) {
			throw new Exception(sprintf('Invalid Group Member Record %s', $id));
		}

		if ($row->group_admin_id != Factory::getApplication()->getIdentity()->id) {
			throw new Exception(sprintf('You are not allowed to delete group member from different group'));
		}

		$db    = $this->getDbo();
		$query = $db->getQuery(true);
		$query->delete('#__osmembership_field_value')
			->where('subscriber_id = ' . $id);
		$db->setQuery($query);
		$db->execute();

		PluginHelper::importPlugin('osmembership');

		$event = new MembershipExpire(['row' => $row]);

		Factory::getApplication()->triggerEvent($event->getName(), $event);

		// Delete the subscription record
		$row->delete();
	}

	/**
	 * Validate group members data before saving
	 *
	 * @param   MPFInput  $input
	 *
	 * @return array
	 */
	public function validate($input)
	{
		$errors = [];
		$db     = $this->getDbo();
		$query  = $db->getQuery(true);

		$planId        = $input->post->getInt('plan_id');
		$groupMemberId = $input->post->getInt('id');

		$query = $db->getQuery(true)
			->select($db->quoteName('id')) // Chọn cột 'id'
			->from($db->quoteName('#__users')) // Bảng users
			->where($db->quoteName('email') . ' = ' . $db->quote($input->post->getString('email'))); // Điều kiện email

		// Thực thi query
		$db->setQuery($query);
		$userId = $db->loadResult();

		// Select existing user for new group member
		if ($userId > 0) {
			$query->select('COUNT(*)')
				->from('#__osmembership_subscribers')
				->where('user_id = ' . $userId)
				->where('plan_id = ' . $planId);
			$db->setQuery($query);
			$total = $db->loadResult();

			// The selected user is already member of the group, return error
			if ($total) {
				$errors[] = Text::_('OSM_USER_IS_GROUP_MEMBER_ALREADY');
			}
		} elseif (!$groupMemberId) {
			// Validate new user with the entered username and password
			$username  = $input->post->getString('email');
			$email     = $input->post->getString('email');
			$firstName = $input->post->getString('first_name');
			$lastName  = $input->post->getString('last_name');
			$name      = trim($firstName . ' ' . $lastName);

			$errors = array_merge($errors, $this->validateUsername($username));
			$errors = array_merge($errors, $this->validateEmail($email));

			if (InputFilter::getInstance()->clean($name, 'TRIM') == '') {
				$errors[] = Text::_('JLIB_DATABASE_ERROR_PLEASE_ENTER_YOUR_NAME');
			}
		}

		return $errors;
	}
}
