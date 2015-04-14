<!-- Connect to Database -->
<?php
    session_start();
    include "dist/common.php";
    $usernameErr = $passErr = "";
    $username = $password = $welcome_msg = "";

    // Fetch the Events from the database
    global $dbhost, $dbname;
    $creds = db_admin();
    $dbuser = array_values($creds)[0];
    $dbpass = array_values($creds)[1];
    $cxn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
    $query = "SELECT * FROM EVENT";
    $results = mysqli_query($cxn, $query) or die("Connection could not be established");
    $events = array();
    $e_id = -1;
    storeEventRows();
    // Comment to add bootstrap in a commit

    /**
     * Fetches the EVENT rows from the database and stores them in an array for
     * us to use when displaying the rows of 3 at the bottom of the page.
     */
    function storeEventRows(){
        global $events, $results;
        $index = 0;
        while ($row = mysqli_fetch_assoc($results)) {
            $events[$index] = $row;
            ++$index;
        }
    }
    
    function generateEventDetails(){
        global $events, $e_id;
        try {
            $id = ($_SERVER['QUERY_STRING']);
            $_SESSION['q_string'] = $id;
            $pos = strpos ($id , '=');
            $id = substr($id, $pos+1);
            $output = "";
            foreach ($events as $value) {
                if ($value['eventid'] == $id){
                    $output .= ('<div class="container">'
                                    .'<div class="row text-center">'
                                        .'<h3>'.$value['eventname'].'</h3>'
                                    .'</div>'
                                    .'<div class="row">'
                                        .'<div class="col-sm-4 vcenter text-center">'
                                            .'<img class="text-center img-thumbnail" src = "data:image/jpeg;base64,' 
                                            .base64_encode($value['img']).'" width="300" height="300"/>'
                                        .'</div>'
                                        .'<div class="col-sm-8 container-fluid">'
                                            .'<table class="table table-bordered">'
                                                .'<tr><td><b>Date:</b></td><td>'.$value['date'].'</td></tr>'
                                                .'<tr><td><b>Time:</b></td><td>'.$value['time'].'</td></tr>'
                                                .'<tr><td><b>Location:</b></td><td>'.$value['location'].'</td></tr>'
                                                .'<tr><td><b>Venue:</b></td><td>'.$value['venue'].'</td></tr>'
                                                .'<tr><td><b>Ticket Price:</b></td><td>$'.$value['price'].'</td></tr>'
                                                .'<tr><td><b>Tickets in Stock:</b></td><td>'.$value['ticket_qty'].'</td></tr>'
                                            .'</table>'
                                            .'<form role="form"  method="post" action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'" enctype="multipart/form-data">'
                                                .'<div class="form-group">'
                                                    .'<input type="number" name="qty" placeholder="Quantity" class="form-control small_form_control" required>'
					                                .'<button type="submit" class="form-control small_form_control btn btn-success" name="add">'
						                                .'Add To Cart</button>'
                                                .'</div>'
                                            .'</form>'
                                        .'</div>'
                                    .'</div>'
                                .'</div>');
                }
            }
            return $output;
        } 
        catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    }

    // If the current session includes a valid user, display the welcome label
    if (isset($_SESSION['user'])){
        $welcome_msg = ("Welcome " . $_SESSION['user']);
    }

    // Handle Login Attempt
    if ($_SERVER["REQUEST_METHOD"] == "POST")
    {
        // If the user has entered a number for the ticket quantity and has
        // pressed "add to cart"
        if (isset($_POST['add'])){
            if (isset($_POST['qty'])){
                $pos = strpos ($_SESSION['q_string'] , '=');
                $id = substr($_SESSION['q_string'], $pos+1);
                // Handle the case where we have a guest who hasn't yet logged
                // in but still wants to purchase tickets
                if (!isset($_SESSION['cart'])){
                    $_SESSION['cart'] = array(); 
                }
                if (!isset($_SESSION['cart'][$id])){
                    $_SESSION['cart'][$id] = $_POST['qty'];  
                }
                else{
                    $_SESSION['cart'][$id] += $_POST['qty'];
                }
                // Redirect back to current page when finished adding item to cart
                $new_url = ('http://localhost/TicketHawk/event_page.php?'.$_SESSION['q_string']);
                header("Location: http://localhost/tickethawk/cart.php");
            }
        }
        // Handle logout attempt
        if (isset($_POST['logout'])){
            return logout();
        }
        $errCount = 0;
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
        }
        if (empty($_POST["password"])) {
            ++$errCount;
            $passwordErr = "Password is required";
        } else {
            $password = test_input($_POST["password"]);
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
        <!-- Carousel Customization -->
        <link href="dist/css/carousel.css" rel="stylesheet">
        <!-- Custom Style sheet for moving the body down below nav bar -->
        <link href="dist/css/custom.css" rel="stylesheet">

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->

    <!--
    <script type="text/javascript">
	    function onLoad(){
		    alert("Hello World");
    	}
    </script>-->
    </head>

    <body role="document" onload="onLoad()">

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
              <a class="navbar-brand" href="#">Ticket Hawk</a>
            </div>
            <div id="navbar" class="navbar-collapse collapse">
              <ul class="nav navbar-nav">
                <?php
                    if (isset($_SESSION['valid_admin'])){
                        if ($_SESSION['valid_admin']){
                            echo "<li class=\"active\"><a href=\"admin_page.php\">My Page</a></li>";
                        }
                        else {
                            echo "<li class=\"active\"><a href=\"homepage.php\">Home</a></li>";
                        }
                    }
                    else{
                        echo "<li class=\"active\"><a href=\"homepage.php\">Home</a></li>";
                    }
                ?>
                <li><a href="#about">About</a></li>
                <li><a href="getContactUsForm.php">Contact</a></li>
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
                            . '<input type="text" name="username" placeholder="Username" class="form-control">'
                            . '</div>'
                            . '<div class="form-group">'
                            . '<input type="password" name="password" placeholder="Password" class="form-control">'
                            . '</div>'
                            . '<button type="submit" class="btn btn-primary" name="submit">Sign in</button>'
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
    
    <!-- Start the Panel that Describes the Events -->
    <div class="container">
        <h3>Event Details</h3>
        <div class="panel panel-default">
            <div class="panel">
                <?php echo generateEventDetails(); ?>
            </div>
        </div>
    </div>
</body>
</html>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="dist/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="dist/js/docs.min.js"></script>

