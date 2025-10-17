<?php 
$date = date("YmdHis");
$jsonData = json_encode(array(
    
    "tin" => "999900823",
    "bhfId" => "00",
    "lastReqDt" => "20200520000000"
        
));

$url = "http://197.243.26.62:8080/rraVsdcSandbox2.1.2.3.7/";

function rra_function($jsonData, $endpoint){
    global $url;
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
    }else{
		return $response;
	}

    $data = json_decode($response, true);
    // if($data['resultCd'] == "00") return $data;
    // else return $response;
    return $data;
    
}

print_r($jsonData);
// varidation goes here using 00 as success response
print_r(rra_function($jsonData, 'branches/saveBrancheUsers'));
