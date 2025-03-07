<?php

namespace Omnipay\Migs\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;

/**
 * Migs Purchase Response
 */
class Response extends AbstractResponse
{
    public function __construct(RequestInterface $request, $data)
    {
        if (!is_array($data)) {
            parse_str($data, $data);
        }

        parent::__construct($request, $data);
    }

    public function isSuccessful()
    {
        return "0" === $this->getCode();
    }

    public function getTransactionReference()
    {
        return isset($this->data['vpc_ReceiptNo']) ? $this->data['vpc_ReceiptNo'] : null;
    }

    public function getMessage()
    {
        return isset($this->data['vpc_Message']) ? $this->data['vpc_Message'] : null;
    }
    
    public function getCode()
    {
        return isset($this->data['vpc_TxnResponseCode']) ? $this->data['vpc_TxnResponseCode'] : null;
    }

	public function getTransactionId()
	{
		if (isset($this->data['vpc_MerchTxnRef']))
		{
			$filter = \JFilterInput::getInstance();

			return $filter->clean($this->data['vpc_MerchTxnRef'], 'INT');
		}

		return parent::getTransactionId();
	}
}
