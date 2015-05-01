<?php
	include_once("./CAS-1.3.2/CAS.php");
	phpCAS::client(CAS_VERSION_2_0,'cas-auth.rpi.edu',443,'/cas/');
	phpCAS::setCasServerCACert("./CACert.pem");//this is relative to the cas client.php file

	require_once("./dbconfig.php");

  //checks login. if entered rcs and password is valid, redirect to shuttlecatchers page
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

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>RPI Shuttle Catchers</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="./css/bootstrap.min.css" type="text/css" />
    <link rel="stylesheet" href="./css/style.css" type="text/css" />

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
          <a class="navbar-brand" href=".">RPI Shuttle Catchers</a>

        </div>
        <div id="navbar" class="navbar-collapse collapse">
        	<ul class="nav navbar-nav">
        		<li>
        			<a href="">Home</a>
        		</li>
        		<li>
        			<a href="#">View Schedule</a>
        		</li>
        	</ul>
        	<form class="navbar-form navbar-right">
	        	<?php if (phpCAS::isAuthenticated()) : ?>
	        		<div id="user">Welcome <span id="rcsid"><?php echo phpCAS::getUser(); ?></span></div>
	        		<?php if($row['phonenumber'] == ""): ?>
		        		<div class="form-group">
		              		<input type="tel" placeholder="XXX-XXX-XXXX" id="phoneNumber" class="form-control" />
                      <select id="phoneCarrier">
                        <option>Phone Carrier</option>
                        <option value="att">AT&amp;T</option>
                        <option value="boost">Boost Mobile</option>
                        <option value="sprint">Sprint</option>
                        <option value="tmobile">T-Mobile</option>
                        <option value="verizon">Verizon</option>
                        <option value="virgin">Virgin Mobile</option>
                      </select>
		            	</div>
		            	<input type="button" id="savePhone" class="btn btn-success" value="Save Phone">
		            <?php else: ?>
		            	<span class="label label-primary"><?php echo $row['phonenumber'].PHP_EOL; ?></span>
		            <?php endif; ?>
                  <a href="./logout.php" class="btn btn-danger">Logout</a>
                <?php else: ?>
                  <input type="button" id="login" class="btn btn-success" value="Login">
                <?php endif; ?>
        	</form>
        </div>
      </div>
    </nav>
    <div class="container theme-showcase" role="main">
    	<div class="jumbotron">
 <!--        <?php if($row['phonenumber'] == ""): ?>
        <h3>
          <span id="noNumber" class="label label-danger">You must enter a phone number (above) before you can set an alert!</span>
        </h1> -->
        
        <?php 
          // else: 
            $stmt = $dbconn->prepare("SELECT * FROM schedules WHERE rcsid = :rcsid");
            $stmt->execute(array(":rcsid"=>$rcsid));
            $row = $stmt->fetch();
                        
            if(count($row) > 1):
              echo "Pickup Location: ".$row['pickup_loc'];
              echo "<br>Pickup Time: ".$row['pickup_time'];
              $alerts = json_decode($row['schedule']);
              echo "<br>Alerts: ".$row['first_alert'];
              for($i=0; $i<count($alerts);$i++){
                echo "<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$alerts[$i];
              }
              
            else:
           
        ?>
	    	<form class="form-signin">
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
    				<!-- <div class="col-md-2">&nbsp;</div> -->
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
		    		</div>
		    		<div class="col-2">
		    			<select id="pickupDate">
		    				<option>Please select a pick-up date</option>
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
		    				<!--<option value="next">Next available shuttle</option>-->
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

      <?php endif; endif; ?>
    </div>
    
    <footer class="footer">
      <div class="container">
        <p class="text-muted"><a href="./credits.php">Credits</a></p>
      </div>
    </footer>


		<div id="map-canvas"></div>
		<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

		<script type="text/javascript" src="./js/jquery-1.11.2.min.js"></script>
  	<script src="./js/bootstrap.min.js"></script>

    <script src="js/main.js" type="text/javascript"></script>

	</body>
</html>