<!-- Connect to Database -->
<?php
include 'dist/config.php';
include 'dist/opendb.php';
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
		<link href="dist/css/sign-up.css" rel="stylesheet">
		<!-- Carousel Customization -->
		<link href="dist/css/carousel.css" rel="stylesheet">

		<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
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
						<li class="active">
							<a href="#">Home</a>
						</li>
						<li>
							<a href="#about">About</a>
						</li>
						<li>
							<a href="#contact">Contact</a>
						</li>
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Search <span class="caret"></span></a>
							<ul class="dropdown-menu" role="menu">
								<li class="dropdown-header">
									Sports
								</li>
								<li>
									<a href="#">All Sports</a>
								</li>
								<li>
									<a href="#">NBA</a>
								</li>
								<li>
									<a href="#">NFL</a>
								</li>
								<li>
									<a href="#">MLB</a>
								</li>
								<li>
									<a href="#">MLH</a>
								</li>
								<li>
									<a href="#">MLS</a>
								</li>
								<li>
									<a href="#">NASCAR</a>
								</li>
								<li class="divider"></li>
								<li class="dropdown-header">
									Movies
								</li>
								<li>
									<a href="#">All Movies</a>
								</li>
								<li>
									<a href="#">New Releases</a>
								</li>
								<li>
									<a href="#">Drama</a>
								</li>
								<li>
									<a href="#">Action</a>
								</li>
								<li>
									<a href="#">Horror</a>
								</li>
								<li>
									<a href="#">Comedy</a>
								</li>
								<li>
									<a href="#">Suspense</a>
								</li>
								<li class="divider"></li>
								<li class="dropdown-header">
									Special Events
								</li>
								<li>
									<a href="#">2016 Olympics</a>
								</li>
								<li>
									<a href="#">World Cup</a>
								</li>
								<li class="divider"></li>
								<li class="dropdown-header">
									Music Tour
								</li>
								<li>
									<a href="#">On The Run(Jay Z & Beyonce)</a>
								</li>
								<li>
									<a href="#">Rock</a>
								</li>
								<li>
									<a href="#">Rap</a>
								</li>
								<li>
									<a href="#">R&B</a>
								</li>
								<li>
									<a href="#">Jazz</a>
								</li>
								<li>
									<a href="#">Gospel</a>
								</li>
								<li class="divider"></li>
								<li class="dropdown-header">
									See All Listing
								</li>
								<li>
									<a href="#">All</a>
								</li>
							</ul>
						</li>
					</ul>
					<form class="navbar-form navbar-right">
						<div class="form-group">
							<input type="text" placeholder="Email" class="form-control">
						</div>
						<div class="form-group">
							<input type="password" placeholder="Password" class="form-control">
						</div>
						<button type="submit" class="btn btn-success" name="submit">
							Sign in
						</button>
					</form>
				</div><!--/.nav-collapse -->
			</div>
		</nav>

		<!--main
		================================================== -->
		<div class="container">
			<div id="sign-up">
				<h2>Sign up</h2>
				<form class="form-horizontal" role="form">
					<div class="form-group">
						<label class="control-label col-sm-2" for="email">Name:</label>
						<div class="col-sm-10">
							<input type="email" class="form-control" name="newName" id="name" placeholder="Enter name">
						</div>
					</div>

					<div class="form-group">
						<label class="control-label col-sm-2" for="email">Email:</label>
						<div class="col-sm-10">
							<input type="email" class="form-control" name="newEmail" id="email" placeholder="Enter email">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-2" for="pwd" style="padding-left: 8px;">Password:</label>
						<div class="col-sm-10">
							<input type="password" class="form-control" name="newPass" id="pwd" placeholder="Enter password">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-2" for="gender"style="padding-left: 8px;">Gender:</label>

						<label class="radio-inline">
							<input type="radio" name="gender" value="male" style="padding-left: 4px;">
							Male </label>
						<label class="radio-inline">
							<input type="radio" name="gender" value="female">
							Female</label>

					</div>
					<div class="form-group">
						<label class="control-label col-sm-2" for="age">Age:</label>
						<div class="col-sm-10">
							<select class="form-control" name="newAge" id="age" style="width: 100px;">
								<option>1998</option>
								<option>1997</option>
								<option>1996</option>
								<option>1995</option>
								<option>1994</option>
								<option>1993</option>
								<option>1992</option>
								<option>1991</option>
								<option>1990</option>
								<option>1989</option>
								<option>1988</option>
								<option>1987</option>
								<option>1986</option>
								<option>1985</option>
								<option>1984</option>
								<option>1983</option>
								<option>1982</option>
								<option>1981</option>
								<option>1980</option>
								<option>1979</option>
								<option>1978</option>
								<option>1977</option>
								<option>1976</option>
								<option>1975</option>
								<option>1974</option>
								<option>1973</option>
								<option>1972</option>
								<option>1971</option>
								<option>1970</option>
								<option>1969</option>
								<option>1968</option>
								<option>1967</option>
								<option>1966</option>
								<option>1965</option>
								<option>1964</option>
								<option>1963</option>
								<option>1962</option>
								<option>1961</option>
								<option>1960</option>
								<option>1959</option>
								<option>1958</option>
								<option>1957</option>
								<option>1956</option>
								<option>1955</option>
								<option>1954</option>
								<option>1953</option>
								<option>1952</option>
								<option>1951</option>
								<option>1950</option>
								<option>1949</option>
								<option>1948</option>
								<option>1947</option>
								<option>1946</option>
								<option>1945</option>
								<option>1944</option>
								<option>1943</option>
								<option>1942</option>
								<option>1941</option>
								<option>1940</option>
							</select>
							<a href="#" style="padding-left: 2px; display: inline;">Why do we need this?</a>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-2 col-sm-10">
							<button type="submit" class="btn btn-default">
								Submit
							</button>
						</div>
					</div>
				</form>
			</div>
		</div>
		<!-- /.main -->

	</body>

</html>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="dist/js/bootstrap.min.js"></script>
<script src="dist/js/docs.min.js"></script>
