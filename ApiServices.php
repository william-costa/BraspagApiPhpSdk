<?php

/**
 * Contains calls to Braspag API.
 *
 * ApiServices description.
 *
 * @version 1.0
 * @author pfernandes
 */
class ApiServices
{
    function __construct(){
        $this->headers = array(
                'MerchantId' => BraspagApiConfig::merchantId,
                'MerchantKey' => BraspagApiConfig::merchantKey
            );
    }
    
    /**
     * Creates a sale
     * @param Sale $sale 
     * @return mixed
     */
    public function CreateSale(Sale $sale){
        $uri = BraspagApiConfig::apiUri . 'sales';        
        $post_data = json_encode($sale);
        
        $response = \Httpful\Request::post($uri)
            ->sendsJson()
            ->addHeaders($this->headers)
            ->body($post_data)
            ->send();
        
        if($response->code == HttpStatus::Ok){            
            $sale->payment->paymentId = $response->body->Payment->PaymentId;
            $sale->payment->authenticationUrl = $response->body->Payment->AuthenticationUrl;
            $sale->payment->authorizationCode = $response->body->Payment->AuthorizationCode;
            $sale->payment->acquirerTransactionId = $response->body->Payment->AcquirerTransactionId;
            $sale->payment->proofOfSale = $response->body->Payment->ProofOfSale;
            $sale->payment->status = $response->body->Payment->Status;
            $sale->payment->reasonCode = $response->body->Payment->ReasonCode;
            $sale->payment->reasonMessage = $response->body->Payment->reasonMessage;
            
            $sale->payment->links = $this->parseLinks($response->body->Payment->Links);
            
            return $sale;
        }elseif($response->code == HttpStatus::BadRequest){          
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
    public function Capture($paymentId, CaptureRequest $captureRequest){        
        $uri = BraspagApiConfig::apiUri . "sales/{$paymentId}/capture"; 
        
        if($captureRequest != null){
            $uri = $uri . "?amount={$captureRequest->amount}&serviceTaxAmount={$captureRequest->serviceTaxAmount}";
        }
        
        $response = \Httpful\Request::put($uri)
            ->sendsJson()
            ->addHeaders($this->headers)
            ->send();
        
        if($response->code == HttpStatus::Ok){    
            
            $captureResponse = new CaptureResponse();
            $captureResponse->status = $response->body->Status;
            $captureResponse->reasonCode = $response->body->ReasonCode;
            $captureResponse->reasonMessage = $response->body->ReasonMessage;
            
            $captureResponse->links = $this->parseLinks($response->body->Links);
            
            return $captureResponse;
            
        }elseif($response->code == HttpStatus::BadRequest){            
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
        
        if($response->code == HttpStatus::Ok){    
            
            $voidResponse = new VoidResponse();
            $voidResponse->status = $response->body->Status;
            $voidResponse->reasonCode = $response->body->ReasonCode;
            $voidResponse->reasonMessage = $response->body->ReasonMessage;
            
            $voidResponse->links = $this->parseLinks($response->body->Links);
            
            return $voidResponse;
            
        }elseif($response->code == HttpStatus::BadRequest){            
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
        $uri = BraspagApiConfig::apiUri . "sales/{$paymentId}"; 
                
        $response = \Httpful\Request::get($uri)
            ->sendsJson()
            ->addHeaders($this->headers)
            ->send();
        
        if($response->code == HttpStatus::Ok){    
            
            $sale = new Sale();
            
            $sale->merchantOrderId = $response->body->MerhcantOrderId;
            $sale->customer = $this->parseCustomer($response->body->Customer);
            $sale->payment = $this->parsePayment($response->body->Payment);
            
            return $sale;
            
        }elseif($response->code == HttpStatus::BadRequest){            
            return $this->getBadRequestErros($response->body);            
        }   
        
        return $response->code;
    }
    
    private function parseLinks($source){
        
        $linkCollection = array();
        
        foreach ($source as $l)
        {
        	$link = new Link();
            $link->href = $l->Href;
            $link->method = $l->Method;
            $link->rel = $l->Rel;
            
            array_push($linkCollection, $link);
        }
        
        return $linkCollection;
    }
       
    private function getBadRequestErros($errors){
        
        $badRequestErrors = array();
      
        foreach ($errors as $e)
        {
            $error = new Error();
            $error->code = $e->Code;
            $error->message = $e->Message;
            
        	array_push($badRequestErrors, $error);
        }  
        
        return $badRequestErrors;
    }
    
    private function parseCustomer($apiCustomer){
        $customer = new Customer();
        $customer->birthDate = $apiCustomer->Birthdate;
        $customer->email = $apiCustomer->Email;
        $customer->identity = $apiCustomer->Identity;
        $customer->identityType = $apiCustomer->IdentityType;
        $customer->name = $apiCustomer->Name;
        
        if($apiCustomer->Address != null){
            $address = new Address();
            $address->city = $apiCustomer->Address->City;
            $address->Complement = $apiCustomer->Address->Complement;
            $address->Country = $apiCustomer->Address->Country;
            $address->District = $apiCustomer->Address->District;
            $address->Number = $apiCustomer->Address->Number;
            $address->State = $apiCustomer->Address->State;
            $address->Street = $apiCustomer->Address->Street;
            $address->ZipCode = $apiCustomer->Address->ZipCode;
            
            $customer->address = $address;
        }
        
        return $customer;
    }
    
    private function parsePayment($apiPayment){
        $payment = new CreditCardPayment();
        
        $payment->paymentId = $apiPayment->PaymentId;
        $payment->authenticationUrl = $apiPayment->AuthenticationUrl;
        $payment->authorizationCode = $apiPayment->AuthorizationCode;
        $payment->acquirerTransactionId = $apiPayment->AcquirerTransactionId;
        $payment->proofOfSale = $apiPayment->ProofOfSale;
        $payment->status = $apiPayment->Status;
        $payment->reasonCode = $apiPayment->ReasonCode;
        $payment->reasonMessage = $apiPayment->reasonMessage;
        $payment->amount = $apiPayment->Amount;
        $payment->carrier = $apiPayment->Carrier;
        $payment->country = $apiPayment->Country;
        $payment->currency = $apiPayment->Currency;
        $payment->installments = $apiPayment->Installments;
        $payment->capture = $apiPayment->Capture;
        $payment->authenticate = $apiPayment->Authenticate;
        $payment->interest = $apiPayment->Interest;
        
        if($apiPayment->CreditCard != null){
            $card = new Card();
            
            $card->brand = $apiPayment->CreditCard->Brand;
            $card->cardNumber = $apiPayment->CreditCard->CardNumber;
            $card->expirationDate = $apiPayment->CreditCard->ExpirationDate;
            $card->holder = $apiPayment->CreditCard->Holder;
            
            $payment->creditCard = $card;
        }
        
        $payment->links = $this->parseLinks($apiPayment->Links);
        
        return $payment;
    }
    
}
