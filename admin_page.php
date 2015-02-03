<!-- Connect to Database -->
<?php
// phpinfo();
include "dist/common.php";
global $dbhost, $dbname;
$creds = db_admin();
$dbuser = array_values($creds)[0];
$dbpass = array_values($creds)[1];
$cxn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
$query = "SELECT * FROM EVENT";
$results = mysqli_query($cxn, $query) or die("Connection could not be established");
$welcome_msg = "";

$eventName= $eventDate= $eventTime= $eventLocation= $eventVenue= $eventPrice= $ticketQuantity= $eventImg="";
$eventNameErr= $eventDateErr=$eventTimeErr=$eventLocationErr=$eventVenueErr=$eventPriceErr=$ticketQuantityErr=$eventImgErr="";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$errCount = 0;
	        if (empty($_POST["eventName"])) {
            ++$errCount;
            $eventNameErr = "Event name is required";
        } 
        else {
            $eventName = test_input($_POST["eventName"]);
			// if (!preg_match("/^[a-zA-Z0-9]*$/",$eventName)) {
                // ++$errCount;
                // $eventNameErr = "Only letters and numbers allowed";
            // }
        }
         if (empty($_POST["eventDate"])) {
            ++$errCount;
            $eventDateErr = "Event date is required";
        } 
        else {
            $eventDate = test_input($_POST["eventDate"]);
			// if (!filter_var($eventDate, FILTER_VALIDATE_EMAIL)) {
                // ++$errCount;
                // $eventDateErr = "Invalid Format";
            // }
        }
		
		 if (empty($_POST["eventTime"])) {
            ++$errCount;
            $eventTimeErr = "Event time is required";
        } 
        else {
            $eventTime = test_input($_POST["eventTime"]);
			// if (!filter_var($eventTime, FILTER_VALIDATE_EMAIL)) {
                // ++$errCount;
                // $eventTimeErr = "Only numbers allowed";
            // }
        }
		
		 if (empty($_POST["eventLocation"])) {
            ++$errCount;
            $eventLocationErr = "Event Location is required";
        } 
        else {
            $eventLocation = test_input($_POST["eventLocation"]);
			// if (!preg_match("/^[a-zA-Z0-9]*$/",$eventLocation)) {
                // ++$errCount;
                // $eventLocationErr = "Only letters and numbers allowed";
            // }
        }
		
		if (empty($_POST["eventVenue"])) {
            ++$errCount;
            $eventVenueErr = "Event Venue is required";
        } 
        else {
            $eventVenue = test_input($_POST["eventVenue"]);
			// if (!preg_match("/^[a-zA-Z0-9]*$/",$eventVenue)) {
                // ++$errCount;
                // $eventVenueErr = "Only letters and numbers allowed";
            // }
        }
		
		if (empty($_POST["eventPrice"])) {
            ++$errCount;
            $eventPriceErr = "Event Price is required";
        } 
        else {
            $eventPrice = test_input($_POST["eventPrice"]);
			// if (!preg_match("/^[a-zA-Z0-9]*$/",$eventPrice)) {
                // ++$errCount;
                // $eventPriceErr = "Only letters and numbers allowed";
            // }
        }
		
		if (empty($_POST["ticketQuantity"])) {
            ++$errCount;
            $ticketQuantity = "Set number of tickets";
        } 
        else {
            $ticketQuantity = test_input($_POST["ticketQuantity"]);
			// if (!preg_match("/^[a-zA-Z0-9]*$/",$ticketQuantity)) {
                // ++$errCount;
                // $ticketQuantityErr = "Only numbers allowed";
            // }
        }
		
		if (empty($_POST["eventImg"])) {
            ++$errCount;
            $eventImgErr = "Event Image is required";
        } 
        else {
    $eventImg = test_input($_FILES["eventImg"]);
			//$eventImg = $_FILES['eventImg']['name'];
			//move_uploaded_file($eventImg, "img");
	// $target_dir = "img/";
	// $target_file = $target_dir . basename($_FILES["eventImage"]["img"]);
	// $uploadOk = 1;
	// $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
	// Check if image file is a actual image or fake image
	// if(isset($_POST["submit"])) {
	    // $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
	    // if($check !== false) {
	        // echo "File is an image - " . $check["mime"] . ".";
	        // $uploadOk = 1;
	    // } else {
	        // echo "File is not an image.";
	        // $uploadOk = 0;
	    // }
	// }
	// // Check if file already exists
	// if (file_exists($target_file)) {
	    // echo "Sorry, file already exists.";
	    // $uploadOk = 0;
	// }
	// // Check file size
	// if ($_FILES["fileToUpload"]["size"] > 500000) {
	    // echo "Sorry, your file is too large.";
	    // $uploadOk = 0;
	// }
	// // Allow certain file formats
	// if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
	// && $imageFileType != "gif" ) {
	    // echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
	    // $uploadOk = 0;
	// }
	// // Check if $uploadOk is set to 0 by an error
	// if ($uploadOk == 0) {
	    // echo "Sorry, your file was not uploaded.";
	// // if everything is ok, try to upload file
	// } else {
	    // if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
	        // //echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
	    // } else {
	        // echo "Sorry, there was an error uploading your file.";
	    // }
	
			// if (!preg_match("/^[a-zA-Z0-9]*$/",$eventLocation)) {
                // ++$errCount;
                // $eventLocationErr = "Only letters and numbers allowed";
            // }
        }
        
	if ($errCount == 0) {
		createEvent($eventName, $eventDate, $eventTime, $eventLocation, $eventVenue, $eventPrice, $ticketQuantity, $eventImg);
	}
}
function createEvent($_eventName, $_eventDate,$_eventTime,$_eventLocation,$_eventVenue,$_eventPrice,$_ticketQuantity,$_eventImg){
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
				height: 400px;
			}
			#events-in {
				/*margin-top: 10px;*/
				/*width:500px;*/
				padding: 10px;
				/*padding-right: 10px;*/
				/*white-space: nowrap;*/
			}
			#events-in input{
				width:500px;
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
                        <li>
                            <div class="form-group">
                                <label><?php echo $welcome_msg; ?></label>
                            </div>
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
                <h3 class="panel-title">Events</h3>
            </div>

            <!-- Table -->
                <table class="table">
                    <thead>
                        <tr>
                            <th>Event Name</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Location</th>
                            <th>Venue</th>
                            <th>Price</th>
                            <th>Ticket Quantity</th>
                        </tr>
                    </thead>
                    <?php

						while ($row = mysqli_fetch_assoc($results)) {
							echo "<tr>";
							echo "<td>" . $row['eventname'] . "</td>";
							echo "<td>" . $row['date'] . "</td>";
							echo "<td>" . $row['time'] . "</td>";
							echo "<td>" . $row['location'] . "</td>";
							echo "<td>" . $row['venue'] . "</td>";
							echo "<td>".sprintf("%01.2f", $row['price'])."</td>";
							echo "<td>" . $row['ticket_qty'] . "</td>";
							echo '<td><img src = "data:image/jpeg;base64,' . base64_encode($row['img']) . '" width="80" height="80"/></td>';
							echo "<td><input type='button' name='delete' value='Delete'/></td>";
							echo "</tr>";
						}
	              	?>
                </table>
        </div>
        <h3>Add Events</h3>
        <div class="panel panel-default" id="events-in">
			<form role="form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
				<div class="form-group">
					<label for="event-name">Event Name:</label>
					<span class="error">* <?php echo $eventNameErr; ?></span>
					<input type="text" class="form-control" id="event-name" placeholder="Event Name" name="eventName" required>
				</div>
				<div class="form-group">
					<label for="pwd">Date:</label>
					<span class="error">* <?php echo $eventDateErr; ?></span>
					<input type="date" class="form-control" id="date" placeholder="Enter Date" name="eventDate" required>
				</div>
				<div class="form-group">
					<label for="time">Time:</label>
					<span class="error">* <?php echo $eventTimeErr; ?></span>
					<input type="time" class="form-control" id="time" placeholder="Enter time" name="eventTime" required>
				</div>
				<div class="form-group">
					<label for="location">Location:</label>
					<span class="error">* <?php echo $eventLocationErr; ?></span>
					<input type="text" class="form-control" id="location" placeholder="Enter Location" name="eventLocation"required>
				</div>
				<div class="form-group">
					<label for="venue">Venue:</label>
					<span class="error">* <?php echo $eventVenueErr; ?></span>
					<input type="text" class="form-control" id="venue" placeholder="Enter Venue" name="eventVenue" required>
				</div>
				<div class="form-group">
					<label for="price">Price:</label>
					<span class="error">* <?php echo $eventPriceErr; ?></span>
					<input type="number" class="form-control" id="price" placeholder="Enter Price" name="eventPrice" required>
				</div>
				<div class="form-group">
					<label for="ticket-amount">Ticket Quantity:</label>
					<span class="error">* <?php echo $ticketQuantityErr; ?></span>
					<input type="number" class="form-control" id="ticket-amount" placeholder="Ticket Quantity" name="ticketQuantity" required="">
				</div>
				<div class="form-group">
					<label for="event-img">Event Image:</label>
					<span class="error">* <?php echo $eventImgErr; ?></span>
					<input type="file" class="form-control" id="event-img" name="eventImg" required="">
				</div>
				<button type="submit" class="btn btn-default" required>
					Submit
				</button>
			</form>
			</div>
		</div>

    </body>
</html>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="dist/js/bootstrap.min.js"></script>
<script src="dist/js/docs.min.js"></script>
