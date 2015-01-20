<!-- Connect to Database -->
<?php
include 'dist/config.php';
// include 'dist/opendb.php';
session_start();
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
              <a class="navbar-brand" href="#">Ticket Hawk</a>
            </div>
            <div id="navbar" class="navbar-collapse collapse">
              <ul class="nav navbar-nav">
                <li class="active"><a href="homepage.php">Home</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="getContactUsForm.php">Contact</a></li>
                <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Dropdown <span class="caret"></span></a>
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
                <form class="navbar-form navbar-right" method="post" action="">
                    <div class="form-group">
                        <input type="email" name="email" placeholder="Email" class="form-control">
                    </div>
                    <div class="form-group">
                        <input type="password" name="pwd" placeholder="Password" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-success" name="submit">Sign in</button>
                    <label id="loginInfo" style="color: red; padding-left: 4px;">
                    	<?php
						if (isset($_SESSION['loginErr'])) {
							echo $_SESSION['loginErr'];
						}
					?></label>
                </form>
                     <?php
					if (isset($_POST['submit'])) {
						$_SESSION['email'] = $_POST['email'];
						$_SESSION['pwd'] = $_POST['pwd'];
						login();
					}
					?>
                    
            </div><!--/.nav-collapse -->            
          </div>
        </nav>
        
        <!-- Carousel
    ================================================== -->
    <div id="myCarousel" class="carousel slide" data-ride="carousel">
      <!-- Indicators -->
      <ol class="carousel-indicators">
        <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
        <li data-target="#myCarousel" data-slide-to="1"></li>
        <li data-target="#myCarousel" data-slide-to="2"></li>
      </ol>
      <div class="carousel-inner" role="listbox">
        <div class="item active">
          <img src="http://p1.pichost.me/i/59/1835974.jpg" alt="First slide">
          <div class="container">
            <div class="carousel-caption">
              <h1>All The Tickets You Need!</h1>
              <p>On Your Favorite Site.</p>
              <p><a class="btn btn-lg btn-primary" href="sign_up.php" role="button">Sign up today</a></p>
            </div>
          </div>
        </div>
        <div class="item">
          <img src="http://www.foxsports.com/content/dam/fsdigital/fscom/nfl/images/2014/01/14/011414-NFL-Seattle-Seahawks-Fans-HF-PI.jpg" alt="Second slide">
          <div class="container">
            <div class="carousel-caption">
              <h1>Save Money!</h1>
              <p>We offer special deals to all Ticket Hawk Members.</p>
              <p> The best seats for a lower price</p>
              <h3>Ticket Hawk</h3>
              <p><a class="btn btn-lg btn-primary" href="sign_up.php" role="button">Learn more</a></p>
            </div>
          </div>
        </div>
        <div class="item">
          <img src="http://www.hdwallpapersinn.com/wp-content/uploads/2014/08/2013-nascar-wallpaper-hd.jpg" alt="Third slide">
          <div class="container">
            <div class="carousel-caption">
              <h1>One more for good measure.</h1>
              <p>Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.</p>
              <p><a class="btn btn-lg btn-primary" href="#" role="button">Browse gallery</a></p>
            </div>
          </div>
        </div>
        
                <div class="item">
          <img src="http://1.bp.blogspot.com/-CJoL1b3qBR8/Ubw5sbzHU4I/AAAAAAAAYjE/-jW-Ch3inyU/s1600/Disney+World+HD+Wallpapers21.jpg" alt="Third slide">
          <div class="container">
            <div class="carousel-caption">
              <h1>All of your favorite palaces.</h1>
              <p>Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.</p>
              <p><a class="btn btn-lg btn-primary" href="#" role="button">Browse gallery</a></p>
            </div>
          </div>
        </div>
        
                <div class="item">
          <img src="http://i2.cdnds.net/14/37/618x411/otr_paris_02.jpg" alt="fifth slide">
          <div class="container">
            <div class="carousel-caption">
              <h1>Events you cant miss.</h1>
              <p>Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.</p>
              <p><a class="btn btn-lg btn-primary" href="#" role="button">Browse gallery</a></p>
            </div>
          </div>
        </div>
        
                <div class="item">
          <img src="http://cdn.caughtoffside.com/wp-content/uploads/2014/03/Lionel-Messi-Barcelona3.jpg" alt="sixth slide">
          <div class="container">
            <div class="carousel-caption">
              <h1>Sign up and receive the best deal out.</h1>
              <p>Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.</p>
              <p><a class="btn btn-lg btn-primary" href="#" role="button">Browse gallery</a></p>
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
    <div class="container">
  <div class="jumbotron" style="text-align: center;">
    <h1>New Movie Releases</h1>
    <p>Preview the hotest releases thats got everyone talking.</p> 
  </div>
  <div class="row">
  	<p></p>
    <div class="col-sm-4">
      <h3>Game of Thrones</h3>
      <iframe width="330" height="215" src="//www.youtube.com/embed/uvX4k_3Cmvs?rel=0" frameborder="0" allowfullscreen></iframe>
      <p>George R.R. Martin's best-selling book series "A Song of Ice and Fire" is brought to the screen as HBO sinks its considerable storytelling teeth into the medieval fantasy epic<a class="btn btn-lg btn-primary" href="#" role="button">Get Tickets</a></p>
    </div>
    <div class="col-sm-4">
      <h3>Get Hard</h3>
      <iframe width="330" height="215" src="//www.youtube.com/embed/sge-AzPU4LU?rel=0" frameborder="0" allowfullscreen></iframe>
      <p>The prison-bound manager (Will Ferrell) of a hedge fund asks a black businessman (Kevin Hart) -- who has never been to jail -- to prepare him for life behind bars<a class="btn btn-lg btn-primary" href="#" role="button">Get Tickets</a></p>
    </div>
    <div class="col-sm-4">
      <h3>American Sinper</h3> 
      <iframe width="330" height="215" src="//www.youtube.com/embed/5bP1f_1o-zo?rel=0" frameborder="0" allowfullscreen></iframe>
      <p>U.S. Navy SEAL Chris Kyle (Bradley Cooper) takes his sole mission -- protect his comrades -- to heart and becomes one of the most lethal snipers in American history.<a class="btn btn-lg btn-primary" href="#" role="button">Get Tickets</a></p>
    </div>
  </div>
  <a>See More</a>
  
    <div class="jumbotron" style="text-align: center;">
    <h1>Events you cant miss.</h1>
    <p>Featured event you must see!</p> 
  </div>
</div>

    </body>

</html>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="dist/js/bootstrap.min.js"></script>
<script src="dist/js/docs.min.js"></script>
