<?php
	include_once("./CAS-1.3.2/CAS.php");
	phpCAS::client(CAS_VERSION_2_0,'cas-auth.rpi.edu',443,'/cas/');
	// SSL!
	phpCAS::setCasServerCACert("./CACert.pem");//this is relative to the cas client.php file


	try {
	  $host = 'localhost';
	  $root = 'root';

	  $dbconn = new PDO("mysql:host=$host;dbname=shuttlecatchers;",$root,$password);

	} catch (PDOException $e) {
	  die("Database Error: ". $e->getMessage());
	}

	if (phpCAS::isAuthenticated()){
		$rcsid = phpCAS::getUser();
		$stmt = $dbconn->prepare("SELECT * FROM users WHERE rcsid = :rcsid");
		$stmt->execute(array(":rcsid"=>$rcsid));
		$row = $stmt->fetch();

		if(count($row) == 0){
			$stmt = $dbconn->prepare("INSERT INTO users (rcsid) VALUES (:rcsid)");
			$stmt->execute(array(":rcsid"=>$rcsid));
			header("Location: index.php");
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
        	<form class="navbar-form navbar-right">
	        	<?php if (phpCAS::isAuthenticated()) : ?>
	        		<div id="user">Welcome <span id="rcsid"><?php echo phpCAS::getUser(); ?></span></div>
	        		<?php if($row['phonenumber'] == ""): ?>
		        		<div class="form-group">
		              		<input type="tel" placeholder="XXX-XXX-XXXX" id="phoneNumber" class="form-control" />
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
		    				<option value="cdta">CDTA</option>
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
		    				<option value="next">Next available shuttle</option>
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


    </div>

    <footer>
    	<a href="./credits.php">Credits</a>
    </footer>

		<div id="map-canvas"></div>
		<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

		<script type="text/javascript" src="./js/jquery-1.11.2.min.js"></script>
  	<script src="./js/bootstrap.min.js"></script>

  	<script type="text/javascript">

    	function getSchedule(route, loc){
    		$.getJSON("rpi_shuttle_schedule.json", function(data){
    			var today = new Date();
    			var dayOfWeek = today.getDay();
    			// var time = today.getTime();
    			console.log(data);

    			if(route == "east"){
    				// it's the weekend
    				if(dayOfWeek == 0 || dayOfWeek == 6){
						var tmp = data.Weekend_East;
    				}
    				// it's a week day
    				else if(dayOfWeek > 0 && dayOfWeek < 6){
    					var tmp = data.Weekday_East;
    				}
    			}
 				else if(route == "west") {
 					// it's sunday
 					if(dayOfWeek == 0){
						var tmp = data.Sunday_West;
    				}
    				// it's saturday
    				else if(dayOfWeek == 6) {
    					var tmp = data.Saturday_West;
    				} 
    				// it's a week day
    				else{
    					var tmp = data.Weekday_West;
    				}
 				}
 				// var time = new Date().toGMTString();
 				var hour = today.getHours();
 				// var shuttleClosed = false;
 				if(hour >= 23){
 					// shuttleClosed = true;
 					//$("#setAlarm").hide();
 				}
 				else {
 					var minute = today.getMinutes();
	 				if(minute<10){
	 					minute = "0"+minute;
	 				}
	 				// after 11pm (last shuttle), the alerts start for the next day
	 				// alert(hour+":"+minute);

	 				$("#pickupTime").html('<option>Please select a pick-up time</option><option value="next">Next available shuttle</option>');
	 				for (var i=0; i<tmp.length; i++) {
						if(tmp[i].location == loc){
							var times = tmp[i].times;
							for(var j=0; j<times.length; j++){
								$("#pickupTime").append("<option value='"+times[j]+"'>"+times[j]+"</option>");
							}
						}
					}
 				}

 				
			});
    	}

	    	$(document).ready(function(){
	    		var eastRoutes = ["Union","Colonie","Brinsmade","Sunset 1 & 2","E-lot","B-lot","9th/Sage","West lot","Sage Ave"];
	    		var westRoutes = ["Union","Sage Ave","Blitman","City Station","Poly Tech","15th & Collage"];
	    		var cdtaRoutes = ["Union"];

	    		var oneDay = 24*3600*1000;
	    		var d = new Date();
	    		var tmp = (d.getMonth()+1)+"/"+d.getDate();

	    		$("#pickupDate").append("<option value='"+tmp+"'>Today</option>");

	    		for(var i=0; i<7; i++){
	    			d.setMilliseconds(d.getMilliseconds()+oneDay);
	    			tmp = (d.getMonth()+1)+"/"+d.getDate();
	    			$("#pickupDate").append("<option value='"+tmp+"'>"+tmp+"</option>");
	    		}

	    		$("#route").change(function() {
	    			$("#pickupLoc").html("<option>Please select a location</option>");
	    			if(this.value == "east"){
	    				for(var i=0; i<eastRoutes.length; i++){
	    					$("#pickupLoc").append("<option value='"+eastRoutes[i]+"'>"+eastRoutes[i]+"</option>");
	    				}

	    			} else if(this.value == "west") {
	    				for(var i=0; i<westRoutes.length; i++){
	    					$("#pickupLoc").append("<option value='"+westRoutes[i]+"'>"+westRoutes[i]+"</option>");
	    				}
	    			} else if(this.value == "cdta") {
	    				for(var i=0; i<cdtaRoutes.length; i++){
	    					$("#pickupLoc").append("<option value='"+cdtaRoutes[i]+"'>"+cdtaRoutes[i]+"</option>");
	    				}
	    			}

	    		});

	    		$("#pickupLoc").change(function(){
	    			getSchedule($("#route").val(),this.value);
	    		});

	    		$("#setAlarm").click(function(){
	    			$("#error").css('visibility', 'hidden');
	    			$("#route").removeClass("error");
	    			$("#pickupLoc").removeClass("error");
	    			$("#walkingSpeed").removeClass("error");
	    			$("#pickupDate").removeClass("error");
	    			$("#pickupTime").removeClass("error");

	    			var route = $("#route").val();
	    			var pickupLoc = $("#pickupLoc").val();
	    			var walkingSpeed = $("#walkingSpeed").val();
	    			var pickupDate = $("#pickupDate").val();
	    			var pickupTime = $("#pickupTime").val();


	    			//console.log(route + " " + pickupLoc + " " + walkingSpeed + " " + pickupDate + " " + pickupTime);
	    			var error = false;
	    			if (route === "Please select a route") {
	    				$("#route").addClass("error");
	    				error = true;
	    			}
	    			if (pickupLoc === "Please select a location") {
	    				$("#pickupLoc").addClass("error");
	    				error = true;
	    			}
	    			if (walkingSpeed == '' || walkingSpeed.length == 0) {
	    				$("#walkingSpeed").addClass("error");
	    				error = true;
	    			}
	    			if (pickupDate === "Please select a pick-up date") {
	    				$("#pickupDate").addClass("error");
	    				error = true;
	    			}
	    			if (pickupTime === "Please select a pick-up time") {
	    				$("#pickupTime").addClass("error");
	    				error = true;
	    			}
	    			//console.log(error);

	    			if (error === false) {
		    			var alertTimes = [];
		    			$("input[name='alert_times[]']:checked").each(function(){
		    				alertTimes.push(this.value);
		    			});



		    			$("#out_route").html("Route: "+route);
		    			$("#out_loc").html("Pick-up Location: "+pickupLoc);
		    			$("#out_speed").html("Waling Speed: "+walkingSpeed);
		    			$("#out_time").html("Pick-up Time: "+pickupTime);
		    			$("#out_alerts").html("Alerts: ");
		    			// for(var i=0;i<alertTimes.length;i++){
		    			// 	$("#out_alerts").append(alertTimes[i]+", ");
		    			// }


		    			$.ajax({
		    				url: "setAlarm.php",
		    				data: {
		    					Route: route,
		    					PickupLoc: pickupLoc,
		    					WalkingSpeed: walkingSpeed,
		    					PickupDate: pickupDate,
		    					PickupTime: pickupTime,
		    					AlertTimes: JSON.stringify(alertTimes)
		    				},
		    				success: function(data){
		    					var times = JSON.parse(data);

		    					for(var i=0; i<times.length; i++){
		    						$("#out_alerts").append(times[i]+", ");
		    					}
		    				}
		    			});
		    		}
		    		else {
		    			$("#error").css('visibility', 'visible');
		    		}

	    		});	    		
				
				$("#login").click(function(){
					window.location.href = "./login.php";
				});

				$("#savePhone").click(function(){

					var phone = $("#phoneNumber").val();

					var phoneRe = /^[2-9]\d{2}[2-9]\d{2}\d{4}$/;
				  	var digits = phone.replace(/\D/g, "");

				  	if(digits.match(phoneRe) !== null){
				  		var rcs_id = $("#rcsid").html();

				  		$.ajax({
				  			url: "addNumber.php",
				  			data: {
				  				rcsid: rcs_id,
				  				phonenumber: digits
				  			},
				  			success: function(data){
				  				location.reload();
				  			}
				  		});
				  	} else{
				  		alert("Invalid phone number");
				  	}
				  	// alert(digits.match(phoneRe));
					// return (digits.match(phoneRe) !== null);


					// if(phone.match(/^[(]{0,1}[0-9]{3}[)]{0,1}[-\s\.]{0,1}[0-9]{3}[-\s\.]{0,1}[0-9]{4}$/) == null){
					// 	alert("Invalid phone number");
					// }
					// alert(phone.match(/^[(]{0,1}[0-9]{3}[)]{0,1}[-\s\.]{0,1}[0-9]{3}[-\s\.]{0,1}[0-9]{4}$/));
					// alert(phone);
					// if(phone < 10){
					// 	alert("Invalid phone number");
					// 	return 1;
					// }

					// alert(phone);
				});

	    	});

    	</script>

	</body>
</html>