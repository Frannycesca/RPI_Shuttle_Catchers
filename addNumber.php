<?php
	// print_r($_REQUEST);

	try {
	  $host = 'localhost';
	  $root = 'sdd';
	  $password = '';

	  $dbconn = new PDO("mysql:host=$host;dbname=shuttlecatchers;",$root,$password);

	} catch (PDOException $e) {
	  die("Database Error: ". $e->getMessage());
	}

	// $phonenumber 
	$stmt = $dbconn->prepare("UPDATE users SET phonenumber = :phone WHERE rcsid = :rcsid");
	$stmt->execute(array(":phone"=>$_REQUEST['phonenumber'],":rcsid"=>$_REQUEST['rcsid']));

?>