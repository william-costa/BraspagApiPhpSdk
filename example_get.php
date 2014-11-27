<?php

include 'BraspagApiIncludes.php';

$paymentId = '038722c3-4de5-4b73-ad5b-ec9fb07794d7';

$api = new ApiServices();
$result = $api->Get($paymentId);

if(is_a($result, 'Sale')){
    /*
     * In this case, you made a succesful call to API and receive a Sale object in response
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