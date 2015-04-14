<!-- Connect to Database -->
<?php
    session_start();
    include "dist/common.php";
    $usernameErr = $fnameErr = $lnameErr = $streetErr = $cityErr = $stateErr = $zipcodeErr = $emailErr = $passwordErr = "";
    $username = $fname = $lname = $street = $city = $state = $zipcode = $email = $password = $hashed_pass = "";
    $welcome_msg = "";

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
        
        $errCount = 0;
        // Get username
        if (empty($_POST["username"])) {
            ++$errCount;
            $usernameErr = "Username is required";
        } else {
            $username = test_input($_POST["username"]);
            // only allow alpha digit characters as part of the username
            if (!preg_match("/^[a-zA-Z0-9]*$/",$username)) {
                ++$errCount;
                $usernameErr = "Only letters and numbers allowed";
            }
            if (!availableUser($username)){
                ++$errCount;
                $usernameErr = "Username is unavailable, please choose another";
            }
        }
    
        // Get fname  
        if (empty($_POST["fname"])) {
            ++$errCount;
            $fnameErr = "First name is required";
        } else {
            $fname = test_input($_POST["fname"]);
            // only allow alpha characters as part of the first name
            if (!preg_match("/^[a-zA-Z]*$/",$fname)) {
                ++$errCount;
                $fnameErr = "Only letters allowed";
            }
        }
        
        // Get lname  
        if (empty($_POST["lname"])) {
            ++$errCount;
            $lnameErr = "Last name is required";
        } else {
            $lname = test_input($_POST["lname"]);
            // only allow alpha characters as part of the last name
            if (!preg_match("/^[a-zA-Z]*$/",$lname)) {
                ++$errCount;
                $lnameErr = "Only letters allowed";
            }
        }

        // Get street 
        if (empty($_POST["street"])) {
            ++$errCount;
            $streetErr = "Street Address is required";
        } else {
            $street = test_input($_POST["street"]);
            // only allow alpha digit characters as part of the street address
            if (!preg_match("/^[a-zA-Z0-9 ]*$/",$street)) {
                ++$errCount;
                $streetErr = "Only letters, numbers and spaces are allowed";
            }
        }
        
        // Get city
        if (empty($_POST["city"])) {
            ++$errCount;
            $cityErr = "City is required";
        } else {
            $city = test_input($_POST["city"]);
            // only allow alpha characters as part of the city
            if (!preg_match("/^[a-zA-Z]*$/",$city)) {
                ++$errCount;
                $cityErr = "Only letters allowed";
            }
        }

        // Get state 
        if (empty($_POST["state"])) {
            ++$errCount;
            $stateErr = "State is required";
        } else {
            $state = test_input($_POST["state"]);
            // only allow alpha characters as part of the state
            if (!preg_match("/^[a-zA-Z]*$/",$state)) {
                ++$errCount;
                $stateErr = "Only letters allowed";
            }
        }

        // Get zipcode
        if (empty($_POST["zipcode"])) {
            ++$errCount;
            $zipcodeErr = "Zipcode is required";
        } else {
            $zipcode = test_input($_POST["zipcode"]);
            // only allow digit characters as part of the zipcode
            if (!preg_match("/^[0-9]*$/",$zipcode)) {
                ++$errCount;
                $zipcodeErr = "Only numbers allowed";
            }
            else{
                if (strlen($zipcode) != 5){
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
            $email = test_input($_POST["email"]);
            // check if e-mail address is well-formed
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                ++$errCount;
                $emailErr = "Invalid email format";
            }
        }

        // Get password  
        if (empty($_POST["password"])) {
            ++$errCount;
            $passwordErr = "Password is required";
        } else {
            $password = test_input($_POST["password"]);
            // hash password for storage
            $hashed_pass = password_hash($password, PASSWORD_DEFAULT);
        }

        // If no errors occured, create a user and store it in the database
        if ($errCount == 0){
            if (createNewAccount($username, $fname, $lname, $street, $city,
                $state, $zipcode, $email, $hashed_pass)){
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

	<body role="document">
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
						<li>
							<a href="#about">About</a>
						</li>
						<li>
							<a href="getContactUsForm.php">Contact</a>
						</li>
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Search <span class="caret"></span></a>
							<ul class="dropdown-menu" role="menu">
<table class="table" style="width: 650px;">
								<tr>
								<th>Sports</th>
								<th>Movies</th>
								<th>Events</th>
								<th>On Tour</th>
								<th>Theme Parks</th>
								</tr>
								<tr>
									<td><a href="#">NBA</a></td>
									<td><a href="#">New Releases</a></td>
									<td><a href="#">World Cupr Qatar</a></td>
									<td><a href="#">Jay Z & Beyonce (On the run)</a></td>
									<td><a href="#">Disney World FL</a></td>
								</tr>
								<tr>
									<td><a href="#">NFL</a></td>
									<td><a href="#">Drama</a></td>
									<td><a href="#">2016 Olympics</a></td>
									<td><a href="#">Rock</a></td>
									<td><a href="#">Sea World</a></td>
								</tr>
								<tr>
									<td><a href="#">MLB</a></td>
									<td><a href="#">Action</a></td>
									<td><label></label></td>
									<td><a href="#">R&B</a></td>
									<td><a href="#">Six Flags GA</a></td>
								</tr>
								<tr>
									<td><a href="#">MLH</a></td>
									<td><a href="#">Horror</a></td>
									<td><label></label></td>
									<td><a href="#">Rap</a></td>
									<td><a href="#">Disney Land CA</a></td>	
								</tr>
								<tr>
									<td><a href="#">MLS</a></td>
									<td><a href="#">Comedy</a></td>
									<td><label></label></td>
									<td><a href="#">Blues</a></td>
									<td><label></label></td>	
								</tr>
								<tr>
									<td><a href="#">NASCAR</a></td>
									<td><a href="#">Suspense</a></td>
									<td><label></label></td>
									<td><a href="#">Gospel</a></td>
									<td><label></label></td>	
								</tr>
								
								</table>
							</ul>
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
			<div id="sign-up">
				<h2>Sign up</h2>
                <form role="form" method="post" id="sign_up_form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div class="form-group">
                        <label for="inputUsername">Username:</label>
                        <span class="error">* <?php echo $usernameErr; ?></span>
                        <input type="text" class="form-control" id="inputUsername" name="username" placeholder="Username">
                    </div>
                    <div class="form-group">
                        <label for="inputFname">First Name:</label>
                        <span class="error">* <?php echo $fnameErr; ?></span>
                        <input type="text" name="fname" class="form-control" id="inputFname" placeholder="First Name">
                    </div>
                    <div class="form-group">
                        <label for="inputLname">Last Name:</label>
                        <span class="error">* <?php echo $lnameErr; ?></span>
                        <input type="text" name="lname" class="form-control" id="inputLname" placeholder="Last Name">
                    </div>
                    <div class="form-group">
                        <label for="inputStreet">Street Address:</label>
                        <span class="error">* <?php echo $streetErr; ?></span>
                        <input type="text" name="street" class="form-control" id="inputStreet" placeholder="Street Address">
                    </div>
                    <div class="form-group">
                        <label for="inputCity">City:</label>
                        <span class="error">* <?php echo $cityErr; ?></span>
                        <input type="text" name="city" class="form-control" id="inputCity" placeholder="City">
                    </div>
                    <div>
                        <label>State: <span class="error">* <?php echo $stateErr; ?></span></label><br/>
                        <select name="state" class="form-control">
                            <option value="AL">AL</option>
                            <option value="AK">AK</option>
                            <option value="AZ">AZ</option>
                            <option value="AR">AR</option>
                            <option value="CA">CA</option>
                            <option value="CO">CO</option>
                            <option value="CT">CT</option>
                            <option value="DE">DE</option>
                            <option value="DC">DC</option>
                            <option value="FL">FL</option>
                            <option value="GA">GA</option>
                            <option value="HI">HI</option>
                            <option value="ID">ID</option>
                            <option value="IL">IL</option>
                            <option value="IN">IN</option>
                            <option value="IA">IA</option>
                            <option value="KS">KS</option>
                            <option value="KY">KY</option>
                            <option value="LA">LA</option>
                            <option value="ME">ME</option>
                            <option value="MD">MD</option>
                            <option value="MA">MA</option>
                            <option value="MI">MI</option>
                            <option value="MN">MN</option>
                            <option value="MS">MS</option>
                            <option value="MO">MO</option>
                            <option value="MT">MT</option>
                            <option value="NE">NE</option>
                            <option value="NV">NV</option>
                            <option value="NH">NH</option>
                            <option value="NJ">NJ</option>
                            <option value="NM">NM</option>
                            <option value="NY">NY</option>
                            <option value="NC">NC</option>
                            <option value="ND">ND</option>
                            <option value="OH">OH</option>
                            <option value="OK">OK</option>
                            <option value="OR">OR</option>
                            <option value="PA">PA</option>
                            <option value="RI">RI</option>
                            <option value="SC">SC</option>
                            <option value="SD">SD</option>
                            <option value="TN">TN</option>
                            <option value="TX">TX</option>
                            <option value="UT">UT</option>
                            <option value="VT">VT</option>
                            <option value="VA">VA</option>
                            <option value="WA">WA</option>
                            <option value="WV">WV</option>
                            <option value="WI">WI</option>
                            <option value="WY">WY</option>
                        </select>
                    </div>
                    <br/>
                    <div class="form-group">
                        <label for="inputZipcode">Zipcode:</label>
                        <span class="error">* <?php echo $zipcodeErr; ?></span>
                        <input type="text" name="zipcode" class="form-control" id="inputZipcode" placeholder="Zipcode">
                    </div>
                    <div class="form-group">
                        <label for="inputEmail">Email</label>
                        <span class="error">* <?php echo $emailErr; ?></span>
                        <input type="email" name="email" class="form-control" id="inputEmail" placeholder="Email">
                    </div>
                    <div class="form-group">
                        <label for="inputPassword">Password</label>
                        <input type="password" name="password" class="form-control" id="inputPassword" placeholder="Password">
                    </div>
					<div class="form-group">
					    <button type="submit" class="btn btn-primary" name="submit">
						    Submit
						</button>
					</div>
				</form>
			</div>
		</div>
		<!-- /.main -->

	</body>

</html>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="dist/js/bootstrap.min.js"></script>
<script src="dist/js/docs.min.js"></script>
