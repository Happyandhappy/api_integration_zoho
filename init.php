<?php
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
			getOrgId();
		}
	}
}

function getOrgId(){
   $curl_url = "https://desk.zoho.com/api/v1/organizations";

	$decode_data = json_decode(getReuqest($curl_url));	
	if (isset($decode_data->data['0']->id))
		$_SESSION['orgId'] = $decode_data->data['0']->id;
}




Most of you are probably familiar with Jumble(TM), a daily newspaper feature, where you reconstruct scrambled words. So you might be given 'ROYIN' and have to come up with 'IRONY'.
One aspect that makes a particular scrambling of letters 'hard' if it looks like a 'real' word. Thus, 'ROYIN' is tougher scramble than 'NRYIO' because it confuses your brain into thinking that it's already a word. Another aspect that makes a scrambling harder is whether letters are in their correct position. So, 'RIONY' isnt a very good scramble, even if it might kind-of look like a real word.
Your task is to write a program that reads a pair of words and scores the scrambled word by its degree of difficulty as 'good', 'fair', 'poor' or 'not'. A scrambled word is 'good' if none of its letters is in the corret place and it looks 'real' (more on that later). A scramble is 'poor' if it doesnt look 'real' and either the first letter is in the correct place or any two consecutive letters are in the correct place. If the word isnt scrambled at all, it is said to be 'not' scrambled. Otherwise, the scramble is 'fair'.

How do we know if a word looks 'real'?  We will use an extremely crude heuristic that the word must alternate between vowels ('Y' is a vowel for these purpose) and consonants. However, certain groups of vowels and consonants are allowed:

Also, all double consonants are allowed. No other combinations are allowed, so 'SWR' wouldnt be good,
even though both 'SW' and 'WR' are both openssl_free_key(key_identifier)

The input for your program will be pairs of words, the word followed by the scramble, all upper-case letters, alternating until end-of-file. You may assume that the words are anagrams of one another.

For each pari, you should print how well the second scores as a scramble of the first, as in the sample output. One one line, print the scrambel, one space, the appropriate phrase, one space, the word.


SPAM
MAPS
IRONY
RIONY
IRONY
ONYRI
IRONY
IRONY
IRONY
INOYR
IRONY
IOYRN
LITTLEST
EILLSTTT
LITTLEST
IELLTTTS
LITERATURE
TERILURETA
POOL
OOPL

