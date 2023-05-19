<?php
include 'assets/php/config.php';
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
  <link rel="icon" href="assets/imgs/logo3.png">

  <!-- font icons -->
  <link rel="stylesheet" href="assets/vendors/themify-icons/css/themify-icons.css">

  <link rel="stylesheet" href="assets/vendors/animate/animate.css">

  <!-- Bootstrap + FoodHut main styles -->
  <link rel="stylesheet" href="assets/css/foodhut.css">
  <!-- <link rel="stylesheet" href="assets/css/menu.css"> -->
  <!-- <link rel="stylesheet" href="assets/css/qty.css">
  <link rel="stylesheet" href="assets/css/login.css">
  <link rel="stylesheet" href="assets/css/signup.css"> -->
  <link rel="stylesheet" href="assets/css/cart.css">

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
          <input type="button" value="Add Items"  class="nav-link te"  href="" onclick="home(this.name)" name="data_insert.php">
        </li>
        <li class="nav-item">
        <input type="button" value="Accept"  class="nav-link te"  href="" onclick="home(this.name)" name="admin.php">
        </li>
        <li class="nav-item">
        <input type="button" value="Deliver"  class="nav-link te"  href="" onclick="home(this.name)" name="admin_ord.php">

        </li>
        <li class="nav-item">
        <input type="button" value="History"  class="nav-link te"  href="" onclick="home(this.name)" name="admin_history.php">
        </li>
        <li class="nav-item">
        <input type="button" value="Products"  class="nav-link te"  href="" onclick="home(this.name)" name="products.php">
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?php if(isset($_SESSION['login'])){echo'assets/php/logout.php';}else{echo 'login.php';} ?>"><?php if(isset($_SESSION['login'])){echo'Log-out';}else{echo 'Login';} ?></a>
        </li>
      </ul>
      <a class="navbar-brand m-auto" href="#">
      <img src="assets/imgs/logo3.png" class="brand-img" alt="" onclick="home(this.name)" name="index.php#Home">
        <span class="brand-txt">Eatery</span>
      </a>
      <ul class="navbar-nav">
       
        <li class="nav-item cart-btn">
          <a href="cart.php" class="btn btn-primary ml-xl-4 cart-btn"><img src="assets/imgs/cart.png" id="cart" alt=""
              srcset=""></a>
          <a href="<?php if(isset($_SESSION['login'])){echo'assets/php/profile.php';}else{echo'assets/php/error.php';} ?>" class="btn btn-primary ml-xl-4 cart-btn"><img src="assets/imgs/user.png" id="cart" alt=""></a>
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
    <!-- <header id="home" class="header">
    <div class="overlay text-white text-center" id=> -->
      <br><br><br><br><br><br><br><br>
      
    <header class="container-fluid">
      <div class="row" id="cart-container">
        <!-- <div class="col-md-3 cart-left"></div> -->
        <div class="col-sm-6 col-md-12 col-xs-12 my-2 cart-main cart-middle">

                    <!-- crt header -->
                  
                  <div class="container-fluid text-c" id="cart-head">
                    <div class="row d-flex cart-head">
                        <div class="col-2">
                            <h5>customer Id</h5>
                        </div>
                        <div class="col-3">
                            <h5>Product name</h5>
                        </div>
                        <div class="col-1">
                            <h5>Quantity</h5>
                        </div>
                        <div class="col-1">
                            <h5>Table No</h5>
                        </div>
                        <div class="col-3">
                            <h5>Description</h5>
                        </div>
                        <div class="col-2">
                            <h5>status</h5>
                        </div>
                        
                    </div>
                  </div> 
                   
                   <!-- cart-product  -->

                <div class="container-fluid text-c" id="cart-head">
                    
                <?php 
                    $qr = "select * from orders where status = 'ordered'";
                    $res = mysqli_query($conn,$qr);
                    $nid = 1;
                    while($orders = mysqli_fetch_assoc($res)) {
                        $id = $orders['product_id'];
                        $qr1 = "select product_name from products where product_id = $id";
                        $nam = mysqli_query($conn,$qr1);
                        $name = mysqli_fetch_array($nam);
                        ?>
                        <div class="row d-flex cart-head">
                        
                        <div class="col-2">
                            <h5><?php echo $orders['customer_id']; ?></h5>
                            
                        </div>
                        <div class="col-3">
                            <h5><?php echo $name['product_name']; ?></h5>
                        </div>
                        <div class="col-1">
                            <h5><?php echo $orders['qty']; ?></h5>
                        </div>
                        <div class="col-1">
                            <h5><?php echo $orders['table_no']; ?></h5>
                        </div>
                        <div class="col-3">
                            <h5><?php echo $orders['order_desc']; ?></h5>
                        </div>
                        
                <?php if($orders['customer_id'] == $nid){?>
                        <div class="col-2">
                        
                        </div>
                <?php  }else{ ?>
                        <div class="col-2">
                            <form action="assets/php/manage_order.php" method="post">
                                <select name="status" id="">
                                    <option name="status" value="accepted">Accepted</option>
                                    <option name="status" value="done">Done</option>
                                    <option name="status" value="Ordered">Ordered</option>
                                </select>
                                <input type="hidden" name="id" value="<?php echo $orders['customer_id']; ?>">
                                <input type="hidden" name="order_id" value="<?php echo $orders['order_id']; ?>">
                                <input type="hidden" name="table_no" value="<?php echo $orders['table_no']; ?>">
                                <!-- <input type="submit" value="✔" name="change"> -->
                                <button type="submit" name="change">✔</button>
                                <!-- <h5><?php // echo $orders['status']; ?></h5> -->
                            </form>
                        </div>
                <?php } ?>
                <?php $nid =  $orders['customer_id']; ?>
                        </div>
                <?php } ?>
                            
                    
                  </div> 
                  
                  
                  

                  
              
                  
                  <!-- cart-total  -->
                  <div class="row">
                    <div class="col-3"></div>
                    <div class="col-9">
                      
                            <div class="col-6">
                              
                             
                            </div>
                          </div>
                        </div>
                    </div>
                  </div>
        </div>
       
       
      </div>
      <?php// if(isset($_SESSION['ordered'])){?>
      
                              
                              
                              
      </form>
       <?php //}else{?>
                                <!-- <div class="container-fluid mt-3">
                                  <div class="row">
                                    <div class="col-12 text-center">


                                      <button  name="order" class="btn btn-primary w-25 mb-3">Ordered</button>
                                   </div>
                                  </div>
                                </div> -->

      <?php // } ?>                      
                              <!-- <div class="container-fluid mt-3">
                                  <div class="row">
                                    <div class="col-12 text-center">
                                      <h5><a href="index.php">Go Back</a></h5>
                                   </div>
                                  </div>
                              </div> -->
    </header>
 
      
    <!-- </div>
  </header> -->
  <div class="container-fluid bg-dark text-light has-height-md middle-items border-top text-center wow fadeIn">
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
    
    <!-- </div> -->
  <!-- </header> -->
  <!-- end of page footer -->

	<!-- core  -->
  <script src="assets/js/function.js"></script>
  <script src="assets/vendors/jquery/jquery-3.4.1.js"></script>
    <script src="assets/vendors/bootstrap/bootstrap.bundle.js"></script>

    <!-- bootstrap affix -->
    <script src="assets/vendors/bootstrap/bootstrap.affix.js"></script>

    <!-- wow.js -->
    <script src="assets/vendors/wow/wow.js"></script>
    
    <!-- google maps -->
    <!-- <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCtme10pzgKSPeJVJrG1O3tjR6lk98o4w8&callback=initMap"></script> -->

    <!-- FoodHut js -->
    <script src="assets/js/foodhut.js"></script>
    </body>
</html>






