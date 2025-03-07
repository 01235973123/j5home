<?php

namespace Omnipay\TargetPay\Message;

use Omnipay\Common\Message\AbstractRequest as BaseAbstractRequest;

class FetchIssuersRequest extends BaseAbstractRequest
{
    /**
     * @var string
     */
    protected $endpoint = 'https://www.targetpay.com/ideal/getissuers.php?format=xml';

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function sendData($data)
    {
        $httpResponse = $this->httpClient->request('GET', $this->endpoint);

        return $this->response = new FetchIssuersResponse($this, simplexml_load_string($httpResponse->getBody()->getContents()));
    }
}
