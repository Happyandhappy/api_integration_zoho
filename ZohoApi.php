<?php
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
   