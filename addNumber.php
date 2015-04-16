<?php

	require_once("./dbconfig.php");
  
  

	// $phonenumber 
	$stmt = $dbconn->prepare("UPDATE users SET phonenumber = :phone, carrier = :carrier WHERE rcsid = :rcsid");
	$stmt->execute(array(":phone"=>$_REQUEST['phonenumber'],":carrier"=>$_REQUEST['phonecarrier'], ":rcsid"=>$_REQUEST['rcsid']));

?>