<?php
    $outpu = json_decode(file_get_contents('../js/countries.geo.json'), true);
    for ($i=0; $i <sizeof($outpu['features']) ; $i++) { 
        //if ($outpu['features'][$i]['properties']['name']!="Antarctica") {
            $output[$i]=$outpu['features'][$i]['properties']['name'];
        //}
    }
    echo json_encode($output,true);
?>