<?php

/**
 * Contains calls to Braspag API.
 *
 * ApiServices description.
 *
 * @version 1.0
 * @author interatia
 */

class BraspagBoletoPayment extends BraspagPayment
{
    public $boleto;
    
    public function __construct(){
        $this->type = "Boleto";
        $this->authenticate = BraspagApiConfig::defaultAuthenticate;
        $this->capture = BraspagApiConfig::defaultCapture;
        $this->interest = BraspagApiConfig::defaultInterest;
    }
}

?>
