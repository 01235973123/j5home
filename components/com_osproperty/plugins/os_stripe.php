<?php
/**
 * @version            3.13.4
 * @package            Joomla
 * @subpackage         OS Property
 * @author             Dang Thuc Dam
 * @copyright          Copyright (C) 2023 joomdonation.com. All Rights Reserved.
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die();
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;

/**
 * Stripe payment plugin for Events Booking
 *
 * @author Tuan Pham Ngoc
 *
 */
class os_stripe extends OSPPaymentOmnipay
{

    protected $omnipayPackage = 'Stripe';

    protected $description;

    public function setDescription($desc){
        $this->description = $desc;
    }

    public function getDescription(){
        return $this->description;
    }
    /**
     * Constructor
     *
     * @param JRegistry $params
     * @param array     $config
     */
    public function __construct($params, $config = array('type' => 1))
    {
        $config['params_map'] = array(
			'apiKey' => 'stripe_api_key'
		);

		$document  = Factory::getDocument();
		$publicKey = $params->get('stripe_public_key');

		
		$document->addScript('https://js.stripe.com/v3/');
		$document->addScriptDeclaration(
			"   var stripe = Stripe('$publicKey');\n
				var elements = stripe.elements();\n
			"
		);

		$config['type'] = 0;

		parent::__construct($params, $config);
    }

    /**
     * Add stripeToken to request message
     *
     * @param \Omnipay\Stripe\Message\AbstractRequest $request
     * @param EventbookingTableRegistrant             $row
     * @param array                                   $data
     */
    protected function beforeRequestSend($request, $row, $data)
    {
        parent::beforeRequestSend($request, $row, $data);

        $request->setToken($data['stripeToken']);

        $metaData['Email']  = $row->email;
        $metaData['Source'] = Text::_('OS_PROPERTY');
        switch ($row->direction){
            case "1":
                    $msg = Text::_('OS_PAYMENT_FOR_LISTING_APPROVAL');
                break;
            case "2":
                    $msg = Text::_('OS_PAYMENT_FOR_FEATURED_UPGRADING');
                break;
            case "3":
                    $msg = Text::_('OS_PAYMENT_FOR_LISTING_APPROVAL');
                break;
        }
        $metaData['Event']  = $msg;

        $request->setMetadata($metaData);
    }
}