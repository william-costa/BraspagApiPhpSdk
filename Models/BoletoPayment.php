<?php

/**
 * Contains calls to Braspag API.
 *
 * ApiServices description.
 *
 * @version 1.0
 * @author interatia
 */

class BoletoPayment extends Payment
{
    public $Address; //Endereço do Cedente - 255.
    public $BoletoNumber; //Número do Boleto ("NossoNumero") - Tamanho: 50.
    public $Assignor; //Nome do Cedente - Tamanho: 200.
    public $Demonstrative; //Texto de Demonstrativo - Tamanho: 450.
    public $ExpirationDate; // Data de expiração do Boleto - YYYY-MM-DD.
    public $Identification; //CPF ou CNPJ do Cedente sem os caracteres especiais (., /, -)
    public $Instructions;  //Instruções do Boleto  - Tamanho: 450.
    public $Url; // Url do Boleto gerado. 
    public $BarCodeNumber; // Representação numérica do código de barras. 
    public $DigitableLine; // Linha digitável. 
    
    
    public function __construct(){
        $this->type = "Boleto";
        $this->authenticate = BraspagApiConfig::defaultAuthenticate;
        $this->capture = BraspagApiConfig::defaultCapture;
        $this->interest = BraspagApiConfig::defaultInterest;
    }
}

?>
