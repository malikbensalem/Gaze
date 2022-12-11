<?php

    $executionStartTime = microtime(true) / 1000;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $output['data']['bounds'] = json_decode(file_get_contents('../js/countries.geo.json'), true);
    
    for ($i=0; $i <sizeof($output['data']['bounds']['features']) ; $i++) { 
        if ($output['data']['bounds']['features'][$i]['id']==$_REQUEST['lCode']){ 
            $output['data']['bounds']=$output['data']['bounds']['features'][$i];
            break; 
        }
    }
    
    // info -- code
	curl_setopt($ch, CURLOPT_URL,'http://api.geonames.org/countryInfoJSON?formatted=true&lang=en&country='.$_REQUEST['sCode'].'&username=flightltd&style=full');

	$result=json_decode(curl_exec($ch),true);
    
    $output['data']['info']['languages']=$result['geonames'][0]['languages'];
    $output['data']['info']['areaInSqKm']=$result['geonames'][0]['areaInSqKm'];
    $output['data']['info']['population']=$result['geonames'][0]['population'];
    $output['data']['info']['capital']=$result['geonames'][0]['capital'];

     // capi -- cap
     curl_setopt($ch, CURLOPT_URL,'http://api.geonames.org/searchJSON?formatted=true&q='.$_REQUEST['cap'].'&lang=en&username=flightltd&style=full');
    
     $result=json_decode(curl_exec($ch),true)['geonames'][0];
     
     $output['data']['cap']['lng']=$result['lng'];
     $output['data']['cap']['lat']=$result['lat'];
    


    //news -- code
    //   https://newsapi.org/v2/top-headlines?country=us&apiKey=e4e69f0c0cfa4819a419da3d55f28fe1
    // curl_setopt($ch, CURLOPT_URL,'https://newsapi.org/v2/top-headlines?country='.$_REQUEST['sCode'].'&apiKey=e4e69f0c0cfa4819a419da3d55f28fe1');
    // $result=curl_exec($ch);
    // $decode = json_decode($result,true);	
    
	// $output['data']['news']['totalResults'] = $decode['totalResults'];    
	// $output['data']['news']['articles'] = $decode['articles'];    

    //rates
    curl_setopt($ch, CURLOPT_URL,'http://data.fixer.io/api/latest?access_key=687789fb76fbe8907e70a6f9724e4b47');
    $result=curl_exec($ch);
	$decode = json_decode($result,true);	
	$output['data']['rates'] = $decode['rates'][$_REQUEST['cc']];    
    
    //weaher -- name
    curl_setopt($ch, CURLOPT_URL,'http://api.weatherapi.com/v1/current.json?key=838e3e6962124a1096e191219200810&q='.$_REQUEST['ctry2']);
	$result=curl_exec($ch);
    $decode = json_decode($result,true);
    
    $output['data']['weather']['current']['condition']['icon'] = $decode['current']['condition']['icon'];
    $output['data']['weather']['current']['condition']['text'] = $decode['current']['condition']['text'];
    $output['data']['weather']['current']['wind_mph'] = $decode['current']['wind_mph'];
    $output['data']['weather']['current']['feelslike_c'] = $decode['current']['feelslike_c'];
    $output['data']['weather']['current']['humidity'] = $decode['current']['humidity'];
    $output['data']['weather']['location']['tz_id'] = $decode['location']['tz_id'];
    $output['data']['weather']['location']['localtime'] = $decode['location']['localtime'];
    
   
    // curl_setopt($ch, CURLOPT_URL,'http://countryapi.gear.host/v1/Country/getCountries?pName='.str_replace("%20", " ",$_REQUEST['ctry']));

    curl_setopt_array($ch, array(
        CURLOPT_URL => "https://api.apilayer.com/exchangerates_data/convert?to=".$_REQUEST['cc']."&from=usd&amount=1",
        CURLOPT_HTTPHEADER => array(
        "Content-Type: text/plain",
        "apikey: ZA8lZfihuUfdIEDbUDFu94vdHC1WwijS"
        ),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET"
    ));
    
    // curl_setopt($ch, CURLOPT_URL,"https://api.apilayer.com/exchangerates_data/convert?to=usd&from=".$_REQUEST['ctry']."&amount=1");
    
	$result=curl_exec($ch);
	$decode = json_decode($result,true);

    $output['data']['currency']['CurrencySymbol'] = $decode['query']['to'];
    $output['data']['currency']['CurrencyName'] = $decode['result'];

    //earthquakes
    curl_setopt($ch, CURLOPT_URL,'http://api.geonames.org/earthquakesJSON?formatted=true&north='.$_REQUEST['north'].'&south='.$_REQUEST['south'].'&east='.$_REQUEST['east'].'&west='.$_REQUEST['west'].'&username=flightltd&style=full');
	$result=curl_exec($ch);
	$decode = json_decode($result,true);	
    $output['data']['earth'] = $decode;

    
	curl_close($ch);
    $output['status']['code'] = "200";
	$output['status']['name'] = "ok";
	$output['status']['description'] = "mission saved";
    $output['status']['returnedIn'] = (microtime(true) - $executionStartTime) / 1000 . " ms";
    
    header('Content-Type: application/json; charset=UTF-8');
	echo json_encode($output); 	
	
?>
