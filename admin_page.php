<!-- Connect to Database -->
<?php
//phpinfo();
include "dist/common.php";
global $dbhost, $dbname;
$creds = db_admin();
$dbuser = array_values($creds)[0];
$dbpass = array_values($creds)[1];
$cxn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
$query = "SELECT * FROM EVENT";
$results = mysqli_query($cxn, $query) or die("Connection could not be established");
$welcome_msg = "";

$eventName = $eventDate = $eventTime = $eventLocation = $eventVenue = $eventPrice = $ticketQuantity = $eventImg = $target_dir = "";
$eventNameErr = $eventDateErr = $eventTimeErr = $eventLocationErr = $eventVenueErr = $eventPriceErr = $ticketQuantityErr = $eventImgErr = "";
$eventImg1 = FALSE;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
	$errCount = 0;
	if (empty($_POST["eventName"])) {
		++$errCount;
		$eventNameErr = "Event name is required";
	} else {
		$eventName = test_input($_POST["eventName"]);
		// if (!preg_match("/^[a-zA-Z0-9]*$/",$eventName)) {
		// ++$errCount;
		// $eventNameErr = "Only letters and numbers allowed";
		// }
	}
	if (empty($_POST["eventDate"])) {
		++$errCount;
		$eventDateErr = "Event date is required";
	} else {
		$eventDate = test_input($_POST["eventDate"]);
		// if (!filter_var($eventDate, FILTER_VALIDATE_EMAIL)) {
		// ++$errCount;
		// $eventDateErr = "Invalid Format";
		// }
	}

	if (empty($_POST["eventTime"])) {
		++$errCount;
		$eventTimeErr = "Event time is required";
	} else {
		$eventTime = test_input($_POST["eventTime"]);
		// if (!filter_var($eventTime, FILTER_VALIDATE_EMAIL)) {
		// ++$errCount;
		// $eventTimeErr = "Only numbers allowed";
		// }
	}

	if (empty($_POST["eventLocation"])) {
		++$errCount;
		$eventLocationErr = "Event Location is required";
	} else {
		$eventLocation = test_input($_POST["eventLocation"]);
		// if (!preg_match("/^[a-zA-Z0-9]*$/",$eventLocation)) {
		// ++$errCount;
		// $eventLocationErr = "Only letters and numbers allowed";
		// }
	}

	if (empty($_POST["eventVenue"])) {
		++$errCount;
		$eventVenueErr = "Event Venue is required";
	} else {
		$eventVenue = test_input($_POST["eventVenue"]);
		// if (!preg_match("/^[a-zA-Z0-9]*$/",$eventVenue)) {
		// ++$errCount;
		// $eventVenueErr = "Only letters and numbers allowed";
		// }
	}

	if (empty($_POST["eventPrice"])) {
		++$errCount;
		$eventPriceErr = "Event Price is required";
	} else {
		$eventPrice = test_input($_POST["eventPrice"]);
		// if (!preg_match("/^[a-zA-Z0-9]*$/",$eventPrice)) {
		// ++$errCount;
		// $eventPriceErr = "Only letters and numbers allowed";
		// }
	}

	if (empty($_POST["ticketQuantity"])) {
		++$errCount;
		$ticketQuantity = "Set number of tickets";
	} else {
		$ticketQuantity = test_input($_POST["ticketQuantity"]);
		// if (!preg_match("/^[a-zA-Z0-9]*$/",$ticketQuantity)) {
		// ++$errCount;
		// $ticketQuantityErr = "Only numbers allowed";
		// }
	}

	if (empty($_FILES["eventImg"])) {
		++$errCount;
		$eventImgErr = "Event Image is required";
	} else {
		$name = $_FILES['eventImg']['name'];
		$size = $_FILES['eventImg']['size'];
		$type = $_FILES['eventImg']['type'];
		$tmp_name = $_FILES['eventImg']['tmp_name'];
		$error = $_FILES['eventImg']['error'];
		//$test = upload_tmp_dir;
		// $target_dir = "upload_tmp_dir'";
		$target_file = $target_dir . basename($_FILES['eventImg']['tmp_name']);

		//move_uploaded_file($_FILES['eventImg'], "uploads/'".$_FILES['eventImg']['tmp_name']."'");
		if (file_exists($target_file)) {

			if (move_uploaded_file($_FILES['eventImg']['tmp_name'], $target_file)) {
				$eventImg = $target_file;
			}
		} else {
			$eventImgErr = "Image Not uploaded";
		}
	}

	if ($errCount == 0) {
		createEvent($eventName, $eventDate, $eventTime, $eventLocation, $eventVenue, $eventPrice, $ticketQuantity, $eventImg);
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
                    <a class="navbar-brand" href="homepage.php">Ticket Hawk</a>
                </div>
                <div id="navbar" class="navbar-collapse collapse">
                    <ul class="nav navbar-nav">
                        <li class="active"><a href="admin_page.php">Events</a></li>
                        <!-- <li>
                            <div class="form-group">
                                <label><?php echo $welcome_msg; ?></label>
                            </div>
                        </li> -->
                                            
                        <li><a href="#">Users</a></li>
                    </ul>
					<ul class="nav navbar-nav navbar-right">
						<li class="navbar-right">
							<a>Hello</a>
						</li>
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
					$query = "REMOVE FROM EVENT WHERE date = "
	              	?>
                </table>
        </div>
        <h3>Control Panel</h3>
        <div class="panel panel-default" id="events-in">
        	
        	<div class="panel-heading">
                <h3 class="panel-title">Add Events</h3>
            </div>
           
			<form role="form" method="post" class="form-inline" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
				<div class="form-group" id="col-1" style="">
					<label for="event-name">Event Name:</label>
					<span class="error">* <?php echo $eventNameErr; ?></span>
					<input type="text" class="form-control" id="event-name" placeholder="Event Name" name="eventName" required>
					<label for="date">Date:</label>
					<span class="error">* <?php echo $eventDateErr; ?></span>
					<input type="date" class="form-control" id="date" placeholder="Enter Date" name="eventDate" required>
					<label for="time">Time:</label>
					<span class="error">* <?php echo $eventTimeErr; ?></span>
					<input type="time" class="form-control" id="time" placeholder="Enter time" name="eventTime" required>
					<label for="location">Location:</label>
					<span class="error">* <?php echo $eventLocationErr; ?></span>
					<input type="text" class="form-control" id="location" placeholder="Enter Location" name="eventLocation"required>
				</div>
				<div class="form-group" id="col-2" style=" margin-top: 10px;">
					<label for="venue">Venue:</label>
					<span class="error">* <?php echo $eventVenueErr; ?></span>
					<input type="text" class="form-control" id="venue" placeholder="Enter Venue" name="eventVenue" required>
					<label for="price">Price:</label>
					<span class="error">* <?php echo $eventPriceErr; ?></span>
					<input type="number" class="form-control" id="price" placeholder="Enter Price" name="eventPrice" required>
					<label for="ticket-amount">Ticket Quantity:</label>
					<span class="error">* <?php echo $ticketQuantityErr; ?></span>
					<input type="number" class="form-control" id="ticket-amount" placeholder="Ticket Quantity" name="ticketQuantity" required>
					<label for="event-img">Event Image:</label>
					<span class="error">* <?php echo $eventImgErr; ?></span>
					<input type="file" class="form-control" id="event-img" name="eventImg" required>
				</div>
				<div class="form-group" id="button-div" style="margin-top: 5px;">
				<button type="submit" class="btn btn-default" name="submit">
					Submit
				</button>
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
							<input  class="form-control" id="datepicker" name="datetime"/>
							<button type="submit" name="deleteBydate"  class="btn btn-default"/>Delete</button>
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
						Select</button>
					</div>
				</form>

			</div>
		</div>
		<script>
		$( "#datepicker" ).datepicker({
	inline: true
});
</script>
    </body>
    
</html>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="dist/js/bootstrap.min.js"></script>
<script src="dist/js/docs.min.js"></script>
