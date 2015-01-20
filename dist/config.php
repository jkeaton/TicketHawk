<?php
// Configuration for server connection credentials
// $dbhost = 'localhost';
// $dbuser = 'admin';
// $dbpass = 'balloonrides';
// $dbname = 'tickethawk';
$_SESSION['loginErr'] = "test1";
function login() {
	
	if ($_SESSION['email'] == "test@aemail.com") {
		$dbuser = 'admin';
		$dbpass = 'balloonrides';
	}
	else {
		$dbuser = 'customer';
		$dbpass = 'userpassword';
	}
	$dbhost = 'localhost';
	$dbname = 'tickethawk';
	$cxn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
	$query = "SELECT * FROM Accounts WHERE email = '".$_SESSION['email']."' ";
	$results = mysqli_query($cxn, $query) or die("Connection could not be established");
	$row = mysqli_fetch_assoc($results); {

		if ($_SESSION['email'] != null & $_SESSION['pwd'] != null) {

			if ($_SESSION['email'] == "test@aemail.com") {
				if (md5($_SESSION['pwd']) == md5($row['password'])) {
					$_SESSION['loginErr'] = "Admin";
					header("Location:http://localhost/TicketHawk/admin_page.html");
				} else {
					$_SESSION['loginErr'] = "Login Err";
					header("Location:http://localhost/TicketHawk/homepage.php");
					$_SESSION['loginInfo'] = "Check email and password";
					echo $_SESSION['loginInfo'];
				}
			 } else {
				 if (md5($_SESSION['pwd']) == md5($row['password'])) {
					 $_SESSION['loginErr'] =$row['name'];
					 header("Location:http://localhost/TicketHawk/homepage.php");
				 } else {
					 $_SESSION['loginErr'] = "Login Error";
					 header("Location:http://localhost/TicketHawk/homepage.php");
				 }
			}
		 } else {
			 $_SESSION['loginErr'] = "Login Error";
			 header("Location:http://localhost/TicketHawk/homepage.php");
		}
	}
}

?>
