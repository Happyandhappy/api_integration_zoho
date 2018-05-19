<?php

$client_id = "1000.4H6Z54RLYWBS70321WEUN90IZJP5NJ";
$client_secret = "85dce0fa056b2c3dc0d1b9b02bb8db89890da549aa";
$redirect_uri = "http://local.zoho.com/callback.php";

function getReuqest($curl_url){
   	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $curl_url);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if (isset($GLOBALS['orgId']) && $GLOBALS['orgId'] != ""){
	    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		    'Authorization:Zoho-oauthtoken '.$GLOBALS['token'],
		    "orgId: ".$GLOBALS['orgId'],
		    "Content-Type: application/json",
	    ));
    }else{
	    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		    "Authorization:Zoho-oauthtoken ".$GLOBALS['token'],
		    "Content-Type: application/json",
	    ));    	
    }
    $result = curl_exec($ch);
	curl_close($ch);
	return $result;
}

function postRequest($curl_url, $post){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $curl_url);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		    'Authorization:Zoho-oauthtoken '.$GLOBALS['token'],
		    "orgId: ".$GLOBALS['orgId'],
		    "Content-Type: application/json",
	));

    $result = curl_exec($ch);
	curl_close($ch);
	return $result;
}

function patchRequest($curl_url, $post){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $curl_url);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    // curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		    'Authorization:Zoho-oauthtoken '.$GLOBALS['token'],
		    "orgId: ".$GLOBALS['orgId'],
		    "Content-Type: application/json",
	));

    $result = curl_exec($ch);
	curl_close($ch);
	return $result;

}
function goToAuthUrl(){
	$scope = "Desk.tickets.ALL"    .",".
			 "Desk.basic.READ"     .",".
			 "Desk.contacts.READ"  .",".
			 "Desk.contacts.CREATE".",".
			 "Desk.contacts.UPDATE".",".
			 "Desk.contacts.WRITE" .",".
			 "Desk.search.READ";
	if ($_SERVER['REQUEST_METHOD'] == 'GET'){
		$url = 
			"https://accounts.zoho.com/oauth/v2/auth?response_type=code&client_id=".
			$GLOBALS['client_id'].
			"&scope=".$scope."&redirect_uri=".
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
			
			$scope = "Desk.tickets.ALL"    .",".
			 "Desk.basic.READ"     .",".
			 "Desk.contacts.READ"  .",".
			 "Desk.contacts.CREATE".",".
			 "Desk.contacts.UPDATE".",".
			 "Desk.contacts.WRITE" .",".
			 "Desk.search.READ";

			$post_data = array(
				'code'          =>  $code,
				'client_id'     =>  $GLOBALS['client_id'],
				'client_secret' =>  $GLOBALS['client_secret'],
				'redirect_uri'  =>  $GLOBALS['redirect_uri'],
				'scope'         =>  $scope,
				'grant_type'	=> 'authorization_code',
			);
			
			$post = http_build_query($post_data);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $curl_url);
		    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		    curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		    
		    $result = curl_exec($ch);
			curl_close($ch);
			$result = (array)json_decode($result);
			$token = $result['access_token'];
			$GLOBALS['token'] = $result['access_token'];
			getOrgId();
		}
	}
}

function getOrgId(){
   $curl_url = "https://desk.zoho.com/api/v1/organizations";

	$decode_data = json_decode(getReuqest($curl_url));	
	if (isset($decode_data->data['0']->id))
		$GLOBALS['orgId'] = $decode_data->data['0']->id;
}

function createContact($data){
	$curl_url = "https://desk.zoho.com/api/v1/contacts";
    echo $GLOBALS['token'].":".$GLOBALS['orgId']."<br>";
	$post_data = array();
	foreach ($data as $key => $value) {
		if ($key != "authtoken" && $key != "orgId")
			$post_data[$key] = $value;	
	}
	$post = json_encode($post_data);
	$result = postRequest($curl_url, $post);
	
	echo $result;
	return $result;
}

function getAllTickets(){
	$curl_url = "https://desk.zoho.com/api/v1/tickets?include=contacts";
	echo("<pre>");
	print_r(json_decode(getReuqest($curl_url)));
	echo "</pre>";
}

function getAllContacts(){
	$curl_url = "https://desk.zoho.com/api/v1/contacts";
	echo "<pre>";
	print_r(json_decode(getReuqest($curl_url)));
	echo "</pre>";
}

function getTicketsofContact($contactID){
	$curl_url = "https://desk.zoho.com/api/v1/contacts/".$contactID."/tickets";
	echo "<pre>";
	print_r(json_decode(getReuqest($curl_url)));
	echo "</pre>";	
}

function getContactbyId($contactID){
	$curl_url = "https://desk.zoho.com/api/v1/contacts/".$contactID;
	echo "<pre>";
	print_r(json_decode(getReuqest($curl_url)));
	echo "</pre>";		
}

function searchContact($searchstr){
	$curl_url = "https://desk.zoho.com/api/v1/search?searchStr=".$searchstr."&module=contacts&from=0&limit=1&sortBy=modifiedTime";
	$search_data =json_decode(getReuqest($curl_url));
	if (isset($search_data)){
	    return $search_data->data[0]->id;
	}
	return "";
}

function updateContact($contactID, $data){
	$curl_url = "https://desk.zoho.com/api/v1/contacts/".$contactID;
	$send_data = json_encode($data);
	$result =json_decode(patchRequest($curl_url, $send_data));
}	