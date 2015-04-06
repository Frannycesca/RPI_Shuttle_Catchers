<?PHP

    include_once("./CAS-1.3.2/CAS.php");
    phpCAS::client(CAS_VERSION_2_0,'cas-auth.rpi.edu',443,'/cas/');
    // SSL!
    phpCAS::setCasServerCACert("./CACert.pem");//this is relative to the cas client.php file
    
    if (phpCAS::isAuthenticated())
    {
        // phpCAS::logout();
        phpCAS::logoutWithUrl("http://127.0.01/sdd");
        // phpCAS::logout(array('service'=>'http://127.0.01/sdd'));
    }else{
        header('location: ./index.php');
    }
?>