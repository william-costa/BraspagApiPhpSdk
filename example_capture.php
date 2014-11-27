<?php

include 'BraspagApiIncludes.php';

$paymentId = '038722c3-4de5-4b73-ad5b-ec9fb07794d7';
$captureRequest = new CaptureRequest();

$captureRequest->amount = 1500;

$api = new ApiServices();
$result = $api->Capture($paymentId, $captureRequest);

if(is_a($result, 'CaptureResponse')){
    /*
     * In this case, you made a succesful call to API and receive a CaptureResponse object in response
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