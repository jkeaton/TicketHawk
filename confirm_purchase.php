<!-- Connect to Database -->
<?php
    session_start();
    include "dist/common.php";
    $usernameErr = $newunameErr = $fnameErr = $lnameErr = $streetErr = $cityErr = $stateErr = $zipcodeErr = $emailErr = $passwordErr = $newpassErr = $confirmPassErr = "";
    $username = $uname = $fname = $lname = $street = $city = $state = $zipcode = $email = $password = $pass = $hashed_pass = "";
    $welcome_msg = "";

    // Fetch the Events from the database
    global $dbhost, $dbname;
    $creds = db_admin();
    $dbuser = array_values($creds)[0];
    $dbpass = array_values($creds)[1];
    $cxn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
    $query = "SELECT * FROM EVENT";
    $results = mysqli_query($cxn, $query) or die("Connection could not be established");
    $events = array();
    $price_total = 0.00;
    storeEventRows();
    $line_items = generateLineItems();

    function storeEventRows(){
        global $events, $results;
        while ($row = mysqli_fetch_assoc($results)) {
            $events[strval($row['eventid'])] = $row;
        }
    }

    // If the current session includes a valid user, display the welcome label
    if (isset($_SESSION['user'])){
        $welcome_msg = ("Welcome " . $_SESSION['user']);
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Handle logout attempt
        if (isset($_POST['logout'])){
            return logout();
        }

        if (isset($_POST['signin'])){
            $errCount = 0;
            if (empty($_POST["uname"])) {
                ++$errCount;
                $usernameErr = "Username is required";
            } else {
                $uname = test_input($_POST["uname"]);
                // only allow alpha digit characters as part of the username
                if (!preg_match("/^[a-zA-Z0-9]*$/",$uname)) {
                    ++$errCount;
                    $usernameErr = "Only letters and numbers allowed";
                }
            }
            if (empty($_POST["pass"])) {
                ++$errCount;
                $passwordErr = "Password is required";
            } else {
                $pass = test_input($_POST["pass"]);
            }
            if ($errCount == 0){
                if (login($uname, $pass)){
                    $_SESSION['user'] = $uname;
                    $welcome_msg = ("Welcome " . $_SESSION['user']);
                    if ($uname == 'admin'){
					    header('Location: http://localhost/TicketHawk/admin_page.php');
                    }
                    else {
		                header('Location: http://localhost/TicketHawk/homepage.php');
                    }
                }
            }
        }

        // Handle purchase from registered user
        if (isset($_POST['confirm_as_reg_user'])){
            // Add each line in cart if the quantity is greater than 0
            foreach ($_SESSION['cart'] as $id => $qty){
                if ($qty > 0){
                    $e = $events[$id];
                    addSale($_SESSION['user_id'], $id, $qty, $e["price"]);
                }
            }
            // Empty the Cart
            $_SESSION['cart'] = array();
            // Clear Payment Info Array
            $_SESSION['payment_info'] = array();
            header('Location: http://localhost/tickethawk/order_history.php');
        }

        if (isset($_POST['sign_and_confirm'])){
            // Log the User In, but do not redirect and handle purchase
            $errCount = 0;
            // Get username
            if (empty($_POST["username"])) {
                ++$errCount;
                $newunameErr = "Username is required";
            } else {
                $username = test_input($_POST["username"]);
                // only allow alpha digit characters as part of the username
                if (!preg_match("/^[a-zA-Z0-9]*$/",$username)) {
                    ++$errCount;
                    $newunameErr = "Only letters and numbers allowed";
                }
                if (!availableUser($username)){
                    ++$errCount;
                    $newunameErr = "Username is unavailable, please choose another";
                }
            }

            // Get password  
            if (empty($_POST["password"])) {
                ++$errCount;
                $newpassErr = "Password is required";
            } else {
                $password = test_input($_POST["password"]);
                // hash password for storage
                $hashed_pass = password_hash($password, PASSWORD_DEFAULT);
            }

            // Get password confirmation  
            if (empty($_POST["confirm_pass"]) || ($_POST["password"] != $_POST["confirm_pass"])) {
                ++$errCount;
                $confirmPassErr = "Password and Password Confirmation do not match";
            }

            if ($errCount == 0){
                if (createNewAccount($username, $_SESSION['payment_info']['fname'], $_SESSION['payment_info']['lname'],
                    $_SESSION['payment_info']['street'], $_SESSION['payment_info']['city'],
                    $_SESSION['payment_info']['state'], $_SESSION['payment_info']['zipcode'],
                    $_SESSION['payment_info']['email'], $hashed_pass)){
                    if (login($username, $password)){
                        $_SESSION['user'] = $username;
                        $welcome_msg = ("Welcome " . $_SESSION['user']);
                        foreach ($_SESSION['cart'] as $id => $qty){
                            if ($qty > 0){
                                $e = $events[$id];
                                addSale($_SESSION['user_id'], $id, $qty, $e["price"]);
                            }
                        }
                        // Empty the Cart
                        $_SESSION['cart'] = array();
                        // Clear Payment Info Array
                        $_SESSION['payment_info'] = array();
                    }
                    header('Location: http://localhost/tickethawk/order_history.php');
                }
            }
        }

        if (isset($_POST['confirm_as_guest'])){
            // Handle purchase for guest
            foreach ($_SESSION['cart'] as $id => $qty){
                if ($qty > 0){
                    $e = $events[$id];
                    addSale(NULL, $id, $qty, $e["price"]);
                }
            }
            // Empty the Cart
            $_SESSION['cart'] = array();
            // Clear Payment Info Array
            $_SESSION['payment_info'] = array();
            header('Location: http://localhost/tickethawk/order_history.php');
        }
    }

    function addSale($_uid, $_eid, $_qty, $_price){
        global $cxn;
        // Add the sale to the sales table
        $query = "INSERT INTO sales (UserID, EventID, Quantity, Price) VALUES ('$_uid', '$_eid', '$_qty', '$_price')";
        $results = mysqli_query($cxn, $query) or die("Connection could not be established");
        // Deduct the amount of tickets purchased from the event's ticket quantity
        $query = ("UPDATE EVENT SET ticket_qty=ticket_qty-".$_qty." WHERE eventid=".$_eid);
        $results = mysqli_query($cxn, $query) or die("Connection could not be established");
        // Add the amount of tickets purchased to the event's ticket_sold quantity
        $query = ("UPDATE EVENT SET ticket_sold=ticket_sold+".$_qty." WHERE eventid=".$_eid);
        $results = mysqli_query($cxn, $query) or die("Connection could not be established");
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
        return true;
    }
    
    function generateLineItems(){
        global $events, $price_total;
        $output = "";
        if (isset($_SESSION['cart'])){
            $count = 1;
            foreach ($_SESSION['cart'] as $id => $qty){
                if ($qty > 0){
                    $e = $events[$id];
                    $output .= (
                        '<div class="row">'
                        . '<div class="col-sm-4 text-left">'. $e["eventname"]. '</div>'
                        . '<div class="col-sm-3 text-center">'. $e["date"]. '</div>'
                        . '<div class="col-sm-2 text-right">'. $qty. '</div>'
                        . '<div class="col-sm-3 text-right">'. sprintf("@ $%5.2f (ea)", $e["price"]) .'</div>'
                        . '</div>');
                    $price_total += ($e["price"]*$qty);
                    $count++;
                }
            }
            // If there are no line items, get me out of here
            if ($price_total == 0){
                header('Location: http://localhost/tickethawk/homepage.php#browse');
            }
        }
        return $output;
    }

    function getSignInFields(){
        global $newunameErr, $newpassErr, $confirmPassErr;
        $output = "";
        if (!isset($_SESSION['user'])){
            $output .= (
                '<div class="form-group col-sm-3">'
                .'<label for="inputUsername">Username:</label>'
                .'<span class="error">* '.$newunameErr.'</span>'
                .'<input type="text" class="form-control" value="" id="inputUsername" name="username" placeholder="Username">'
                .'</div>'
                .'<div class="form-group col-sm-3">'
                .'<label for="password">Password</label>'
                .'<span class="error">* '.$newpassErr.'</span>'
                .'<input type="password" name="password" value="" class="form-control" id="inputPassword" placeholder="Password">'
                .'</div>'
                .'<div class="form-group col-sm-3">'
                .'<label for="confirm_pass">Confirm Password</label>'
                .'<span class="error">* '.$confirmPassErr.'</span>'
                .'<input type="password" name="confirm_pass" value="" class="form-control" id="confirmPassword" placeholder="Password">'
                .'</div>');
        }
        return $output;
    }

    function getCorrectFooter(){
        $output = "";
        if (isset($_SESSION['user'])){
            $output .= (  
			    '<div class="form-group col-sm-3 col-sm-offset-9">'
	            .'<button type="submit" name="confirm_as_reg_user" class="btn btn-primary form-control">'
                .'Confirm Purchase'
				.'</button>'
			    .'</div>');
        }
        else {
            $output .= (  
			    '<div class="form-group col-sm-3 col-sm-offset-6">'
	            .'<button type="submit" name="sign_and_confirm" class="btn btn-primary form-control">'
                .'Sign Up & Confirm Purchase'
				.'</button>'
			    .'</div>'
			    .'<div class="form-group col-sm-3">'
			    .'<button type="submit" name="confirm_as_guest" class="btn btn-primary form-control">'
				.'Confirm Purchase As Guest'
                .'</button>'
				.'</div>');
        }
        return $output;
    }

    function printPaymentInfo(){
        $output = "";
        if (isset($_SESSION['payment_info']['fname'])){
            $output .= ($_SESSION['payment_info']['fname']." ".$_SESSION['payment_info']['lname']
                . "<br/>"
                . $_SESSION['payment_info']['street']
                . "<br/>"
                . $_SESSION['payment_info']['city'].", ".$_SESSION['payment_info']['state'].", ".$_SESSION['payment_info']['zipcode']
                . "<br/>"
                . $_SESSION['payment_info']['email']);
        }
        return $output;
    }
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Ticket Hawk</title>

		<!-- Bootstrap -->
		<link href="dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Custom Style sheet for moving the body down below nav bar -->
        <link href="dist/css/custom.css" rel="stylesheet">

		<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>

	<body role="document" class="bg-gradient">
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
					<a class="navbar-brand" href="homepage.php">Ticket Hawk</a>
				</div>
				<div id="navbar" class="navbar-collapse collapse">
					<ul class="nav navbar-nav">
						<li class="active">
							<a href="homepage.php">Home</a>
						</li>
						<!--<li>
							<a href="#about">About</a>
						</li>-->
						<li>
							<a href="getContactUsForm.php">Contact</a>
						</li>
                        <li><a href="http://localhost/tickethawk/homepage.php#browse">Events</a></li>
					</ul>
                <?php
                    if (isset($_SESSION['user'])) {
                        echo ('<ul class="nav navbar-nav navbar-right">'
                            . '<li>'
                            . '<a href="http://localhost/tickethawk/cart.php">'
                            . '<i class="glyphicon glyphicon-shopping-cart icon-flipped"></i>'
                            . '</a>'
                            . '</li>'
                            . '<li class="navbar-left"><a href="http://localhost/tickethawk/order_history.php">'
                            . $welcome_msg
                            . '</a></li><form role="form" class="navbar-form navbar-right" method="post"'
                            . 'action="'
                            . htmlspecialchars($_SERVER["PHP_SELF"])
                            . '"><button type="submit" class="btn btn-danger" name="logout">'
                            . "Log Out</button></form>"
                            . "</ul>");
                    }
                    else {
                        $tmp = "";
                        if (isset($_SESSION['loginErr'])){
                            $tmp = $_SESSION['loginErr'];  
                        }
                        echo (
                              '<ul class="nav navbar-nav navbar-right">'
                            . '<li>'
                            . '<a href="http://localhost/tickethawk/cart.php">'
                            . '<i class="glyphicon glyphicon-shopping-cart icon-flipped"></i>'
                            . '</a>'
                            . '</li>'
                            . '<form class="navbar-form navbar-nav navbar-right form-inline" role="form" method="post" action="'
                            . htmlspecialchars($_SERVER["PHP_SELF"]). '">'
                            . '<div class="form-group">'
                            . '<input type="text" name="uname" placeholder="Username" class="form-control">'
                            . '</div>'
                            . '<div class="form-group">'
                            . '<input type="password" name="pass" placeholder="Password" class="form-control">'
                            . '</div>'
                            . '<button type="submit" class="btn btn-primary" name="signin">Sign in</button>'
                            . '<label id="loginInfo" style="color: red; padding-left: 4px;">'
                    	    . $tmp
                            . '</label>'
                            . '</form>'
                            . '</ul>');
                    }
                ?>
				</div><!--/.nav-collapse -->
			</div>
		</nav>

		<!--main
		================================================== -->
        <div class="container">
        <form role="form" method="post" id="sign_up_form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
		<div class="panel panel-default" style="margin-top: 30px;">
			<div class="panel-heading">
				<p class="panel-title">Confirm Purchase</p>
            </div>
            <div class="panel-body">
                <h3>Order Invoice: </h3>
                <div class="row">
                    <div class="col-sm-6">
                        <p>
                            <?php echo printPaymentInfo(); ?>   
                        </p>
                    </div>
                </div>
                <br/>
                <div class="row">
                    <div class="col-sm-4 text-left">
                        <b><u>Event Name</u></b>
                    </div>
                    <div class="col-sm-3 text-center">
                        <b><u>Event Date</u></b>
                    </div>
                    <div class="col-sm-2 text-right">
                        <b><u>Ticket Count</u></b>
                    </div>
                    <div class="col-sm-3 text-right">
                        <b><u>Price Per Ticket</u></b>
                    </div>
                </div>
                <?php echo $line_items; ?>
                <br/>
                <div class="row">
                    <div class="col-sm-3 col-sm-offset-9 text-right">
                        <?php echo sprintf("<b>Total Price:  <u>$%5.2f</u></b>", $price_total); ?>
                    </div>
                </div>
                <br/>
                <br/>
                <div class="row">
                    <?php echo getSignInFields(); ?>
                </div>
            </div>
            <div class="panel-footer">
                <div class="row">
                    <?php echo getCorrectFooter(); ?>
                </div>
            </div>       
	    </div>
        </form>
	    </div>
	</body>

</html>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="dist/js/bootstrap.min.js"></script>
<script src="dist/js/docs.min.js"></script>
