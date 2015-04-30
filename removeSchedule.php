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

	$rcs_id = $_REQUEST['rcsid'];
	$sched_id = $_REQUEST['schedid'];
	
	if($rcs_id != $rcsid){
		echo "Error: RCS Id's do not match";
		phpCAS::logout(array('service'=>'http://shuttlecatchers.myrpi.org/'));
	}

	$stmt = $dbconn->prepare("DELETE FROM schedules WHERE rcsid = :rcsid AND sched_id = :sched_id");
	$stmt->execute(array(":rcsid"=>$rcsid, ":sched_id"=>$sched_id));

?>