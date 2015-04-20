<!-- Connect to Database -->
<?php
    session_start();
    include "dist/common.php";
    bounce();

    // Create the connection to the database to be reused
    global $dbhost, $dbname;
	//$results = NULL;
    $creds = db_admin();
    $dbuser = array_values($creds)[0];
    $dbpass = array_values($creds)[1];
    $cxn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

    // Fetch the Events from the database
    $query = "SELECT * FROM EVENT";
    $results = mysqli_query($cxn, $query) or die("Connection could not be established");
    $username = $_SESSION['user'];
    $welcome_msg = ("Welcome " . $username);

    $deleteDate="";
	$tmpid="";

    // Set Max Sizes
    $maxSizeBlob = 65535;

    $eventName = $eventDate = $eventTime = $eventLocation = $eventVenue = $eventPrice = $ticketQuantity = $eventImg = $target_dir = $dateToDB = $timeToDB = "";
    $eventNameErr = $eventDateErr = $eventTimeErr = $eventLocationErr = $eventVenueErr = $eventPriceErr = $ticketQuantityErr = $eventImgErr = "*";
	

    $eventImg1 = FALSE;

    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        // Handle insert event attempt
        if (isset($_POST['submit'])) {
            validateFields();
        }
		elseif (isset($_POST['savechanges'])) {
			//echo "<script> alert('Save Changes clicked'); </script>";
			//echo "<script> alert('Post Value date = ".$_POST['eventDate_U']."'); </script>";
			validateFields_U();
		}
        // Handle logout attempt
        elseif (isset($_POST['logout'])){
            return logout();
        }
    }
		function validateFields_U(){
			//echo "<script> alert($_POST['eventDate_U']); </script>";
			global $tmpid;
        global $cxn;
        global $eventNameErr, $eventDateErr, $eventTimeErr, $eventLocationErr, $eventVenueErr, $eventPriceErr, $ticketQuantityErr, $eventImgErr, $maxSizeBlob;
        $eventNameErr = $eventDateErr = $eventTimeErr = $eventLocationErr = $eventVenueErr = $eventPriceErr = $ticketQuantityErr = $eventImgErr = "*";
        $errCount = 0;
        if (empty($_POST["eventName_U"])) {
            ++$errCount;
            $eventNameErr = "Event name is required";
        } else {
            $eventName = test_input($_POST["eventName_U"]);
            if (!preg_match("/^[a-zA-Z0-9 ]*$/",$eventName)) {
                ++$errCount;
                $eventNameErr = "Only letters, numbers and spaces allowed";
            }
        }
        // if (empty($_POST["eventDate_U"])) {
        	// echo "<script> alert('empty date'); </script>";
            // ++$errCount;
            // $eventDateErr = "Event date is required";
        // } else {
            // $eventDate = $_POST["eventDate_U"];
			// echo "<script> alert($eventDate);</script>";
            // $eventDate = DateTime::createFromFormat('m/d/Y', $eventDate);
// 			
            // if (!$eventDate){
               // ++$errCount;
                // $eventDateErr = "Invalid Date";
				// echo "<script> alert('date error 1'); </script>";
            // }
            // else {
                // $dateToDB = $eventDateU->format("Y-m-d");
				// echo "<script> alert($dateToDB);</script>";
                // $year = (int) ($eventDateU->format("Y"));
                // $month = (int) ($eventDateU->format("m"));
                // $day = (int) ($eventDateU->format("d"));
                // if (!checkdate ($month , $day , $year )){
                    // //++$errCount;
                    // $eventDateErr = "Invalid Date";
					// echo "<script> alert('date error 2'); </script>";
                // }
            // }
        // }

        if (empty($_POST["eventTime_U"])) {
            ++$errCount;
            $eventTimeErr = "Event time is required";
        } else {
            $eventTime = test_input($_POST["eventTime_U"]);
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

        if (empty($_POST["eventLocation_U"])) {
            ++$errCount;
            $eventLocationErr = "Event Location is required";
        } else {
            $eventLocation = test_input($_POST["eventLocation_U"]);
        }

        if (empty($_POST["eventVenue_U"])) {
            ++$errCount;
            $eventVenueErr = "Event Venue is required";
        } else {
            $eventVenue = test_input($_POST["eventVenue_U"]);
        }

        if (empty($_POST["eventPrice_U"])) {
            ++$errCount;
            $eventPriceErr = "Event Price is required";
        } else {
            $eventPrice = test_input($_POST["eventPrice_U"]);
            if (!is_numeric ($eventPrice)){
                ++$errCount;
                $eventPriceErr = "Invalid Price";
            }
            elseif (!preg_match("/^[0-9\.]*$/",$eventPrice)) {
                ++$errCount;
                $eventPriceErr = "Only digits and a radix point";
            }
            else {
                $eventPrice = floatval ($eventPrice);
            }
        }

        if (empty($_POST["ticketQuantity_U"])) {
            ++$errCount;
            $ticketQuantityErr = "Invalid Quantity";
        } else {
            $ticketQuantity = test_input($_POST["ticketQuantity_U"]);
            if (!is_numeric ($ticketQuantity)){
                ++$errCount;
                $ticketQuantityErr = "Invalid Quantity";
            }
            elseif (!preg_match("/^[0-9]*$/",$ticketQuantity)) {
                ++$errCount;
                $ticketQuantityErr = "Only positive integers";
            }
            else{
                $ticketQuantity = (int) ($ticketQuantity);
            }
        }

        if (empty($_FILES["eventImg_U"])) {
            ++$errCount;
            $eventImgErr = "Event Image is required";
        } else {
            $imgData = mysqli_real_escape_string($cxn, file_get_contents($_FILES['eventImg_U']['tmp_name']));
            $imgType = mysqli_real_escape_string($cxn, $_FILES['eventImg_U']['type']);
            $imgSize = mysqli_real_escape_string($cxn, $_FILES['eventImg_U']['size']);
            if (!substr($imgType, 0, 5) == "image"){
                ++$errCount;
                $eventImgErr = "File type must be 'image'";
            }
            elseif ($imgSize > $maxSizeBlob){
                ++$errCount;
                $eventImgErr = "Max file size is 65,535 bytes";
            }
            else {
                $eventImg = $imgData;    
            }
        }
		if(empty($_POST['tmpid'])){

			//echo "<script> alert('Test'); </script>";
			++$errCount;
		}
		else{

			$id = test_input($_POST["tmpid"]);
			//echo "<script> alert('Variable id value = '$id); </script>";
		}

        if ($errCount == 0) {
        	
        	//echo "<script> alert('error count 0'); </script>";
            updateEvent($eventName, $timeToDB, $eventLocation, $eventVenue, $eventPrice, $ticketQuantity, $eventImg, $id);
            /* Clear the POST array so we don't insert duplicate events */
            $_POST = array();
            header('Location: http://localhost/TicketHawk/admin_page.php');
        }
    }


    function validateFields(){
        global $cxn;
        global $eventNameErr, $eventDateErr, $eventTimeErr, $eventLocationErr, $eventVenueErr, $eventPriceErr, $ticketQuantityErr, $eventImgErr, $maxSizeBlob;
        $eventNameErr = $eventDateErr = $eventTimeErr = $eventLocationErr = $eventVenueErr = $eventPriceErr = $ticketQuantityErr = $eventImgErr = "*";
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
            if (!$eventDate){
                ++$errCount;
                $eventDateErr = "Invalid Date";
            }
            else {
            	
                $dateToDB = $eventDate->format("Y-m-d");
                $year = (int) ($eventDate->format("Y"));
                $month = (int) ($eventDate->format("m"));
                $day = (int) ($eventDate->format("d"));
                if (!checkdate ($month , $day , $year )){
                    ++$errCount;
                    $eventDateErr = "Invalid Date";
                }
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
                $eventPriceErr = "Only digits and a radix point";
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
            elseif (!preg_match("/^[0-9]*$/",$ticketQuantity)) {
                ++$errCount;
                $ticketQuantityErr = "Only positive integers";
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
            $imgSize = mysqli_real_escape_string($cxn, $_FILES['eventImg']['size']);
            if (!substr($imgType, 0, 5) == "image"){
                ++$errCount;
                $eventImgErr = "File type must be 'image'";
            }
            elseif ($imgSize > $maxSizeBlob){
                ++$errCount;
                $eventImgErr = "Max file size is 65,535 bytes";
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
        global $cxn;
        $query = "INSERT INTO EVENT(eventname, date, time, location, venue, price, ticket_qty, img) 
            VALUES('$_eventName', '$_eventDate', '$_eventTime', '$_eventLocation', '$_eventVenue', '$_eventPrice', '$_ticketQuantity', '$_eventImg')";
        $results = mysqli_query($cxn, $query) or die("Could not perform request");
    }
	function updateEvent($_eventName,  $_eventTime, $_eventLocation, $_eventVenue, $_eventPrice, $_ticketQuantity, $_eventImg) {
        global $cxn, $tmpid;
        $query = "UPDATE EVENT SET eventname ='$_eventName', date ='".$_POST['eventDate_U']."', time = '$_eventTime', 
        location = '$_eventLocation', venue =  '$_eventVenue', price = '$_eventPrice', ticket_qty = '$_ticketQuantity', img = '$_eventImg' 
        WHERE eventid = ".$_POST['tmpid'];
        $results = mysqli_query($cxn, $query) or die("Could not perform request");
		if($results){
			//echo "<h1>$tmpid</h1>";
		}
    }

    function deleteByDate(){
        global $cxn;
        $query = "DELETE FROM EVENT WHERE date = '".$_POST['delete-by-date']."' ";
        $results = mysqli_query($cxn, $query);
    }

    function deleteById(){
        global $cxn;
        $query = "DELETE FROM EVENT WHERE eventid = '".$_POST['delete-by-id']."' ";
        $results = mysqli_query($cxn, $query);
    }

    function filterByDate(){
		global $cxn, $results;
        $query = "SELECT * FROM EVENT WHERE date BETWEEN '".$_POST['date-1']."' AND '".$_POST['date-2']."'";
        $results = mysqli_query($cxn, $query)or die(mysqli_error($cxn));
    }
	
    if (isset($_POST['deleteBydate'])) {
        deleteByDate();
    }
    if (isset($_POST['deleteById'])) {
        deleteById();
    }
    if (isset($_POST['filter']) && isset($_POST['date-1']) && isset($_POST['date-2'])) {
        filterByDate();
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
        <link href="dist/css/hint.css" rel="stylesheet">
        <link href="dist/css/bootstrap-timepicker.css" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->

        <!-- jquery js -->
   	    <script src="dist/js/main.js"></script>
   	    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
   	    
   	   
        <script src="dist/bootstrap/dist/js/bootstrap.min.js"></script>
        <!-- <script src="dist/js/dropzone.min.js"></script> -->
        <!-- <link href="dist/css/dropzone.css" /> -->
        <!-- Missing -->
        <script type="text/javascript" src="dist/bootstrap/js/transition.js"></script>
        <script type="text/javascript" src="dist/bootstrap/js/collapse.js"></script>
        <script src="dist/js/docs.min.js"></script>
        <!--End Missing -->
        <script src="dist/js/moment.js"></script>
        <script src="dist/js/moment-with-locales.js"></script>
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
		<script>
	$('#d1').datepicker({
		format : 'yyyy-mm-dd'
	});
		</script>
		<script>
			$('#d2').datepicker({
				format : 'yyyy-mm-dd'
			});
		</script>
		<script>
			$('#d3').datepicker({
				format : 'yyyy-mm-dd'
			});
		</script>
				<script>
	$('#d4').datepicker({
		format : 'yyyy-mm-dd'
	});
		</script>
		<style type="text/css">
    .bs-example{
    	margin: 20px;
    }
</style>
                    <!-- <style>
        #dz-area{
            border: 2px dashed #0087F7;
            border-radius: 5px;
            background: white;
            height:200px;
        }
    </style> -->
        
        
        <style>
			#events-ready {
				overflow-y: scroll;
				height: 380px;
			}
			#events-in  .form-group {
				padding: 10px;
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
            .td_id, .th_id {
                overflow: hidden;
                width: 3%;  
            }
            .td_name, .th_name {
                overflow: hidden;
                width: 17%;  
            }
            .td_date, .th_date {
                overflow: hidden;
                width: 8%;  
            }
            .td_time, .th_time {
                overflow: hidden;
                width: 7%;  
            }
            .td_loc, .th_loc {
                overflow: hidden;
                width: 16%;  
            }
            .td_venue {
                overflow: hidden;
                width: 16%;  
            }
            .th_venue {
                overflow: hidden;
                width: 15%;  
            }
            .td_price {
                overflow: hidden;
                width: 7%;  
            }
            .th_price {
                overflow: hidden;
                width: 6%;  
            }
            .td_qty {
                overflow: hidden;
                width: 8%;  
            }
            .th_qty {
                overflow: hidden;
                width: 8%;  
            }
            .td_purch{
            	overflow: hidden;
                width: 8%;
            }
            .th_purch{
            	overflow: hidden;
                width: 9%;
            }
            .td_img, .th_img {
                overflow: hidden;
                width: 10%;  
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
                        <li><a href="http://localhost/tickethawk/homepage.php#browse">Events</a></li>
                        <li><a href="other_admin.php">Users</a></li>
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
                            . '</a></li><form role="form" class="navbar-form navbar-right" method="post" '
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
        <br/>
        <div class="container">
        	 <h3>Control Panel</h3>
        <div class="panel panel-default">
            <!-- Default panel contents -->
            <div class="panel-heading"  style="padding: 20px;">
            	<input type='button' href='#myModal' id='modal_button' style='float: right; margin-top:-9px; margin-right: -13px; ' class='' data-toggle='modal'/>
                <h3 class="panel-title" style="width:200px;">Listed Events</h3>
                
            </div>
            <div class="panel">
                <!-- Table -->
                <table class="table">
                    <thead>
                        <tr>
                        	<th class="th_id">ID</th>
                            <th class="th_name">Name</th>
                            <th class="th_date">Date</th>
                            <th class="th_time">Time</th>
                            <th class="th_loc">Location</th>
                            <th class="th_venue">Venue</th>
                            <th class="th_price">Price</th>
                            <th class="th_qty">Ticket Qty</th>
                            <th class="th_purch">Purchased</th>
                            <th class="th_img">Image</th>
                        </tr>
                    </thead>
                </table>
            </div>
        <script>
        	function myFunction(eventid){
        		 document.getElementById("eventNum").setAttribute("value", eventid);
				 document.getElementById("the_button").click();	
        	}
        </script>
                <script>
        	function showModal(){
			    document.getElementById("modal_button").click();
        	}
        </script>
        
        <script>
        	function validate(){
        		return true;
        		
        	}
        </script>
        <style>
::-webkit-scrollbar {
    -webkit-appearance: none;
    width: 7px;
}
::-webkit-scrollbar-thumb {
    border-radius: 4px;
    background-color: rgba(0,0,0,.5);
    -webkit-box-shadow: 0 0 1px rgba(255,255,255,.5);
}
        </style>
            <div class="panel" id="events-ready">
		        <!-- <form method='post' action ='' id='myForm'>
			        <input type='hidden' name ='eventNum'  id ='eventNum'>
		            <button name = 'the_button' id='the_button' type='submit' style='visibility: hidden;'></button> -->
                    <table class="table">
                        <tbody>
                        <?php
                        $counter = 0;
                        while ($row = mysqli_fetch_assoc($results)) {
                        	$ticketSold = ticketsAdmin($row['eventname']);
							echo "<tr>";
							echo "<form method='post' action ='#myModal' id='myForm'>";
							echo "<input type='hidden' name ='eventNum'  id ='eventNum'>";
							echo "<button name = 'the_button' id='the_button' type='submit' onclick='show_modal()' style='visibility: hidden;'></button>";
							echo "<td class='td_id'>".$row['eventid']."</td>";
							echo "<td class='td_name'>".$row['eventname']."";
							echo "<br/>";
							echo "<input type='button' value='Select' id='edit_button' class='btn btn-primary' onclick ='myFunction(".$row['eventid'].")'/>";
							//echo "";		
							echo "</td>";
							echo "<td class='td_date'>".$row['date']."</td>";
							echo '<td class="td_time">' . $row['time'] . "</td>";
							echo '<td class="td_loc">' . $row['location'] . "</td>";
							echo '<td class="td_venue">' . $row['venue'] . "</td>";
							echo '<td class="td_price">' . sprintf("%01.2f", $row['price']) . "</td>";
							echo '<td class="td_qty">' . $row['ticket_qty'] . "</td>";
							echo "<td class='td_purch'>$ticketSold</td>";
							echo '<td class="td_img"><img src = "data:image/jpeg;base64,' . base64_encode($row['img']) . '" width="80" height="80"/></td>';
							echo "</form>";
							echo "</tr>";
							//echo "</form>";
							
                        }
	              	?>
                    </tbody>
                 
                </table>
                <!-- </form> -->
            </div>
        </div>
        <div class="panel panel-default" id="events-in">
        	
        	<div class="panel-heading">
                <h3 class="panel-title">Add Events</h3>
            </div>
           
			<form role="form"  method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
                <div class="container-fluid">
                    <div class="row">
                        <div class="form-group">
                            <label for="event-name">Event Name:</label>
                            <span class="error"><?php echo $eventNameErr; ?></span>
                            <span class='hint-top' data-hint="Name of the event" style="display: inline;"><input type="text" class="form-control" id="event-name" placeholder="Event Name" name="eventName" required></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-6 col-sm-3 form-group">
                            <label for="event-date">Date:</label>
                            <span class="error"><?php echo $eventDateErr; ?></span>
                            <div class='input-group input-ammend' id='event-date'>
                                <span class='hint--top' data-hint="Event Date" style="display: inline;"><input type='text' class="datepicker form-control" placeholder='Event Date' name='eventDate' required/>
                                </span><span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                        <div class="col-xs-6 col-sm-3 form-group">
                            <label for="time">Time:</label>
                            <span class="error"><?php echo $eventTimeErr; ?></span>
                            <div class="input-group input-ammend" id='time'>
                                <span class='hint--top' data-hint="Event time" style="display: inline;"><input type="text" class="form-control timepicker bootstrap-timepicker" placeholder="Enter Time" name="eventTime" required>
                                </span><span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                                </span>
                            </div>
                        </div>
                        <div class="col-xs-6 col-sm-3 form-group">
                            <label for="price">Price:</label>
                            <span class="error"><?php echo $eventPriceErr; ?></span>
                            <span class='hint-left' data-hint="Name of the event" style="display: inline;"><input type="text" class="form-control" id="price" placeholder="Enter Price" name="eventPrice" required></span>
                        </div>
                        <div class="col-xs-6 col-sm-3 form-group">
                            <label for="ticket-amount">Ticket Quantity:</label>
                            <span class="error"><?php echo $ticketQuantityErr; ?></span>
                            <span class='hint-top' data-hint="Enter whole numbers" style="display: inline;"><input type="text" class="form-control" id="ticket-amount" placeholder="Ticket Quantity" name="ticketQuantity" required>
                            	</span>
                        </div>
                    </div>
                    <div class="row">
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="location">Location:</label>
                            <span class="error"><?php echo $eventLocationErr; ?></span>
                            <input type="text" class="form-control" id="location" placeholder="Enter Location" name="eventLocation" required>
                        </div>
                        <div class="col-xs-6 form-group">
                            <label for="venue">Venue:</label>
                            <span class="error"><?php echo $eventVenueErr; ?></span>
                            <input type="text" class="form-control" id="venue" placeholder="Enter Venue" name="eventVenue" required>
                        </div>
                    </div>
                    <style>
                    	.dropzone{
                    		width:300px;
                    		height:300px;
                    		border:2px dashed #ccc;
                    		color:#ccc;
                    		line-height: 300px;
                    		text-align:center;
                    	}
                    	.dropzone.dragover{
                    		color:black;
                    		border-color:black;
                    	}
                    </style>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="event-img">Event Image:</label>
                            <div id "container"></div>
                            <!-- <div class="dropzone" id ="dropzone"> Drop fole here to upload
                            <span class="error"><?php echo $eventImgErr; ?></span>
                            <input type="file" id="event-img" name="eventImg"  required>
                           </div> -->
                           <input type="file" id="event-img" name="eventImg"  required>
                   		</div>
                       <div class="col-md-6 form-group" id="button-div" style="margin-top: 5px;">
                            <button type="submit" class="btn btn-primary" name="submit">
                                Submit
                            </button>
                        </div>
                	</div>

            </form>
            </div>
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
                        <input type="submit" value="Delete" name="deleteById"  class="btn btn-danger"/>
                    </div>	
                </form>
            </div>
            <div style=" display: inline-block;">
                <form role="form" class="form-inline" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div class="form-group" >
                        <label for="id-num">Delete By Date:</label>
                        <input type="text"  class="datepicker form-control" name="delete-by-date" id="d3" data-date-format="yyyy-mm-dd"/>
                        <button type="submit" name="deleteBydate"  class="btn btn-danger"/>Delete</button>
                    </div>	
                </form>
            </div>
        </div>
		<!-- Filter -->
       <div class=" panel panel-default" id="second-panel">
            <div class="panel-heading">
                <h3 class="panel-title">Filter Events</h3>
            </div>
            
			<form class="form-inline" role="form" method="post">
					<div class="form-group">
						<label for="date-1">Date 1:</label>
						<input type="text" class="datepicker form-control" id="d1" name="date-1" data-date-format="yyyy-mm-dd" />
					</div>
					<div class="form-group">
						<label for="date-2">Date 2:</label>
						<input type="text" class="datepicker form-control" id="d2" name="date-2" data-date-format="yyyy-mm-dd" />
					</div>
					<button type="submit" class="btn btn-default" name="filter">
						Submit
					</button>
			
				</form>

				
				
				
        </div>

            
    <!-- Modal HTML -->
    <?php 

	
if(isset($_POST['eventNum'])){
		$tmpid = $_POST['eventNum'];
		$query = "SELECT * FROM EVENT WHERE eventid =". $tmpid;
		$results = mysqli_query($cxn, $query)or die(mysqli_error($cxn));
		$row  = mysqli_fetch_assoc($results);
		//echo "<script>alert(".$tmpid.");</script>";
 
echo "<div id='myModal' class='modal fade'>
        <div class='modal-dialog' style='width: 700px;'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                    <h4 class='modal-title'>Confirmation</h4>
                </div>
                <div class='modal-body'>
							 <div class='panel panel-default' id='events-in'>
        	
        	<div class='panel-heading'>
                <h3 class='panel-title'>Modify Events</h3>
            </div>
           
			<form role='form'  method='post' action='".htmlspecialchars($_SERVER['PHP_SELF'])."' enctype='multipart/form-data'>
			<input type='hidden' name = 'tmpid' value = '$tmpid' id = 'temp-id'/>
                <div class='container-fluid'>
                    <div class='row'>
                        <div class='form-group'>
                            <label for='event-name'>Event Name:</label>
                            <span class='error'>* $eventNameErr;</span>
                            <input type='text' class='form-control' id='event-name-u' value='".$row['eventname']."' placeholder='Event Name' name='eventName_U' required>
                        </div>
                    </div>
                    <div class='row'>
                        <div class='col-xs-6 col-sm-3 form-group'>
                            <label for='event-date'>Date:</label>
                            <span class='error'>* <?php echo '$eventDateErr'; ?></span>
                            <div class='input-group input-ammend' id='event-date_u'>
                                <input type='text' class='datepicker form-control' value='".$row['date']."'  placeholder='Event Date' name='eventDate_U' required/>
                                <span class='input-group-addon'>
                                    <span class='glyphicon glyphicon-calendar'></span>
                                </span>
                            </div>
                        </div>
                        <div class='col-xs-6 col-sm-3 form-group'>
                            <label for='time'>Time:</label>
                            <span class='error'>* <?php echo '$eventTimeErr'; ?></span>
                            <div class='input-group input-ammend' id='time-u'>
                                <input type='text' value = '".$row['time']."' class='form-control input-small bootstrap-timepicker timepicker' placeholder='Enter Time' name='eventTime_U' required>
                                <span class='input-group-addon'>
                                    <span class='glyphicon glyphicon-time'></span>
                                </span>
                            </div>
                        </div>
                        <div class='col-xs-6 col-sm-3 form-group'>
                            <label for='price'>Price:</label>
                            <span class='error'>* <?php echo '$eventPriceErr'; ?></span>
                            <input type='text' value= '".$row['price']."' class='form-control' id='price-u' placeholder='Enter Price' name='eventPrice_U' required>
                        </div>
                        <div class='col-xs-6 col-sm-3 form-group'>
                            <label for='ticket-amount'>Ticket Quantity:</label>
                            <span class='error'>* <?php echo '$ticketQuantityErr'; ?></span>
                            <input type='number' value = '".$row['ticket_qty']."' class='form-control' id='ticket-amount-u' placeholder='Ticket Quantity' name='ticketQuantity_U' required>
                        </div>
                    </div>
                    <div class='row'>
                    </div>
                    <div class='row'>
                        <div class='col-md-6 form-group'>
                            <label for='location'>Location:</label>
                            <span class='error'>* <?php echo '$eventLocationErr'; ?></span>
                            <input type='text' value = '".$row['location']."' class='form-control' id='location-u' placeholder='Enter Location' name='eventLocation_U'required>
                        </div>
                        <div class='col-xs-6 form-group'>
                            <label for='venue'>Venue:</label>
                            <span class='error'>* <?php echo '$eventVenueErr'; ?></span>
                            <input type='text' value = '".$row['venue']."' class='form-control' id='venue-u' placeholder='Enter Venue' name='eventVenue_U' required>
                        </div>
                    </div>
                    <div class='row'>
                        <div class='col-md-6 form-group'>
                            <label for='event-img'>Event Image:</label>
                            <span class='error'>* <?php echo $eventImgErr; ?></span>
                            <div>
                            <img src='data:image/jpeg;base64," . base64_encode($row['img']) ."' width= '50' height ='50'/>
                            </div>
                            <input type='file' value = 'data:image/jpeg;base64," . base64_encode($row['img']) ."' id='event-img-u' name='eventImg_U' required>
                        </div>
                        <a name='addEvent'><div class='col-md-6 form-group' id='button-div' style='margin-top: 5px;'>
                        </div></a>
                    </div>
                </div>
                 <div class='modal-footer'>
                    <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
                    <input type='submit' class='btn btn-primary' name ='savechanges'></button>
                </div>
            </form>
        </div>
          <p class='text-warning'><small>If you don't save, your changes will be lost.</small></p>
       </div>

            </div>
        </div>
    </div>
</div>"; 
  }	
?>
        
      </div>
                      <script>
                    	( function(){
                    		var dropzone = document.getElementById("dropzone");
                    		
                    		var uploads = function(files){
                    			var formData = new FormData(),
                    			xhr = new XMLHttpRequest(),x;
                    			for(x = 0; x < files.lenght; x++){
                    				formData.append('files[]', files[x]);
                    			}
                    			xhr.open('post', 'uploads.php');
                    			xhr.send(formData);
                    		}
                    		
                    		dropzone.ondrop = function(){
                    			event.preventDefault();
                    			this.className = 'dropzone';
                    			uploads(event.dataTransfer.files)
                    		};
                    		
                    		dropzone.ondragover = function(){
                    			this.className = 'dropzone dragover';
                    			return false;
                    		};
                    		dropzone.ondragleave = function(){
                    			this.className = 'dropzone';
                    			return false;
                    		};
                    	}());
                    </script>
   </body>
    
</html>
