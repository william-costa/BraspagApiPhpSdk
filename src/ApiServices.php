<?php

/**
 * Contains calls to Braspag API.
 *
 * ApiServices description.
 *
 * @version 2.0
 * @author pfernandes
 */
class BraspagApiServices
{
    function __construct(){
        $this->headers = array(
                'MerchantId' => BraspagApiConfig::merchantId,
                'MerchantKey' => BraspagApiConfig::merchantKey
            );

        $this->utils = new BraspagUtils();
    }
    
    /**
     * Creates a sale
    
     * @param Sale $sale 
     * @return mixed
     */
    public function CreateSale(BraspagSale $sale){

        $uri = BraspagApiConfig::apiUri . 'sales'; 

        $request = json_encode($sale, JSON_UNESCAPED_UNICODE);
        
        $response = \Httpful\Request::post($uri)
            ->sendsJson()
            ->addHeaders($this->headers)
            ->body($request)            
            ->send();
        
        if($response->code == BraspagHttpStatus::Created){            
            $responsePayment = $response->body->Payment;

            $sale->payment->paymentId = $responsePayment->PaymentId;
            $sale->payment->status = $responsePayment->Status;
            $sale->payment->reasonCode = $responsePayment->ReasonCode;
            $sale->payment->reasonMessage = $responsePayment->ReasonMessage;
            $sale->payment->currency = $responsePayment->Currency;
            $sale->payment->country = $responsePayment->Country;
            $sale->payment->receivedDate = $this->utils->getResponseValue($responsePayment, 'ReceivedDate');
            $sale->capturedDate = $this->utils->getResponseValue($responsePayment, 'CapturedDate');
            $sale->voidedDate = $this->utils->getResponseValue($responsePayment, 'VoidedDate');
            $sale->capturedAmount = $this->utils->getResponseValue($responsePayment, 'CapturedAmount');
            $sale->capturedAmount = $this->utils->getResponseValue($responsePayment, 'VoidedAmount');
            $sale->payment->providerReturnCode = $this->utils->getResponseValue($responsePayment, 'ProviderReturnCode');
            $sale->payment->providerReturnMessage = $this->utils->getResponseValue($responsePayment, 'ProviderReturnMessage');
            
            if($responsePayment->Type == 'CreditCard' || $responsePayment->Type == 'DebitCard'){
                $sale->payment->authenticationUrl = $this->utils->getResponseValue($responsePayment, 'AuthenticationUrl');
                $sale->payment->authorizationCode = $this->utils->getResponseValue($responsePayment, 'AuthorizationCode');
                $sale->payment->acquirerTransactionId = $this->utils->getResponseValue($responsePayment, 'AcquirerTransactionId');
                $sale->payment->proofOfSale = $this->utils->getResponseValue($responsePayment, 'ProofOfSale');

            }elseif($response->body->Payment->Type == 'Boleto'){
                $sale->payment->url = $this->utils->getResponseValue($responsePayment, 'Url');
                $sale->payment->barCodeNumber = $this->utils->getResponseValue($responsePayment, 'BarCodeNumber');
                $sale->payment->digitableLine = $this->utils->getResponseValue($responsePayment, 'DigitableLine');
                $sale->payment->boletoNumber = $this->utils->getResponseValue($responsePayment, 'BoletoNumber');

            }elseif($response->body->Payment->Type == 'EletronicTransfer'){    
                $sale->payment->url = $this->utils->getResponseValue($responsePayment, 'Url');                

            }            

            $recurrentResponse = $this->utils->getResponseValue($responsePayment, 'RecurrentPayment');

            if($recurrentResponse != null){
                $sale->payment->recurrentPayment->recurrentPaymentId = $this->utils->getResponseValue($recurrentResponse, 'RecurrentPaymentId');
                $sale->payment->recurrentPayment->reasonCode = $recurrentResponse->ReasonCode;
                $sale->payment->recurrentPayment->reasonMessage = $recurrentResponse->ReasonMessage;
                $sale->payment->recurrentPayment->nextRecurrency = $this->utils->getResponseValue($recurrentResponse, 'NextRecurrency');
                $sale->payment->recurrentPayment->startDate = $this->utils->getResponseValue($recurrentResponse, 'StartDate');
                $sale->payment->recurrentPayment->endDate = $this->utils->getResponseValue($recurrentResponse, 'EndDate');
                $sale->payment->recurrentPayment->interval = $this->utils->getResponseValue($recurrentResponse, 'Interval');
                $sale->payment->recurrentPayment->link = $this->parseLink($this->utils->getResponseValue($recurrentResponse, 'Link'));
            }

            $sale->payment->links = $this->parseLinks($response->body->Payment->Links);
            
            return $sale;
        }elseif($response->code == BraspagHttpStatus::BadRequest){          
            return $this->getBadRequestErros($response->body);             
        }  
        
        return $response->code;
    }
    
    /**
     * Captures a pre-authorized payment
     * @param GUID $paymentId 
     * @param CaptureRequest $captureRequest 
     * @return mixed
     */
    public function Capture($paymentId, BraspagCaptureRequest $captureRequest){        
        $uri = BraspagApiConfig::apiUri . "sales/{$paymentId}/capture"; 
        
        if($captureRequest != null){
            $uri = $uri . "?amount={$captureRequest->amount}&serviceTaxAmount={$captureRequest->serviceTaxAmount}";
        }
        
        $response = \Httpful\Request::put($uri)
            ->sendsJson()
            ->addHeaders($this->headers)
            ->send();
        
        if($response->code == BraspagHttpStatus::Ok){    
            
            $captureResponse = new BraspagCaptureResponse();
            $captureResponse->status = $response->body->Status;
            $captureResponse->reasonCode = $response->body->ReasonCode;
            $captureResponse->reasonMessage = $response->body->ReasonMessage;
            
            $captureResponse->links = $this->parseLinks($response->body->Links);
            
            return $captureResponse;
            
        }elseif($response->code == BraspagHttpStatus::BadRequest){            
            return $this->getBadRequestErros($response->body);            
        }   
        
        return $response->code;
    }
    
    /**
     * Void a payment
     * @param GUID $paymentId 
     * @param int $amount 
     * @return mixed
     */
    public function Void($paymentId, $amount){
        $uri = BraspagApiConfig::apiUri . "sales/{$paymentId}/void"; 
        
        if($amount != null){
            $uri = $uri . "?amount={$amount}";
        }
        
        $response = \Httpful\Request::put($uri)
            ->sendsJson()
            ->addHeaders($this->headers)
            ->send();
        
        if($response->code == BraspagHttpStatus::Ok){    
            
            $voidResponse = new BraspagVoidResponse();
            $voidResponse->status = $response->body->Status;
            $voidResponse->reasonCode = $response->body->ReasonCode;
            $voidResponse->reasonMessage = $response->body->ReasonMessage;
            
            $voidResponse->links = $this->parseLinks($response->body->Links);
            
            return $voidResponse;
            
        }elseif($response->code == BraspagHttpStatus::BadRequest){            
            return $this->getBadRequestErros($response->body);            
        }   
        
        return $response->code;
    }    
    
    /**
     * Gets a sale
     * @param GUID $paymentId 
     * @return mixed
     */
    public function Get($paymentId){
        $uri = BraspagApiConfig::apiQueryUri . "sales/{$paymentId}"; 
        $response = \Httpful\Request::get($uri)
            ->sendsJson()
            ->addHeaders($this->headers)
            ->send();
        
        if($response->code == BraspagHttpStatus::Ok){    
            $sale = new BraspagSale();
            $sale->merchantOrderId = $response->body->MerchantOrderId;
            $sale->customer = $this->parseCustomer($response->body->Customer);
            $sale->payment = $this->parsePayment($response->body->Payment);
            return $sale;
            
        }elseif($response->code == BraspagHttpStatus::BadRequest){            
            return $this->getBadRequestErros($response->body);            
        }   
        
        return $response->code;
    }
    
    private function parseLink($source){
        if($source == null) return null;

        $link = new BraspagLink();
        $link->href = $source->Href;
        $link->method = $source->Method;
        $link->rel = $source->Rel;

        return $link;
    }

    private function parseLinks($source){        
        $linkCollection = array();
        
        foreach ($source as $l)
        {
            $link = $this->parseLink($l);            
            array_push($linkCollection, $link);
        }
        
        return $linkCollection;
    }
    
    private function getBadRequestErros($errors){
        
        $badRequestErrors = array();
        
        foreach ($errors as $e)
        {
            $error = new BraspagError();
            $error->code = $e->Code;
            $error->message = $e->Message;
            
            array_push($badRequestErrors, $error);
        }  
        
        return $badRequestErrors;
    }
    
    private function parseCustomer($apiCustomer){
        $customer = new BraspagCustomer();
        $customer->name = $apiCustomer->Name;
        $customer->email = $this->utils->getResponseValue($apiCustomer, 'Email');
        $customer->identity = $this->utils->getResponseValue($apiCustomer, 'Identity');
        $customer->identityType = $this->utils->getResponseValue($apiCustomer, 'IdentityType');
        $customer->birthDate = $this->utils->getResponseValue($apiCustomer, 'Birthdate');
        
        $apiAddress = $this->utils->getResponseValue($apiCustomer, 'Address');
        if($apiAddress != null){
            $address = new BraspagAddress();
            $address->country = $apiAddress->Country;
            $customer->city = $this->utils->getResponseValue($apiAddress, 'City');
            $customer->complement = $this->utils->getResponseValue($apiAddress, 'Complement');
            $customer->district = $this->utils->getResponseValue($apiAddress, 'District');
            $customer->number = $this->utils->getResponseValue($apiAddress, 'Number');
            $customer->state = $this->utils->getResponseValue($apiAddress, 'State');
            $customer->street = $this->utils->getResponseValue($apiAddress, 'Street');
            $customer->zipCode = $this->utils->getResponseValue($apiAddress, 'ZipCode');
            $customer->address = $address;
        }
        
        return $customer;
    }
    
    private function parsePayment($apiPayment){
        $payment = new BraspagPayment();

        if($apiPayment->Type == 'CreditCard'){
            $payment = new BraspagCreditCardPayment();
            $this->parseCreditAndDebitPayment($payment, $apiPayment, $apiPayment->CreditCard);
            
            $payment->capture = $apiPayment->Capture;
            $payment->authenticate = $apiPayment->Authenticate;
            $payment->installments = $apiPayment->Installments;
            
        }elseif($apiPayment->Type == 'DebitCard'){
            $payment = new BraspagDebitCardPayment();
            $this->parseCreditAndDebitPayment($payment, $apiPayment, $apiPayment->DebitCard);

        }elseif($apiPayment->Type == 'Boleto') {
            $payment = new BraspagBoletoPayment();    

            $payment->url = $this->utils->getResponseValue($apiPayment, 'Url');
            $payment->barCodeNumber = $this->utils->getResponseValue($apiPayment, 'BarCodeNumber');
            $payment->digitableLine = $this->utils->getResponseValue($apiPayment, 'DigitableLine');
            $payment->boletoNumber = $this->utils->getResponseValue($apiPayment, 'BoletoNumber');
            
            $payment->instructions = $this->utils->getResponseValue($apiPayment, 'Instructions');
            $payment->expirationDate = $this->utils->getResponseValue($apiPayment, 'ExpirationDate');
            $payment->demonstrative = $this->utils->getResponseValue($apiPayment, 'Demonstrative');
            $payment->assignor = $this->utils->getResponseValue($apiPayment, 'Assignor');
            $payment->address = $this->utils->getResponseValue($apiPayment, 'Address');
            $payment->identification = $this->utils->getResponseValue($apiPayment, 'Identification');

        }elseif($apiPayment->Type == 'EletronicTransfer'){
            $payment->url = $this->utils->getResponseValue($apiPayment, 'Url');
        }
        
        $payment->paymentId = $apiPayment->PaymentId;
        $payment->amount = $apiPayment->Amount;
        $payment->capturedAmount = $this->utils->getResponseValue($apiPayment, 'CapturedAmount');
        $payment->capturedAmount = $this->utils->getResponseValue($apiPayment, 'VoidedAmount');
        $payment->receivedDate = $apiPayment->ReceivedDate;
        $payment->capturedDate = $this->utils->getResponseValue($apiPayment, 'CapturedDate');
        $payment->voidedDate = $this->utils->getResponseValue($apiPayment, 'VoidedDate');
        $payment->country = $apiPayment->Country;
        $payment->currency = $apiPayment->Currency;
        $payment->provider = $apiPayment->Provider;
        $payment->status = $apiPayment->Status;
        $payment->reasonCode = $apiPayment->ReasonCode;
        $payment->reasonMessage = $apiPayment->ReasonMessage;
        $payment->providerReturnCode = $this->utils->getResponseValue($apiPayment, 'ProviderReturnCode');
        $payment->providerReturnMessage = $this->utils->getResponseValue($apiPayment, 'ProviderReturnMessage');
        $payment->returnUrl = $this->utils->getResponseValue($apiPayment, 'ReturnUrl');        
        $payment->links = $this->parseLinks($apiPayment->Links);
        
        return $payment;
    }
    
    private function parseCreditAndDebitPayment($payment, $apiPayment, $card){
        $payment->authenticationUrl = $this->utils->getResponseValue($apiPayment, 'AuthenticationUrl');
        $payment->authorizationCode = $this->utils->getResponseValue($apiPayment, 'AuthorizationCode');
        $payment->acquirerTransactionId = $this->utils->getResponseValue($apiPayment, 'AcquirerTransactionId');
        $payment->proofOfSale = $this->utils->getResponseValue($apiPayment, 'ProofOfSale');
        $payment->eci = $this->utils->getResponseValue($apiPayment, 'Eci');

        $parsedCard = new BraspagCard();
        $parsedCard->brand = $card->Brand;
        $parsedCard->cardNumber = $card->CardNumber;
        $parsedCard->expirationDate = $card->ExpirationDate;
        $parsedCard->holder = $card->Holder;
        $payment->creditCard = $parsedCard;
    }

    /**     
     * Debug Function
     * @param Sale $debug,$title 
     * @return standardoutput
     * @autor interatia
     */
    public function debug($debug,$title="Debug:")
    {
        echo "<hr/>";
        echo "<h2>Start: $title</h2>";
        echo '<textarea cols="100" rows="50">';    
        print_r($debug);
        echo "</textarea>";
        echo "<h2>End: $title</h2>";
        echo "<hr/>";
    }   
}
