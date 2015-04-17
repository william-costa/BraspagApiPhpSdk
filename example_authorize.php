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

$payment = new CreditCardPayment();
$payment->amount = 15900;
$payment->provider = "Simulado";

$payment->installments = 3;

$card = new Card();
$card->brand = "Visa";
$card->cardNumber = "4532117080573700";
$card->expirationDate = "12/2015";
$card->holder = "Test T S Testando";
$card->securityCode = "000";

$payment->creditCard = $card;

$sale->payment = $payment;

$api = new ApiServices();
$result = $api->CreateSale($sale);
			
if(is_a($result, 'Sale')){
    /*
     * In this case, you made a succesful call to API and receive a Sale object in response
     */			
    var_export($sale);
} elseif(is_array($result)){
    /*
     * In this case, you made a Bad Request and receive a collection with all errors
     */
    var_export($result);
} else{    
    /*
     * In this case, you received other error, such as Forbidden or Unauthorized
     */
    echo "HTTP Status Code: {$result}";
}

?>