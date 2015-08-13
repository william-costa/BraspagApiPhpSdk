<?php

/**
 * Provide services to manage recurrencies
 *
 * @version 1.0
 * @author pfernandes
 */
class RecurrentApiServices
{
    function __construct(){
        $this->headers = array(
                'MerchantId' => BraspagApiConfig::merchantId,
                'MerchantKey' => BraspagApiConfig::merchantKey
            );

        $this->utils = new BraspagUtils();
    }

    /**
     * Updates the customer of one recurrent payment
     * @param mixed $recurrentId 
     * @param BraspagCustomer $customer 
     */
    public function UpdateCustomer($recurrentId, $customer){
        $uri = BraspagApiConfig::apiUri . "RecurrentPayment/$recurrentId/Customer"; 

        $request = json_encode($customer, JSON_UNESCAPED_UNICODE);
        
        $response = \Httpful\Request::put($uri)
            ->sendsJson()
            ->addHeaders($this->headers)
            ->body($request)            
            ->send();

        if($response->code == BraspagHttpStatus::BadRequest){
            return $this->utils->getBadRequestErros($response->body);
        }

        return $response->code;
    }
}
