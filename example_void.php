<?php

include 'BraspagApiIncludes.php';

$paymentId = '038722c3-4de5-4b73-ad5b-ec9fb07794d7';

$voidAmount = 1500;

$api = new ApiServices();
$result = $api->Void($paymentId, $voidAmount);

if(is_a($result, 'VoidResponse')){
    /*
     * In this case, you made a succesful call to API and receive a VoidResponse object in response
     */
    var_export($result);
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