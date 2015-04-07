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
	    	<h1>Credits</h1>

	    	<ul>
	    		<li><a href="https://github.com/clarkb7/Pebble_RPI_Shuttle_Schedule">Pebble_RPI_Shuttle_Schedule</a></li>
          <li><a href="https://buildingtents.wordpress.com/2013/04/16/rpi-phpcas-authentication-tutorial/">RPI phpCAS Auth</a></li>
	    	</ul>
    	</div>
    </div>

		<div id="map-canvas"></div>
		<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

		<script type="text/javascript" src="./js/jquery-1.11.2.min.js"></script>
  	<script src="./js/bootstrap.min.js"></script>


	</body>
</html>