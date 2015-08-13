<?php

/**
 * Define BoletoPayment model.
 *
 * @version 1.0
 * @author interatia
 */

class BraspagBoletoPayment extends BraspagPayment
{
    public $address;
    public $assignor;
    public $barcodeNumber;
    public $boletoNumber;
    public $demonstrative;
    public $digitableLine;
    public $expirationDate;
    public $identification;
    public $instructions;
    public $url;
    
    public function __construct(){
        $this->type = "Boleto";        
    }
}

?>
