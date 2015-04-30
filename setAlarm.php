<?php

	include_once("./CAS-1.3.2/CAS.php");
	phpCAS::client(CAS_VERSION_2_0,'cas-auth.rpi.edu',443,'/cas/');
	phpCAS::setCasServerCACert("./CACert.pem");//this is relative to the cas client.php file

  	if (phpCAS::isAuthenticated()){
		$rcsid = phpCAS::getUser();
	} else {
		echo "Error not logged in";
	}

	require_once("./dbconfig.php");

	$route = $_REQUEST['Route'];
	$pickupLoc = $_REQUEST['PickupLoc'];
	$walkingSpeed = $_REQUEST['WalkingSpeed'];
  $pickupDate = strtotime($_REQUEST['PickupDate']);
	$pickupTime = strtotime($_REQUEST['PickupTime']);
	$alertTimes = json_decode($_REQUEST['AlertTimes']);
	$alertType = $_REQUEST['AlertType'];
	$numAlerts = count($alertTimes);


	$realAlertTimes = array();
	for($i=0; $i<$numAlerts; $i++){
		$tmp = date("H:i", strtotime("-".($walkingSpeed+$alertTimes[$i])." minutes", $pickupTime));
		array_push($realAlertTimes, $tmp);
	}
  
	$pickupDate = date("Y-m-d", $pickupDate);
	$pickupTime = date("H:i:s",  $pickupTime);

	$realAlertTimes = array_reverse($realAlertTimes);
	$recur_sched = $realAlertTimes;
	$firstAlert = array_shift($realAlertTimes);

	if($alertType == "single"){
		$stmt = $dbconn->prepare("INSERT INTO schedules (rcsid, date, pickup_time, pickup_loc, schedule, first_alert) VALUES (:rcsid, :date, :pickupTime, :pickupLoc, :schedule, :firstAlert)");
		$stmt->execute(array(":rcsid"=>$rcsid, ":date"=>$pickupDate, ":pickupTime"=>$pickupTime,":pickupLoc"=>$pickupLoc,  ":schedule"=>json_encode($realAlertTimes), ":firstAlert"=>$firstAlert));
	} else {
		$stmt = $dbconn->prepare("INSERT INTO schedules (rcsid, date, pickup_time, pickup_loc, schedule, first_alert, recurring, recur_sched) VALUES (:rcsid, :date, :pickupTime, :pickupLoc, :schedule, :firstAlert, 1, :recur_sched)");
		$stmt->execute(array(":rcsid"=>$rcsid, ":date"=>$pickupDate, ":pickupTime"=>$pickupTime,":pickupLoc"=>$pickupLoc,  ":schedule"=>json_encode($realAlertTimes), ":firstAlert"=>$firstAlert, ":recur_sched"=>json_encode($recur_sched)));
	}

 	echo "Alerts set";

?>