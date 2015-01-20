<?php
// Open a Connection to the MySQL Database
$dbhost = 'localhost';
$dbuser = 'admin';
$dbpass = 'balloonrides';
$dbname = 'tickethawk';
$conn = mysqli_connect($dbhost, $dbuser, $dbpass,$dbname) or die('Error connecting to server');
?>
