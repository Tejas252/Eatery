<?php
session_start();
include('data_insert_take.php');
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
  <link rel="icon" href="assets/imgs/logo3.png">

  <!-- font icons -->
  <link rel="stylesheet" href="assets/vendors/themify-icons/css/themify-icons.css">

  <link rel="stylesheet" href="assets/vendors/animate/animate.css">

  <!-- Bootstrap + FoodHut main styles -->
  <link rel="stylesheet" href="assets/css/foodhut.css">
  <link rel="stylesheet" href="assets/css/menu.css">
  <link rel="stylesheet" href="assets/css/qty.css">
  <link rel="stylesheet" href="assets/css/login.css">
<style>
   
</style>
  <!-- <link rel="stylesheet" href="cart.css"> -->
</head>

<body data-spy="scroll" data-target=".navbar" data-offset="40" id="home">
  <!-- Navbar -->
  <nav class="custom-navbar navbar navbar-expand-lg navbar-dark fixed-top" data-spy="affix" data-offset-top="10">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
      aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" href="#home">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#book-table">Book-Table</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#Menu">Menu</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#about">About</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?php if($_SESSION['login']){echo'assets/php/logout.php';}else{echo 'login.html';} ?>"><?php if($_SESSION['login']){echo'Log-out';}else{echo 'Login';} ?></a>
        </li>
      </ul>
      <a class="navbar-brand m-auto" href="#">
        <img src="assets/imgs/logo3.png" class="brand-img" alt="">
        <span class="brand-txt">Eatery</span>
      </a>
      <ul class="navbar-nav">
       
        <li class="nav-item cart-btn">
          <a href="cart.php" class="btn btn-primary ml-xl-4 cart-btn"><img src="assets/imgs/cart.png" id="cart" alt=""
              srcset=""></a>
          <a href="<?php if($_SESSION['login']){echo'assets/php/profile.php';}else{echo'assets/php/error.php';} ?>" class="btn btn-primary ml-xl-4 cart-btn"><img src="assets/imgs/user.png" id="cart" alt=""></a>
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
    <div class="container-fluid form ">
        <div class="row">
            <div class="col"></div>
            <div class="col">
                    <!-- <img  src="assets/imgs/logo3.png" alt="" class="logo cart-btn">   -->
            </div>
            <div class="col"></div>
        </div>
        <div class="row">
            <div class="col"></div>
            <div class="col-md-3 col-sm-6 col-xs-10">
                <form class="py-4 pl-4 pr-4" target="_self" action="assets/php/validation.php" method="post">
                    <h1 class="mb-4">Sign up</h1>
                    <div class="mb-3">
                      <label for="exampleInputEmail1" class="form-label">Email</label>
                      <input name="Email" type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="xyz@gmail.com">
                      <!-- <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div> -->
                    </div>
                    <div class="mb-3">
                        <label for="exampleInputEmail1" class="form-label">Username</label>
                        <input name="username" type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="User123">
                        <!-- <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div> -->
                      </div>
                      <div class="mb-3">
                        <label for="exampleInputEmail1" class="form-label">Name</label>
                        <input name="name" type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Your Name">
                        <!-- <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div> -->
                      </div>
                      <div class="mb-3">
                        <label for="exampleInputEmail1" class="form-label">Phone No</label>
                        <input name="phone" type="number" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp">
                        <!-- <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div> -->
                      </div>
                      <div class="mb-3">
                        <label for="exampleInputEmail1" class="form-label">Password</label>
                        <input name="password" type="password" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp">
                        <!-- <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div> -->
                      </div>
                      
                    <div class="mb-3">
                      <label for="exampleInputPassword1" class="form-label">Confirm Password</label>
                      <input type="password" class="form-control" id="exampleInputPassword1">
                      <!-- <p class="sm-text"><a href="forget.html">Forget password?</a></p> -->
                    </div>
                    <div class="mb-3 form-check">
                      <input type="checkbox" class="form-check-input" id="exampleCheck1">
                      <label class="form-check-label" for="exampleCheck1">Accept Terms&Condition</label>
                    </div>
                    <button type="submit" class="btn btn-primary ">Sign Up</button>
                    <!-- <p class="sm-text mt-2">Don't have an account?<a href="signup.html"> Sign Up</a></p> -->
                  </form>
            </div>
            <div class="col"></div>
        </div>
      </div>
    <!-- </div> -->
  <!-- </header> -->
 
    </body>
</html>






