<?php

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


?>