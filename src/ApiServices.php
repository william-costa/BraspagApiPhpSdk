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
    }
    
    /**
     * Creates a sale
    
     * @param Sale $sale 
     * @return mixed
     */
    public function createSale(BraspagSale $sale){

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
            $sale->payment->receivedDate = BraspagUtils::getResponseValue($responsePayment, 'ReceivedDate');
            $sale->capturedDate = BraspagUtils::getResponseValue($responsePayment, 'CapturedDate');
            $sale->voidedDate = BraspagUtils::getResponseValue($responsePayment, 'VoidedDate');
            $sale->capturedAmount = BraspagUtils::getResponseValue($responsePayment, 'CapturedAmount');
            $sale->capturedAmount = BraspagUtils::getResponseValue($responsePayment, 'VoidedAmount');
            $sale->payment->providerReturnCode = BraspagUtils::getResponseValue($responsePayment, 'ProviderReturnCode');
            $sale->payment->providerReturnMessage = BraspagUtils::getResponseValue($responsePayment, 'ProviderReturnMessage');
            
            if($responsePayment->Type == 'CreditCard' || $responsePayment->Type == 'DebitCard'){
                $sale->payment->authenticationUrl = BraspagUtils::getResponseValue($responsePayment, 'AuthenticationUrl');
                $sale->payment->authorizationCode = BraspagUtils::getResponseValue($responsePayment, 'AuthorizationCode');
                $sale->payment->acquirerTransactionId = BraspagUtils::getResponseValue($responsePayment, 'AcquirerTransactionId');
                $sale->payment->proofOfSale = BraspagUtils::getResponseValue($responsePayment, 'ProofOfSale');
				$sale->payment->creditCard = BraspagUtils::getResponseValue($responsePayment, 'CreditCard');

                if(BraspagUtils::getResponseValue($responsePayment, 'FraudAnalysis') != null){
                    $antiFraudResponse = BraspagUtils::getResponseValue($responsePayment, 'FraudAnalysis');

                    $replyData = new BraspagFraudAnalysisReplyData();
                    $replyData->addressInfoCode = BraspagUtils::getResponseValue($antiFraudResponse->ReplyData, 'AddressInfoCode');
                    $replyData->factorCode = BraspagUtils::getResponseValue($antiFraudResponse->ReplyData, 'FactorCode');
                    $replyData->score = BraspagUtils::getResponseValue($antiFraudResponse->ReplyData, 'Score');
                    $replyData->binCountry = BraspagUtils::getResponseValue($antiFraudResponse->ReplyData, 'BinCountry');
                    $replyData->cardIssuer = BraspagUtils::getResponseValue($antiFraudResponse->ReplyData, 'CardIssuer');
                    $replyData->cardScheme = BraspagUtils::getResponseValue($antiFraudResponse->ReplyData, 'CardScheme');
                    $replyData->hostSeverity = BraspagUtils::getResponseValue($antiFraudResponse->ReplyData, 'HostSeverity');
                    $replyData->internetInfoCode = BraspagUtils::getResponseValue($antiFraudResponse->ReplyData, 'InternetInfoCode');
                    $replyData->ipRoutingMethod = BraspagUtils::getResponseValue($antiFraudResponse->ReplyData, 'IpRoutingMethod');
                    $replyData->scoreModelUsed = BraspagUtils::getResponseValue($antiFraudResponse->ReplyData, 'ScoreModelUsed');
                    $replyData->casePriority = BraspagUtils::getResponseValue($antiFraudResponse->ReplyData, 'CasePriority');

                    $sale->payment->fraudAnalysis->status = $antiFraudResponse->Status;

                    $sale->payment->fraudAnalysis->replyData = $replyData;
                }

            }elseif($response->body->Payment->Type == 'Boleto'){
                $sale->payment->url = BraspagUtils::getResponseValue($responsePayment, 'Url');
                $sale->payment->barCodeNumber = BraspagUtils::getResponseValue($responsePayment, 'BarCodeNumber');
                $sale->payment->digitableLine = BraspagUtils::getResponseValue($responsePayment, 'DigitableLine');
                $sale->payment->boletoNumber = BraspagUtils::getResponseValue($responsePayment, 'BoletoNumber');

            }elseif($response->body->Payment->Type == 'EletronicTransfer'){    
                $sale->payment->url = BraspagUtils::getResponseValue($responsePayment, 'Url');                

            }            

            $recurrentResponse = BraspagUtils::getResponseValue($responsePayment, 'RecurrentPayment');

            if($recurrentResponse != null){
                $sale->payment->recurrentPayment->recurrentPaymentId = BraspagUtils::getResponseValue($recurrentResponse, 'RecurrentPaymentId');
                $sale->payment->recurrentPayment->reasonCode = $recurrentResponse->ReasonCode;
                $sale->payment->recurrentPayment->reasonMessage = $recurrentResponse->ReasonMessage;
                $sale->payment->recurrentPayment->nextRecurrency = BraspagUtils::getResponseValue($recurrentResponse, 'NextRecurrency');
                $sale->payment->recurrentPayment->startDate = BraspagUtils::getResponseValue($recurrentResponse, 'StartDate');
                $sale->payment->recurrentPayment->endDate = BraspagUtils::getResponseValue($recurrentResponse, 'EndDate');
                $sale->payment->recurrentPayment->interval = BraspagUtils::getResponseValue($recurrentResponse, 'Interval');
                $sale->payment->recurrentPayment->link = $this->parseLink(BraspagUtils::getResponseValue($recurrentResponse, 'Link'));
            }

            $sale->payment->links = $this->parseLinks($response->body->Payment->Links);
            
            return $sale;
        }elseif($response->code == BraspagHttpStatus::BadRequest){          
            return BraspagUtils::getBadRequestErros($response->body);             
        }  
        
        return $response->code;
    }
    
    /**
     * Captures a pre-authorized payment
     * @param GUID $paymentId 
     * @param CaptureRequest $captureRequest 
     * @return mixed
     */
    public function capture($paymentId, BraspagCaptureRequest $captureRequest){        
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
            return BraspagUtils::getBadRequestErros($response->body);            
        }   
        
        return $response->code;
    }
    
    /**
     * Void a payment
     * @param GUID $paymentId 
     * @param int $amount 
     * @return mixed
     */
    public function void($paymentId, $amount){
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
            return BraspagUtils::getBadRequestErros($response->body);            
        }   
        
        return $response->code;
    }    
    
    /**
     * Gets a sale
     * @param GUID $paymentId 
     * @return mixed
     */
    public function get($paymentId){
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
            return BraspagUtils::getBadRequestErros($response->body);            
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
    
    private function parseCustomer($apiCustomer){
        $customer = new BraspagCustomer();
        $customer->name = $apiCustomer->Name;
        $customer->email = BraspagUtils::getResponseValue($apiCustomer, 'Email');
        $customer->identity = BraspagUtils::getResponseValue($apiCustomer, 'Identity');
        $customer->identityType = BraspagUtils::getResponseValue($apiCustomer, 'IdentityType');
        $customer->birthDate = BraspagUtils::getResponseValue($apiCustomer, 'Birthdate');
        
        $apiAddress = BraspagUtils::getResponseValue($apiCustomer, 'Address');
        if($apiAddress != null){
            $address = new BraspagAddress();
            $address->country = $apiAddress->Country;
            $customer->city = BraspagUtils::getResponseValue($apiAddress, 'City');
            $customer->complement = BraspagUtils::getResponseValue($apiAddress, 'Complement');
            $customer->district = BraspagUtils::getResponseValue($apiAddress, 'District');
            $customer->number = BraspagUtils::getResponseValue($apiAddress, 'Number');
            $customer->state = BraspagUtils::getResponseValue($apiAddress, 'State');
            $customer->street = BraspagUtils::getResponseValue($apiAddress, 'Street');
            $customer->zipCode = BraspagUtils::getResponseValue($apiAddress, 'ZipCode');
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

            $payment->url = BraspagUtils::getResponseValue($apiPayment, 'Url');
            $payment->barCodeNumber = BraspagUtils::getResponseValue($apiPayment, 'BarCodeNumber');
            $payment->digitableLine = BraspagUtils::getResponseValue($apiPayment, 'DigitableLine');
            $payment->boletoNumber = BraspagUtils::getResponseValue($apiPayment, 'BoletoNumber');
            
            $payment->instructions = BraspagUtils::getResponseValue($apiPayment, 'Instructions');
            $payment->expirationDate = BraspagUtils::getResponseValue($apiPayment, 'ExpirationDate');
            $payment->demonstrative = BraspagUtils::getResponseValue($apiPayment, 'Demonstrative');
            $payment->assignor = BraspagUtils::getResponseValue($apiPayment, 'Assignor');
            $payment->address = BraspagUtils::getResponseValue($apiPayment, 'Address');
            $payment->identification = BraspagUtils::getResponseValue($apiPayment, 'Identification');

        }elseif($apiPayment->Type == 'EletronicTransfer'){
            $payment->url = BraspagUtils::getResponseValue($apiPayment, 'Url');
        }
        
        $payment->paymentId = $apiPayment->PaymentId;
        $payment->amount = $apiPayment->Amount;
        $payment->capturedAmount = BraspagUtils::getResponseValue($apiPayment, 'CapturedAmount');
        $payment->capturedAmount = BraspagUtils::getResponseValue($apiPayment, 'VoidedAmount');
        $payment->receivedDate = $apiPayment->ReceivedDate;
        $payment->capturedDate = BraspagUtils::getResponseValue($apiPayment, 'CapturedDate');
        $payment->voidedDate = BraspagUtils::getResponseValue($apiPayment, 'VoidedDate');
        $payment->country = $apiPayment->Country;
        $payment->currency = $apiPayment->Currency;
        $payment->provider = $apiPayment->Provider;
        $payment->status = $apiPayment->Status;
        $payment->reasonCode = $apiPayment->ReasonCode;
        $payment->reasonMessage = $apiPayment->ReasonMessage;
        $payment->providerReturnCode = BraspagUtils::getResponseValue($apiPayment, 'ProviderReturnCode');
        $payment->providerReturnMessage = BraspagUtils::getResponseValue($apiPayment, 'ProviderReturnMessage');
        $payment->returnUrl = BraspagUtils::getResponseValue($apiPayment, 'ReturnUrl');        
        $payment->links = $this->parseLinks($apiPayment->Links);
        
        return $payment;
    }
    
    private function parseCreditAndDebitPayment($payment, $apiPayment, $card){
        $payment->authenticationUrl = BraspagUtils::getResponseValue($apiPayment, 'AuthenticationUrl');
        $payment->authorizationCode = BraspagUtils::getResponseValue($apiPayment, 'AuthorizationCode');
        $payment->acquirerTransactionId = BraspagUtils::getResponseValue($apiPayment, 'AcquirerTransactionId');
        $payment->proofOfSale = BraspagUtils::getResponseValue($apiPayment, 'ProofOfSale');
        $payment->eci = BraspagUtils::getResponseValue($apiPayment, 'Eci');

        $parsedCard = new BraspagCard();
        $parsedCard->brand = $card->Brand;
        $parsedCard->cardNumber = $card->CardNumber;
        $parsedCard->expirationDate = $card->ExpirationDate;
        $parsedCard->holder = $card->Holder;
        $payment->creditCard = $parsedCard;
    }  
}
