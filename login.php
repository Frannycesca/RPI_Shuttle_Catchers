<?PHP

    include_once("./CAS-1.3.2/CAS.php");
    phpCAS::client(CAS_VERSION_2_0,'cas-auth.rpi.edu',443,'/cas/');
    // SSL!
    phpCAS::setCasServerCACert("./CACert.pem");//this is relative to the cas client.php file
    
    if (!phpCAS::isAuthenticated()) {
        phpCAS::forceAuthentication();
    }else{
        header('location: http://shuttlecatchers.myrpi.org/');
    }

  echo "login";

?>