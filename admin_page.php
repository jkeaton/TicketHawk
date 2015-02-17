<!-- Connect to Database -->
<?php
session_start();
include "dist/common.php";
bounce();
global $dbhost, $dbname;
$creds = db_admin();
$dbuser = array_values($creds)[0];
$dbpass = array_values($creds)[1];
$cxn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
$query = "SELECT * FROM EVENT";
$results = mysqli_query($cxn, $query) or die("Connection could not be established");
$username = $_SESSION['user'];
$welcome_msg = ("Welcome " . $username);

$deleteDate="";
$eventName = $eventDate = $eventTime = $eventLocation = $eventVenue =
$eventPrice = $ticketQuantity = $eventImg = $target_dir = $dateToDB = $timeToDB = "";
$eventNameErr = $eventDateErr = $eventTimeErr = $eventLocationErr = $eventVenueErr = $eventPriceErr = $ticketQuantityErr = $eventImgErr = "";
$eventImg1 = FALSE;

if ($_SERVER["REQUEST_METHOD"] == "POST"){
    // Handle insert event attempt
    if (isset($_POST['submit'])) {
        validateFields();
    }
    // Handle logout attempt
    elseif (isset($_POST['logout'])){
        return logout();
    }
}

function validateFields(){
    global $cxn;
    $errCount = 0;
	if (empty($_POST["eventName"])) {
		++$errCount;
		$eventNameErr = "Event name is required";
	} else {
		$eventName = test_input($_POST["eventName"]);
		if (!preg_match("/^[a-zA-Z0-9 ]*$/",$eventName)) {
		    ++$errCount;
            $eventNameErr = "Only letters, numbers and spaces allowed";
        }
	}
	if (empty($_POST["eventDate"])) {
		++$errCount;
		$eventDateErr = "Event date is required";
	} else {
		$eventDate = test_input($_POST["eventDate"]);
        $eventDate = DateTime::createFromFormat('m/d/Y', $eventDate);
        $dateToDB = $eventDate->format("Y-m-d");
        $year = (int) ($eventDate->format("Y"));
        $month = (int) ($eventDate->format("m"));
        $day = (int) ($eventDate->format("d"));
        if (!checkdate ($month , $day , $year )){
		    ++$errCount;
		    $eventDateErr = "Invalid Date";
		}
	}

	if (empty($_POST["eventTime"])) {
		++$errCount;
		$eventTimeErr = "Event time is required";
	} else {
		$eventTime = test_input($_POST["eventTime"]);
        if (!strtotime($eventTime)){
		    ++$errCount;
		    $eventTimeErr = "Invalid Time";
		}
        else {
            if (!date('H:i:s', strtotime($eventTime))){
                ++$errCount;
                $eventTimeErr = "Invalid Time";
            }
            else {
                $timeToDB = date('H:i:s', strtotime($eventTime));
            }
        }
	}

	if (empty($_POST["eventLocation"])) {
		++$errCount;
		$eventLocationErr = "Event Location is required";
	} else {
		$eventLocation = test_input($_POST["eventLocation"]);
	}

	if (empty($_POST["eventVenue"])) {
		++$errCount;
		$eventVenueErr = "Event Venue is required";
	} else {
		$eventVenue = test_input($_POST["eventVenue"]);
	}

	if (empty($_POST["eventPrice"])) {
		++$errCount;
		$eventPriceErr = "Event Price is required";
	} else {
		$eventPrice = test_input($_POST["eventPrice"]);
        if (!is_numeric ($eventPrice)){
            ++$errCount;
            $eventPriceErr = "Invalid Price";
        }
		elseif (!preg_match("/^[0-9\.]*$/",$eventPrice)) {
		    ++$errCount;
		    $eventPriceErr = "Only numbers and decimal points allowed";
        }
        else {
            $eventPrice = floatval ($eventPrice);
        }
	}

	if (empty($_POST["ticketQuantity"])) {
		++$errCount;
		$ticketQuantityErr = "Invalid Quantity";
	} else {
		$ticketQuantity = test_input($_POST["ticketQuantity"]);
        if (!is_numeric ($ticketQuantity)){
            ++$errCount;
            $ticketQuantityErr = "Invalid Quantity";
        }
        else{
            $ticketQuantity = (int) ($ticketQuantity);
        }
	}

	if (empty($_FILES["eventImg"])) {
		++$errCount;
		$eventImgErr = "Event Image is required";
	} else {
		$imgData = mysqli_real_escape_string($cxn, file_get_contents($_FILES['eventImg']['tmp_name']));
		$imgType = mysqli_real_escape_string($cxn, $_FILES['eventImg']['type']);
        if (!substr($imgType, 0, 5) == "image"){
		    ++$errCount;
		    $eventImgErr = "File type must be 'image'";
        }
        else {
            $eventImg = $imgData;    
        }
	}

	if ($errCount == 0) {
		createEvent($eventName, $dateToDB, $timeToDB, $eventLocation, $eventVenue, $eventPrice, $ticketQuantity, $eventImg);
        /* Clear the POST array so we don't insert duplicate events */
        $_POST = array();
	    header('Location: http://localhost/TicketHawk/admin_page.php');
	}
}

function createEvent($_eventName, $_eventDate, $_eventTime, $_eventLocation, $_eventVenue, $_eventPrice, $_ticketQuantity, $_eventImg) {
	$dbuser = 'admin';
	$dbpass = 'balloonrides';
	$dbhost = 'localhost';
	$dbname = 'tickethawk';
	$cxn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

	$query = "INSERT INTO EVENT(eventname, date, time, location, venue, price, ticket_qty, img) 
		VALUES('$_eventName', '$_eventDate', '$_eventTime', '$_eventLocation', '$_eventVenue', '$_eventPrice', '$_ticketQuantity', '$_eventImg')";
	$results = mysqli_query($cxn, $query) or die("Could not perform request");
}

function deleteByDate(){
	$dbuser = 'admin';
	$dbpass = 'balloonrides';
	$dbhost = 'localhost';
	$dbname = 'tickethawk';
	$cxn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
	$query = "DELETE FROM EVENT WHERE date = '".$_POST['delete-by-date']."' ";
	$results = mysqli_query($cxn, $query);
}

if (isset($_POST['deleteBydate'])) {
	deleteByDate();
}
	if (isset($_POST['select-by-id'])) {
	deleteById();
}
function deleteById(){
	$dbuser = 'admin';
	$dbpass = 'balloonrides';
	$dbhost = 'localhost';
	$dbname = 'tickethawk';
	$cxn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
	$query = "DELETE FROM EVENT WHERE eventid = '".$_POST['select-by-id']."' ";
	$results = mysqli_query($cxn, $query);
	}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
    	
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Ticket Hawk</title>
		<link href="jquery-ui.css" rel="stylesheet">
        <!-- Bootstrap -->
        <link href="dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="dist/bootstrap/dist/css/bootstrap.css" rel="stylesheet">
        <!-- Carousel Customization -->
        <link href="dist/css/custom.css" rel="stylesheet">
        <!-- Datepicker stylesheet -->
        <link href="dist/css/datepicker.css" rel="stylesheet">
        <link href="dist/css/bootstrap-timepicker.css" rel="stylesheet">
        

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->

        <!-- jquery js -->
   	    <script src="dist/js/main.js"></script>
        <script src="dist/bootstrap/dist/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="dist/bootstrap/js/transition.js"></script>
        <script type="text/javascript" src="dist/bootstrap/js/collapse.js"></script>
        <script src="dist/js/moment.js"></script>
        <script src="dist/js/moment-with-locales.js"></script>
        <script src="dist/js/docs.min.js"></script>
        <script src="dist/js/bootstrap-datepicker.js"></script>
        <script src="dist/js/bootstrap-timepicker.js"></script>
        <script type="text/javascript">
            $(function () {
                $('.datepicker').datepicker()
            });
        </script>
        <script type="text/javascript">
            $(function () {
                $('.timepicker').timepicker()
            });
        </script>
        
        <style>
			#events-ready {
				overflow-y: scroll;
				height: 380px;
			}
			#events-in  .form-group {
				/*margin-top: 10px;*/
				/*width:500px;*/
				padding: 10px;
				/*padding-right: 10px;*/
				/*white-space: nowrap;*/
			}
			#events-in input {
				/*width:500px;*/
			}
			#event-img {
				max-width: 200px;
			}
			#second-panel .form-group{
				padding: 10px;
			}
			#third-panel .form-group{
				padding: 10px;
			}
        </style>
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
                    <a class="navbar-brand" href="#">Ticket Hawk</a>
                </div>
                <div id="navbar" class="navbar-collapse collapse">
                    <ul class="nav navbar-nav">
                        <li class="active"><a href="homepage.php">Home</a></li>
                        <li class="active"><a href="admin_page.php">Events</a></li>
                        <li><a href="#">Users</a></li>
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                    <?php
                        if (isset($_SESSION['user']))
                        {
                            echo "<li class=\"navbar-left\">
                            <a>".$welcome_msg."</a></li><li
                            class=\"navbar-left\"><form role=\"form\"
                            class=\"navbar-form navbar-left\" method=\"post\"
                            action=\"" . htmlspecialchars($_SERVER["PHP_SELF"]) . "\"><button
                            type=\"submit\" class=\"btn btn-danger\"
                            name=\"logout\">Log Out</button></form></li>";
                        }
                    ?>
                    </ul>
                </div><!--/.nav-collapse -->            
            </div>
        </nav>
        </br>
        <div class="container">
        	 <h3>Listed Events</h3>
        <div class="panel panel-default" id="events-ready">
            <!-- Default panel contents -->
            <div class="panel-heading">
                <h3 class="panel-title">ListedEvents</h3>
            </div>

            <!-- Table -->
                <table class="table">
                    <thead>
                        <tr>
                        	<th>Event I.D.</th>
                            <th>Event Name</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Location</th>
                            <th>Venue</th>
                            <th>Price</th>
                            <th>Ticket Quantity</th>
                            <th>Event Image</th>
                        </tr>
                    </thead>
                    <?php

                        while ($row = mysqli_fetch_assoc($results)) {
                            echo "<tr>";
                            echo "<td>" . $row['eventid'] . "</td>";
                            echo "<td>" . $row['eventname'] . "</td>";
                            echo "<td>" . $row['date'] . "</td>";
                            echo "<td>" . $row['time'] . "</td>";
                            echo "<td>" . $row['location'] . "</td>";
                            echo "<td>" . $row['venue'] . "</td>";
                            echo "<td>" . sprintf("%01.2f", $row['price']) . "</td>";
                            echo "<td>" . $row['ticket_qty'] . "</td>";
                            echo '<td><img src = "data:image/jpeg;base64,' . base64_encode($row['img']) . '" width="80" height="80"/></td>';

                            echo "</tr>";
                        }
	              	?>
                </table>
        </div>
        <h3>Control Panel</h3>
        <div class="panel panel-default" id="events-in">
        	
        	<div class="panel-heading">
                <h3 class="panel-title">Add Events</h3>
            </div>
           
			<form role="form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
                <div class="container-fluid">
                    <div class="row">
                        <div class="form-group">
                            <label for="event-name">Event Name:</label>
                            <span class="error">* <?php echo $eventNameErr; ?></span>
                            <input type="text" class="form-control" id="event-name" placeholder="Event Name" name="eventName" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-6 col-sm-3 form-group">
                            <label for=event-"date">Date:</label>
                            <span class="error">* <?php echo $eventDateErr; ?></span>
                            <div class='input-group input-ammend' id='event-date'>
                                <input type='date' class="datepicker form-control" placeholder="Event Date" name='eventDate' required/>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                        <div class="col-xs-6 col-sm-3 form-group">
                            <label for="time">Time:</label>
                            <span class="error">* <?php echo $eventTimeErr; ?></span>
                            <div class="input-group input-ammend" id='time'>
                                <input type="time" class="form-control timepicker bootstrap-timepicker" placeholder="Enter Time" name="eventTime" required>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                                </span>
                            </div>
                        </div>
                        <div class="col-xs-6 col-sm-3 form-group">
                            <label for="price">Price:</label>
                            <span class="error">* <?php echo $eventPriceErr; ?></span>
                            <input type="text" class="form-control" id="price" placeholder="Enter Price" name="eventPrice" required>
                        </div>
                        <div class="col-xs-6 col-sm-3 form-group">
                            <label for="ticket-amount">Ticket Quantity:</label>
                            <span class="error">* <?php echo $ticketQuantityErr; ?></span>
                            <input type="number" class="form-control" id="ticket-amount" placeholder="Ticket Quantity" name="ticketQuantity" required>
                        </div>
                    </div>
                    <div class="row">
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="location">Location:</label>
                            <span class="error">* <?php echo $eventLocationErr; ?></span>
                            <input type="text" class="form-control" id="location" placeholder="Enter Location" name="eventLocation"required>
                        </div>
                        <div class="col-xs-6 form-group">
                            <label for="venue">Venue:</label>
                            <span class="error">* <?php echo $eventVenueErr; ?></span>
                            <input type="text" class="form-control" id="venue" placeholder="Enter Venue" name="eventVenue" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="event-img">Event Image:</label>
                            <span class="error">* <?php echo $eventImgErr; ?></span>
                            <input type="file" id="event-img" name="eventImg" required>
                        </div>
                        <div class="col-md-6 form-group" id="button-div" style="margin-top: 5px;">
                            <button type="submit" class="btn btn-primary" name="submit">
                                Submit
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
			
        <div class=" panel panel-default" id="second-panel">
            <div class="panel-heading">
                <h3 class="panel-title">Delete Events</h3>
            </div>
            <div style="float: left; display: inline-block;">
                <form role="form" class="form-inline" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div class="form-group">
                        <label for="id-num">Delete By ID:</label>
                        <input type="number"  class="form-control" name="delete-by-id"/>
                        <button type="submit" name="deleteById"  class="btn btn-default"/>Delete</button>
                    </div>	
                </form>
            </div>
            <div style=" display: inline-block;">
                <form role="form" class="form-inline" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div class="form-group" >
                        <label for="id-num">Delete By Date:</label>
                        <input type="date"  class="form-control" name="delete-by-date"/>
                        <button type="submit" name="deleteBydate"  class="btn btn-default"/>Delete</button>
                    </div>
					<div class="form-group">
							<label for="id-num">Archive By Date:</label>
							<input type="number"  class="form-control" name="archive"/>
							<button type="submit" name="archive-btn"  class="btn btn-default"/>
							Archive
							</button>
					</div>	
                </form>
            </div>
        </div>
	
        <div class="panel panel-default" id="third-panel">
            <div class="panel-heading">
                <h3 class="panel-title">Modify Events</h3>
            </div>
            <form class="form-inline" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="form-group">
                    <label for="id-num">Select ID:</label>
                    <input type="number"  class="form-control" name="select-by-id"/>
                    <button type="submit" name="selectById"  class="btn btn-default"/>
                        Select
                    </button>
                </div>
            </form>
        </div>
    </body>
    
</html>

