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
    /**
     * Generates the Event displays in rows of 3. Calculates the number of rows
     * needed and generates the html needed to display each event in the row in
     * which it belongs.
     */
    function getEventDisplays(){
        global $events;
        $out = "";
        for ($i = 0; $i < (count($events)/3); ++$i){
            $out .= '<div class="row">';
            for ($j = 0; $j < 3; ++$j){
                if (($i*3)+$j >= count($events)){
                    break;
                }
                $out .= ('<div class="col-lg-4"><img class="img-circle" src="data:image/jpeg;base64,'.base64_encode($events[($i*3)+$j]['img']).'" style="width: 140px; height: 140px"><h2>'.$events[($i*3)+$j]['eventname'].'</h2><p><a class="btn btn-primary" href="event_page.php?event_id='.$events[($i*3)+$j]['eventid'].'" role="button">View Details &raquo;</a></p></div>');  
            }
            $out .= '</div>';
        }
        return $out;   
    }

    // If the current session includes a valid user, display the welcome label
    if (isset($_SESSION['user'])){
        $welcome_msg = ("Welcome " . $_SESSION['user']);
    }

    // Handle Login Attempt
    if ($_SERVER["REQUEST_METHOD"] == "POST")
    {
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
        <script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
		<script src="http://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->


        	<style>
    		#search_div{
				background-color:white;
    		}
    		body{
    			/*background-image: url("dist/images/lebron-james-dunkman-kings.jpg");
    			background-repeat: no-repeat;
    			background-size: 100% 100%;*/
    		}
    		/*html{
    			height: 100%;
    		}*/
				#menuPics {
					border: solid 3px black;
					width: 540px;
					height: 350px;
					margin: auto;
					margin-top: 40px;
					overflow: hidden;
					background-color: #000;
				}
				#search-results{
					border: solid 3px black;
					width: 540px;
					height:300px;
				}
    	</style>
    </head>

    <body role="document" style="background-color: black;">

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
                <!--<li><a href="#about">About</a></li>-->
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
        
        <!-- Carousel
    ================================================== -->
    <div id="myCarousel" class="carousel slide" data-ride="carousel">
        
        <ol class="carousel-indicators">
            <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
            <li data-target="#myCarousel" data-slide-to="1"></li>
            <li data-target="#myCarousel" data-slide-to="2"></li>
        </ol>
        <div class="carousel-inner" role="listbox">
            <div class="item active">
                <img src="dist/images/1835974.jpg" alt="First slide" id="img1">
                <div class="container">
                    <div class="carousel-caption">
                        <h1>All The Tickets You Need!</h1>
                        <p>On Your Favorite Site.</p>
                        <p><a class="btn btn-lg btn-primary" href="sign_up.php" role="button">Sign up today</a></p>
                    </div>
                </div>
            </div>
            <div class="item">
                <img src="dist/images/011414-NFL-Seattle-Seahawks-Fans-HF-PI.jpg" alt="Second slide">
                <div class="container">
                    <div class="carousel-caption">
                        <h1>Save Money!</h1>
                        <p>The best seats for a lower price</p>
                        <p><a class="btn btn-lg btn-primary" href="sign_up.php" role="button">Sign up today</a></p>
                    </div>
                </div>
            </div>
            <div class="item">
                <img src="dist/images/2013-nascar-wallpaper-hd.jpg" alt="Third slide">
                <div class="container">
                    <div class="carousel-caption">
                        <h1>Get Your Tickets Fast!</h1>
                        <p>With our speedy checkout, you can order tickets fast!</p>
                        <p><a class="btn btn-lg btn-primary" href="sign_up.php" role="button">Sign up today</a></p>
                    </div>
                </div>
            </div>
            <div class="item">
                <img src="dist/images/Disney World HD Wallpapers21.jpg" alt="Third slide">
                <div class="container">
                    <div class="carousel-caption">
                        <h1>All of your favorite places!</h1>
                        <p><a class="btn btn-lg btn-primary" href="sign_up.php" role="button">Sign up today</a></p>
                    </div>
                </div>
            </div>
            <div class="item">
                <img src="dist/images/otr_paris_02.jpg" alt="fifth slide">
                <div class="container">
                    <div class="carousel-caption">
                        <h1>Events you can't miss!</h1>
                        <p><a class="btn btn-lg btn-primary" href="sign_up.php" role="button">Sign up today</a></p>
                    </div>
                </div>
            </div>
            <div class="item">
                <img src="dist/images/Lionel-Messi-Barcelona3.jpg" alt="sixth slide">
                <div class="container">
                    <div class="carousel-caption">
                        <h1>Sign up and receive the best deal out.</h1>
                        <p><a class="btn btn-lg btn-primary" href="sign_up.php" role="button">Sign up today</a></p>
                    </div>
                </div>
            </div>
        </div>
        <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
            <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
            <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div><!-- /.carousel -->
    <div class="container-fluid" id="main-div">
<script>
	var count = 1;

	$("#menuPics :nth-child(" + 1 + ")").click(function() {
		$(location).attr('href', "http://localhost/Tarsha's_Handbags/Handbag_Gallery.php?show=purses");

	});
	$("#menuPics :nth-child(" + 2 + ")").click(function() {
		$(location).attr('href', "http://localhost/Tarsha's_Handbags/Handbag_Gallery.php?show=kidzcorner");

	});

	$("#menuPics :nth-child(" + 3 + ")").click(function() {
		$(location).attr('href', "http://localhost/Tarsha's_Handbags/Handbag_Gallery.php?show=Accessories");

	});

	$("#menuPics :nth-child(" + 4 + ")").click(function() {
		$(location).attr('href', "http://localhost/Tarsha's_Handbags/Handbag_Gallery.php?show=ProductsForTravel");

	});

	setInterval(function() {
		count = ($("#menuPics :nth-child(" + count + ")").fadeOut().next().length == 0) ? 1 : count + 1;
		$("#menuPics :nth-child(" + count + ")").fadeIn();

	}, 3000);

			</script>
            <!--
    		<div class="panel panel-default" id="search_div" style="margin-top:10px; solid 4px black;">
                <div class="panel-heading">
    				<h3>Search for Tickets</h3>
                </div>
    			<div class="row" style="padding: 10px;">
    			<div class="col-md-5" style="">
    			<div style="padding-top: 30px;">
				<form method="post">
                    <div class="input-group custom-search-form">
                        <input type="text" class="form-control" placeholder="Search..." name="search_events"> 
                        <span class="input-group-btn">
                            <button type="submit" class="btn btn-default" form_id="searchTrans"  type="button" name="srch">
                          <span class="glyphicon glyphicon-search"></span>
                           </button>          
                       </span>
                    </div>
				</form>
				</div>
				<div style="padding-top: 10px;">
				 <label>Trending</label>
				 </div>
                    <p>
                        <a href="#" class="btn btn-primary btn-xs">Event 1</a>
                    	<a href="#" class="btn btn-primary btn-xs">Event 2</a>
                    	<a href="#" class="btn btn-primary btn-xs">Event 3</a>
                    	<a href="#" class="btn btn-primary btn-xs">Event 4</a>
                    </p>
                    <p>
                    	<a href="#" class="btn btn-primary btn-xs">Event 1</a>
                    	<a href="#" class="btn btn-primary btn-xs">Event 2</a>
                    	<a href="#" class="btn btn-primary btn-xs">Event 3</a>
                    	<a href="#" class="btn btn-primary btn-xs">Event 4</a>
                    	<a href="#" class="btn btn-primary btn-xs">Event 5</a>
                    	<a href="#" class="btn btn-primary btn-xs">Event 6</a>
                    </p> 
				</div>
				<div class="col-md-5" style="margin-left: 120px;" id="search-results">
					<?php
						if(isset($_POST['srch']) && isset($_POST['search_events'])){
							$srch_event = $_POST['search_events']; 
							$query = "SELECT * FROM EVENT WHERE eventname ='$srch_event'";
							$results = mysqli_query($cxn, $query);
							while($row = mysqli_fetch_assoc($results)){
							if(empty($row)){
								echo "<h1 style='color:red;'> Event Not found</h1>";
							}
							else {
								echo "<h3>".$row['eventname']."</h3>";
							}
						}
					}
					
					?>
						<!-- <img src="dist/images/images.jpeg"  width="660px" height = "400px" />
						<img src="dist/images/katWilliams.jpeg" width="550px" height = "400px" />
						<img src="dist/images/lebron-james-dunkman-kings.jpg"  width="550px" height = "400px" />
						<img src="dist/images/on-the-run-650.jpg" width="550px" height = "400px" /> -->
				</div>

				</div>
    		</div>
    <div class="container" id="browse" style="height: 50px;"></div>		
    <div class="panel panel-default marketing">
        <div class="panel-heading">
            <h3>Browse Event Tickets</h3>
        </div>
        <div class="panel-body">
            <!-- Here, display all the events in rows of 3 -->
            <?php
                echo getEventDisplays();
            ?>
        </div>
    </div>
  
    <div id="footer" class="panel panel-default" style="background-color: #D8D8D8">
        <div class="panel-body">
            <div class="col-md-4">
                <h3>Contact us</h3>
                <p>Phone: 678-915-7778</p>
                <p>1100 South Marietta Pkwy</p>
                <p>Marietta, GA 30060</p>
            </div>
        </div>
        <div id="copy_right" style="text-align: center;">
            <p> &copy; 2015-2020 Ticket Hawk All rights reserved.</p>
        </div>
    </div>
</body>
</html>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="dist/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="dist/js/docs.min.js"></script>
