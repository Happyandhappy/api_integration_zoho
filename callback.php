<?php
session_start();
/**
   * className  : ZohoApi
     
     variables
		$oauthToken	   :  oAuthToken
		$orgId		   :  Organization ID
	 
	 functions
		getHeaders()   :  get header array for curl request
		Httprequest()  :  curl function
		createContact():  create new contact with data 
		search()	   :  search function
		updateContact():  update existing contact
   */
   class ZohoApi
   {
   	public $oauthToken 	  = "";
   	public $orgId		  = "";
   	
   	function __construct($oauthToken, $orgId)
   	{
   		$this->oauthToken = $oauthToken;
   		$this->orgId	  = $orgId;		
   	}

   	function getHeaders(){
        $authToken = $this->oauthToken;
        return array(
            "Authorization:Zoho-oauthtoken ".$this->oauthToken,
            "orgId: ".$this->orgId,
            "Content-Type: application/json",
        );
   	}

   	function Httprequest($url,$headers,$method,$data=""){
   		$curl= curl_init($url);
        curl_setopt($curl,CURLOPT_HTTPHEADER,$headers);
        curl_setopt($curl,CURLOPT_CUSTOMREQUEST,$method);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        if($method=="POST" || $method=="PATCH"){
            curl_setopt($curl, CURLOPT_POSTFIELDS, (gettype($data)==="string")?$data:json_encode($data));
        }
        $response= curl_exec($curl);
        curl_close($curl);
        return ($response);
   	}

   	function createContact($data=""){
   		$_url = "https://desk.zoho.com/api/v1/contacts";
   		$result = json_decode($this->Httprequest($_url, $this->getHeaders(), "POST", $data));
      if (isset($result->id)){
      	var_dump($result);
      	echo "<br>";
        return $result->id;
      }
      return "";
   	}

   	function search($searchstr, $module){
   		$_url = "https://desk.zoho.com/api/v1/search?searchStr=".$searchstr."&module=".$module."&from=0&limit=1&sortBy=modifiedTime";
		$search_data =json_decode($this->Httprequest($_url, $this->getHeaders(),"GET"));
		if (isset($search_data->data[0]->id)){
		    return $search_data->data[0]->id;
		}
		return "";
   	}

   	function updateContact($contactID, $data){
   		$_url = "https://desk.zoho.com/api/v1/contacts/".$contactID;
      $result = json_decode($this->Httprequest($_url, $this->getHeaders(), "PATCH", $data));
      if (isset($result->id))
   		   return $result->id;
      else 
         return "";
   	}

    function createTicket($contactID, $data){
      $url = "https://desk.zoho.com/api/v1/tickets";
      $result = json_decode($this->Httprequest($url, $this->getHeaders(), "POST", $data));
      if (isset($result->id))
          return $result;
      else return NULL;
    }

    function getAllTickets(){
       $url = "https://desk.zoho.com/api/v1/tickets?include=contacts";
       $result = json_decode($this->Httprequest($url, $this->getHeaders(), "GET"));
       return $result;
    }

    function getTicket($ticketID){
      $_url = "https://desk.zoho.com/api/v1/tickets/".$ticketID."?include=contacts,products";
      $result = json_decode($this->Httprequest($_url, $this->getHeaders(), "GET"));
      return $result;
    }

    function createTicketComment($ticketID, $data){
      $_url = "https://desk.zoho.com/api/v1/tickets/".$ticketID."/comments";
      $result = json_decode($this->Httprequest($_url, $this->getHeaders(), "POST", $data));
      if (isset($result->id)) return $result->id;
      else return "Error occured in create comment of ticket";
    }

   }
   

/*
	Definition of Rest functions
	
*/

function getReuqest($curl_url){
   	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $curl_url);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if (isset($_SESSION['orgId']) && $_SESSION['orgId'] != ""){
	    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		    'Authorization:Zoho-oauthtoken '.$_SESSION['token'],
		    "orgId: ".$_SESSION['orgId'],
		    "Content-Type: application/json",
	    ));
    }else{
	    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		    "Authorization:Zoho-oauthtoken ".$_SESSION['token'],
		    "Content-Type: application/json",
	    ));    	
    }
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
			$_SESSION['client_id'].
			"&scope=".$scope."&redirect_uri=".
			$_SESSION['redirect_uri'].
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
				'client_id'     =>  $_SESSION['client_id'],
				'client_secret' =>  $_SESSION['client_secret'],
				'redirect_uri'  =>  $_SESSION['redirect_uri'],
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
			$_SESSION['token'] = $result['access_token'];
			setcookie("token", $token, time() + (86400 * 30), "/");
			getOrgId();
		}
	}
}

function getOrgId(){
   $curl_url = "https://desk.zoho.com/api/v1/organizations";

	$decode_data = json_decode(getReuqest($curl_url));	
	if (isset($decode_data->data['0']->id)){
		$_SESSION['orgId'] = $decode_data->data['0']->id;
	}

}

function process(){
	$zoho = new ZohoApi($_SESSION['token'], $_SESSION['orgId']);

		// search contact using searchstr, if exist, update, if not, create new one.
		$accountId = $zoho->search($_SESSION['account'], "accounts");

		if ($accountId=="")
			    $contactData = array(
			    	"firstName" => $_SESSION['firstName'],
			    	"lastName"  => $_SESSION['lastName'],
			    	"email"	    => $_SESSION['email'],
			    	"phone"		=> $_SESSION['phone'],
			    );
		else
    	  		$contactData = array(
			    	"firstName" => $_SESSION['firstName'],
			    	"lastName"  => $_SESSION['lastName'],
			    	"email"	    => $_SESSION['email'],
			    	"phone"		=> $_SESSION['phone'],
			    	"accountId" => $accountId,
			    );    
        $contactID = $zoho->search($_SESSION['searchstr'], "contacts");
        
        if ($contactID == ""){
        	$result = $zoho->createContact($contactData);
        	if ($result != "") $contactID = $result;
        	else 
        	{
        		echo "Error Occured";
        		exit;
        	}
        }else{
        	$contactID = $zoho->updateContact($contactID, $contactData);
			if ($contactID == "") {
				echo "Error Occured"; exit;
			}
        }

        //create new ticket
        $ticketData = array(
        		"contactId"     => $contactID,
        		"subject"       => $_SESSION['subject'],
        		"description"   => $_SESSION['description'],
        		// "assigneeId"    => $_SESSION['assigneeId'],
        		"channel"		=> $_SESSION['channel'],
        		"departmentId"  => $_SESSION['departmentId']
        );
        $result = $zoho->createTicket($contactID, $ticketData);
        if ($result==NULL) {
        	echo "Error Occured in createing Ticket"; exit;
        }
        echo "Ticket Number : " . $result->ticketNumber . "   ,Contact Id : " . $contactID;	
        
        $ticketId = $result->id;
        //create comment to ticket
        $commentData = array(
        	"isPublic"      => "true",
        	"attachmentIds" => array(
        							"4000000008892"	
        					   ),
        	"content"       =>  $_SESSION['comment'],
        );
        $result = $zoho->createTicketComment($ticketId, $commentData);
        echo "  CommentId:" . $result;
}

/////////////////////////////////////////////////////////////////////////////
if(count($_GET)>0){
    foreach($_GET as $key=>$value){
        	$_SESSION[$key]=$value;
    }

	if (isset($_GET['code'])) {
		getToken();

		process();
	} 
	else if(isset($_GET['client_id']) && isset($_GET['client_secret']) && isset($_GET['redirect_uri']))
	{
			$_SESSION['client_id'] = $_GET['client_id'];
			$_SESSION['client_secret'] = $_GET['client_secret'];
			$_SESSION['redirect_uri'] = $_GET['redirect_uri'];
			if(!isset($_SESSION["token"])) goToAuthUrl();
			process();
	}


}
?>

<!-- http://local.zoho.com/callback.php?client_id=1000.4H6Z54RLYWBS70321WEUN90IZJP5NJ&client_secret=85dce0fa056b2c3dc0d1b9b02bb8db89890da549aa&redirect_uri=http://local.zoho.com/callback.php&searchstr=jim&firstName=jim&lastName=Smith&email=test@mail.com&phone=91020080878&subject=Subject&description=Ticket Description&assigneeId=1892000000056007&channel=Email&departmentId=271459000000006907&account=Zoho&comment=Description -->
