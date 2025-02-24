<?php

/**
 * Part of the Ossolution Payment Package
 *
 * @copyright  Copyright (C) 2023 joomdonation.com. All Rights Reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
use Joomla\CMS\Table\Table;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

require_once JPATH_LIBRARIES . '/omnipay/vendor/autoload.php';

use Ossolution\Payment\OmnipayPayment;

/**
 * Payment class which use Omnipay payment class for processing payment
 *
 * @since 1.0
 */
class OSPPaymentOmnipay extends OmnipayPayment
{
	/**
	 * Method to check whether we need to show card type on form for this payment method.
	 * Always return false as when use Omnipay, we don't need card type parameter. It can be detected automatically
	 * from given card number
	 *
	 * @return bool|int
	 */
	public function getCardType()
	{
		return 0;
	}

	/**
	 * Method to check whether we need to show card holder name in the form
	 *
	 * @return bool|int
	 */
	public function getCardHolderName()
	{
		return $this->type;
	}

	/**
	 * Method to check whether we need to show card cvv input on form
	 *
	 * @return bool|int
	 */
	public function getCardCvv()
	{
		return $this->type;
	}

	/**
	 * By default, the payment method won't support recurring donation
	 *
	 * @return int
	 */
	public function getEnableRecurring()
	{
		return 0;
	}

	/**
	 * This method need to be implemented by the payment plugin class. It needs to set url which users will be
	 * redirected to after a successful payment. The url is stored in paymentSuccessUrl property
	 *
	 * @param JTable $row
	 * @param array  $data
	 *
	 * @return void
	 */
	protected function setPaymentSuccessUrl($id, $data = array())
	{
		$Itemid = Factory::getApplication()->input->get->getInt('Itemid', 0);

		$this->paymentSuccessUrl = Route::_('index.php?option=com_osproperty&task=payment_return&order_id='.$id);
	}


	/**
	 * This method need to be implemented by the payment plugin class. It needs to set url which users will be
	 * redirected to when the payment is not success for some reasons. The url is stored in paymentFailureUrl property
	 *
	 * @param int   $id
	 * @param array $data
	 *
	 * @return void
	 */
	protected function setPaymentFailureUrl($id, $data = array())
	{
		if (empty($id))
		{
			$id = Factory::getApplication()->input->getInt('id', 0);
		}

		$this->paymentFailureUrl = Route::_('index.php?option=com_osproperty&task=payment_failure&id='.$id);//Route::_('index.php?option=com_osproperty&view=failure&id=' . $id . '&Itemid=' . $Itemid, false, false);
	}

	/**
	 * This method need to be implemented by the payment plugin class. It is called when a payment success. Usually,
	 * this method will update status of the order to success, trigger onPaymentSuccess event and send notification emails
	 * to administrator(s) and customer
	 *
	 * @param JTable $row
	 * @param string $transactionId
	 *
	 * @return void
	 */
	protected function onPaymentSuccess($row, $transactionId)
	{
	    //require_once JPATH_COMPONENT_ADMINISTRATOR.'/tables/order.php';
        //$row = Table::getInstance('Property','OspropertyTable');
        $row->transaction_id    = $transactionId;
        $row->order_status      = "S";
        $row->payment_made      = 1;
        $row->store();
        OspropertyPayment::paymentComplete($row->id);
	}

	/**
	 * This method need to be implemented by the payment gateway class. It needs to init the JTable order record,
	 * update it with transaction data and then call onPaymentSuccess method to complete the order.
	 *
	 * @param int    $id
	 * @param string $transactionId
	 *
	 * @return mixed
	 */
	protected function onVerifyPaymentSuccess($id, $transactionId)
	{
        require_once JPATH_ADMINISTRATOR."/components/com_osproperty/tables/order.php";
		$row = Table::getInstance('Order', 'OspropertyTable');
		$row->load($id);

		if (!$row->id)
		{
			return false;
		}

		if ($row->published)
		{
			return false;
		}
		$this->onPaymentSuccess($row, $transactionId);
	}

	/**
	 * This method is usually called by payment method class to add additional data
	 * to the request message before that message is actually sent to the payment gateway
	 *
	 * @param \Omnipay\Common\Message\AbstractRequest $request
	 * @param JTable                                  $row
	 * @param array                                   $data
	 **/
	protected function beforeRequestSend($request, $row, $data)
	{
		parent::beforeRequestSend($request, $row, $data);

		// Set return, cancel and notify URL
		$Itemid  = Factory::getApplication()->input->getInt('Itemid', 0);
		$siteUrl = Uri::base();
		$request->setCancelUrl($siteUrl . 'index.php?option=com_osproperty&task=payment_cancel&order_id=' . $row->id . '&Itemid=' . $Itemid);
		$request->setReturnUrl($siteUrl . 'index.php?option=com_osproperty&task=payment_return&id=' . $row->id . '&payment_method=' . $this->name . '&Itemid=' . $Itemid);
		
		$request->setNotifyUrl($siteUrl . 'index.php?option=com_osproperty&task=payment_confirm&payment_method=' . $this->name . '&Itemid=' . $Itemid.'&amp;notify=1');
		$request->setAmount($data['gateway_amount']);
		$request->setCurrency($data['currency']);
		$request->setDescription($data['item_name']);

		if (empty($this->redirectHeading))
		{
			$language    = Factory::getLanguage();
			$languageKey = 'OS_WAIT_' . strtoupper(substr($this->name, 3));
			if ($language->hasKey($languageKey))
			{
				$redirectHeading = Text::_($languageKey);
			}
			else
			{
				$redirectHeading = Text::sprintf('OS_REDIRECT_HEADING', $this->getTitle());
			}

			$this->setRedirectHeading($redirectHeading);
		}
	}
}
