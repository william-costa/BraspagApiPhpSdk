<?php
namespace Braspag\Api\Request;

use Braspag\Api\Request\AbstractSaleRequest;
use Braspag\Environment;
use Braspag\Merchant;
use Braspag\Api\Payment;

class UpdateSaleRequest extends AbstractSaleRequest
{

    private $environment;

    private $type;

    private $serviceTaxAmount;

    private $amount;

    public function __construct($type, Merchant $merchant, Environment $environment)
    {
        parent::__construct($merchant);

        $this->environment = $environment;
        $this->type = $type;
    }

    protected function unserialize($json)
    {
        return Payment::fromJson($json);
    }

    public function execute($paymentId)
    {
        $url = $this->environment->getApiUrl() . 'v2/sales/' . $paymentId . '/' . $this->type;
        $params = [];

        if ($this->amount != null) {
            $params['amount'] = $this->amount;
        }

        if ($this->serviceTaxAmount != null) {
            $params['serviceTaxAmount'] = $this->serviceTaxAmount;
        }

        $url .= '?' . http_build_query($params);

        return $this->sendRequest('PUT', $url);
    }

    public function getServiceTaxAmount()
    {
        return $this->serviceTaxAmount;
    }

    public function setServiceTaxAmount($serviceTaxAmount)
    {
        $this->serviceTaxAmount = $serviceTaxAmount;
        return $this;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }
}