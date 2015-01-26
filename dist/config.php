<?php
    // Setting Database Connection Credentials
    $dbhost = 'localhost';
    $dbname = 'tickethawk';

    function db_admin(){
        return array('admin', 'balloonrides');
    }

    function db_customer(){
        return array('customer', 'userpassword');
    }
?>
