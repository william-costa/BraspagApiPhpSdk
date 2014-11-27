<?php

class Payment
{
    public $paymentId;
    public $type;
    public $amount;
    public $currency;
    public $country;
    public $carrier;
    public $credentials;
    public $extraDatas;
    public $returnUrl;
    public $reasonCode;
    public $reasonMessage;
    public $status;
    public $links;
    
    public function __construct(){
        $this->country = BraspagApiConfig::defaultCountry;
        $this->currency = BraspagApiConfig::defaultCurrency;
    }
}
