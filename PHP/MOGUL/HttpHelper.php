<?php


class HttpHelper
{
	private $base_url;

	function __construct()
	{
		$this->base_url = "http://localhost:8000/";

		if(!empty($this->get('MongoCheck.php')))
		{
			$this->base_url = $this->base_url . "Mongo/";
		}
	}

	function post($path, $data){
	    $postdata = json_encode($data);
	    $url = $this->base_url.$path;
	    //var_dump($url);
	    $curl = curl_init($url);
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($curl, CURLOPT_POST, true);
	    curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);

	    $json_response = curl_exec($curl);

		//var_dump('POST: ' . $this->base_url . $path);
	    //var_dump($json_response);

    if (curl_errno($curl)) {
        print curl_error($curl);
    }

    curl_close($curl);

    return json_decode($json_response, true);
}

function get($path) {
    $curl = curl_init($this->base_url.$path);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPGET, true);

    $json_response = curl_exec($curl);

		/*if($path !== 'MongoCheck.php')	// MongoCheck is called to early, so that the Page crashed if there would be a var_dump
		{
			var_dump('GET: ' . $this->base_url . $path);
		    var_dump($json_response);
		}*/

    curl_close($curl);

    return json_decode($json_response, true);
}

    function changeDB() {

        $response = $this->get('MongoInit.php');

        $this->base_url = $this->base_url . "Mongo/";

        return($response);
    }
}