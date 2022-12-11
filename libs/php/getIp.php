<?php
    //ip
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($ch, CURLOPT_URL,'https://www.iplocate.io/api/lookup/');
    $result=json_decode(curl_exec($ch),true)['country'];

    echo json_encode($result);
?>