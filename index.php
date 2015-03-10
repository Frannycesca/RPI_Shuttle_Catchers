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
	          	<div class="form-group">
	              <input type="text" placeholder="Email" class="form-control" />
	            </div>
	            <div class="form-group">
	              <input type="password" placeholder="Password" class="form-control" />
	            </div>
	            <button type="submit" class="btn btn-success">Sign in</button>
	            <button type="submit" class="btn btn-danger">Register</button>
	          </form>
	        </div>
	      </div>
	    </nav>
	    <div class="container theme-showcase" role="main">
	    	<div class="jumbotron">
		    	<form class="form-signin">
		    		
		    		<label for="route">Route:</label>
	    			<select id="route">
	    				<option>Please select a route</option>
	    				<option value="east">East</option>
	    				<option value="west">West</option>
	    				<option value="cdta">CDTA</option>
	    			</select>
	    			
	    			<br/>
	    			
	    			<label for="pickupLoc">Pick-up Location:</label>
	    			<select id="pickupLoc">
	    				<option>Please select a location</option>
	    			</select>

	    			<br/>

	    			<label for="walkingSpeed">Walking Speed (minutes):</label>
	    			<input type="number" id="walkingSpeed" />

	    			<br/>

	    			<label for="pickupTime">Pick-up Time:</label>
	    			<select id="pickupTime">
	    				<option>Please select a pick-up time</option>
	    				<option value="next">Next available shuttle</option>
	    			</select>

	    			<br/>

	    			<label for="alertTimes">Alerts:</label>
	    			<!-- <select id="alertTimes" multiple=""></select> -->
	    			<input type="checkbox" id="alertTimes" name="alert_times[]" value="5" />5 Minutes <br />
	    			<input type="checkbox" id="alertTimes" name="alert_times[]" value="10" />10 Minutes <br />
	    			<input type="checkbox" id="alertTimes" name="alert_times[]" value="15" />15 Minutes <br />

	    			<input type="button" id="setAlarm" value="Set Shuttle Alert!" />
			    </form>

			    <div id="output">
			    	<span id="out_route"></span> <br/>
			    	<span id="out_loc"></span> <br/>
			    	<span id="out_speed"></span> <br/>
			    	<span id="out_time"></span> <br/>
			    	<span id="out_alerts"></span>
			    </div>
	    	</div>
	    </div>

		
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
 				$("#pickupTime").html('<option>Please select a pick-up time</option><option value="next">Next available shuttle</option>');
 				for (var i=0; i<tmp.length; i++) {
					if(tmp[i].location == loc){
						var times = tmp[i].times;
						for(var j=0; j<times.length; j++){
							$("#pickupTime").append("<option value='"+times[j]+"'>"+times[j]+"</option>");
						}
					}
				}
			});
    	}

	    	$(document).ready(function(){
	    		var eastRoutes = ["Union","Colonie","Brinsmade","Sunset 1 & 2","E-lot","B-lot","9th/Sage","West lot","Sage Ave"];
	    		var westRoutes = ["Union","Sage Ave","Blitman","City Station","Poly Tech","15th & Collage"];
	    		var cdtaRoutes = ["Union"];

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
	    			var route = $("#route").val();
	    			var pickupLoc = $("#pickupLoc").val();
	    			var walkingSpeed = $("#walkingSpeed").val();
	    			var pickupTime = $("#pickupTime").val();


	    			var alertTimes = [];
	    			$("input[name='alert_times[]']:checked").each(function(){
	    				alertTimes.push(this.value);
	    			});

	    			// $("#output").html("")
	    			$("#out_route").html("Route: "+route);
	    			$("#out_loc").html("Pick-up Location: "+pickupLoc);
	    			$("#out_speed").html("Waling Speed: "+walkingSpeed);
	    			$("#out_time").html("Pick-up Time: "+pickupTime);
	    			$("#out_alerts").html("Alerts: ");
	    			for(var i=0;i<alertTimes.length;i++){
	    				$("#out_alerts").append(alertTimes[i]+", ");
	    			}

	    			// console.log(alertTimes);
	    			// alert(alertTimes[0].value);
	    			// console.log(alertTimes);
	    		});

	    		

	    	});

    	</script>

	</body>
</html>