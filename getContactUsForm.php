<?php
    /*
     Name: 			getContactUsForm.php
     Description: 	Displays a contact form for users
     written by:	Matthew Eddy
     Created:		12/22/14
     Modified:		d/m/yr
     */
    session_start();
    include "dist/common.php";
    require ("botdetect.php");
    $name = $email = $subject = $CaptchaCode = $var = $SampleCaptcha =null;
    $nameErr = $messageErr = $emailErr = $CaptchaCodeErr= $formErr = "";
    $errors = FALSE;
    $welcome_msg = "";

    // If the current session includes a valid user, display the welcome label
    if (isset($_SESSION['user'])){
        $welcome_msg = ("Welcome " . $_SESSION['user']);
    }

	if ($_SERVER["REQUEST_METHOD"] == "POST") 
	{
        // Handle logout attempt first and return without continuing
        if (isset($_POST['logout'])){
            return logout();
        }

		if (empty($_POST['name'])) 
		{
			$nameErr = "Name is Requred";
			$errors =TRUE;
		} 
		else {
			$name = test_input($_POST['name']);
			if (!preg_match("/^[a-zA-Z ]*$/", $name)) {
				$nameErr = "Only letters and white space allowed";
				$errors =TRUE;
			}
		}
		if (empty($_POST["email"])) {
			$emailErr = "Email Required";
			$errors =TRUE;
		} 
		else {
			$email = test_input($_POST['email']);
			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				$emailErr = "Invalid email format";
				$errors =TRUE;
			}
		}
		if (empty($_POST["subject"])) {
			$messageErr = "Message Required";
			$errors =TRUE;
		} 
		else {
			$subject = test_input($_POST['subject']);
		}
		if (empty($_POST['CaptchaCode'])) {
			$CaptchaCodeErr = "Enter code";
			$errors =TRUE;
		}
		else {
			
			if (isset($_SESSION['CaptchaCode'])) {
				$isHuman = $SampleCaptcha->Validate();
				if (! $isHuman) {
					$CaptchaCodeErr = "Code does not match";
					$errors =TRUE;
				}
				
			}
		}
    }
	if ($errors == FALSE) {
		$to = "meddy672@gmail.com";
		mail($to, $name, $subject);
		
	}
	else {
		$formErr = "Form incomplete";
	}
?>
<head>
	<title>Ticket Hawk</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Load style sheets -->
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css"> 
    <link href="dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="dist/css/custom.css" rel="stylesheet">
    <link type="text/css" rel="Stylesheet" href="<?php echo CaptchaUrls::LayoutStylesheetUrl() ?>" />

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="dist/js/bootstrap.min.js"></script>
    <script src="dist/js/docs.min.js"></script>
</head>
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


<div class="container">
    <div id="main-container">
        <h2>Contact us</h2>
        <form role="form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" target="_self">
            <div class="form-group">
                <label for="name">Name:</label>
                <span class="error">* <?php echo $nameErr; ?></span>
                <input type="text" class="form-control" name="name" placeholder="Enter Name">
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <span class="error">* <?php echo $emailErr; ?></span>
                <input type="email" class="form-control" name="email" placeholder="Email Address">
            </div>

            <div>
                <label for="subject">Message:</label>
                <span class="error">* <?php echo $messageErr; ?></span>
                <textarea class="form-control" rows="5"  placeholder="Comments" name="subject"></textarea>
            </div>

            <div id="Captcha-div">
                <?php // Adding BotDetect Captcha to the page
                $SampleCaptcha = new Captcha("SampleCaptcha");
                $SampleCaptcha -> UserInputID = "CaptchaCode";
                echo $SampleCaptcha -> Html();
                ?>
            </div>
            <div class="validationDiv">
                <label for="name">Enter the code below:</label>
            <span class="error">* <?php echo $CaptchaCodeErr; ?></span>
            <input name="CaptchaCode" type="text" id="CaptchaCode" />
          </div>
          <div id="button-div">
                 <button type="submit" class="btn btn-primary" name="submit">
                Submit
                </button>
                <span class="error"><?php echo $formErr; ?></span>
          </div>
        </form>
    </div>
</div>
