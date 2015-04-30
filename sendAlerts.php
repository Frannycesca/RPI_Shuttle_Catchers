<?php

  require("./PHPMailer/PHPMailerAutoload.php");
  
  require_once("./dbconfig.php");
  
  date_default_timezone_set('America/New_York');
  
  //Create a new PHPMailer instance
  $mail = new PHPMailer;

  //Tell PHPMailer to use SMTP
  $mail->isSMTP();

  //Enable SMTP debugging
  // 0 = off (for production use)
  // 1 = client messages
  // 2 = client and server messages
  $mail->SMTPDebug = 0;

  //Ask for HTML-friendly debug output
  $mail->Debugoutput = 'html';

  //Set the hostname of the mail server
  $mail->Host = 'smtp.gmail.com';

  //Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
  $mail->Port = 587;

  //Set the encryption system to use - ssl (deprecated) or tls
  $mail->SMTPSecure = 'tls';

  //Whether to use SMTP authentication
  $mail->SMTPAuth = true;

  //Username to use for SMTP authentication - use full email address for gmail
  $mail->Username = "shuttlecatchers@gmail.com";

  //Password to use for SMTP authentication
  $mail->Password = "shuttleatRPI";

  //Set who the message is to be sent from
  $mail->setFrom('shuttlecatchers@gmail.com', 'ShuttleCatchers at RPI');

  //Set an alternative reply-to address
  $mail->addReplyTo('shuttlecatchers@gmail.com', 'ShuttleCatchers at RPI');

  $today = date("Y-m-d");
  $timeR1 = date("H:i:00");
  $timeR2 = date("H:i", strtotime("+1 hour"));

  if($timeR1 > "23:00:00"){
    exit();
  }
  
  echo $timeR1." - ".$timeR2."<br>".PHP_EOL;
  
  echo $today."<br>";

  
  
//  $stmt = $dbconn->prepare("SELECT * FROM schedules WHERE date = :today AND first_alert >= :timeR1 AND first_alert <= :timeR2;");
//  $stmt->execute(array(":today"=>$today, ":timeR1"=>$timeR1, ":timeR2"=>$timeR2));
  
  $stmt = $dbconn->prepare("SELECT * FROM schedules WHERE date = :today AND first_alert = :timeR1;");
  $stmt->execute(array(":today"=>$today, ":timeR1"=>$timeR1));
  
  // $stmt = $dbconn->prepare("SELECT * FROM schedules WHERE date = :today ;");
  // $stmt->execute(array(":today"=>$today));
 
  while($row = $stmt->fetch()){
    $tmpMail = $mail;
    $rcsid = $row['rcsid'];
    $sched_id = $row['sched_id'];
    $alertTimes = json_decode($row['schedule']);
    $pickupTime = $row['pickup_time'];
    $pickupLoc = $row['pickup_loc'];
    $alertTime = $row['first_alert'];
    $recurring = $row['recurring'];
    $recur_sched = json_decode($row['recur_sched']);

    $Userstmt = $dbconn->prepare("SELECT * FROM users WHERE rcsid = :rcsid;");
    $Userstmt->execute(array(":rcsid"=>$rcsid));
    $userData = $Userstmt->fetch();

    $phoneNumber = $userData['phonenumber'];
    $carrier = $userData['carrier'];

    switch($carrier){
      case "att":
        $phoneNumber .= "@txt.att.net";
        break;
      case "boost":
        $phoneNumber .= "@myboostmobile.com";
        break;
      case "sprint":
        $phoneNumber .= "@messaging.sprintpcs.com";
        break;
      case "tmobile":
        $phoneNumber .= "@tmomail.net";
        break;
      case "verizon":
        $phoneNumber .= "@vtext.com";
        break;
      case "virgin":
        $phoneNumber .= "@vmobl.com";
        break;
      default:
        $phoneNumber = $rcsid."@rpi.edu";
        break;
    }
    
    $warningTime =  (( strtotime($pickupTime) - strtotime($alertTime) ) / 60);
    echo "alert time: ".$alertTime."<br>";
    //Set who the message is to be sent to
    $tmpMail->addAddress($phoneNumber, '');

    //Set the subject line
    $tmpMail->Subject = 'Shuttle Alert for '.$pickupLoc.' pickup @ '.$pickupTime;

    //Read an HTML message body from an external file, convert referenced images to embedded,
    //convert HTML into a basic plain-text alternative body
    $tmpMail->msgHTML("$warningTime Minute warning");

    //Replace the plain text body with one created manually
    $tmpMail->AltBody = "$warningTime Minute warning";

    //send the message, check for errors
    if (!$tmpMail->send()) {
        echo "Mailer Error: " . $tmpMail->ErrorInfo;
    } else {
        echo "Message sent!<br>";

        $nextAlert = array_shift($alertTimes);
        print_r($alertTimes);
        echo $pickupLoc."->".$nextAlert;
        
        if($nextAlert == ""){
          echo "<br>no next";
          if($recurring == 0){
            $stmt = $dbconn->prepare("DELETE FROM schedules WHERE sched_id = :sched_id");
            $stmt->execute(array(":sched_id"=>$sched_id));
          } else {
            $nextWeek = date("Y-m-d",strtotime("+1 week"));
            $alertTimes = $recur_sched;
            $nextAlert = array_shift($alertTimes);
            $stmt = $dbconn->prepare("UPDATE schedules SET date = :nextWeek, schedule = :schedule, first_alert = :firstAlert WHERE sched_id = :sched_id");
            $stmt->execute(array(":nextWeek"=>$nextWeek, ":schedule"=>json_encode($alertTimes), ":firstAlert"=>$nextAlert, ":sched_id"=>$sched_id));
          }
          
        } else{
          $stmt = $dbconn->prepare("UPDATE schedules SET schedule = :schedule, first_alert = :firstAlert WHERE sched_id = :sched_id");
          $stmt->execute(array(":schedule"=>json_encode($alertTimes), ":firstAlert"=>$nextAlert, ":sched_id"=>$sched_id));
        }        
    }
    
  }
