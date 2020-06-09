<?php


class HttpHelper
{
	private $base_url = "http://localhost:8000/";

	function __construct()
	{
		$settings = $this->post('BalanceSettings/post_get_setting.php', array());
		if(!empty($settings))
		{
			$this->base_url = $this->base_url . "Mongo/";
		}
	}

	function post($path, $data){
		var_dump('Using Mongo? ' . $this->base_url);
		
	    $postdata = json_encode($data);
	    $url = $this->base_url.$path;
	    //var_dump($url);
	    $curl = curl_init($url);
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($curl, CURLOPT_POST, true);
	    curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);

	    $json_response = curl_exec($curl);

	    //echo "json-response";
	    var_dump($json_response);

	    if (curl_errno($curl)) {
	        print curl_error($curl);
	    }

	    curl_close($curl);

	    return json_decode($json_response, true);
	}

	function get($path) {
		var_dump('Using Mongo? ' . $this->base_url);
		
	    $curl = curl_init($this->base_url.$path);
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($curl, CURLOPT_HTTPGET, true);

	    $json_response = curl_exec($curl);

	    var_dump($json_response);

	    curl_close($curl);

	    return json_decode($json_response, true);
	}

    function changeDB() {
        $response = $this->get('MongoInit.php');

        $this->base_url = $this->base_url . "Mongo/";

        $response = $this->get('test.php');

        return($response);
    }
}