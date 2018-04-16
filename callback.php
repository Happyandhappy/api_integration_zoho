<?php
require_once ("init.php");
if(count($_GET)>0){
	if (isset($_GET['code'])) {
		getToken();
		getOrgId();
	}
	else if (isset($_GET['firstName'])){
	    $sendData = array();
	    $fields = '';
	    foreach($_GET as $key=>$value){
	        $sendData[$key]=$value;
	        $fields = $fields.$key.",";
	    }
	    createContact($sendData);
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
		<div>
			<label>OrgID : </label>
			<input type="text" name="orgid" value="Joseph">
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
