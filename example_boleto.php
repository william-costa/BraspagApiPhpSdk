<?php
include 'BraspagApiIncludes.php';

$sale = new Sale();
$sale->merchantOrderId = '2014112703';

$customer = new Customer();
$customer->name = "Comprador de Testes";
$customer->email = "compradordetestes@braspag.com.br";
$customer->birthDate = "1991-01-02";

$address = new Address();
$address->city = "Rio de Janeiro";
$address->complement = "Sala 934";
$address->country = "BRA";
$address->district = "Centro";
$address->number = "160";
$address->state = "RJ";
$address->street = "Av. Marechal Câmara";
$address->zipCode = "20020-080";

$customer->address = $address;
$sale->customer = $customer;

$payment = new BoletoPayment();
$payment->amount = 15900;
$payment->provider = "Simulado";
$payment->Address = "Endereço do Cedente";
$payment->BoletoNumber = '2014112703';
$payment->Assignor =  'Nome do Cedente';
$payment->Demonstrative =  'Texto de Demonstrativo';
$payment->ExpirationDate = "2015-09-02";
$payment->Identification = '005383715000194';
$payment->Instructions = 'Instruções do Boleto';
$sale->payment = $payment;

$api = new ApiServices();
$result = $api->CreateSale($sale);

if(is_a($result, 'Sale')){
    /*
     * In this case, you made a succesful call to API and receive a Sale object in response
     */            
    $api->debug($sale,"Boleto Sucess!");  
} elseif(is_array($result)){
    /*
     * In this case, you made a Bad Request and receive a collection with all errors
     */
    $api->debug($result,"Bad Request Auth!");
} else{    
    /*
     * In this case, you received other error, such as Forbidden or Unauthorized
     */
    $api->debug($result,"HTTP Status Code!");
}

?>