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
    $query = "SELECT * FROM EVENT WHERE ACTIVE = 1";
    $results = mysqli_query($cxn, $query) or die("Connection could not be established");
    $events = array();
    $e_id = -1;
    $price_total = 0.00;
    storeEventRows();

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

    // Handle Login Attempt
    if ($_SERVER["REQUEST_METHOD"] == "POST")
    {
        if (isset($_POST['remove_event'])){
            if (isset($_POST['id_to_remove'])){
                $_SESSION['cart'][strval($_POST['id_to_remove'])] = 0;
                header('Location: http://localhost/TicketHawk/cart.php');
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

    function generateTicketInfo(){
        global $events, $price_total;
        $output = "";
        if (isset($_SESSION['cart'])){
            foreach ($_SESSION['cart'] as $id => $qty){
                if ($qty > 0){
                    $e = $events[$id];
                    $output .= ('<tr>'
                            . '<td>'.$e["eventname"].'</td>'
                            . '<td class="text-center">'.$e["date"].'</td>'           
                            . '<td class="text-center">'.$e["time"].'</td>'           
                            . '<td>'.$e["location"].'</td>'           
                            . '<td>'.$e["venue"].'</td>'           
                            . '<td class="text-right">'.sprintf("$%01.2f", $e["price"]*$qty).'</td>'           
                            . '<td class="text-center">'.$qty.'</td>'
                            . '<td class="text-center">'
                            . '<button onClick="erase_event('.$id.');" class="btn btn-danger" name="remove_event" type="submit">'           
                            . 'Remove</button>'
                            . '</td>'
                            . '</tr>');
                    $price_total += ($e["price"]*$qty);
                }
            }
        }
        return $output;
    }

    function addPurchaseBtn(){
        $item_ct = 0;
        if (!isset($_SESSION['cart'])){
            return NULL;
        }
        foreach ($_SESSION['cart'] as $id => $qty){
            if ($qty > 0){
                $item_ct++;
            }
        }
        if ($item_ct == 0){
            return NULL;
        }
        else {
            return (
                '<div class="col-sm-2 text-right">'
                .'<a class="btn btn-success" href="http://localhost/tickethawk/payment_info.php" role="button">'
                .'Purchase'
                .'</a>'
                .'</div>');
        }
    }

    function generateTotal(){
        global $price_total;
        return ('Total: '.sprintf("$%01.2f", $price_total));
    }
?>

<!DOCTYPE html>
<html lang="en">
    
    <script>
        function erase_event(num){
            document.getElementById("rem").setAttribute("value", num);
        }
    </script>

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

    </head>

    <body role="document" class="bg-gradient" style="height: 100% !important;" onload="onLoad()">

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
              <a class="navbar-brand" href="http://localhost/tickethawk/homepage.php">Ticket Hawk</a>
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
                <!--<li><a href="#about">About</a></li>-->
                <li><a href="getContactUsForm.php">Contact</a></li>
                <li><a href="http://localhost/tickethawk/homepage.php#main-div">Events</a></li>
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
        <form role="form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <h3>My Cart</h3>
            <div class="panel panel-default">
                <div>
                    <table class="table">
                        <thead>
                            <tr>
                        	    <th class="text-center">Event Name</th>
                                <th class="text-center">Date</th>
                                <th class="text-center">Time</th>
                                <th class="text-left">Location</th>
                                <th class="text-left">Venue</th>
                                <th class="text-right">Price</th>
                                <th class="text-center">Quantity</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php echo generateTicketInfo(); ?>
                        </tbody>
                    </table>
                </div>
                <div class="panel-footer">
                    <div class="form-group row">
                        <div class="col-sm-3 text-left">
                            <a class="btn btn-primary" href="http://localhost/tickethawk/homepage.php#main-div" role="button">
                                Add More Tickets
                            </a>
                        </div>
                        <div class="col-sm-2 col-sm-offset-5 text-right">
                            <h4><?php echo generateTotal(); ?></h4>
                        </div>
                        <?php echo addPurchaseBtn(); ?>
                    </div>
                </div>
            </div>
            <input type="text" id="rem" name="id_to_remove" style="visibility: hidden;"></input>
        </form>
    </div>
</body>
</html>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="dist/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="dist/js/docs.min.js"></script>


