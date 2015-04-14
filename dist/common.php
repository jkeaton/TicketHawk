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

    function login($_username, $_pass){
        // Set Database connection credentials
        global $dbhost, $dbname;
        $dbuser = $dbpass = $_SESSION['loginErr'] = "";
	    if ($_username === 'admin') {
            $creds = db_admin();
            $dbuser = array_values($creds)[0];
            $dbpass = array_values($creds)[1];
        }
        else {
            $creds = db_customer();
            $dbuser = array_values($creds)[0];
            $dbpass = array_values($creds)[1];
        }
        // Determine if the username entered exists in the database
		$query = "SELECT * FROM USER WHERE username = '$_username'";
	    $cxn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
	    $results = mysqli_query($cxn, $query) or die("Connection could not be established");
        // Check to ensure that exactly 1 row is included in the results
        if (mysqli_num_rows($results) == 1){
	        $row = mysqli_fetch_assoc($results);
            // Now ensure that the password entered matches the one in the
            // database by using the password_verify () method
            if (password_verify ($_pass , $row['hashed_pass'])){
	            if ($_username === 'admin') {
                    $_SESSION['valid_admin'] = true; 
                    $_SESSION['user_id'] = $row['user_id'];
                }
                else {
                    $_SESSION['valid_admin'] = false; 
                }
                $_SESSION['cart'] = array();
                return true;    
            }
        }
        // The only correct path hasn't been followed, so return false,
        // indicating an invalid login attempt

        // If the user has tried to login to a different user account
        // unsuccessfully, they are logged out of their current user and the
        // session is ended.
        soft_logout();
        return false;
    }

    function bounce(){
        if (!isset($_SESSION['valid_admin'])) {
            header('Location: http://localhost/TicketHawk/homepage.php');    
            return;
        }
        if ($_SESSION['valid_admin'] === false){
            header('Location: http://localhost/TicketHawk/homepage.php');    
            return;
        }
    }
	// For Front end users
	function ticketsAdmin($eventName){
			$creds = db_admin();
            $dbuser = array_values($creds)[0];
            $dbpass = array_values($creds)[1];
			global $dbhost, $dbname;
		 	$cxn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
			$query = "SELECT * FROM EVENT where eventname = '$eventName'";
			$results = mysqli_query($cxn, $query) or die("Connection could not be established");
			$ticket_qty="";
			$tickets_left = 0;
		$results = mysqli_query($cxn, $query) or die("Connection could not be established");
		while ($row = mysqli_fetch_assoc($results)) {
			if ($row['ticket_qty'] === 0) {
				$ticket_qty = "Sold Out!";
				return $ticket_qty;
			
			}
			else {
				return $row['ticket_sold'];
				
			}
		}

	}
	
		function tickets($eventName, $numberOfTickets){
			$creds = db_admin();
            $dbuser = array_values($creds)[0];
            $dbpass = array_values($creds)[1];
			global $dbhost, $dbname;
		 	$cxn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
			$query = "SELECT * FROM EVENT where eventname = '$eventName'";
			$results = mysqli_query($cxn, $query) or die("Connection could not be established");
			$ticket_qty="";
			$tickets_left = 0;
			while ($row = mysqli_fetch_assoc($results)) {
				if ($row['ticket_qty'] == 0) {
					$ticket_qty = "Sold Out!";
					return $ticket_qty;
				}
				else if($row['ticket_sold'] == 0) {
					 return $tickets_left;
					
				}
				else {
					$tickets_left = $row['ticket_qty'] - $row['ticket_sold'];
					return $tickets_left;
				}
		}
	}
?>
