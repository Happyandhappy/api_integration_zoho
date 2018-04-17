<?php
require_once ("init.php");
require_once "ZohoApi.php";
if(count($_GET)>0){
	if (isset($_GET['code'])) {
		getToken();
	}
	else if (isset($_GET['authtoken'])){
		$GLOBALS['token'] = $_GET['authtoken'];
		$GLOBALS['orgId'] = $_GET['orgId'];

		$zoho = new ZohoApi($_GET['authtoken'], $_GET['orgId']);
	    $sendData = array();
	    foreach($_GET as $key=>$value){
	    	if ($key != "authtoken" && $key != "orgId")
	        	$sendData[$key]=$value;
	    }

	    $zoho->createContact($sendData);                //Create new Contact 
	    $contactID = $zoho->search("John","contacts");  // Search Contact with seachStr "John"
	    echo $contactID."<br/>";						
	    if ($contactID!="") 
	    	$zoho->updateContact($contactID, array("firstName"=>"ESAMPLE"));  //update contact
	}
}
?>

<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
	<form>
		<?php
			if (isset($GLOBALS['token']) && $GLOBALS['token'] !=''){
		?>
			<div>
				<input type="hidden" name="authtoken" value="<?php echo($GLOBALS['token']) ?>">
			</div>
		<?php } ?>

		<div>
			<label>OrgID : </label>
			<input type="text" name="orgId" value="<?php echo $GLOBALS['orgId'] ?>">
		</div>
		<div>
			<label>First Name : </label>
			<input type="text" name="firstName" value="Joseph">
		</div>
		<div>
			<label>Last Name : </label>
			<input type="text" name="lastName" value="John">
		</div>
		<div>
			<label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Email : </label>
			<input type="email" name="email" value="jack@zylker.com">
		</div>
		<div>
			<label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Title:</label>
			<input type="text" name="title" value="The contact">
		</div>
		<div>
			<label>Description : </label>
			<input type="text" name="description" value="first priority contact">
		</div>
		<div>
			<input type="submit" name="" value="submit">
		</div>
	</form>	
</body>
</html>
