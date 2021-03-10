<?php
include('openCage\AbstractGeocoder.php');
include('openCage\Geocoder.php'); 
$geocoder = new \OpenCage\Geocoder\Geocoder('ef9b0bca019946d7a6f618f8d59a3d99');

$result = $geocoder->geocode($_REQUEST['name'])['results'][0];

$output['components']['ISO_3166-1_alpha-2'] = $result['components']['ISO_3166-1_alpha-2'];
$output['components']['ISO_3166-1_alpha-3'] = $result['components']['ISO_3166-1_alpha-3'];
$output['components']['country'] = $result['components']['country'];


$output['geometry']['lng'] = $result['geometry']['lng'];
$output['geometry']['lat'] = $result['geometry']['lat'];

$output['annotations']['currency']['iso_code'] = $result['annotations']['currency']['iso_code'];

$output['bounds']['northeast']['lng'] = $result['bounds']['northeast']['lng'];
$output['bounds']['northeast']['lat'] = $result['bounds']['northeast']['lat'];
$output['bounds']['southwest']['lat'] = $result['bounds']['southwest']['lat'];
$output['bounds']['southwest']['lng'] = $result['bounds']['southwest']['lng'];

$executionStartTime = microtime(true) / 1000;
$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

if (strtolower($_REQUEST['name'])=="united%20kingdom") {

    curl_setopt($ch, CURLOPT_URL,'http://api.weatherapi.com/v1/current.json?key=838e3e6962124a1096e191219200810&q='."england");
    $result=json_decode(curl_exec($ch),true);
    $output['components']['cap']=$result['location']['name'];
}
else{
    curl_setopt($ch, CURLOPT_URL,'http://api.weatherapi.com/v1/current.json?key=838e3e6962124a1096e191219200810&q='.$_REQUEST['name']);
    $result=json_decode(curl_exec($ch),true);
    $output['components']['cap']=$result['location']['name'];
}
    
curl_close($ch);
$output['status']['code'] = "200";
$output['status']['name'] = "ok";
$output['status']['description'] = "mission saved";
$output['status']['returnedIn'] = (microtime(true) - $executionStartTime) / 1000 . " ms";

header('Content-Type: application/json; charset=UTF-8');
    
    
echo json_encode($output,true);
?>