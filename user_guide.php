<!-- Connect to Database -->
<?php
    session_start();
    include "dist/common.php";
    $welcome_msg = "";
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

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->

    </head>

    <body role="document" class="bg-gradient" onload="onLoad()">

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
                            . ' action="'
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
    <div class="panel container" style="margin-top: 30px;">
<h1>Ticket Hawk User Guide</h1>

<hr />

<h3>Table of Contents</h3>

<ol>
<li><a href="#s1"><strong>Introduction</strong></a></li>
<li><a href="#s2"><strong>Levels of Users</strong></a></li>
<li><a href="#s3"><strong>Customer Guide</strong></a>

<ul>
<li><a href="#s3_1">3.1 Creating an Account</a></li>
<li><a href="#s3_2">3.2 Sign In / Sign Out of Account</a></li>
<li><a href="#s3_3">3.3 Browsing Events</a></li>
<li><a href="#s3_4">3.4 Viewing Event Details</a></li>
<li><a href="#s3_5">3.5 Purchasing Event Tickets</a>

<ul>
<li><a href="#s3_5_1">3.5.1    Purchasing Tickets as a Registered
Customer</a></li>
<li><a href="#s3_5_2">3.5.2    Purchasing Tickets as a Guest</a></li>
<li><a href="#s3_5_3">3.5.3    Purchasing Tickets as a Guest Signing Up</a></li>
</ul>
</li>
<li><a href="#s3_6">3.6  View Purchase History</a></li>
</ul>
</li>
<li><a href="#s4"><strong>Administrator Guide</strong></a>

<ul>
<li><a href="#s4_1">4.1 Adding Events</a></li>
<li><a href="#s4_2">4.2 Editing Events</a></li>
<li><a href="#s4_3">4.3 Deleting Events</a></li>
<li><a href="#s4_4">4.4 Filtering Events</a></li>
<li><a href="#s4_5">4.5 Clearing Filters</a></li>
</ul>
</li>
<li><a href="#s5"><strong>References</strong></a></li>
</ol>


<hr />

<h3>1. Introduction <a class='h_link' id="s1"></a></h3>
<a href="user_guide.php">Return to top</a>

<p>This document describes how different classifications of users can interact
with the TicketHawk system and the steps needed to complete each task in the
Ticket Hawk system. Section 2 describes the differences between the two
different levels of users. Section 3 of this document describes the
functionality available to the customer user and the steps needed to complete
the various functions accessible to the customer. Section 4 of this document
describes the functionality available to the administrator and the steps needed
to perform their job as an administrator.</p>

<hr />

<h3>2. Levels of Users <a class="h_link" id="s2"></a></h3>
<a href="user_guide.php">Return to top</a>

<h5>Customer</h5>

<p>The first level of user is the customer user. The customer is the broad range
of people who use the TicketHawk system to fulfill their event ticketing needs.
The customer is able to create an account, browse events, view details of
events, purchase event tickets, and view his purchase history.</p>

<h5>Administrator</h5>

<p>The second level of user is the administrator user. The administrator user is
the individual who maintains the database and event information for Ticket Hawk.
The administrator is in charge of adding events, modifying events, and deleting
events.</p>

<hr />

<h3>3. Customer Guide <a class="h_link" id="s3"></a></h3>
<a href="user_guide.php">Return to top</a>

<p>When a customer first visits the Ticket Hawk website, they will be directed
to the main page.</p>

<h4>3.1 Creating an Account <a class="h_link" id="s3_1"></a></h4>

<p>One of the first tasks that any customer will perform when visiting the
Ticket Hawk website is creating an account. Creating an account is not mandatory
for a user, but creating an account is the only way a customer can see his past
    purchase history.</p>

    <ol>
    <li>Click on the <strong>Sign up today</strong> button located on the
    homepage</li>
    <li>Enter Username</li>
    <li>Enter Password</li>
    <li>Enter Confirmation Password</li>
    <li>Enter First Name</li>
    <li>Enter Last Name</li>
    <li>Enter Street Address</li>
    <li>Enter City</li>
    <li>Enter State</li>
    <li>Enter Zipcode</li>
    <li>Enter Email address</li>
    <li>Press the <strong>Submit</strong> button

    <ul>
    <li>You will automatically be signed in and will notice your welcome sign in
    the top right corner left of the <strong>Log Out</strong> button.</li>
    </ul>
    </li>
    </ol>


    <h4>3.2 Sign In / Sign Out <a class="h_link" id="s3_2"></a></h4>
    <a href="user_guide.php">Return to top</a>

    <h5>Sign In</h5>

    <ol>
    <li>To sign in go to the top of the main page

    <ol type="a">
    <li>Enter username in the username text field</li>
    <li>Enter your password into the password text field</li>
    </ol>
    </li>
    </ol>


    <h5>Sign Out</h5>

    <ol>
    <li>Click on the Log out button</li>
    </ol>


    <h4>3.3    Browsing Events <a class="h_link" id="s3_3"></a></h4>
    <a href="user_guide.php">Return to top</a>

    <p>One way for customers to find events is to browse for an event.</p>

    <ol>
    <li>On the homepage, scroll down to the Browse Event Tickets section or
    click on <strong>Events</strong> in the navigation bar at the top.

    <ul>
    <li>This section displays all current events.</li>
    </ul>
    </li>
    </ol>


    <h4>3.4 Viewing Event Details <a class="h_link" id="s3_4"></a></h4>
    <a href="user_guide.php">Return to top</a>

    <p>When browsing for an event, the homepage will only show a limited amount
    of information for each event. In order to view the event details the
    customer must:</p>

    <ol>
    <li>Scroll down to the <strong>Browse Event Tickets</strong> panel or click
    <strong>Events</strong> in the navigation bar at the top of the
    homepage.</li>
    <li>Click on the <strong>View Details</strong> button of the desired event

    <ul>
    <li>The system will redirect the customer to a new page that will list all
    of the important details for the selected event.</li>
    </ul>
    </li>
    </ol>


    <h4>3.5    Purchasing Event Tickets <a class="h_link" id="s3_5"></a></h4>
    <a href="user_guide.php">Return to top</a>

    <p>The main goal of this product is to allow customers the option to
    purchase tickets to different events. The steps to purchase tickets are
    mostly the same for registered customers who are logged in and those
    customers who are not registered but still want to purchase tickets as a
    guest. The largest difference between registered customers and guests when
    purchasing tickets is that the customer information form is prefilled with
    the logged-in customer’s information.</p>

    <h5>3.5.1 Purchasing Tickets as a Registered Customer <a class='h_link' id="s3_5_1"></a></h5>

    <ol>
    <li>Scroll down the homepage to the <strong>Browse Event Tickets</strong>
    panel or click <strong>Events</strong> in the navigation bar at the top of
    the homepage.</li>
    <li>Click on the <strong>View Details</strong> button</li>
    <li>Once you decide to buy tickets for this event, enter the quantity of
    tickets to purchase in the text field</li>
    <li>Click <strong>Add To Cart</strong>.

    <ul>
    <li>The page is redirected to the shopping cart with the options to add more
    tickets, remove the purchase, or to purchase the tickets.</li>
    </ul>
    </li>
    <li>Click <strong>Purchase</strong>.</li>
    <li>If you are logged in, the system will enter your account information
    into the fields provided on the new page.</li>
    <li>Make sure the information is correct and select
    <strong>Continue</strong>.

    <ul>
    <li>After the continue button is pressed, the page is redirected to an order
    invoice page.</li>
    </ul>
    </li>
    <li>Select <strong>Confirm Purchase</strong>

    <ul>
    <li>You will be redirected to your order history.</li>
    </ul>
    </li>
    </ol>


    <h5>3.5.2 Purchasing Tickets as a Guest <a class="h_link" id="s3_5_2"></a></h5>
    <a href="user_guide.php">Return to top</a>

    <ol>
    <li>Scroll down the homepage to the <strong>Browse Event Tickets</strong>
    panel or click <strong>Events</strong> in the navigation bar at the top of
    the homepage.</li>
    <li>Click on the <strong>View Details</strong> button</li>
    <li>Once you decide to buy tickets for this event, enter the quantity of
    tickets to purchase in the text field</li>
    <li>Click <strong>Add To Cart</strong>

    <ul>
    <li>The page is redirected to the shopping cart with the options to add more
    tickets, remove the purchase, or to purchase the tickets.</li>
    </ul>
    </li>
    <li>Click on the <strong>Purchase</strong></li>
    <li>Enter your contact information into the given form to purchase the
    tickets.</li>
    <li>Make sure the information is correct and select
    <strong>Continue</strong>

    <ul>
    <li>After the continue button is pressed, the page is redirected to an order
    invoice page.</li>
    </ul>
    </li>
    <li>Select <strong>Confirm Purchase As Guest</strong></li>
    </ol>


    <h5>3.5.3 Purchasing Tickets as a Guest Signing Up <a class="h_link" id="s3_5_3"></a></h5>
    <a href="user_guide.php">Return to top</a>

    <ol>
    <li>Scroll down the homepage to the <strong>Browse Event Tickets</strong>
    panel or click <strong>Events</strong> in the navigation bar at the
    top.</li>
    <li>Click on the <strong>View Details</strong> button</li>
    <li>Once you decide to buy tickets for this event, enter the quantity of
    tickets to purchase in the text field</li>
    <li>Click <strong>Add To Cart</strong>

    <ul>
    <li>The page is redirected to the shopping cart with the options to add more
    tickets, remove the purchase, or to purchase the tickets.</li>
    </ul>
    </li>
    <li>Click on the <strong>Purchase</strong> button</li>
    <li>Enter your contact information into the form to purchase the
    tickets.</li>
    <li>Make sure the information is correct and select
    <strong>Continue</strong>.

    <ul>
    <li>After the continue button is pressed, the page is redirected to an order
    invoice page.</li>
    </ul>
    </li>
    <li>Enter a Username, Password and re-enter your password to confirm
    it.</li>
    <li>Select <strong>Sign Up &amp; Confirm Purchase</strong>.

    <ul>
    <li>You will be automatically logged in and redirected to your order history
    page.</li>
    </ul>
    </li>
    </ol>


    <h4>3.6 View Purchase History <a class="h_link" id="s3_6"></a></h4>
    <a href="user_guide.php">Return to top</a>

    <p>Sometimes customers want to see their purchase history. For a customer to
    see his purchase history, he must:</p>

    <ol>
    <li>Be logged into his account</li>
    <li>Click on his account name at the top-right of the screen

    <ul>
    <li>This will redirect the customer to his account where he can see his
    order history.</li>
    </ul>
    </li>
    </ol>


    <hr />

    <h3>4. Administrator Guide <a class="h_link" id="s4"></a></h3>
    <a href="user_guide.php">Return to top</a>

    <p>The administrator account is accessible only by Ticket Hawk
    employees. The main functions of an administrator are adding events, editing
    events, and deleting events. Once the administrator is logged into her
    account, she is redirected to the administrator homepage, which provides a
    view enabling the following options.</p>

    <h4>4.1 Adding Events <a class="h_link" id="s4_1"></a></h4>

    <ol>
    <li>Sign in with your admin username and password at the top of the page</li>
    <li>Scroll down to the <strong>Add Events</strong> panel</li>
    <li>Enter Event Name</li>
    <li>Choose Date and Time</li>
    <li>Enter the Price per ticket</li>
    <li>Enter the Ticket Quantity available for the event</li>
    <li>Enter the Location of the event</li>
    <li>Enter the name of the Venue</li>
    <li>Upload the event image file</li>
    <li>Click <strong>Submit</strong>

    <ul>
    <li>The newly added event should be visible in the <strong>Listed
    Events</strong> panel of Admin page and on the main page of the
    website.</li>
    </ul>
    </li>
    </ol>


    <h4>4.2 Editing Events <a class="h_link" id="s4_2"></a></h4>
    <a href="user_guide.php">Return to top</a>

    <ol>
    <li>Scroll to the <strong>Listed Events</strong> panel on the administrator
    homepage.</li>
    <li>Click the <strong>Select</strong> button for the event you want to
    edit</li>
    <li>Click on the <strong>Edit selected row</strong> button</li>
    <li>Only change the information that you are modifying:

    <ol type="a">
    <li>Enter the Event Name</li>
    <li>Choose Date and Time</li>
    <li>Enter the Price per ticket</li>
    <li>Enter the Ticket Quantity</li>
    <li>Enter Location of the event</li>
    <li>Enter the Venue</li>
    <li>Choose the event image</li>
    </ol>
    </li>
    <li>Click <strong>Save Changes</strong>

    <ul>
    <li>You may check the <strong>Listed Events</strong> panel to ensure that
    the event has been properly updated.</li>
    </ul>
    </li>
    </ol>


    <h4>4.3 Deleting Events <a class="h_link" id="s4_3"></a></h4>
    <a href="user_guide.php">Return to top</a>

    <ol>
    <li>Scroll down to the <strong>Delete Events</strong> panel on the
    administrator homepage.</li>
    <li><p>Delete the event through one of two methods:</p>

    <ol type="a">
    <li>Enter the ID of the event to be deleted

    <ul>
    <li>The <strong>ID</strong> can be found in the <strong>Listed
    Events</strong> panel on the administrator homepage.</li>
    </ul>
    </li>
    <li><p>Enter the Date of the event to be deleted.</p>

    <pre><code> Note: All events with this date will be deleted
    </code></pre></li>
    </ol>
    </li>
    <li><p>You may ensure that the event has been deleted by looking at the
    <strong>Listed Events</strong> panel on the admin page and on the main page
    of the website.</p></li>
    </ol>


    <h4>4.4 Filtering Events <a class="h_link" id="s4_4"></a></h4>
    <a href="user_guide.php">Return to top</a>

    <ol>
    <li>Scroll to the bottom of the administrator page.</li>
    <li>Enter a date in the <strong>Date 1</strong> field
        <ul>
            <li>OR</li>
        </ul>
    </li>
    <li>Enter a date in the <strong>Date 2</strong> field
        <ul>
            <li>OR</li>
        </ul>
    </li>
    <li>Enter dates in both the <strong>Date 1</strong> and <strong>Date
    2</strong> fields</li>
    <li>Click out of the datepicker widgets</li>
    <li>Click <strong>Filter</strong>

    <ul>
    <li>The <strong>Listed Events</strong> page will be updated to show only
    those events with dates after or including <strong>Date 1</strong> and
    before or including <strong>Date 2</strong>.</li>
    </ul>
    </li>
    </ol>


    <h4>4.5 Clearing Filters <a class="h_link" id="s4_5"></a></h4>
    <a href="user_guide.php">Return to top</a>

    <ol>
    <li>Click <strong>Clear Filter</strong> at the bottom of the admin homepage

    <ul>
    <li>Now you should be able to see all active events again.</li>
    </ul>
    </li>
    </ol>

    <h3>5. References <a class="h_link" id="s5"></a></h3>
    <a href="user_guide.php">Return to top</a>
    <p>All of our referenced photographs were Labeled for reuse with modification. Therefore, below we have provided the license for each referenced image and listed the modifications that were made to each.</p>
    <ol>
        <li>
            <a href="https://farm6.staticflickr.com/5018/5575566075_72b0ddc727_o.jpg">
                Sedona Hot Air Balloon Ride
            </a>
            <p><a href="https://creativecommons.org/licenses/by/2.0/legalcode">
                License
            </a></p>
            Changes:
            <ul>
                <li>Resized</li>
                <li>Cropped</li>
            </ul>
        </li>
        <li>
            <a href="http://www.allbum.it/images/phantom-of-the-opera-1.jpg">
                The Phantom of the Opera
            </a>
            <p><a href="https://creativecommons.org/licenses/by/2.0/legalcode">
                License
            </a></p>
            Changes:
            <ul>
                <li>Resized</li>
                <li>Cropped</li>
                <li>Text At Top Removed</li>
                <li>Watermark Removed</li>
            </ul>
        </li>
        <li>
            <a href="https://encrypted-tbn2.gstatic.com/images?q=tbn:ANd9GcTbMiW2iNHmwZZYS-pjuMDWQqeaj9IKljnTGAAlj91uJ52LwOLH">
                World Cup Qatar
            </a>
            <p><a href="https://creativecommons.org/licenses/by/2.0/legalcode">
                License
            </a></p>
            Changes:
            <ul>
                <li>Resized</li>
                <li>Cropped</li>
            </ul>
        </li>
        <li>
            <a href="http://www.luckymountainhome.com/images/neighborhoods/breckenridge_rafting_colorado_whitewater.jpg">
                Rafting Photo
            </a>
            <p><a href="https://creativecommons.org/licenses/by/2.0/legalcode">
                License
            </a></p>
            Changes:
            <ul>
                <li>Resized</li>
                <li>Cropped</li>
            </ul>
        </li>
        <li>
            <a href="http://upload.wikimedia.org/wikipedia/commons/c/ce/Zipline.JPG">
                Ziplines Photo
            </a>
            <p><a href="https://creativecommons.org/licenses/by/2.0/legalcode">
                License
            </a></p>
            Changes:
            <ul>
                <li>Resized</li>
                <li>Cropped</li>
            </ul>
        </li>
        <li>
            <a href="http://upload.wikimedia.org/wikipedia/commons/c/ce/Waterfalls-rocks-landscape_-_Virginia_-_ForestWander.jpg">
                Waterfall Photo
            </a>
            <p><a href="https://creativecommons.org/licenses/by/2.0/legalcode">
                License
            </a></p>
            Changes:
            <ul>
                <li>Resized</li>
                <li>Cropped</li>
            </ul>
        </li>
        <li>
            <a href="http://upload.wikimedia.org/wikipedia/commons/0/06/BuschSeriesFieldAtTexasApril2007.jpg">
                Nascar
            </a>
            <p><a href="https://creativecommons.org/licenses/by/2.0/legalcode">
                License
            </a></p>
            Changes:
            <ul>
                <li>Resized</li>
                <li>Cropped</li>
            </ul>
        </li>
        <li>
            <a href="https://farm5.staticflickr.com/4083/4950349054_aa1e70136d_o_d.jpg">
                Jordan
            </a>
            <p><a href="https://creativecommons.org/licenses/by/2.0/legalcode">
                License
            </a></p>
            Changes:
            <ul>
                <li>Resized</li>
                <li>Cropped</li>
            </ul>
        </li>
        <li>
            <a href="http://pixabay.com/static/uploads/photo/2012/11/27/16/43/american-football-67439_640.jpg">
                NFL  
            </a>
            <p><a href="https://creativecommons.org/licenses/by/2.0/legalcode">
                License
            </a></p>
            Changes:
            <ul>
                <li>Resized</li>
                <li>Cropped</li>
            </ul>
        </li>
        <li>
            <a href="http://mediad.publicbroadcasting.net/p/shared/npr/201302/171057967.jpg">
                Beyonce
            </a>
            <p><a href="https://creativecommons.org/licenses/by/2.0/legalcode">
                License
            </a></p>
            Changes:
            <ul>
                <li>Resized</li>
                <li>Cropped</li>
            </ul>
        </li>
        <li>
            <a href="http://upload.wikimedia.org/wikipedia/commons/6/68/Disney_Orlando_castle_at_night.JPG">
                Disney
            </a>
            <p><a href="https://creativecommons.org/licenses/by/2.0/legalcode">
                License
            </a></p>
            Changes:
            <ul>
                <li>Resized</li>
                <li>Cropped</li>
            </ul>
        </li>
        <li>
            <a href="https://farm6.staticflickr.com/5003/5202784642_fa299904ef_o.jpg">
                MLS
            </a>
            <p><a href="https://creativecommons.org/licenses/by/2.0/legalcode">
                License
            </a></p>
            Changes:
            <ul>
                <li>Resized</li>
                <li>Cropped</li>
            </ul>
        </li>
    </ol>
    <a href="user_guide.php">Return to top</a>
    </div>
</body>
</html>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="dist/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="dist/js/docs.min.js"></script>

