<?php
// Configuration for server connection credentials
// $dbhost = 'localhost';
// $dbuser = 'admin';
// $dbpass = 'balloonrides';
// $dbname = 'tickethawk';
// $_SESSION['loginErr'] = "test1";
function login() {

	if ($_SESSION['email'] == "admin1@email.com" || $_SESSION['email'] == "admin2@email.com") {
		$dbuser = 'admin';
		$dbpass = 'balloonrides';
		$query = "SELECT * FROM Admin_Accounts WHERE email = '" . $_SESSION['email'] . "' ";
		$admin = TRUE;
	} 
	else {
		$dbuser = 'customer';
		$dbpass = 'userpassword';
		$query = "SELECT * FROM Accounts WHERE email = '" . $_SESSION['email'] . "' ";
	}
	$dbhost = 'localhost';
	$dbname = 'tickethawk';
	$cxn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
	$results = mysqli_query($cxn, $query) or die("Connection could not be established");
	$row = mysqli_fetch_assoc($results); {
		if ($_SESSION['email'] != null && $_SESSION['pwd'] != null) {
			if (md5($_SESSION['pwd']) == md5($row['password'])) {
				$_SESSION['loginErr'] = $row['name'];
				if($admin) {
					header('Location: http://localhost/TicketHawk/admin_page.html');
				} 
				else {
					header('Location: http://localhost/TicketHawk/homepage.php');
				}
			} 
			else {
				$_SESSION['loginErr'] = "Login Error";
				header('Location: http://localhost/TicketHawk/homepage.php');
			}
		} 
		else {
			$_SESSION['loginErr'] = "Login Error";
			header('Location: http://localhost/TicketHawk/homepage.php');
		}
	}
}

function createNewAccount($_username, $_fname, $_lname, $_street, $_city, $_state, $_zipcode, $_email, $_password) {
	$dbuser = 'customer';
	$dbpass = 'userpassword';
	$dbhost = 'localhost';
	$dbname = 'tickethawk';
	$cxn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
	$query = "INSERT INTO USER
    (username, fname, lname, street_address, city, state, zipcode, email, hashed_pass)
    VALUES ('$_username', '$_fname', '$_lname', '$_street', '$_city', '$_state', '$_zipcode', '$_email', '$_password')";
	$results = mysqli_query($cxn, $query) or die($query);
}

function test_input($data) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	$data = strip_tags($data);
	return $data;
}
?>
