<?php


class HttpHelper
{
	private $base_url;

	function __construct()
	{
		$base_url = "http://localhost:8000/";

		$file = fopen('mongo.txt', 'r');
		
		if($file != false)
		{
			$filetext = fread($file, 1024);
			fclose($file);
		}
		
		if($filetext === 'mongo')
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

	    //echo "json-response";
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

	    //var_dump($json_response);

	    curl_close($curl);

	    return json_decode($json_response, true);
	}

    function changeDB() {
        $response = $this->get('MongoInit.php');

        $this->base_url = $this->base_url . "Mongo/";
		
		$file = fopen('mongo.txt', 'w');
		fwrite($file, 'mongo');
		fclose($file);

        $response = $this->get('test.php');

        return($response);
    }
}