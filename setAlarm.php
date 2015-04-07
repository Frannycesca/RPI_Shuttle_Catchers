<?php

	try {
	  $host = 'localhost';
	  $root = 'sdd';
	  $password = '';

	  $dbconn = new PDO("mysql:host=$host;dbname=shuttlecatchers;",$root,$password);

	} catch (PDOException $e) {
	  die("Database Error: ". $e->getMessage());
	}

	$route = $_REQUEST['Route'];
	$pickupLoc = $_REQUEST['PickupLoc'];
	$walkingSpeed = $_REQUEST['WalkingSpeed'];
	$pickupTime = strtotime($_REQUEST['PickupTime']);
	$alertTimes = json_decode($_REQUEST['AlertTimes']);
	$numAlerts = count($alertTimes);


	$realAlertTimes = array();
	for($i=0; $i<$numAlerts; $i++){
		$tmp = date("H:i", strtotime("-".($walkingSpeed+$alertTimes[$i])." minutes", $pickupTime));
		array_push($realAlertTimes, $tmp);
	}

	echo json_encode($realAlertTimes);

	// $stmt = $dbconn->prepare("INSERT INTO users (rcsid) VALUES (:rcsid)");
	// $stmt->execute(array(":rcsid"=>$rcsid));




?>