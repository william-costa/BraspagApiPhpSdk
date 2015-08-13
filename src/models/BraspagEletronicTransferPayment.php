<?php

/**
 * Define EletronictTransferPayment model.
 *
 * @version 1.0
 * @author pfernandes
 */
class BraspagEletronicTransferPayment extends BraspagPayment
{
    public $url;

    public function __construct(){        
        $this->type = "EletronicTransfer";
    }
}
