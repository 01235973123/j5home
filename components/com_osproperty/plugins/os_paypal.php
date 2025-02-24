<?php
/**
 * @version		1.1.1
 * @package		Joomla
 * @subpackage	OS Property
 * @author		Dang Thuc Dam
 * @copyright	Copyright (C) 2018 Ossolution Team
 * @license		GNU/GPL, see LICENSE.php
 */
// no direct access
defined( '_JEXEC' ) or die ;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Uri\Uri;

class os_paypal extends OSPPayment {
    /**
     * Constructor functions, init some parameter
     *
     * @param JRegistry $params
     * @param array     $config
     */
    public function __construct($params, $config = array())
    {
        parent::__construct($params, $config);
        $this->mode = $params->get('paypal_mode');
        if ($this->mode)
        {
            $this->url = 'https://www.paypal.com/cgi-bin/webscr';
        }
        else
        {
            $this->url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
        }
        $this->setParameter('business', $params->get('paypal_id'));
        $this->setParameter('rm', 2);
        $this->setParameter('cmd', '_xclick');
        $this->setParameter('no_shipping', 1);
        $this->setParameter('no_note', 1);
        $locale = $params->get('paypal_locale','');

        if ($locale == '')
        {
            if (Multilanguage::isEnabled())
            {
                $locale = Factory::getLanguage()->getTag();
                $locale = str_replace("-","_",$locale);
            }
            else
            {
                $locale = 'en_US';
            }
        }

        $this->setParameter('lc', $locale);
        $this->setParameter('charset', 'utf-8');
    }

    /**
     * Check to see whether this payment gateway support recurring payment
     *
     */
    public function getEnableRecurring()
    {
        return 0;
    }

    /**
     * Process Payment
     *
     * @param object $row
     * @param array  $data
     */
    public function processPayment($row, $data)
    {
        $jinput = Factory::getApplication()->input;
        $Itemid = $jinput->getInt('Itemid');
        $siteUrl = Uri::base() ;
        $this->setParameter('item_name', $data['item_name']);
        $this->setParameter('amount', $data['amount']);
        $this->setParameter('currency_code', $data['currency']);

        $this->setParameter('custom', $row->id);
        $this->setParameter('return', $siteUrl."index.php?option=com_osproperty&task=payment_return&order_id=$row->id&Itemid=".$Itemid);
        $this->setParameter('cancel_return', $siteUrl.'index.php?option=com_osproperty&task=payment_cancel&order_id='.$row->id);
        $this->setParameter('notify_url', $siteUrl.'index.php?option=com_osproperty&task=payment_confirm&payment_method=os_paypal');
        $this->setParameter('address1', $data['address']);
        $this->setParameter('address2', '');
        $this->setParameter('city', $data['city']);
        $this->setParameter('country', $data['country']);
        $this->setParameter('first_name', $data['first_name']);
        $this->setParameter('last_name', $data['last_name']);
        $this->setParameter('state', $data['state']);
        $this->setParameter('zip', $data['zip']);
        $this->setParameter('email', $data['email']) ;
        $this->renderRedirectForm();
    }

    /**
     * Verify payment
     *
     * @return bool
     */
    public function verifyPayment()
    {
        $db = Factory::getDbo();
        $ret = $this->validate();
        if ($ret)
        {
            $id            = $this->notificationData['custom'];
            $transactionId = $this->notificationData['txn_id'];
            $amount        = $this->notificationData['mc_gross'];
            $currency      = $this->notificationData['mc_currency'];

            if ($amount < 0)
            {
                return false;
            }
            require_once JPATH_COMPONENT_ADMINISTRATOR.'/tables/order.php';
            $row = Table::getInstance('Order', 'OspropertyTable');
            $row->load($id);
            if (!$row->id) {
                return false;
            }
            if ($row->order_status != "S")
            {
                $this->onPaymentSuccess($row, $transactionId);
            }
        }
    }

    /**
     * Validate the post data from paypal to our server
     *
     * @return string
     */
    protected function validate()
    {
        if ($this->params->get('use_new_paypal_ipn_verification') && function_exists('curl_init'))
        {
            return $this->validateIPN();
        }
        $this->notificationData = $_POST;

        $hostname = $this->mode ? 'www.paypal.com' : 'www.sandbox.paypal.com';
        $url      = 'ssl://' . $hostname;
        $port     = 443;
        $req      = 'cmd=_notify-validate';

        foreach ($_POST as $key => $value)
        {
            $value = urlencode(stripslashes($value));
            $req .= "&$key=$value";
        }

        $header = '';
        $header .= "POST /cgi-bin/webscr HTTP/1.1\r\n";
        $header .= "Host: $hostname:$port\r\n";
        $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $header .= "Content-Length: " . strlen($req) . "\r\n";
        $header .= "User-Agent: Events Booking\r\n";
        $header .= "Connection: Close\r\n\r\n";

        $errNum   = '';
        $errStr   = '';
        $response = '';
        $fp       = fsockopen($url, $port, $errNum, $errStr, 30);

        if (!$fp)
        {
            $response = 'Could not open SSL connection to ' . $hostname . ':' . $port;
            $this->logGatewayData($response);

            return false;
        }

        fputs($fp, $header . $req);
        while (!feof($fp))
        {
            $response .= fgets($fp, 1024);
        }
        fclose($fp);


        $this->logGatewayData($response);

        if (!$this->mode || stristr($response, "VERIFIED"))
        {
            return true;
        }

        return false;
    }
    /**
     * Validate PayPal IPN using PayPal library
     *
     * @return bool
     */
    protected function validateIPN()
    {
        JLoader::register('PaypalIPN', JPATH_ROOT . '/components/com_osproperty/plugins/paypal/PayPalIPN.php');
        $ipn = new PaypalIPN;
        // Use sandbox URL if test mode is configured
        if (!$this->mode)
        {
            $ipn->useSandbox();
        }
        // Disable use custom certs
        $ipn->usePHPCerts();
        $this->notificationData = $_POST;
        try
        {
            $valid = $ipn->verifyIPN();
            $this->logGatewayData($ipn->getResponse());
            if (!$this->mode || $valid)
            {
                return true;
            }
            return false;
        }
        catch (Exception $e)
        {
            $this->logGatewayData($e->getMessage());
            return false;
        }
    }
}
