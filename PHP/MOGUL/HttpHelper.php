<?php


class HttpHelper
{
private $base_url = "http://localhost:8000/";

function post($path, $data){
    $postdata = json_encode($data);
    $url = $this->base_url.$path;
    var_dump($url);
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

    var_dump($curl);

    $test = file_get_contents($this->base_url."api_test.php");
    var_dump($test);

    $json_response = curl_exec($curl);

    if (curl_errno($curl)) {
        print curl_error($curl);
    }

    curl_close($curl);

    return json_decode($json_response);
}

function get($path) {
    $curl = curl_init($this->base_url.$path);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPGET, true);

    $json_response = curl_exec($curl);

    curl_close($curl);

    return json_decode($json_response);
}
}