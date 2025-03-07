<?php

namespace Omnipay\BluePay;

use Omnipay\BluePay\Message\AuthRequest;
use Omnipay\BluePay\Message\CaptureRequest;
use Omnipay\BluePay\Message\RefundRequest;
use Omnipay\BluePay\Message\SaleRequest;
use Omnipay\BluePay\Message\CreateCardRequest;
use Omnipay\BluePay\Message\CreateCardPurchaseRequest;
use Omnipay\Common\AbstractGateway;

/**
 * BluePay Gateway Class
 */
class Gateway extends AbstractGateway
{
    // Override createRequests to pass on the testMode value
    protected function createRequest($class, array $parameters)
    {
        $parameters['testMode'] = $this->getParameter('testMode');
        return parent::createRequest($class, $parameters);
    }

    public function getName()
    {
        return 'BluePay';
    }

    public function getDefaultParameters()
    {
        return [
            'accountId'     => '',
            'secretKey'     => '',
            'developerMode' => false,
            'testMode'      => 0,
        ];
    }

    public function getAccountId()
    {
        return $this->getParameter('accountId');
    }

    public function setAccountId($value)
    {
        return $this->setParameter('accountId', $value);
    }


    public function getSecretKey()
    {
        return $this->getParameter('secretKey');
    }

    public function setSecretKey($value)
    {
        return $this->setParameter('secretKey', $value);
    }


    public function getToken()
    {
        return $this->getParameter('token');
    }

    public function setToken($value)
    {
        return $this->setParameter('token', $value);
    }


    public function getCustomId1()
    {
        return $this->getParameter('customId1');
    }

    public function setCustomId1($value)
    {
        return $this->setParameter('customId1', $value);
    }


    public function getCustomId2()
    {
        return $this->getParameter('customId2');
    }

    public function setCustomId2($value)
    {
        return $this->setParameter('customId2', $value);
    }


    public function getOrderId()
    {
        return $this->getParameter('orderId');
    }

    public function setOrderId($value)
    {
        return $this->setParameter('orderId', $value);
    }


    public function getInvoiceId()
    {
        return $this->getParameter('invoiceId');
    }

    public function setInvoiceId($value)
    {
        return $this->setParameter('invoiceId', $value);
    }


    public function getMemo()
    {
        return $this->getParameter('memo');
    }

    public function setMemo($value)
    {
        return $this->setParameter('memo', $value);
    }


    public function getDeveloperMode()
    {
        return $this->getParameter('developerMode');
    }


    public function setDeveloperMode($value)
    {
        $this->setParameter('developerMode', $value);
    }


    public function authorize(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\BluePay\Message\AuthRequest', $parameters);
    }


    public function capture(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\BluePay\Message\CaptureRequest', $parameters);
    }


    public function refund(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\BluePay\Message\RefundRequest', $parameters);
    }


    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\BluePay\Message\SaleRequest', $parameters);
    }


    public function achPurchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\BluePay\Message\AchSaleRequest', $parameters);
    }

    public function createCard(array $parameters = array())
    {
        if (isset($parameters['action']) && $parameters['action'] == 'Purchase') {
            return $this->createRequest('\Omnipay\BluePay\Message\CreateCardPurchaseRequest', $parameters);
        } else {
            return $this->createRequest('\Omnipay\BluePay\Message\CreateCardRequest', $parameters);
        }
    }
}
