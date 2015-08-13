<?php

class BraspagDebitCardPayment extends BraspagPayment
{
    public $serviceTaxAmount;
    public $debitCard;
    public $authenticationUrl;
    public $authorizationCode;
    public $proofOfSale;
    public $acquirerTransactionId;
    public $softDescriptor;
    public $eci;
    
    public function __construct(){
        $this->type = "DebitCard";
        $this->authenticate = BraspagApiConfig::defaultAuthenticate;
        $this->capture = BraspagApiConfig::defaultCapture;
        $this->interest = BraspagApiConfig::defaultInterest;
    }
}
