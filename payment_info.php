<!-- Connect to Database -->
<?php
    session_start();
    include "dist/common.php";
    $usernameErr = $fnameErr = $lnameErr = $streetErr = $cityErr = $stateErr = $zipcodeErr = $emailErr = $passwordErr = "";
    $username = $fname = $lname = $street = $city = $state = $zipcode = $email = $password = $hashed_pass = "";
    $welcome_msg = "";
    $states = [
        'AL', 'AK', 'AZ', 'AR', 'CA', 'CO', 'CT', 'DE', 'DC', 'FL', 'GA', 'HI',
        'ID', 'IL', 'IN', 'IA', 'KS', 'KY', 'LA', 'ME', 'MD', 'MA', 'MI', 'MN',
        'MS', 'MO', 'MT', 'NE', 'NV', 'NH', 'NJ', 'NM', 'NY', 'NC', 'ND', 'OH',
        'OK', 'OR', 'PA', 'RI', 'SC', 'SD', 'TN', 'TX', 'UT', 'VT', 'VA', 'WA',
        'WV', 'WI', 'WY'
    ];

    function generate_states(){
        global $states;
        $output = '';
        foreach($states as $st){
            if ($_SESSION['state'] == $st){
                $output .= ('<option selected value="'.$st.'">'.$st.'</option>');
            }
            else {
                $output .= ('<option value="'.$st.'">'.$st.'</option>');
            }
        }
        return $output;
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
                $username = test_input($_POST["uname"]);
                // only allow alpha digit characters as part of the username
                if (!preg_match("/^[a-zA-Z0-9]*$/",$username)) {
                    ++$errCount;
                    $usernameErr = "Only letters and numbers allowed";
                }
            }
            if (empty($_POST["pass"])) {
                ++$errCount;
                $passwordErr = "Password is required";
            } else {
                $password = test_input($_POST["pass"]);
            }
            if ($errCount == 0){
                if (login($username, $password)){
                    $_SESSION['user'] = $username;
                    $welcome_msg = ("Welcome " . $_SESSION['user']);
                    if ($username == 'admin'){
					    header('Location: http://localhost/TicketHawk/admin_page.php');
                    }
                    else {
		                header('Location: http://localhost/TicketHawk/homepage.php');
                    }
                }
            }
        }

        if (isset($_POST['continue'])){
            $_SESSION['payment_info'] = array();
            $errCount = 0;

            // Get fname  
            if (empty($_POST["fname"])) {
                ++$errCount;
                $fnameErr = "First name is required";
            } else {
                $_SESSION['payment_info']['fname'] = test_input($_POST["fname"]);
                // only allow alpha characters as part of the first name
                if (!preg_match("/^[a-zA-Z]*$/",$_SESSION['payment_info']['fname'])) {
                    ++$errCount;
                    $fnameErr = "Only letters allowed";
                }
            }
        
            // Get lname  
            if (empty($_POST["lname"])) {
                ++$errCount;
                $lnameErr = "Last name is required";
            } else {
                $_SESSION['payment_info']['lname'] = test_input($_POST["lname"]);
                // only allow alpha characters as part of the last name
                if (!preg_match("/^[a-zA-Z]*$/",$_SESSION['payment_info']['lname'])) {
                    ++$errCount;
                    $lnameErr = "Only letters allowed";
                }
            }

            // Get street 
            if (empty($_POST["street"])) {
                ++$errCount;
                $streetErr = "Street Address is required";
            } else {
                $_SESSION['payment_info']['street'] = test_input($_POST["street"]);
                // only allow alpha digit characters as part of the street address
                if (!preg_match("/^[a-zA-Z0-9 ]*$/",$_SESSION['payment_info']['street'])) {
                    ++$errCount;
                    $streetErr = "Only letters, numbers and spaces are allowed";
                }
            }
            
            // Get city
            if (empty($_POST["city"])) {
                ++$errCount;
                $cityErr = "City is required";
            } else {
                $_SESSION['payment_info']['city'] = test_input($_POST["city"]);
                // only allow alpha characters as part of the city
                if (!preg_match("/^[a-zA-Z ]*$/",$_SESSION['payment_info']['city'])) {
                    ++$errCount;
                    $cityErr = "Only letters allowed";
                }
            }

            // Get state 
            if (empty($_POST["state"])) {
                ++$errCount;
                $stateErr = "State is required";
            } else {
                $_SESSION['payment_info']['state'] = test_input($_POST["state"]);
                // only allow alpha characters as part of the state
                if (!preg_match("/^[a-zA-Z]*$/",$_SESSION['payment_info']['state'])) {
                    ++$errCount;
                    $stateErr = "Only letters allowed";
                }
            }

            // Get zipcode
            if (empty($_POST["zipcode"])) {
                ++$errCount;
                $zipcodeErr = "Zipcode is required";
            } else {
                $_SESSION['payment_info']['zipcode'] = test_input($_POST["zipcode"]);
                // only allow digit characters as part of the zipcode
                if (!preg_match("/^[0-9]*$/",$zipcode)) {
                    ++$errCount;
                    $zipcodeErr = "Only numbers allowed";
                }
                else{
                    if (strlen($_SESSION['payment_info']['zipcode']) != 5){
                        ++$errCount;
                        $zipcodeErr = "Zipcode must be 5 digits long";
                    }
                }
            }

            // Get email 
            if (empty($_POST["email"])) {
                ++$errCount;
                $emailErr = "Email address is required";
            } else {
                $_SESSION['payment_info']['email'] = test_input($_POST["email"]);
                // check if e-mail address is well-formed
                if (!filter_var($_SESSION['payment_info']['email'], FILTER_VALIDATE_EMAIL)) {
                    ++$errCount;
                    $emailErr = "Invalid email format";
                }
            }

            // If no errors occured, create a user and store it in the database
            if ($errCount == 0){
                header("Location: http://localhost/TicketHawk/confirm_purchase.php");
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
        return true;
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
                        <li><a href="http://localhost/tickethawk/homepage.php#main-div">Events</a></li>
                        <li>
                            <a href="http://localhost/tickethawk/user_guide.php" id="guide_link">
                                <i class="glyphicon glyphicon-question-sign"></i>
                            </a>
                        </li>
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
				<p class="panel-title">Please Enter Your Contact Information</p>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="form-group col-sm-6">
                        <label for="inputFname">First Name:</label>
                        <span class="error">* <?php echo $fnameErr; ?></span>
                        <input type="text" name="fname" class="form-control" id="inputFname" placeholder="First Name" value="<?php if (isset($_SESSION['fname'])) echo $_SESSION['fname'];?>">
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="inputLname">Last Name:</label>
                        <span class="error">* <?php echo $lnameErr; ?></span>
                        <input type="text" name="lname" class="form-control" id="inputLname" placeholder="Last Name" value="<?php if (isset($_SESSION['lname'])) echo $_SESSION['lname'];?>">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-6">
                        <label for="inputStreet">Street Address:</label>
                        <span class="error">* <?php echo $streetErr; ?></span>
                        <input type="text" name="street" class="form-control" id="inputStreet" placeholder="Street Address" value="<?php if (isset($_SESSION['street'])) echo $_SESSION['street'];?>">
                    </div>
                    <div class="form-group col-sm-2">
                        <label for="inputCity">City:</label>
                        <span class="error">* <?php echo $cityErr; ?></span>
                        <input type="text" name="city" class="form-control" id="inputCity" placeholder="City" value="<?php if (isset($_SESSION['city'])) echo $_SESSION['city'];?>">
                    </div>
                    <div class="form-group col-sm-2">
                        <label>State: <span class="error">* <?php echo $stateErr; ?></span></label><br/>
                        <select name="state" class="form-control" value="<?php if (isset($_SESSION['state'])) echo $_SESSION['state'];?>">
                            <?php echo generate_states(); ?>
                        </select>
                    </div>
                    <div class="form-group col-sm-2">
                        <label for="inputZipcode">Zipcode:</label>
                        <span class="error">* <?php echo $zipcodeErr; ?></span>
                        <input type="text" name="zipcode" class="form-control" id="inputZipcode" placeholder="Zipcode" value="<?php if (isset($_SESSION['zipcode'])) echo $_SESSION['zipcode'];?>">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-6">
                        <label for="inputEmail">Email</label>
                        <span class="error">* <?php echo $emailErr; ?></span>
                        <input type="email" name="email" class="form-control" id="inputEmail" placeholder="Email" value="<?php if (isset($_SESSION['email'])) echo $_SESSION['email'];?>">
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                <div class="row">
			        <div class="form-group col-sm-3 col-sm-offset-9">
					    <button type="submit" name="continue" class="btn btn-primary form-control">
						    Continue
						</button>
					</div>
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
