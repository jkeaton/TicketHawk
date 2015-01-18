<?php
// Open a Connection to the MySQL Database
$conn = mysql_connect($dbhost, $dbuser, $dbpass) or die('Error connecting to mysql');
mysql_select_db($dbname);
?>
