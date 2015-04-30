<?php
	include_once("./CAS-1.3.2/CAS.php");
	phpCAS::client(CAS_VERSION_2_0,'cas-auth.rpi.edu',443,'/cas/');
	phpCAS::setCasServerCACert("./CACert.pem");//this is relative to the cas client.php file

	require_once("./dbconfig.php");

	if (phpCAS::isAuthenticated()){
		$rcsid = phpCAS::getUser();
		$stmt = $dbconn->prepare("SELECT * FROM users WHERE rcsid = :rcsid");
		$stmt->execute(array(":rcsid"=>$rcsid));
		$row = $stmt->fetch();
    
		if(count($row) == 1){
			$stmt = $dbconn->prepare("INSERT INTO users (rcsid) VALUES (:rcsid)");
			$stmt->execute(array(":rcsid"=>$rcsid));
			header("Location: http://shuttlecatchers.myrpi.org/");
		}
	}

	$url = "http://shuttlecatchers.myrpi.org/";

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>RPI Shuttle Catchers</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="resources/css/bootstrap.min.css" type="text/css" />
    <link rel="stylesheet" href="resources/css/style.css" type="text/css" />

  </head>

	<body role="document" style="zoom: 1;">
		<!-- Fixed navbar -->
    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">RPI Shuttle Catchers</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li><a href="<?php echo $url;?>">Home</a></li>
            <li class="active"><a href="<?php echo $url.'schedules.php';?>">View Schedules</a></li>
            <li><a href="<?php echo $url.'scheduleAlert.php';?>">Schedule Alert</a></li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
          	<?php 
          		if (phpCAS::isAuthenticated()) {
	          		echo '<li><a href="'.$url.'profile.php" id="user">Welcome <span id="rcsid">'.phpCAS::getUser().'</span></a></li>';
	          		if($row['phonenumber'] == ""){

	          		}
          		} else {
          			echo '<li><a href="'.$url.'login.php" id="login" class="btn btn-success" >Login</a></li>';
          		}
          		echo '<li><a href="'.$url.'logout.php" id="logout" class="btn btn-danger" >Logout</a></li>';
          	?>
          
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>
    <div class="container theme-showcase" role="main">
    	<div class="jumbotron">
        <?php if($row['phonenumber'] == ""): ?>
        <h3>
          <span id="noNumber" class="label label-danger">You must enter a phone number (above) before you can set an alert!</span>
        </h1>
        
        <?php 
          else: 
          	echo "<h3>Schedules</h3>";
            $stmt = $dbconn->prepare("SELECT * FROM schedules WHERE rcsid = :rcsid");
            $stmt->execute(array(":rcsid"=>$rcsid));
            $table = "<table id='schedules' border='1'>";
            $table.= "<tr><th>Pickup Location</th><th>Time</th><th>Day</th><th>Alert Times</th><th>Recurring</th><th>Delete</th></tr>";
            while($row = $stmt->fetch()){

            	if(count($row) > 1){
            		$tableRow = "<tr><td>".$row['pickup_loc']."</td>";
            		$tableRow.="<td>".$row['pickup_time']."</td>";
            		if($row['recurring']){
            			$tableRow.="<td>".date("l", strtotime($row['date']))."'s</td>";
            		} else{
            			$tableRow.="<td>".$row['date']."</td>";
            		}

            		$alerts = json_decode($row['schedule']);
            		$tableRow.="<td>".date("H:i", strtotime($row['first_alert'])).", ";
            		for($i=0; $i<count($alerts);$i++){
            			$tableRow.=$alerts[$i].", ";
            		}

            		$tableRow = rtrim($tableRow,", ")."</td>";

            		if($row['recurring']){
            			$tableRow.="<td>Yes</td>";
            		} else {
            			$tableRow.="<td>No</td>";
            		}

            		$tableRow.="<td><input type='button' class='removeSchedule' ";
            		$tableRow.=" onclick='removeSchedule(".$row['sched_id'].");'";
            		$tableRow.= " value='Delete' /></td>";

            		$table.=$tableRow."</tr>";
	        	}
            }

            $table.="</table>";
            echo $table;

        endif;
           
        ?>
	    	
    </div>
    
    <footer class="footer">
      <div class="container">
        <p class="text-muted">
        	<a href="./credits.php">Credits</a> |
        	<a href="https://github.com/Frannycesca/RPI_Shuttle_Catchers">Github</a> |
        	<a href="mailto:shuttlecatchers@gmail.com">Email</a>
      	</p>
      </div>
    </footer>

		<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

		<script type="text/javascript" src="resources/js/jquery-1.11.2.min.js"></script>
  	<script src="resources/js/bootstrap.min.js"></script>
    <script src="resources/js/main.js" type="text/javascript"></script>

	</body>
</html>