<?php
    include 'dist/common.php';
	global $dbhost, $dbname;
		
    $creds = db_admin();
    $dbuser = array_values($creds)[0];
    $dbpass = array_values($creds)[1];
    $cxn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

    // Fetch the Events from the database
    $query = "SELECT * FROM EVENT";
    $results = mysqli_query($cxn, $query) or die("Connection could not be established");
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
        <script src="dist/bootstrap/dist/js/bootstrap.min.js"></script>
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
		<style type="text/css">
    .bs-example{
    	margin: 20px;
    }
</style>
        
        
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
            .td_id, .th_id {
                overflow: hidden;
                width: 3%;  
            }
            .td_name, .th_name {
                overflow: hidden;
                width: 19%;  
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
                width: 19%;  
            }
            .td_venue, .th_venue {
                overflow: hidden;
                width: 19%;  
            }
            .td_price, .th_price {
                overflow: hidden;
                width: 7%;  
            }
            .td_qty, .th_qty {
                overflow: hidden;
                width: 8%;  
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
        	 <h3>Control Panel</h3>
        <div class="panel panel-default">
            <!-- Default panel contents -->
            <div class="panel-heading">
                <h3 class="panel-title">Listed Events</h3>
            </div>
            <div class="panel">
                <!-- Table -->
                <table class="table">
                    <thead>
                        <tr>
                        	<th class="col-md-1">ID</th>
                            <th class="col-md-1">Name</th>
                            <th class="col-md-1">Date</th>
                            <th class="col-md-1">Time</th>
                            <th class="col-md-1">Location</th>
                            <th class="col-md-1">Venue</th>
                            <th class="col-md-1">Price</th>
                            <th class="col-md-1">Ticket Qty</th>
                            <th class="col-md-1">Purchased</th>
                            <th class="col-md-2">Image</th>
                            <!-- <th class="col-md-1">Modify</th> -->

                        </tr>
                    </thead>
                </table>
            </div>
            <div class="panel" id="events-ready">
                <table class="table">
                    <tbody>
                    <?php
                     
                        while ($row = mysqli_fetch_assoc($results)) {
                        	$ticketSold = ticketsAdmin($row['eventname']);
                            echo "<tr>";
                            echo '<td class="col-md-1">' . $row['eventid'] . "</td>";
                            echo '<td class="col-md-1">' . $row['eventname'] . "</td>";
                            echo '<td class="col-md-1">' . $row['date'] . "</td>";
                            echo '<td class="col-md-1">' . $row['time'] . "</td>";
                            echo '<td class="col-md-1">' . $row['location'] . "</td>";
                            echo '<td class="col-md-1">' . $row['venue'] . "</td>";
                            echo '<td class="col-md-1">' . sprintf("%01.2f", $row['price']) . "</td>";
                            echo '<td class="col-md-1">' . $row['ticket_qty'] . "</td>";
							echo "<td class='col-md-1'>$ticketSold</td>";
                            echo '<td class="col-md-2"><img src = "data:image/jpeg;base64,' . base64_encode($row['img']) . '" width="80" height="80"/></td>';
							// echo "<td class='col-md-1'><a href='#myModal' class='btn btn-warning btn-xs' data-toggle='modal'>Modify</a></td>";
                            echo "</tr>";
                        }
	              	?>
                    </tbody>
                    </div>
                </table>
            </div>
        </div>
