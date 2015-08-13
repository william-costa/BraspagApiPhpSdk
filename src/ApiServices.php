<?php

/**
 * Contains calls to Braspag API.
 *
 * ApiServices description.
 *
 * @version 1.0
 * @author pfernandes
 */
class BraspagApiServices
{
    function __construct(){
        $this->headers = array(
                'MerchantId' => BraspagApiConfig::merchantId,
                'MerchantKey' => BraspagApiConfig::merchantKey
            );
    }
    
    /**
     * Creates a sale
     * Boleto Implements: @autor interatia
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
            $sale->payment->paymentId = $response->body->Payment->PaymentId;
            (property_exists($response->body->Payment,'AuthenticationUrl'))?($sale->payment->authenticationUrl = $response->body->Payment->AuthenticationUrl):('');
            (property_exists($response->body->Payment,'AuthorizationCode'))?($sale->payment->authorizationCode = $response->body->Payment->AuthorizationCode):('');
            (property_exists($response->body->Payment,'AcquirerTransactionId'))?($sale->payment->acquirerTransactionId = $response->body->Payment->AcquirerTransactionId):('');
            (property_exists($response->body->Payment,'ProofOfSale'))?($sale->payment->proofOfSale = $response->body->Payment->ProofOfSale):('');
            (property_exists($response->body->Payment,'Status'))?($sale->payment->status = $response->body->Payment->Status):('');
            (property_exists($response->body->Payment,'ReasonCode'))?($sale->payment->reasonCode = $response->body->Payment->ReasonCode):('');
            (property_exists($response->body->Payment,'ReasonMessage'))?($sale->payment->reasonMessage = $response->body->Payment->ReasonMessage):('');
            (property_exists($response->body->Payment,'ProviderReturnCode'))?($sale->payment->providerReturnCode = $response->body->Payment->ProviderReturnCode):('');
            (property_exists($response->body->Payment,'ProviderReturnMessage'))?($sale->payment->providerReturnMessage = $response->body->Payment->ProviderReturnMessage):('');
            (property_exists($response->body->Payment,'Currency'))?($sale->payment->currency = $response->body->Payment->Currency):('');
            (property_exists($response->body->Payment,'Country'))?($sale->payment->country = $response->body->Payment->Country):('');
            
            if($response->body->Payment->Type == 'Boleto'){
                unset($sale->payment->address);
                unset($sale->payment->boletoNumber);
                unset($sale->payment->assignor);
                unset($sale->payment->demonstrative);
                unset($sale->payment->expirationDate);
                unset($sale->payment->identification);
                unset($sale->payment->instructions);
                (property_exists($response->body->Payment,'Url'))?($sale->payment->url = $response->body->Payment->Url):('');
                (property_exists($response->body->Payment,'BarCodeNumber'))?($sale->payment->barcodeNumber = $response->body->Payment->BarCodeNumber):('');
                (property_exists($response->body->Payment,'DigitableLine'))?($sale->payment->digitableLine = $response->body->Payment->DigitableLine):('');            
                (property_exists($response->body->Payment,'BoletoNumber'))?($sale->payment->boletoNumber = $response->body->Payment->BoletoNumber):('');
                (property_exists($response->body->Payment,'Address'))?($sale->payment->address = $response->body->Payment->Address):('');
                (property_exists($response->body->Payment,'Assignor'))?($sale->payment->assignor = $response->body->Payment->Assignor):('');
                (property_exists($response->body->Payment,'Identification'))?($sale->payment->identification = $response->body->Payment->Identification):('');
            }elseif($response->body->Payment->Type == 'EletronicTransfer'){                
                (property_exists($response->body->Payment,'Url'))?($sale->payment->url = $response->body->Payment->Url):('');
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
    
    private function parseLinks($source){
        
        $linkCollection = array();
        
        foreach ($source as $l)
        {
            $link = new BraspagLink();
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
            $error = new BraspagError();
            $error->code = $e->Code;
            $error->message = $e->Message;
            
            array_push($badRequestErrors, $error);
        }  
        
        return $badRequestErrors;
    }
    
    private function parseCustomer($apiCustomer){
        $customer = new BraspagCustomer();
        (property_exists($apiCustomer,'Name'))?($customer->name = $apiCustomer->Name):('');
        (property_exists($apiCustomer,'Email'))?($customer->email = $apiCustomer->Email):('');
        (property_exists($apiCustomer,'Identity'))?($customer->identity = $apiCustomer->Identity):('');
        (property_exists($apiCustomer,'IdentityType'))?($customer->identityType = $apiCustomer->IdentityType):('');
        (property_exists($apiCustomer,'Birthdate'))?($customer->birthDate = $apiCustomer->Birthdate):('');
        
        if($apiCustomer->Address != null){
            $address = new BraspagAddress();
            (property_exists($apiCustomer->Address,'City'))?($address->city = $apiCustomer->Address->City):('');
            (property_exists($apiCustomer->Address,'Complement'))?($address->complement = $apiCustomer->Address->Complement ):('');
            (property_exists($apiCustomer->Address,'Country'))?($address->country = $apiCustomer->Address->Country):('');
            (property_exists($apiCustomer->Address,'District'))?($address->district = $apiCustomer->Address->District):('');
            (property_exists($apiCustomer->Address,'Number'))?($address->number = $apiCustomer->Address->Number):('');
            (property_exists($apiCustomer->Address,'State'))?($address->state = $apiCustomer->Address->State):('');
            (property_exists($apiCustomer->Address,'Street'))?($address->street = $apiCustomer->Address->Street):('');
            (property_exists($apiCustomer->Address,'ZipCode'))?($address->zipCode = $apiCustomer->Address->ZipCode):('');
            $customer->address = $address;
        }
        
        return $customer;
    }
    
    private function parsePayment($apiPayment){
        
        if(property_exists($apiPayment,'BarCodeNumber')) {
            $payment = new BraspagBoletoPayment();    
            
            $boleto = new BraspagBoleto();
            (property_exists($apiPayment,'Instructions'))?($boleto->instructions = $apiPayment->Instructions):('');
            (property_exists($apiPayment,'ExpirationDate'))?($boleto->expirationDate = $apiPayment->ExpirationDate):('');
            (property_exists($apiPayment,'Demonstrative'))?($boleto->demonstrative = $apiPayment->Demonstrative):('');
            (property_exists($apiPayment,'Url'))?($boleto->url = $apiPayment->Url):('');
            (property_exists($apiPayment,'BoletoNumber'))?($boleto->boletoNumber = $apiPayment->BoletoNumber):('');
            (property_exists($apiPayment,'BarCodeNumber'))?($boleto->barcodeNumber = $apiPayment->BarCodeNumber):('');
            (property_exists($apiPayment,'DigitableLine'))?($boleto->digitableLine = $apiPayment->DigitableLine):('');
            (property_exists($apiPayment,'Assignor'))?($boleto->assignor = $apiPayment->Assignor):('');
            (property_exists($apiPayment,'Address'))?($boleto->address = $apiPayment->Address):('');
            (property_exists($apiPayment,'Identification'))?($boleto->identification = $apiPayment->Identification):('');
            $payment->boleto = $boleto;
            
        }
        
        if(property_exists($apiPayment,'CreditCard')){
            $payment = new BraspagCreditCardPayment();
            $payment->installments = $apiPayment->Installments;
            
            $card = new BraspagCard();
            $card->brand = $apiPayment->CreditCard->Brand;
            $card->cardNumber = $apiPayment->CreditCard->CardNumber;
            $card->expirationDate = $apiPayment->CreditCard->ExpirationDate;
            $card->holder = $apiPayment->CreditCard->Holder;
            $payment->creditCard = $card;

        }
        
        $payment->paymentId = $apiPayment->PaymentId;
        
        (property_exists($apiPayment,'AuthenticationUrl'))?($payment->authenticationUrl = $apiPayment->AuthenticationUrl):('');
        (property_exists($apiPayment,'AuthorizationCode'))?($payment->authorizationCode = $apiPayment->AuthorizationCode):('');
        (property_exists($apiPayment,'AcquirerTransactionId'))?($payment->acquirerTransactionId = $apiPayment->AcquirerTransactionId):('');
        (property_exists($apiPayment,'ProofOfSale'))?($payment->proofOfSale = $apiPayment->ProofOfSale):('');
        (property_exists($apiPayment,'Status'))?($payment->status = $apiPayment->Status):('');
        (property_exists($apiPayment,'ReasonCode'))?($payment->reasonCode = $apiPayment->ReasonCode):('');
        (property_exists($apiPayment,'reasonMessage'))?($payment->reasonMessage = $apiPayment->reasonMessage):('');
        (property_exists($apiPayment,'Amount'))?($payment->amount = $apiPayment->Amount):('');
        (property_exists($apiPayment,'Carrier'))?($payment->carrier = $apiPayment->Carrier):('');
        (property_exists($apiPayment,'Country'))?($payment->country = $apiPayment->Country):('');
        (property_exists($apiPayment,'Currency'))?($payment->currency = $apiPayment->Currency):('');
        
        (property_exists($apiPayment,'Capture'))?($payment->capture = $apiPayment->Capture):('');
        (property_exists($apiPayment,'Authenticate'))?($payment->authenticate = $apiPayment->Authenticate):('');
        (property_exists($apiPayment,'Interest'))?($payment->interest = $apiPayment->Interest):('');
        
        $payment->links = $this->parseLinks($apiPayment->Links);
        
        return $payment;
    }
    
    /**
     * Creates a sale
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
