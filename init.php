<?php

$client_id = "1000.4H6Z54RLYWBS70321WEUN90IZJP5NJ";
$client_secret = "85dce0fa056b2c3dc0d1b9b02bb8db89890da549aa";
$redirect_uri = "http://local.zoho.com/callback.php";
$auth_token = "";

function request($post, $curl_url){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $curl_url);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
	curl_close($ch);
	return $result;
}

function goToAuthUrl($client_id){
	if ($_SERVER['REQUEST_METHOD'] == 'GET'){
		$url = 
			"https://accounts.zoho.com/oauth/v2/auth?response_type=code&client_id=".
			$GLOBALS['client_id'].
			"&scope=Desk.tickets.READ,Desk.basic.READ&redirect_uri=".
			$GLOBALS['redirect_uri'].
			"&state=-5466400890088961855";

		header("location: ".$url);
		exit;
	}
}

function getToken(){
	$curl_url = "https://accounts.zoho.com/oauth/v2/token";
	if ($_SERVER['REQUEST_METHOD']=='GET'){
		if (isset($_GET['code'])){
			$code = $_GET['code'];
			$post_data = array(
				'code'=>$code,
				'client_id'=>$GLOBALS['client_id'],
				'client_secret' => $GLOBALS['client_secret'],
				'redirect_uri'=>$GLOBALS['redirect_uri'],
				'scope' =>'Desk.tickets.READ,Desk.basic.READ',
				'grant_type'=> 'authorization_code',
			);
			$post = http_build_query($post_data);
			$result = (array)json_decode(request($post, $curl_url));
			$GLOBALS['auth_token'] = $result['access_token'];
		    echo $result['access_token'];
		}
	}
}

function createContact($data){
	$url = "https://desk.zoho.com/api/v1/contacts";
	$post_data = array();
	foreach ($data as $key => $value) {
		$post_data[$key] = $value;	
	}
	
	var_dump($post_data);

}


function getOrgId(){
   $curl_url = "https://desk.zoho.com/api/v1/organizations";
   echo $GLOBALS['auth_token'];
   	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $curl_url);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Authorization:Zoho-oauthtoken: '.$GLOBALS['auth_token'],
    ));
    $result = curl_exec($ch);
	curl_close($ch);
	echo "<pre>";
	print_r($result);
	echo "</pre>";
	return $result;
}