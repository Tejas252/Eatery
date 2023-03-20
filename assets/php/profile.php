<?php
session_start();
// include('data_insert_take.php');
// include('assets/php.config.php');

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="Start your development with FoodHut landing page.">
  <meta name="author" content="Devcrud">
  <title>Eatery : Flavor For Royalty</title>
  <link rel="icon" href="../imgs/logo3.png">

  <!-- font icons -->
  <link rel="stylesheet" href="../vendors/themify-icons/css/themify-icons.css">

  <link rel="stylesheet" href="../vendors/animate/animate.css">

  <!-- Bootstrap + FoodHut main styles -->
  <link rel="stylesheet" href="../css/foodhut.css">
  <link rel="stylesheet" href="../css/menu.css">
  <link rel="stylesheet" href="../css/qty.css">
  <link rel="stylesheet" href="../css/login.css">
<style>
   .te{
    background: transparent;
    border: none;
   }
</style>
  <!-- <link rel="stylesheet" href="cart.css"> -->
</head>

<body data-spy="scroll" data-target=".navbar" data-offset="40" id="home">
      <!-- Navbar -->
      <nav class="custom-navbar navbar navbar-expand-lg navbar-dark fixed-top" data-spy="affix" data-offset-top="10">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav">
        <li class="nav-item">      
          <input type="button" value="Home"  class="nav-link te"  href="" onclick="home(this.name)" name="../../index.php#Home">
        </li>
        <li class="nav-item">
        <input type="button" value="Book-table"  class="nav-link te"  href="" onclick="home(this.name)" name="../../index.php#book-table">
        </li>
        <li class="nav-item">
        <input type="button" value="Menu"  class="nav-link te"  href="" onclick="home(this.name)" name="../../index.php#Menu">

        </li>
        <li class="nav-item">
          <a class="nav-link" href="#About">About</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?php if(isset($_SESSION['login'])){echo'../php/logout.php';}else{echo '../../login.php';} ?>"><?php if(isset($_SESSION['login'])){echo'Log-out';}else{echo 'Login';} ?></a>
        </li>
      </ul>
      <a class="navbar-brand m-auto" href="#">
        <img src="../imgs/logo3.png" class="brand-img" alt="" onclick="home(this.name)" name="../../index.php#Home">
        <span class="brand-txt">Eatery</span>
      </a>
      <ul class="navbar-nav">
       
        <li class="nav-item cart-btn">
          <a href="../../cart.php" class="btn btn-primary ml-xl-4 cart-btn"><img src="../imgs/cart.png" id="cart" alt=""srcset=""></a>
          <a href="<?php if(isset($_SESSION['login'])){echo'../php/profile.php';}else{echo'../php/error.php';} ?>" class="btn btn-primary ml-xl-4 cart-btn"><img src="../imgs/user.png" id="cart" alt=""></a>
        </li>
      </ul>
    </div>
    </nav>


  <!-- header -->

  <!-- <header id="home" class="header">
    <div class="overlay text-white text-center" id=>
      <h1 class="display-2 font-weight-bold my-3">Eatery</h1>
      <h2 class="display-4 mb-5"> Flavors for Royalty </h2>
      <a class="btn btn-lg btn-primary" href="#Menu">View Our Menu</a>
    </div>
  </header> -->

  <!-- <header id="home" class="header"> -->
    <!-- <div class="overlay text-white text-center" id=> -->

    <!-- header -->
    <header id="home" class="header">
    <div class="overlay text-white text-center" id=>
    <div class="container rounded mt-5 mb-5" >
 <!-- <button class="btn profile-button ml-5 my-3" type="button"><a class="go_back" href="../../index.php#Home"> Go Back </a></button> -->
    
    <div class="row ">
        <div class="col-md-3 ">
            <div class="d-flex flex-column align-items-center text-center p-3 py-5"><img class="rounded-circle mt-5" width="150px" src="../imgs/prof.jpg"><span class="font-weight-bold  d " ><?php echo "ID:".$_SESSION['id']; ?></span></div>
        </div>
        <div class="col-md-5 ">
            <div class="p-3 py-5">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="text-right">Profile Details</h4>
                </div>
                <!-- <div class="row mt-2">
                    <div class="col-md-6"><label class="labels">Name</label><input type="text" class="form-control" placeholder="first name" value=""></div>
                    <div class="col-md-6"><label class="labels">Surname</label><input type="text" class="form-control" value="" placeholder="surname"></div>
                </div> -->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <label class="labels">Username</label>
                        <input type="text" class="form-control" placeholder="username" value="<?php if(isset($_SESSION['username'])) { echo $_SESSION['username'];}else{echo 'Login';} ?>" readonly>
                    </div>
                    <div class="col-md-12">
                        <label class="labels">Name</label>
                        <input type="text" class="form-control" placeholder="name" value="<?php if(isset($_SESSION['name'])) { echo $_SESSION['name'];}else{echo 'Login';} ?>" readonly>
                    </div>
                    <div class="col-md-12">
                        <label class="labels">Mobile Number</label>
                        <input type="text" class="form-control" placeholder="enter phone number" value="<?php if(isset($_SESSION['phone'])) { echo $_SESSION['phone'];}else{echo 'Login';} ?>" readonly>
                    </div>
                        <!-- <div class="col-md-12"><label class="labels">Address Line 1</label><input type="text" class="form-control" placeholder="enter address line 1" value=""></div> -->
                        <!-- <div class="col-md-12"><label class="labels">Address Line 2</label><input type="text" class="form-control" placeholder="enter address line 2" value=""></div> -->
                        <!-- <div class="col-md-12"><label class="labels">Postcode</label><input type="text" class="form-control" placeholder="enter address line 2" value=""></div> -->
                        <!-- <div class="col-md-12"><label class="labels">State</label><input type="text" class="form-control" placeholder="enter address line 2" value=""></div> -->
                        <!-- <div class="col-md-12"><label class="labels">Area</label><input type="text" class="form-control" placeholder="enter address line 2" value=""></div> -->
                    <div class="col-md-12">
                        <label class="labels">Email ID</label>
                        <input type="text" class="form-control" placeholder="enter email id" value="<?php if(isset($_SESSION['email'])) { echo $_SESSION['email'];}else{echo 'Login';} ?>"readonly>
                    </div>
                        <!-- <div class="col-md-12"><label class="labels">Education</label><input type="text" class="form-control" placeholder="education" value=""></div> -->
                </div>
                <!-- <div class="row mt-3">
                    <div class="col-md-6"><label class="labels">Country</label><input type="text" class="form-control" placeholder="country" value=""></div>
                    <div class="col-md-6"><label class="labels">State/Region</label><input type="text" class="form-control" value="" placeholder="state"></div>
                </div> -->
                <!-- <div class="mt-5 text-center">
                    <button class="btn profile-button mr-5" type="button">Save Profile</button>
                    <button class="btn profile-button ml-5" type="button">Edit Profile</button>

                </div> -->
                
            </div>
        </div>
        <!-- <div class="col-md-4">
             <div class="p-3 py-5">
                <div class="d-flex justify-content-between align-items-center experience"><span>Edit Experience</span><span class="border px-3 p-1 add-experience"><i class="fa fa-plus"></i>&nbsp;Experience</span></div><br>
                <div class="col-md-12"><label class="labels">Experience in Designing</label><input type="text" class="form-control" placeholder="experience" value=""></div> <br>
                <div class="col-md-12"><label class="labels">Additional Details</label><input type="text" class="form-control" placeholder="additional details" value=""></div>
            </div> -->
        <!-- </div> --> 
    <!-- </div> -->
</div>
</div>
</div>
      
    </div>
  </header>
  <div class="container-fluid bg-dark text-light has-height-md middle-items border-top text-center wow fadeIn" id="About">
    <div class="row">
      <div class="col-sm-4">
        <h3>EMAIL US</h3>
        <P class="text-muted">info@website.com</P>
      </div>
      <div class="col-sm-4">
        <h3>CALL US</h3>
        <P class="text-muted">(123) 456-7890</P>
      </div>
      <div class="col-sm-4">
        <h3>FIND US</h3>
        <P class="text-muted">12345 Fake ST NoWhere AB Country</P>
      </div>
    </div>
  </div>

  <!-- <script src="../js/function.js">
   
  </script> -->
  <script src="../js/function.js"></script>
    <!-- </div> -->
  <!-- </header> -->
  <!-- end of page footer -->

	<!-- core  -->
  <script src="../vendors/jquery/jquery-3.4.1.js"></script>
    <script src="../vendors/bootstrap/bootstrap.bundle.js"></script>

    <!-- bootstrap affix -->
    <script src="../vendors/bootstrap/bootstrap.affix.js"></script>

    <!-- wow.js -->
    <script src="../vendors/wow/wow.js"></script>
    
    <!-- google maps -->
    <!-- <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCtme10pzgKSPeJVJrG1O3tjR6lk98o4w8&callback=initMap"></script> -->

    <!-- FoodHut js -->
    <script src="../js/foodhut.js"></script>
    </body>
</html>
