<?php

class BraspagDebitCardPayment extends BraspagPayment
{
    public $serviceTaxAmount;
    public $installments;
    public $interest;
    public $capture;
    public $authenticate;
    public $creditCard;
    public $authenticationUrl;
    public $authorizationCode;
    public $proofOfSale;
    public $acquirerTransactionId;
    public $softDescriptor;
    
    public function __construct(){
        $this->type = "DebitCard";
        $this->authenticate = BraspagApiConfig::defaultAuthenticate;
        $this->capture = BraspagApiConfig::defaultCapture;
        $this->interest = BraspagApiConfig::defaultInterest;
    }
}
