<?php
/**
 * @package            Joomla
 * @subpackage         Membership Pro
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2012 - 2024 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('behavior.core');

OSMembershipHelperPayments::writeJavascriptObjects();

if ($this->row->gross_amount > 0)
{
	$paymentNeeded = true;
}
else
{
	$paymentNeeded = false;
}

Factory::getApplication()->getDocument()->addScriptOptions('hasStripePaymentMethod', $this->hasStripe)
	->addScriptOptions('paymentNeeded', $paymentNeeded)
	->addScriptOptions('selectedState', $selectedState)
	->addScriptOptions('currencyCode', $this->plan->currency ?: $this->config->currency_code)
	->addScript(Uri::root(true) . '/media/com_osmembership/js/site-payment-default.min.js');