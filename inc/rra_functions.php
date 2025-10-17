<?php 

// include'link.php';

$date = date("YmdHis");
$jsonData = json_encode(array(
    
    "tin" => "999900823",
    "bhfId" => "00",
    "lastReqDt" => "20180520000000"
        
));


function rra_function($jsonData, $endpoint){
    global $url;
    // echo $url.$endpoint;
    $ch = curl_init($url.$endpoint);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($jsonData)
    ));
    $response = curl_exec($ch);
    curl_close($ch);
    parse_str( $response, $responseFields );

    if ($response === FALSE){
        return 'Sync error: Unable to connect to the rra gateway, please try again';
        // return $response;
    }

    $data = json_decode($response, true);
    return $data;
    
}
