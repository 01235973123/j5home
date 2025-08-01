<?php

/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2024 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\User\UserHelper;
use Joomla\Registry\Registry;
use OSSolution\EventBooking\Admin\Event\Registrant\RegistrantStatusChanged;
use OSSolution\EventBooking\Admin\Event\Registration\AfterPaymentSuccess;
use OSSolution\EventBooking\Admin\Event\Registration\AfterStoreRegistrant;
use OSSolution\EventBooking\Admin\Event\Registration\RegistrationCancel;
use OSSolution\EventBooking\Admin\Event\Registration\RegistrationCancelled;

class EventbookingModelRegister extends RADModel
{
	use EventbookingModelFilter;

	/**
	 * Check to see whether registrant entered correct password for private event
	 *
	 * @param $eventId
	 * @param $password
	 *
	 * @return bool
	 */
	public function checkPassword($eventId, $password)
	{
		$event = EventbookingHelperDatabase::getEvent($eventId);

		if ($event && $event->event_password == $password) {
			return true;
		}

		return false;
	}

	/**
	 * Process individual registration
	 *
	 * @param $data
	 *
	 * @return int
	 * @throws Exception
	 */
	public function processIndividualRegistration(&$data)
	{
		$app     = Factory::getApplication();
		$db      = $this->getDbo();
		$query   = $db->getQuery(true);
		$user    = $app->getIdentity();
		$config  = EventbookingHelper::getConfig();
		$eventId = (int) $data['event_id'];
		$event   = EventbookingHelperDatabase::getEvent($eventId);

		EventbookingHelper::overrideGlobalConfig($config, $event);

		/* @var EventbookingTableRegistrant $row */
		$row                       = $this->getTable('Registrant');
		$data['transaction_id']    = strtoupper(UserHelper::genRandomPassword());
		$data['registration_code'] = EventbookingHelperRegistration::getRegistrationCode();
		$data['created_by']        = $user->id;

		if (!$user->id && $config->user_registration) {
			$userId          = EventbookingHelper::callOverridableHelperMethod('Registration', 'saveRegistration', [$data]);
			$data['user_id'] = $userId;
		}

		$row->ticket_qrcode = EventbookingHelperRegistration::getTicketCode();

		if (($event->event_capacity > 0) && ($event->event_capacity <= $event->total_registrants)) {
			$waitingList        = true;
			$typeOfRegistration = 2;
		} else {
			$waitingList        = false;
			$typeOfRegistration = 1;
		}

		$paymentMethod = $data['payment_method'] ?? '';
		$rowFields     = EventbookingHelperRegistration::getFormFields($eventId, 0, null, null, $typeOfRegistration);

		// Filter data
		$data = $this->filterFormData($rowFields, $data);

		$form = new RADForm($rowFields);
		$form->bind($data);
		$form->handleFieldsDependOnPaymentMethod($paymentMethod);

		if ($waitingList) {
			$fees = EventbookingHelper::callOverridableHelperMethod(
				'Registration',
				'calculateIndividualRegistrationFees',
				[$event, $form, $data, $config, ''],
				'Helper'
			);
		} else {
			$fees = EventbookingHelper::callOverridableHelperMethod(
				'Registration',
				'calculateIndividualRegistrationFees',
				[$event, $form, $data, $config, $paymentMethod],
				'Helper'
			);
		}

		$paymentType = isset($data['payment_type']) ? (int) $data['payment_type'] : 0;

		if ($paymentType == 0) {
			$fees['deposit_amount'] = 0;
		}

		$data['total_amount']           = round($fees['total_amount'], 2);
		$data['discount_amount']        = round($fees['discount_amount'], 2);
		$data['late_fee']               = round($fees['late_fee'], 2);
		$data['tax_amount']             = round($fees['tax_amount'], 2);
		$data['amount']                 = round($fees['amount'], 2);
		$data['deposit_amount']         = $fees['deposit_amount'];
		$data['payment_processing_fee'] = $fees['payment_processing_fee'];
		$data['coupon_discount_amount'] = round($fees['coupon_discount_amount'], 2);
		$data['tax_rate']               = $fees['tax_rate'];
		$row->bind($data);
		$row->id = 0;

		if ($config->show_subscribe_newsletter_checkbox) {
			$row->subscribe_newsletter = empty($data['subscribe_to_newsletter']) ? 0 : 1;
		} else {
			$row->subscribe_newsletter = 1;
		}

		$row->agree_privacy_policy = 1;
		$row->group_id             = 0;
		$row->published            = 0;
		$row->register_date        = gmdate('Y-m-d H:i:s');
		$row->number_registrants   = 1;

		if (isset($data['user_id'])) {
			$row->user_id = $data['user_id'];
		} else {
			$row->user_id = $user->id;
		}

		if ($row->deposit_amount > 0) {
			$row->payment_status = 0;
		} else {
			$row->payment_status = 1;
		}

		$row->user_ip = EventbookingHelper::getUserIp();

		//Save the active language
		if ($app->getLanguageFilter()) {
			$row->language = $app->getLanguage()->getTag();
		} else {
			$row->language = '*';
		}

		$couponCode = $data['coupon_code'] ?? null;

		if ($couponCode && $fees['coupon_valid']) {
			$coupon         = $fees['coupon'];
			$row->coupon_id = $coupon->id;

			if (!empty($fees['coupon_usage_times'])) {
				$row->coupon_usage_times = $fees['coupon_usage_times'];
			}
		}

		if (!empty($fees['bundle_discount_ids'])) {
			$query->clear()
				->update('#__eb_discounts')
				->set('used = used + 1')
				->whereIn('id', $fees['bundle_discount_ids']);
			$db->setQuery($query);
			$db->execute();
		}

		if ($waitingList) {
			$row->published      = 3;
			$row->payment_method = 'os_offline';
		}

		$params = new Registry($row->params);
		$params->set('fields_fee_amount', $fees['fields_fee_amount'] ?? []);
		$row->params = $params->toString();

		$row->store();

		// Store custom fields data
		$form->storeData($row->id, $data);

		// Store registrant tickets
		if ($event->has_multiple_ticket_types) {
			$this->storeRegistrantTickets($row, $event, $data);
		}

		/* Accept privacy consent to avoid Joomla requires users to accept it again */
		if (PluginHelper::isEnabled(
			'system',
			'privacyconsent'
		) && $row->user_id > 0 && $config->show_privacy_policy_checkbox) {
			EventbookingHelperRegistration::acceptPrivacyConsent($row);
		}

		$data['event_title'] = $event->title;

		PluginHelper::importPlugin('eventbooking');

		$eventObj = new AfterStoreRegistrant(
			'onAfterStoreRegistrant',
			['row' => $row]
		);

		$app->triggerEvent('onAfterStoreRegistrant', $eventObj);

		if ($row->deposit_amount > 0) {
			$data['amount'] = $row->deposit_amount;
		}

		// Store registration_code into session, use for registration complete code
		$app->getSession()->set('eb_registration_code', $row->registration_code);

		if ($row->amount > 0 && !$waitingList) {
			$this->processRegistrationPayment($row, $event, $data);
		} else {
			if (!$waitingList) {
				$this->completeNonePaymentRegistration($row, $event);

				return 1;
			}

			EventbookingHelper::callOverridableHelperMethod('Mail', 'sendWaitinglistEmail', [$row, $config]);

			return 2;
		}
	}

	/**
	 * Process Group Registration
	 *
	 * @param $data
	 *
	 * @return int
	 * @throws Exception
	 */
	public function processGroupRegistration(&$data)
	{
		$app     = Factory::getApplication();
		$session = $app->getSession();
		$user    = $app->getIdentity();
		$db      = $this->getDbo();
		$query   = $db->getQuery(true);
		$config  = EventbookingHelper::getConfig();
		$eventId = (int) $data['event_id'];
		$event   = EventbookingHelperDatabase::getEvent($eventId);

		EventbookingHelper::overrideGlobalConfig($config, $event);

		/* @var EventbookingTableRegistrant $row */
		$row = $this->getTable('Registrant');

		if (isset($data['number_registrants']) && $data['number_registrants'] > 0) {
			$numberRegistrants = (int) $data['number_registrants'];
		} else {
			$numberRegistrants = (int) $session->get('eb_number_registrants', '');
		}

		$membersData = $session->get('eb_group_members_data', null);

		if ($membersData) {
			$membersData = unserialize($membersData);
		} else {
			$membersData = [];
		}

		$data['number_registrants'] = $numberRegistrants;
		$data['transaction_id']     = strtoupper(UserHelper::genRandomPassword());
		$data['registration_code']  = EventbookingHelperRegistration::getRegistrationCode();
		$data['created_by']         = $user->id;

		if (!$user->id && $config->user_registration) {
			$userId          = EventbookingHelper::callOverridableHelperMethod('Registration', 'saveRegistration', [$data]);
			$data['user_id'] = $userId;
		}

		if (($event->event_capacity > 0) && ($event->event_capacity <= $event->total_registrants)) {
			$waitingList        = true;
			$typeOfRegistration = 2;
		} else {
			$typeOfRegistration = 1;
			$waitingList        = false;
		}

		$rowFields = EventbookingHelperRegistration::getFormFields($eventId, 1, null, null, $typeOfRegistration);

		$data = $this->filterFormData($rowFields, $data);

		$memberFormFields = EventbookingHelperRegistration::getFormFields($eventId, 2, null, null, $typeOfRegistration);
		$form             = new RADForm($rowFields);
		$form->bind($data);

		$paymentMethod = $data['payment_method'] ?? '';
		$form->handleFieldsDependOnPaymentMethod($paymentMethod);

		if ($waitingList) {
			$fees = EventbookingHelper::callOverridableHelperMethod(
				'Registration',
				'calculateGroupRegistrationFees',
				[$event, $form, $data, $config, null],
				'Helper'
			);
		} else {
			$fees = EventbookingHelper::callOverridableHelperMethod(
				'Registration',
				'calculateGroupRegistrationFees',
				[$event, $form, $data, $config, $paymentMethod],
				'Helper'
			);
		}

		$paymentType = (int) @$data['payment_type'];

		if ($paymentType == 0) {
			$fees['deposit_amount'] = 0;
		}

		//The data for group billing record
		$data['total_amount']           = $fees['total_amount'];
		$data['discount_amount']        = $fees['discount_amount'];
		$data['late_fee']               = $fees['late_fee'];
		$data['tax_amount']             = $fees['tax_amount'];
		$data['deposit_amount']         = $fees['deposit_amount'];
		$data['payment_processing_fee'] = $fees['payment_processing_fee'];
		$data['amount']                 = $fees['amount'];
		$data['coupon_discount_amount'] = round($fees['coupon_discount_amount'], 2);
		$data['tax_rate']               = $fees['tax_rate'];

		if (!isset($data['first_name'])) {
			//Get data from first member
			$firstMemberForm = new RADForm($memberFormFields);
			$firstMemberForm->setFieldSuffix(1);
			$firstMemberForm->bind($membersData);
			$firstMemberForm->removeFieldSuffix();
			$data = $data + $firstMemberForm->getFormData();
		}

		$row->bind($data);

		if ($config->show_subscribe_newsletter_checkbox) {
			$row->subscribe_newsletter = empty($data['subscribe_to_newsletter']) ? 0 : 1;
		} else {
			$row->subscribe_newsletter = 1;
		}

		$row->agree_privacy_policy = 1;
		$row->group_id             = 0;
		$row->published            = 0;
		$row->register_date        = gmdate('Y-m-d H:i:s');
		$row->is_group_billing     = 1;

		if (isset($data['user_id'])) {
			$row->user_id = $data['user_id'];
		} else {
			$row->user_id = $user->id;
		}

		if ($row->deposit_amount > 0) {
			$row->payment_status = 0;
		} else {
			$row->payment_status = 1;
		}

		// Save the active language
		if ($app->getLanguageFilter()) {
			$row->language = $app->getLanguage()->getTag();
		} else {
			$row->language = '*';
		}

		// Unique registration code for the registration
		$row->ticket_qrcode = EventbookingHelperRegistration::getTicketCode();

		// Coupon code
		$couponCode = $data['coupon_code'] ?? null;

		if ($couponCode && $fees['coupon_valid']) {
			$coupon         = $fees['coupon'];
			$row->coupon_id = $coupon->id;

			if (!empty($fees['coupon_usage_times'])) {
				$row->coupon_usage_times = $fees['coupon_usage_times'];
			}
		}

		if (!empty($fees['bundle_discount_ids'])) {
			$query->clear()
				->update('#__eb_discounts')
				->set('used = used + 1')
				->whereIn('id', $fees['bundle_discount_ids']);
			$db->setQuery($query);
			$db->execute();
		}

		if ($waitingList) {
			$row->published      = 3;
			$row->payment_method = 'os_offline';
		}

		$row->user_ip = EventbookingHelper::getUserIp();
		$row->id      = 0;

		$params = new Registry($row->params);
		$params->set('fields_fee_amount', $fees['fields_fee_amount'] ?? []);
		$row->params = $params->toString();

		//Clear the coupon session
		$row->store();
		$form->storeData($row->id, $data);

		if ($event->collect_member_information === '') {
			$collectMemberInformation = $config->collect_member_information;
		} else {
			$collectMemberInformation = $event->collect_member_information;
		}

		//Store group members data
		if ($collectMemberInformation) {
			$this->storeGroupMembers($row, $fees);
		}

		/* Accept privacy consent to avoid Joomla requires users to accept it again */
		if (PluginHelper::isEnabled(
			'system',
			'privacyconsent'
		) && $row->user_id > 0 && $config->show_privacy_policy_checkbox) {
			EventbookingHelperRegistration::acceptPrivacyConsent($row);
		}

		$data['event_title'] = $event->title;

		// Trigger onAfterStoreRegistrant event
		PluginHelper::importPlugin('eventbooking');

		$eventObj = new AfterStoreRegistrant(
			'onAfterStoreRegistrant',
			['row' => $row]
		);

		$app->triggerEvent('onAfterStoreRegistrant', $eventObj);

		// Support deposit payment
		if ($row->deposit_amount > 0) {
			$data['amount'] = $row->deposit_amount;
		}

		// Clear session data
		$session->clear('eb_number_registrants');
		$session->clear('eb_group_members_data');
		$session->clear('eb_group_billing_data');

		//Store registration code in session, use it for registration complete page
		$session->set('eb_registration_code', $row->registration_code);

		if ($row->amount > 0 && !$waitingList) {
			$this->processRegistrationPayment($row, $event, $data);
		} else {
			if (!$waitingList) {
				$this->completeNonePaymentRegistration($row, $event);

				return 1;
			}
			if ($row->is_group_billing) {
				EventbookingHelperRegistration::updateGroupRegistrationRecord($row->id);
			}

			EventbookingHelper::callOverridableHelperMethod('Mail', 'sendWaitinglistEmail', [$row, $config]);

			return 2;
		}
	}

	/**
	 * Process payment confirmation, update status of the registration records, sending emails...
	 *
	 * @param   string  $paymentMethod
	 */
	public function paymentConfirm($paymentMethod)
	{
		$method = EventbookingHelperRegistration::loadPaymentMethod($paymentMethod);

		try {
			$method->verifyPayment();
		} catch (RADPaymentException $e) {
			$this->handlePaymentError($e);
		}
	}

	/**
	 * Process registration cancellation
	 *
	 * @return void
	 */
	public function cancelRegistration($id)
	{
		if (!$id) {
			return;
		}

		$app    = Factory::getApplication();
		$db     = $this->getDbo();
		$query  = $db->getQuery(true);
		$config = EventbookingHelper::getConfig();

		/* @var EventbookingTableRegistrant $row */
		$row = $this->getTable('Registrant');
		$row->load($id);

		if (!$row->id) {
			return;
		}

		if (in_array($row->published, [2, 4])) {
			return;
		}

		$published = $row->published;

		//Trigger the cancellation
		PluginHelper::importPlugin('eventbooking');

		$eventObj = new RegistrationCancel(
			'onRegistrationCancel',
			['row' => $row]
		);

		$app->triggerEvent('onRegistrationCancel', $eventObj);

		if ($published == 3) {
			$row->published = 4;
		} else {
			$row->published = 2;
		}

		$row->registration_cancel_date = Factory::getDate()->toSql();

		$row->store();

		// Update status of group members record to cancelled as well
		if ($row->is_group_billing) {
			// We will need to set group members records to be cancelled
			$query->update('#__eb_registrants')
				->set('published = ' . $row->published)
				->set('registration_cancel_date = ' . $db->quote($row->registration_cancel_date))
				->where('group_id = ' . (int) $row->id);
			$db->setQuery($query)
				->execute();
		} elseif ($row->group_id > 0) {
			$groupId = (int) $row->group_id;
			$query->update('#__eb_registrants')
				->set('published = ' . $row->published)
				->set('registration_cancel_date = ' . $db->quote($row->registration_cancel_date))
				->where('group_id = ' . $groupId . ' OR id = ' . $groupId);
			$db->setQuery($query)
				->execute();
		}

		$eventObj = new RegistrationCancelled(
			'onRegistrationCancelled',
			['row' => $row, 'published' => $published]
		);

		$app->triggerEvent('onRegistrationCancelled', $eventObj);

		$eventObj = new RegistrantStatusChanged(
			'onAfterRegistrantStatusChanged',
			['row' => $row, 'oldStatus' => $published, 'newStatus' => $row->published]
		);

		$app->triggerEvent('onAfterRegistrantStatusChanged', $eventObj);

		EventbookingHelper::callOverridableHelperMethod('Mail', 'sendUserCancelRegistrationEmail', [$row, $config]);

		if (in_array($published, [0, 1])) {
			EventbookingHelper::callOverridableHelperMethod(
				'Mail',
				'sendWaitingListNotificationEmail',
				[$row, $config]
			);
		}
	}

	/**
	 * Store ticket types data for registration
	 *
	 * @param   EventbookingTableRegistrant  $row
	 * @param   EventbookingTableEvent       $event
	 * @param   array                        $data
	 *
	 * @return  void
	 */
	protected function storeRegistrantTickets($row, $event, $data)
	{
		$db          = $this->getDbo();
		$query       = $db->getQuery(true);
		$ticketTypes = EventbookingHelperData::getTicketTypes($event->id, true);

		foreach ($ticketTypes as $ticketType) {
			if (!empty($data['ticket_type_' . $ticketType->id])) {
				$quantity = (int) $data['ticket_type_' . $ticketType->id];
				$query->clear()
					->insert('#__eb_registrant_tickets')
					->columns('registrant_id, ticket_type_id, quantity')
					->values("$row->id, $ticketType->id, $quantity");
				$db->setQuery($query)
					->execute();
			}
		}

		$params = new Registry($event->params);

		if ($params->get('ticket_types_collect_members_information')) {
			// Store Members information
			$numberRegistrants = 0;
			$count             = 0;

			foreach ($ticketTypes as $ticketType) {
				if (!empty($data['ticket_type_' . $ticketType->id])) {
					$quantity          = (int) $data['ticket_type_' . $ticketType->id];
					$numberRegistrants += $quantity;

					$memberFormFields = EventbookingHelperRegistration::getFormFields($event->id, 2);

					for ($i = 0; $i < $quantity; $i++) {
						$rowMember                       = $this->getTable('Registrant');
						$rowMember->group_id             = $row->id;
						$rowMember->transaction_id       = $row->transaction_id;
						$rowMember->ticket_qrcode        = EventbookingHelperRegistration::getTicketQRCode();
						$rowMember->event_id             = $row->event_id;
						$rowMember->payment_method       = $row->payment_method;
						$rowMember->payment_status       = $row->payment_status;
						$rowMember->user_id              = $row->user_id;
						$rowMember->register_date        = $row->register_date;
						$rowMember->user_ip              = $row->user_ip;
						$rowMember->created_by           = $row->created_by;
						$rowMember->registration_code    = EventbookingHelperRegistration::getRegistrationCode();
						$rowMember->total_amount         = $ticketType->price;
						$rowMember->discount_amount      = 0;
						$rowMember->late_fee             = 0;
						$rowMember->tax_amount           = 0;
						$rowMember->amount               = $ticketType->price;
						$rowMember->number_registrants   = 1;
						$rowMember->subscribe_newsletter = $row->subscribe_newsletter;
						$rowMember->language             = $row->language;
						$rowMember->agree_privacy_policy = 1;

						$count++;

						$memberForm = new RADForm($memberFormFields);
						$memberForm->setFieldSuffix($count);
						$memberForm->bind($data, true);
						$memberForm->buildFieldsDependency();

						$memberForm->removeFieldSuffix();
						$memberData = $memberForm->getFormData();
						$rowMember->bind($memberData);
						$rowMember->store();

						$memberForm->storeData($rowMember->id, $memberData);

						// Store registrant ticket type information
						$query->clear()
							->insert('#__eb_registrant_tickets')
							->columns('registrant_id, ticket_type_id, quantity')
							->values("$rowMember->id, $ticketType->id, 1");
						$db->setQuery($query)
							->execute();
					}

					$row->is_group_billing   = 1;
					$row->number_registrants = $numberRegistrants;
					$row->store();
				}
			}
		}
	}

	/**
	 * Store group members data
	 *
	 * @param   EventbookingTableRegistrant  $row
	 * @param   array                        $fees
	 */
	protected function storeGroupMembers($row, $fees)
	{
		$membersForm           = $fees['members_form'];
		$membersTotalAmount    = $fees['members_total_amount'];
		$membersDiscountAmount = $fees['members_discount_amount'];
		$membersTaxAmount      = $fees['members_tax_amount'];
		$membersLateFee        = $fees['members_late_fee'];
		$membersAmount         = $fees['members_amount'];

		for ($i = 0; $i < $row->number_registrants; $i++) {
			/* @var EventbookingTableRegistrant $rowMember */
			$rowMember                     = $this->getTable('Registrant');
			$rowMember->group_id           = $row->id;
			$rowMember->transaction_id     = $row->transaction_id;
			$rowMember->ticket_qrcode      = EventbookingHelperRegistration::getTicketQRCode();
			$rowMember->event_id           = $row->event_id;
			$rowMember->payment_method     = $row->payment_method;
			$rowMember->payment_status     = $row->payment_status;
			$rowMember->user_id            = $row->user_id;
			$rowMember->register_date      = $row->register_date;
			$rowMember->user_ip            = $row->user_ip;
			$rowMember->language           = $row->language;
			$rowMember->created_by         = $row->created_by;
			$rowMember->registration_code  = EventbookingHelperRegistration::getRegistrationCode();
			$rowMember->total_amount       = $membersTotalAmount[$i];
			$rowMember->discount_amount    = $membersDiscountAmount[$i];
			$rowMember->late_fee           = $membersLateFee[$i];
			$rowMember->tax_amount         = $membersTaxAmount[$i];
			$rowMember->amount             = $membersAmount[$i];
			$rowMember->number_registrants = 1;

			$rowMember->subscribe_newsletter = $row->subscribe_newsletter;
			$rowMember->agree_privacy_policy = 1;
			$rowMember->language             = $row->language;

			$membersForm[$i]->removeFieldSuffix();
			$memberData = $membersForm[$i]->getFormData();

			// Filter members
			$memberFields = $membersForm[$i]->getFields();
			$memberData   = $this->filterFormData($memberFields, $memberData);

			$rowMember->bind($memberData);
			$rowMember->store();

			//Store members data custom field
			$membersForm[$i]->storeData($rowMember->id, $memberData);
		}
	}

	/**
	 * Process payment for registration
	 *
	 * @param   EventbookingTableRegistrant  $row
	 * @param   EventbookingTableEvent       $event
	 * @param   array                        $data
	 *
	 * @throws RuntimeException
	 */
	protected function processRegistrationPayment($row, $event, $data)
	{
		require_once JPATH_COMPONENT . '/payments/' . $row->payment_method . '.php';

		$config        = EventbookingHelper::getConfig();
		$paymentMethod = $row->payment_method;
		$itemName      = Text::_('EB_EVENT_REGISTRATION');

		$replaces = EventbookingHelperRegistration::getRegistrationReplaces($row);

		$itemName = EventbookingHelper::replaceCaseInsensitiveTags($itemName, $replaces);

		$data['item_name'] = $itemName;

		// Validate credit card
		if (!empty($data['x_card_num']) && empty($data['card_type'])) {
			$data['card_type'] = EventbookingHelperCreditcard::getCardType($data['x_card_num']);
		}

		$db    = $this->getDbo();
		$query = $db->getQuery(true)
			->select('title, params')
			->from('#__eb_payment_plugins')
			->where('name = ' . $db->quote($paymentMethod))
			->where('published = 1');
		$db->setQuery($query);
		$plugin = $db->loadObject();

		if (!$plugin) {
			throw new RuntimeException(sprintf('Payment Method %s Not Found', $paymentMethod), 404);
		}

		$params       = new Registry($plugin->params);
		$paymentClass = new $paymentMethod($params);
		$paymentClass->setTitle(Text::_($plugin->title));

		// Convert payment amount to USD if the currency is not supported by payment gateway
		$currency = $event->currency_code ?: $config->currency_code;

		if (method_exists($paymentClass, 'getSupportedCurrencies')) {
			$currencies = $paymentClass->getSupportedCurrencies();

			if (!in_array($currency, $currencies)) {
				$data['amount'] = EventbookingHelper::callOverridableHelperMethod(
					'Helper',
					'convertAmountToUSD',
					[$data['amount'], $currency]
				);
				$currency       = 'USD';
			}
		}

		$data['currency'] = $currency;

		$country         = empty($data['country']) ? $config->default_country : $data['country'];
		$data['country'] = EventbookingHelper::getCountryCode($country);

		// Store payment amount and payment currency for future validation
		$row->payment_currency = $currency;
		$row->payment_amount   = $data['amount'];
		$row->store();

		try {
			$paymentClass->processPayment($row, $data);
		} catch (RADPaymentException $e) {
			$this->handlePaymentError($e);
		}
	}

	/**
	 * Complete payment process for none payment registration
	 *
	 * @param   EventbookingTableRegistrant  $row
	 * @param   EventbookingTableEvent       $event
	 */
	protected function completeNonePaymentRegistration($row, $event)
	{
		$app    = Factory::getApplication();
		$config = EventbookingHelper::getConfig();

		$row->payment_date = gmdate('Y-m-d H:i:s');

		if ($row->total_amount <= 0) {
			$published = $event->free_event_registration_status;
		} else {
			$published = 1;
		}

		if ($published == 0) {
			$row->payment_method = 'os_offline';
		} else {
			$row->payment_method = '';
		}

		$row->published = $published;

		$row->store();

		// Update ticket members information status
		if ($row->is_group_billing) {
			EventbookingHelperRegistration::updateGroupRegistrationRecord($row->id);
		}

		if ($row->published == 1) {
			$eventObj = new AfterPaymentSuccess(
				'onAfterPaymentSuccess',
				['row' => $row]
			);

			$app->triggerEvent('onAfterPaymentSuccess', $eventObj);
		}

		EventbookingHelper::callOverridableHelperMethod('Mail', 'sendEmails', [$row, $config]);
	}

	/**
	 * Redirect to payment failure page when error happens
	 *
	 * @param   RADPaymentException  $e
	 *
	 * @return void
	 */
	protected function handlePaymentError(RADPaymentException $e): void
	{
		// Redirect to payment failure page
		$app    = Factory::getApplication();
		$Itemid = $app->getInput()->getInt('Itemid');
		$url    = Route::_('index.php?option=com_eventbooking&view=failure&Itemid=' . $Itemid, false);
		$app->getSession()->set('omnipay_payment_error_reason', $e->getMessage());
		$app->redirect($url);
	}
}
