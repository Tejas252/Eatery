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
    <div class="container-fluid form">
        <div class="row">
          <div class="col">
            <br><br><br><br><br><br><br><br><br>
          </div>
        </div>
        <div class="row">
            <div class="col"></div>
            <div class="col-md-4 col-sm-6 col-xs-10">
                <form class="py-4 pl-4 pr-4" target="_self" action="../php/validation.php" method="post">
                    <h1 class="mb-4">Sign up</h1>
                    <div class="box">
                      <div class="mb-3">
                        <label for="exampleInputEmail1" class="form-label">Email</label>
                        <input name="Email" type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="xyz@gmail.com" required>
                        <!-- <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div> -->
                      </div>
                      <div class="mb-3">
                          <label for="exampleInputEmail1" class="form-label">Phone No</label>
                          <input name="phone" type="number" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" required>
                          <!-- <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div> -->
                        </div>
                    </div>
                    <!-- <div class="box">
                      <div class="mb-3">
                          <label for="exampleInputEmail1" class="form-label">Name</label>
                          <input name="name" type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Your Name" required>
                        </div>
                        <div class="mb-3">
                          <label for="exampleInputEmail1" class="form-label">Phone No</label>
                          <input name="phone" type="number" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" required>
                        </div>
                      </div> -->
                    <div class="box">
                      <div class="mb-3">
                          <label for="exampleInputEmail1" class="form-label">Password</label>
                          <input name="password" type="password" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" required>
                          <!-- <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div> -->
                        </div>
                        
                      <div class="mb-3">
                        <label for="exampleInputPassword1" class="form-label">Confirm Password</label>
                        <input type="password" name="cpassword" class="form-control" id="exampleInputPassword1" required>
                        <!-- <p class="sm-text"><a href="forget.html">Forget password?</a></p> -->
                      </div>
                    </div>                 
                    <!-- <div class="mb-3 form-check">
                      <input type="checkbox" name="terms" class="form-check-input" id="exampleCheck1">
                      <label class="form-check-label" for="exampleCheck1">Accept Terms&Condition</label>
                    </div> -->
                    <button type="submit" class="btn btn-primary " name="forgot">Change Password</button>
                    <!-- <p class="sm-text mt-2">Don't have an account?<a href="signup.html"> Sign Up</a></p> -->
                  </form>
            </div>
            <div class="col"></div>
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
