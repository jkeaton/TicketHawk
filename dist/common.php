<?php
    include "dist/config.php";

    // Place for functions commonly used
    function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        $data = strip_tags($data);
        return $data;
    }

    // This can be used when the User has explicitly tried to logout of the
    // current session. In this case the session is actually destroyed.
    function logout(){
        session_unset(); 
        session_destroy();
        header('Location: http://localhost/TicketHawk/homepage.php');
        return 0;
    }

    // In this case the session is not destroyed because we'd like to update the
    // $_SESSION['loginErr'] variable because it can persist across the
    // different php pages and when the user is redirected they can view the
    // login error and know something went wrong.
    function soft_logout(){
        session_unset(); 
        $_SESSION['loginErr'] = "Login Error";
        header('Location: http://localhost/TicketHawk/homepage.php');
        return 0;
    }

    // Check to ensure that the username entered is currently available
    function availableUser($username){
        // Get ready to connect to the database
        global $dbhost, $dbname;
        $creds = db_customer(); // no need for admin privileges here
        $dbuser = array_values($creds)[0];
        $dbpass = array_values($creds)[1];
	    $connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
		$query = "SELECT * FROM USER WHERE username = '$username'";
        // Query the database to get all results that match the current username
	    $results = mysqli_query($connection, $query) or die("Connection could not be established");
        // return true only if the number of results returned from the database
        // is equale to 0, meaning there are no users in the database that
        // match the current username.
        return (mysqli_num_rows($results) == 0);
    }
?>
