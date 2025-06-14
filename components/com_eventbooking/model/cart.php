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
use Joomla\CMS\User\UserHelper;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;
use OSSolution\EventBooking\Admin\Event\Registration\AfterPaymentSuccess;
use OSSolution\EventBooking\Admin\Event\Registration\AfterStoreRegistrant;

class EventbookingModelCart extends RADModel
{
	use EventbookingModelFilter;

	/**
	 * Add one or multiple events to cart
	 *
	 * @param $data
	 *
	 * @return bool
	 */
	public function processAddToCart($data)
	{
		if (is_array($data['id']))
		{
			$eventIds = $data['id'];
		}
		else
		{
			$eventIds = [$data['id']];
		}

		$eventIds = ArrayHelper::toInteger($eventIds);

		$cart = new EventbookingHelperCart();
		$cart->addEvents($eventIds);

		return true;
	}

	/**
	 * Update cart with new quantities
	 *
	 * @param   array  $eventIds
	 * @param   array  $quantities
	 *
	 * @return bool
	 */
	public function processUpdateCart($eventIds, $quantities)
	{
		$eventIds   = ArrayHelper::toInteger($eventIds);
		$quantities = ArrayHelper::toInteger($quantities);

		$cart = new EventbookingHelperCart();
		$cart->updateCart($eventIds, $quantities);

		return true;
	}

	/**
	 * Remove an event from cart
	 *
	 * @param $id
	 *
	 * @return bool
	 */
	public function removeEvent($id)
	{
		$cart = new EventbookingHelperCart();
		$cart->remove($id);

		return true;
	}

	/**
	 * Process checkout in case customer using shopping cart feature
	 *
	 * @param $data
	 *
	 * @return int
	 * @throws Exception
	 */
	public function processCheckout(&$data)
	{
		$app    = Factory::getApplication();
		$db     = $this->getDbo();
		$query  = $db->getQuery(true);
		$user   = $app->getIdentity();
		$config = EventbookingHelper::getConfig();

		/* @var EventbookingTableRegistrant $row */
		$row                    = $this->getTable('Registrant');
		$data['transaction_id'] = strtoupper(UserHelper::genRandomPassword());
		$cart                   = new EventbookingHelperCart();
		$items                  = $cart->getItems();
		$quantities             = $cart->getQuantities();
		$paymentMethod          = $data['payment_method'] ?? '';

		if (!$user->id && $config->user_registration)
		{
			$userId          = EventbookingHelper::callOverridableHelperMethod('Registration', 'saveRegistration', [$data]);
			$data['user_id'] = $userId;
		}

		$rowFields = EventbookingHelperRegistration::getFormFields(0, 4);
		$form      = new RADForm($rowFields);

		$data = $this->filterFormData($rowFields, $data);

		$form->bind($data);
		$form->handleFieldsDependOnPaymentMethod($paymentMethod);
		$data['collect_records_data'] = true;

		$fees = EventbookingHelper::callOverridableHelperMethod(
			'Registration',
			'calculateCartRegistrationFee',
			[$cart, $form, $data, $config, $paymentMethod],
			'Helper'
		);

		// Save the active language
		if ($app->getLanguageFilter())
		{
			$language = $app->getLanguage()->getTag();
		}
		else
		{
			$language = '*';
		}

		$recordsData = $fees['records_data'];
		$cartId      = 0;

		PluginHelper::importPlugin('eventbooking');

		$userIp = EventbookingHelper::getUserIp();

		$registrationCode = '';

		for ($i = 0, $n = count($items); $i < $n; $i++)
		{
			$eventId = $items[$i];

			$recordData = $recordsData[$eventId];
			$row->bind($data);

			// This is here for backward compatible purpose only
			if (!array_key_exists('tax_rate', $recordData))
			{
				$event         = EventbookingHelperDatabase::getEvent($eventId);
				$row->tax_rate = EventbookingHelperRegistration::calculateEventTaxRate($event);
			}
			else
			{
				$row->tax_rate = $recordData['tax_rate'];
			}

			$row->coupon_id              = $recordData['coupon_id'] ?? 0;
			$row->user_ip                = $userIp;
			$row->total_amount           = $recordData['total_amount'];
			$row->discount_amount        = $recordData['discount_amount'];
			$row->late_fee               = $recordData['late_fee'];
			$row->tax_amount             = $recordData['tax_amount'];
			$row->payment_processing_fee = $recordData['payment_processing_fee'];
			$row->amount                 = $recordData['amount'];
			$row->deposit_amount         = $recordData['deposit_amount'];
			$row->created_by             = $user->id;

			if ($row->deposit_amount > 0 && $row->deposit_amount < $row->amount)
			{
				$row->payment_status = 0;
			}
			else
			{
				$row->payment_status = 1;
			}

			if ($config->collect_member_information_in_cart)
			{
				$row->is_group_billing = 1;
			}

			$row->group_id      = 0;
			$row->published     = 0;
			$row->register_date = gmdate('Y-m-d H:i:s');
			$row->user_id       = $data['user_id'] ?? $user->id;

			$row->number_registrants = $quantities[$i];
			$row->event_id           = $eventId;
			$row->registration_code  = EventbookingHelperRegistration::getRegistrationCode();
			$row->ticket_qrcode      = EventbookingHelperRegistration::getTicketQrCode();

			if ($i == 0)
			{
				$data['registration_code']   = $row->registration_code;
				$row->cart_id                = 0;
				$row->coupon_discount_amount = $fees['coupon_discount_amount'];
				$row->coupon_usage_times     = $fees['coupon_usage_times'] ?? 1;
				$registrationCode            = $row->registration_code;
			}
			else
			{
				$row->cart_id                = $cartId;
				$row->coupon_discount_amount = 0;
			}

			$row->language = $language;

			if ($config->show_subscribe_newsletter_checkbox)
			{
				$row->subscribe_newsletter = empty($data['subscribe_to_newsletter']) ? 0 : 1;
			}
			else
			{
				$row->subscribe_newsletter = 1;
			}

			$row->agree_privacy_policy = 1;

			$params = new Registry($row->params);
			$params->set('fields_fee_amount', $fees['fields_fee_amount'] ?? []);
			$row->params = $params->toString();

			$row->id = 0;
			$row->store();
			$form->storeData($row->id, $data);

			if ($i == 0)
			{
				$cartId = $row->id;
			}

			if ($config->collect_member_information_in_cart)
			{
				$this->storeCartGroupMembers($row, $fees);
			}

			$eventObj = new AfterStoreRegistrant(
				'onAfterStoreRegistrant',
				['row' => $row]
			);

			$app->triggerEvent('onAfterStoreRegistrant', $eventObj);
		}

		/* Accept privacy consent to avoid Joomla requires users to accept it again */
		if (PluginHelper::isEnabled('system', 'privacyconsent') && $row->user_id > 0 && $config->show_privacy_policy_checkbox)
		{
			EventbookingHelperRegistration::acceptPrivacyConsent($row);
		}

		if (!empty($fees['bundle_discount_ids']))
		{
			$query->clear()
				->update('#__eb_discounts')
				->set('used = used + 1')
				->whereIn('id', $fees['bundle_discount_ids']);
			$db->setQuery($query);
			$db->execute();
		}

		$app->getSession()->set('eb_registration_code', $registrationCode);

		if ($fees['amount'] > 0)
		{
			$this->processCartPayment($cartId, $data, $fees, $items);
		}
		else
		{
			$this->completeNonePaymentCartRegistration($cartId);

			return 1;
		}
	}

	/**
	 * Get information of events which user added to cart
	 *
	 * @return array|mixed
	 */
	public function getData()
	{
		$config = EventbookingHelper::getConfig();
		$cart   = new EventbookingHelperCart();
		$rows   = $cart->getEvents();

		if ($config->show_price_including_tax && !$config->get('setup_price'))
		{
			foreach ($rows as $row)
			{
				$taxRate   = $row->tax_rate;
				$row->rate = round($row->rate * (1 + $taxRate / 100), 2);

				if ($config->show_discounted_price)
				{
					$row->discounted_rate = round($row->discounted_rate * (1 + $taxRate / 100), 2);
				}
			}
		}

		return $rows;
	}

	/**
	 * Store group members for cart
	 *
	 * @param   EventbookingTableRegistrant  $row
	 * @param   array                        $fees
	 *
	 * @return  void
	 */
	protected function storeCartGroupMembers($row, $fees)
	{
		$membersForm           = $fees['members_form'];
		$membersTotalAmount    = $fees['members_total_amount'];
		$membersDiscountAmount = $fees['members_discount_amount'];
		$membersTaxAmount      = $fees['members_tax_amount'];
		$membersLateFee        = $fees['members_late_fee'];
		$membersAmount         = $fees['members_amount'];
		$eventId               = $row->event_id;

		for ($i = 0; $i < $row->number_registrants; $i++)
		{
			/* @var EventbookingTableRegistrant $rowMember */
			$rowMember                       = $this->getTable('Registrant');
			$rowMember->group_id             = $row->id;
			$rowMember->transaction_id       = $row->transaction_id;
			$rowMember->event_id             = $row->event_id;
			$rowMember->payment_method       = $row->payment_method;
			$rowMember->user_id              = $row->user_id;
			$rowMember->register_date        = $row->register_date;
			$rowMember->user_ip              = $row->user_ip;
			$rowMember->created_by           = $row->created_by;
			$rowMember->ticket_qrcode        = EventbookingHelperRegistration::getTicketQrCode();
			$rowMember->registration_code    = EventbookingHelperRegistration::getRegistrationCode();
			$rowMember->total_amount         = $membersTotalAmount[$eventId][$i];
			$rowMember->discount_amount      = $membersDiscountAmount[$eventId][$i];
			$rowMember->late_fee             = $membersLateFee[$eventId][$i];
			$rowMember->tax_amount           = $membersTaxAmount[$eventId][$i];
			$rowMember->amount               = $membersAmount[$eventId][$i];
			$rowMember->number_registrants   = 1;
			$rowMember->subscribe_newsletter = $row->subscribe_newsletter;
			$rowMember->agree_privacy_policy = 1;

			/* @var RADForm $memberForm */
			$memberForm = $membersForm[$eventId][$i];
			$memberForm->removeFieldSuffix();

			$memberData = $memberForm->getFormData();

			$memberFields = $memberForm->getFields();
			$memberData   = $this->filterFormData($memberFields, $memberData);

			$rowMember->bind($memberData);
			$rowMember->store();

			//Store members data custom field
			$memberForm->storeData($rowMember->id, $memberData);
		}
	}

	/**
	 * Process payment for cart registration
	 *
	 * @param   int    $cartId
	 * @param   array  $data
	 * @param   array  $fees
	 * @param   array  $items
	 *
	 * @throws Exception
	 */
	protected function processCartPayment($cartId, $data, $fees, $items)
	{
		/* @var EventbookingTableRegistrant $row */
		$row = $this->getTable('Registrant');
		$row->load($cartId);

		require_once JPATH_COMPONENT . '/payments/' . $row->payment_method . '.php';

		$config        = EventbookingHelper::getConfig();
		$fieldSuffix   = EventbookingHelper::getFieldSuffix();
		$paymentMethod = $row->payment_method;

		if ($fees['deposit_amount'] > 0)
		{
			$data['amount'] = $fees['deposit_amount'];
		}
		else
		{
			$data['amount'] = $fees['amount'];
		}

		$replaces = EventbookingHelperRegistration::getRegistrationReplaces($row);

		$db    = $this->getDbo();
		$query = $db->getQuery(true)
			->select($db->quoteName('title' . $fieldSuffix, 'title'))
			->from('#__eb_events')
			->whereIn('id', $items)
			->order('FIND_IN_SET(id, "' . implode(',', $items) . '")');

		$db->setQuery($query);
		$eventTitles         = $db->loadColumn();
		$data['event_title'] = implode(', ', $eventTitles);

		$replaces['event_title'] = $data['event_title'];

		$itemName = Text::_('EB_EVENT_REGISTRATION');

		$itemName = EventbookingHelper::replaceCaseInsensitiveTags($itemName, $replaces);

		$data['item_name'] = $itemName;

		// Validate credit card
		if (!empty($data['x_card_num']) && empty($data['card_type']))
		{
			$data['card_type'] = EventbookingHelperCreditcard::getCardType($data['x_card_num']);
		}

		$query->clear()
			->select('title, params')
			->from('#__eb_payment_plugins')
			->where('name = ' . $db->quote($paymentMethod))
			->where('published = 1');
		$db->setQuery($query);
		$plugin = $db->loadObject();

		if (!$plugin)
		{
			throw new RuntimeException(sprintf('Payment Method %s Not Found', $paymentMethod));
		}

		$params       = new Registry($plugin->params);
		$paymentClass = new $paymentMethod($params);
		$paymentClass->setTitle(Text::_($plugin->title));

		// Convert payment amount to USD if the currency is not supported by payment gateway
		$currency = $config->currency_code;

		if (method_exists($paymentClass, 'getSupportedCurrencies'))
		{
			$currencies = $paymentClass->getSupportedCurrencies();

			if (!in_array($currency, $currencies))
			{
				$data['amount'] = EventbookingHelper::callOverridableHelperMethod('Helper', 'convertAmountToUSD', [$data['amount'], $currency]);
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

		$paymentClass->processPayment($row, $data);
	}

	/**
	 * Finish registration in case no payment is needed
	 *
	 * @param   int  $cartId
	 *
	 * @throws Exception
	 */
	protected function completeNonePaymentCartRegistration($cartId)
	{
		$row = $this->getTable('Registrant');
		$row->load($cartId);
		$row->payment_date = gmdate('Y-m-d H:i:s');
		$row->published    = 1;
		$row->store();

		// Update status of all registrants
		$app    = Factory::getApplication();
		$config = EventbookingHelper::getConfig();
		$db     = $this->getDbo();

		$query = $db->getQuery(true)
			->update('#__eb_registrants')
			->set('published = 1')
			->set('payment_date = ' . $db->quote($row->payment_date))
			->where('cart_id = ' . $row->id);
		$db->setQuery($query)
			->execute();

		$eventObj = new AfterPaymentSuccess(
			'onAfterPaymentSuccess',
			['row' => $row]
		);

		$app->triggerEvent('onAfterPaymentSuccess', $eventObj);

		EventbookingHelper::callOverridableHelperMethod('Mail', 'sendEmails', [$row, $config]);
	}
}
