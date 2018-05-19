<?php
/*
Variables 
    - email         : User Email
    - pass          : User Password
    - token         : Zoho Token

Functions
    - getToken()      : get token by using user-email and password
    - getOrgId()      : get Organization Id 
    - testToken()     : check if session token is not expired or not 
    - getContactList(): get contact lists of selected account
    - getAccount()    : get account information
*/

header('Access-Control-Allow-Origin: *'); 
session_start();


if (count($_POST) == 0){
    // if (!isset($_POST['email']) || !isset($_POST['password']) || !isset($_POST['kinds']) || ($_POST['kinds']=='account') && !isset($_POST['id'])) {
    //     echo json_encode(array('message'=>'error'));
    //     return;
    // }
    // $email = $_POST['email'];
    // $pass  = $_POST['password'];
    // $kinds = $_POST['kinds'];
    // $email = "widadsaghir1993@gmail.com";
    $email = "honestdev21@gmail.com";
    $pass = "ahgifrhehdejd";

    if (!isset($_SESSION['email']) || $_SESSION['email'] != $email || $_SESSION['password'] != $pass){
        $_SESSION['email'] = $email;
        $_SESSION['password'] = $pass;
        $_SESSION['token'] = "" ;
    }

    if (!isset($_SESSION['token']) || $_SESSION['token']=="" || !testToken()){
        getToken();
    }
    
    echo "Token => " . $_SESSION['token'] . "<br>";
    echo "OrgID => " . $_SESSION['orgId'] . "<br>";

    // if ($kinds == 'contact')
        getContactsList();
    // else if ($kinds == 'account'){
        // $id = $_POST['id'];
        // getAccount($id);
    // }
}

// Function for get token from zoho desk api
function getToken(){
    $authUrl = "https://accounts.zoho.com/apiauthtoken/nb/create";

    $data = array(
        'SCOPE' => 'ZohoSupport/supportapi,ZohoSearch/SearchAPI',
        'EMAIL_ID' => $_SESSION['email'],
        'PASSWORD' => $_SESSION['password'],
    );
    
    $dt = postRequest($authUrl, $data);
    $arr = explode("=", $dt);
    $token =trim(str_replace('RESULT', '', $arr[1]));
    $_SESSION['token'] = $token;  
    getOrgId(); 
}

// Function for get OrgId from zoho desk api
function getOrgId(){
    $url = "https://desk.zoho.com/api/v1/organizations";
    $data =json_decode(getReuqest($url));
    $_SESSION['orgId'] = $data->data[0]->id;
}

// Function to Chenk if session token is expired or not
function testToken(){
    $token = $_SESSION['token'];
    $url = "https://desk.zoho.com/api/v1/tickets?include=contacts";
    $data = json_decode(getReuqest($url));

    if (isset($data->message)) return false;
    else return true;
}

// Post Curl 
function postRequest($url, $data){

    $post = http_build_query($data);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

// Get Curl 
function getReuqest($curl_url){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $curl_url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if (isset($_SESSION['orgId']) && $_SESSION['orgId'] != ""){
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization:Zoho-authtoken '.$_SESSION['token'],
            "orgId: ".$_SESSION['orgId'],
            "Content-Type: application/json",
        ));
    }else{
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authorization:Zoho-authtoken ".$_SESSION['token'],
            "Content-Type: application/json",
        ));    	
    }
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}


function getContactsList(){
    $url = "https://desk.zoho.com/api/v1/contacts";
    $data  =getReuqest($url);
    return $data;
}

function getAccount($id){
    $url = "https://desk.zoho.com/api/v1/accounts/" . $id;
    $data = getReuqest($url);
    return $data;
}
