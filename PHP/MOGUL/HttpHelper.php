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

    var_dump($curl);

    $json_response = curl_exec($curl);

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