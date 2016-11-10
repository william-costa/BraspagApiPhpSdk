<?php
namespace Braspag\Api;

use Braspag\Api\BraspagSerializable;

class Payment implements BraspagSerializable
{

    const PAYMENTTYPE_CREDITCARD = 'CreditCard';

    const PAYMENTTYPE_DEBITCARD = 'DebitCard';

    const PAYMENTTYPE_ELECTRONIC_TRANSFER = 'ElectronicTransfer';

    const PAYMENTTYPE_BOLETO = 'Boleto';

    const PROVIDER_BRADESCO = 'Bradesco';

    const PROVIDER_BANCO_DO_BRASIL = 'BancoDoBrasil';

    const PROVIDER_SIMULADO = 'Simulado';

    private $type;

    private $amount;

    private $currency;

    private $country;

    private $provider;

    private $serviceTaxAmount;

    private $installments;

    private $interest;

    private $capture = false;

    private $authenticate = false;

    private $recurrent;

    private $softDescriptor = '';

    private $returnUrl;

    private $extraDataCollection;

    private $creditCard;

    private $acquirerTransactionId;

    private $proofOfSale;

    private $authorizationCode;

    private $paymentId;

    private $receivedDate;

    private $capturedAmount;

    private $capturedDate;

    private $reasonCode;

    private $reasonMessage;

    private $providerReturnCode;

    private $providerReturnMessage;

    private $status;

    private $links;

    private $authenticationUrl;

    private $url;

    private $expirationDate;

    private $boletoNumber;

    private $barCodeNumber;

    private $digitableLine;

    private $address;

    private $recurrentPayment;

    public function __construct($amount = 0, $installments = 1)
    {
        $this->setAmount($amount);
        $this->setInstallments($installments);
    }

    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }

    public function populate(\stdClass $data)
    {
        $this->serviceTaxAmount = isset($data->ServiceTaxAmount) ? $data->ServiceTaxAmount : null;
        $this->installments = isset($data->Installments) ? $data->Installments : null;
        $this->interest = isset($data->Interest) ? $data->Interest : null;
        $this->capture = isset($data->Capture) ? ! ! $data->Capture : false;
        $this->authenticate = isset($data->Authenticate) ? ! ! $data->Authenticate : false;
        $this->recurrent = isset($data->Recurrent) ? ! ! $data->Recurrent : false;

        if (isset($data->RecurrentPayment)) {
            $this->recurrentPayment = new RecurrentPayment(false);
            $this->recurrentPayment->populate($data->RecurrentPayment);
        }

        if (isset($data->CreditCard)) {
            $this->creditCard = new CreditCard();
            $this->creditCard->populate($data->CreditCard);
        }

        $this->proofOfSale = isset($data->ProofOfSale) ? $data->ProofOfSale : null;
        $this->acquirerTransactionId = isset($data->AcquirerTransactionId) ? $data->AcquirerTransactionId : null;
        $this->authorizationCode = isset($data->AuthorizationCode) ? $data->AuthorizationCode : null;
        $this->softDescriptor = isset($data->SoftDescriptor) ? $data->SoftDescriptor : null;
        $this->paymentId = isset($data->PaymentId) ? $data->PaymentId : null;
        $this->type = isset($data->Type) ? $data->Type : null;
        $this->amount = isset($data->Amount) ? $data->Amount : null;
        $this->receivedDate = isset($data->ReceivedDate) ? $data->ReceivedDate : null;
        $this->capturedAmount = isset($data->CapturedAmount) ? $data->CapturedAmount : null;
        $this->capturedDate = isset($data->CapturedDate) ? $data->CapturedDate : null;
        $this->currency = isset($data->Currency) ? $data->Currency : null;
        $this->country = isset($data->Country) ? $data->Country : null;
        $this->provider = isset($data->Provider) ? $data->Provider : null;
        $this->reasonCode = isset($data->ReasonCode) ? $data->ReasonCode : null;
        $this->reasonMessage = isset($data->ReasonMessage) ? $data->ReasonMessage : null;
        $this->status = isset($data->Status) ? $data->Status : null;
        $this->providerReturnCode = isset($data->ProviderReturnCode) ? $data->ProviderReturnCode : null;
        $this->providerReturnMessage = isset($data->ProviderReturnMessage) ? $data->ProviderReturnMessage : null;

        $this->extraDataCollection = isset($data->ExtraDataCollection) ? $data->ExtraDataCollection : [];
        $this->links = isset($data->Links) ? $data->Links : [];
    }

    public static function fromJson($json)
    {
        $payment = new Payment();
        $payment->populate(json_decode($json));

        return $payment;
    }

    public function creditCard($securityCode, $brand)
    {
        $creditCard = new CreditCard();
        $creditCard->setSecurityCode($securityCode);
        $creditCard->setBrand($brand);

        $this->setType(self::PAYMENTTYPE_CREDITCARD);
        $this->setCreditCard($creditCard);

        return $creditCard;
    }

    public function recurrentPayment($authorizeNow = true)
    {
        $recurrentPayment = new RecurrentPayment($authorizeNow);

        $this->setRecurrentPayment($recurrentPayment);

        return $recurrentPayment;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
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

    public function getCurrency()
    {
        return $this->currency;
    }

    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function setCountry($country)
    {
        $this->country = $country;
        return $this;
    }

    public function getProvider()
    {
        return $this->provider;
    }

    public function setProvider($provider)
    {
        $this->provider = $provider;
        return $this;
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

    public function getInstallments()
    {
        return $this->installments;
    }

    public function setInstallments($installments)
    {
        $this->installments = $installments;
        return $this;
    }

    public function getInterest()
    {
        return $this->interest;
    }

    public function setInterest($interest)
    {
        $this->interest = $interest;
        return $this;
    }

    public function getCapture()
    {
        return $this->capture;
    }

    public function setCapture($capture)
    {
        $this->capture = $capture;
        return $this;
    }

    public function getAuthenticate()
    {
        return $this->authenticate;
    }

    public function setAuthenticate($authenticate)
    {
        $this->authenticate = $authenticate;
        return $this;
    }

    public function getRecurrent()
    {
        return $this->recurrent;
    }

    public function setRecurrent($recurrent)
    {
        $this->recurrent = $recurrent;
        return $this;
    }

    public function getSoftDescriptor()
    {
        return $this->softDescriptor;
    }

    public function setSoftDescriptor($softDescriptor)
    {
        $this->softDescriptor = $softDescriptor;
        return $this;
    }

    public function getReturnUrl()
    {
        return $this->returnUrl;
    }

    public function setReturnUrl($returnUrl)
    {
        $this->returnUrl = $returnUrl;
        return $this;
    }

    public function getExtraDataCollection()
    {
        return $this->extraDataCollection;
    }

    public function setExtraDataCollection($extraDataCollection)
    {
        $this->extraDataCollection = $extraDataCollection;
        return $this;
    }

    public function getCreditCard()
    {
        return $this->creditCard;
    }

    public function setCreditCard(CreditCard $creditCard)
    {
        $this->setType(self::PAYMENTTYPE_CREDITCARD);
        $this->creditCard = $creditCard;
        return $this;
    }

    public function getAcquirerTransactionId()
    {
        return $this->acquirerTransactionId;
    }

    public function setAcquirerTransactionId($acquirerTransactionId)
    {
        $this->acquirerTransactionId = $acquirerTransactionId;
        return $this;
    }

    public function getProofOfSale()
    {
        return $this->proofOfSale;
    }

    public function setProofOfSale($proofOfSale)
    {
        $this->proofOfSale = $proofOfSale;
        return $this;
    }

    public function getAuthorizationCode()
    {
        return $this->authorizationCode;
    }

    public function setAuthorizationCode($authorizationCode)
    {
        $this->authorizationCode = $authorizationCode;
        return $this;
    }

    public function getPaymentId()
    {
        return $this->paymentId;
    }

    public function setPaymentId($paymentId)
    {
        $this->paymentId = $paymentId;
        return $this;
    }

    public function getReceivedDate()
    {
        return $this->receivedDate;
    }

    public function setReceivedDate($receivedDate)
    {
        $this->receivedDate = $receivedDate;
        return $this;
    }

    public function getCapturedAmount()
    {
        return $this->capturedAmount;
    }

    public function setCapturedAmount($capturedAmount)
    {
        $this->capturedAmount = $capturedAmount;
        return $this;
    }

    public function getCapturedDate()
    {
        return $this->capturedDate;
    }

    public function setCapturedDate($capturedDate)
    {
        $this->capturedDate = $capturedDate;
        return $this;
    }

    public function getReasonCode()
    {
        return $this->reasonCode;
    }

    public function setReasonCode($reasonCode)
    {
        $this->reasonCode = $reasonCode;
        return $this;
    }

    public function getReasonMessage()
    {
        return $this->reasonMessage;
    }

    public function setReasonMessage($reasonMessage)
    {
        $this->reasonMessage = $reasonMessage;
        return $this;
    }

    public function getProviderReturnCode()
    {
        return $this->providerReturnCode;
    }

    public function setProviderReturnCode($providerReturnCode)
    {
        $this->providerReturnCode = $providerReturnCode;
        return $this;
    }

    public function getProviderReturnMessage()
    {
        return $this->providerReturnMessage;
    }

    public function setProviderReturnMessage($providerReturnMessage)
    {
        $this->providerReturnMessage = $providerReturnMessage;
        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    public function getLinks()
    {
        return $this->links;
    }

    public function setLinks($links)
    {
        $this->links = $links;
        return $this;
    }

    public function getAuthenticationUrl()
    {
        return $this->authenticationUrl;
    }

    public function setAuthenticationUrl($authenticationUrl)
    {
        $this->authenticationUrl = $authenticationUrl;
        return $this;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    public function getExpirationDate()
    {
        return $this->expirationDate;
    }

    public function setExpirationDate($expirationDate)
    {
        $this->expirationDate = $expirationDate;
        return $this;
    }

    public function getBoletoNumber()
    {
        return $this->boletoNumber;
    }

    public function setBoletoNumber($boletoNumber)
    {
        $this->boletoNumber = $boletoNumber;
        return $this;
    }

    public function getBarCodeNumber()
    {
        return $this->barCodeNumber;
    }

    public function setBarCodeNumber($barCodeNumber)
    {
        $this->barCodeNumber = $barCodeNumber;
        return $this;
    }

    public function getDigitableLine()
    {
        return $this->digitableLine;
    }

    public function setDigitableLine($digitableLine)
    {
        $this->digitableLine = $digitableLine;
        return $this;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function setAddress($address)
    {
        $this->address = $address;
        return $this;
    }

    public function getRecurrentPayment()
    {
        return $this->recurrentPayment;
    }

    public function setRecurrentPayment($recurrentPayment)
    {
        $this->recurrentPayment = $recurrentPayment;
        return $this;
    }
}