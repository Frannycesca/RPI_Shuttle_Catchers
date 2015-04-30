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
            <li><a href="<?php echo $url.'schedules.php';?>">View Schedules</a></li>
            <li class="active"><a href="<?php echo $url.'scheduleAlert.php';?>">Schedule Alert</a></li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <?php 
              if (phpCAS::isAuthenticated()) {
                echo '<li><a href="'.$url.'profile.php" id="user">Welcome <span id="rcsid">'.phpCAS::getUser().'</span></a></li>';
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
        <?php if($row['phonenumber'] == "") : ?>
        	<h3>
						<span id="noNumber" class="label label-danger">Before you can schedule alerts, you need to enter your phone number!</span>
					</h3>
        		
    			<form class="form-signin">
	        	<label for="phoneNumber">Phone Number:</label>
	        	<input type="tel" id="phoneNumber" class="form-control" placeholder="XXX-XXX-XXXX" required autofocus>
	        	<div>&nbsp;</div>
	        	<label for="phoneCarrier">Phone Carrier:</label>
	        	<select id="phoneCarrier">
            	<option>Please Select One</option>
              <option value="att">AT&amp;T</option>
              <option value="boost">Boost Mobile</option>
              <option value="sprint">Sprint</option>
              <option value="tmobile">T-Mobile</option>
              <option value="verizon">Verizon</option>
              <option value="virgin">Virgin Mobile</option>
            </select>
	        	<div>&nbsp;</div>
	        	<input type="button" id="savePhone" class="btn btn-lg btn-primary btn-block" value="Save Phone Number">
	      	</form>
        
        <?php else: ?>
          <h2 id="welcome">Schedule Alert</h2>
          <br/>
          <div id="alertType" class="btn-group btn-group-justified" role="group" aria-label="Alert Type">
            <a href="#" id="singleAlert" class="btn btn-default" role="button">Single Alert</a>
            <a href="#" id="recurringAlert" class="btn btn-default" role="button">Recurring Alert</a>
          </div>

        	<form class="form-signin" id="scheduleForm">
             <div class="row">
              <div class="col-1">
                <label for="route">Route:</label>
              </div>
              <div class="col-2">
                <select id="route">
                  <option>Please select a route</option>
                  <option value="east">East</option>
                  <option value="west">West</option>
                </select>
              </div>
            </div>

            <div class="row">
              <div class="col-1">
                <label for="pickupLoc">Pick-up Location:</label>
              </div>
              <div class="col-2">
                <select id="pickupLoc">
                  <option>Please select a location</option>
                </select>
              </div>
            </div>

            <div class="row">
              <div class="col-1">
                <label for="walkingSpeed">Walking Speed (minutes):</label>
              </div>
              <div class="col-2">
                <input type="number" id="walkingSpeed" />
              </div>
            </div>

           <div class="row">
              <div class="col-1">
                <label for="pickupDate">Pick-up Date:</label>
                <label for="pickupDay">Pick-up Day:</label>
              </div>
              <div class="col-2">
                <select id="pickupDate">
                  <option>Please select a pick-up date</option>
                </select>
                <select id="pickupDay">
                  <option>Please select a pick-up day</option>
                  <option value="monday">Monday</option>
                  <option value="tuesday">Tuesday</option>
                  <option value="wednesday">Wednesday</option>
                  <option value="thursday">Thursday</option>
                  <option value="friday">Friday</option>
                  <option value="saturday">Saturday</option>
                  <option value="sunday">Sunday</option>
                </select>
              </div>
            </div>

            <div class="row">
              <div class="col-1">
                <label for="pickupTime">Pick-up Time:</label>
              </div>
              <div class="col-2">
                <select id="pickupTime">
                  <option>Please select a pick-up time</option>
                </select>
              </div>
            </div>

            <div class="row">
              <div class="col-1">
                <label for="alertTimes">Alerts:</label>
              </div>
              <div class="col-2">
                <input type="checkbox" id="alertTimes" name="alert_times[]" value="5" /> 5 Minutes
              </div>
            </div>

            <div class="row">
              <div class="col-1">&nbsp;</div>
              <div class="col-2">
                <input type="checkbox" id="alertTimes" name="alert_times[]" value="10" /> 10 Minutes
              </div>
            </div>

            <div class="row">
              <div class="col-1">&nbsp;</div>
              <div class="col-2">
                <input type="checkbox" id="alertTimes" name="alert_times[]" value="15" /> 15 Minutes
              </div>
            </div>
            
            <div class="row">
              <div class="col-xs-6">&nbsp;</div>
              <div class="col-cs-6">
                <input type="button" id="setAlarm" value="Set Shuttle Alert!" />
              </div>
            </div>
          </form>
          
          <div id="error">
            Please fill all values
          </div>

          <div id="output">
            <span id="out_route"></span> <br/>
            <span id="out_loc"></span> <br/>
            <span id="out_speed"></span> <br/>
            <span id="out_time"></span> <br/>
            <span id="out_alerts"></span>
          </div>
        </div>

      <?php endif; ?>
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