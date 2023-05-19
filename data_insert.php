<?php session_start();  ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data insert</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN"
        crossorigin="anonymous"></script>
    <link rel="stylesheet" href="assets/css/foodhut.css">
    <link rel="stylesheet" href="assets/css/login.css">
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
   .form{
    margin-top:30vh;
   }
</style>

</head>

<body>
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

    <div class="container-fluid form">
     
        <div class="row">
            <div class="col"></div>
                <div class="col-md-6 col-sm-6 col-xs-10">
                    <form class="py-4 pl-4 pr-4" target="_self" action="data_insert_take.php" method="post"
                        enctype="multipart/form-data">
                        
                        <div class="mb-3">
                            <label for="producutnumber" class="form-label">producut number</label>
                            <input name='product_no' type="number" class="form-control" id="exampleInputEmail1"
                                aria-describedby="emailHelp" placeholder="producut number" required>
                            <!-- <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div> -->
                        </div>
                        <div class="mb-3">
                            <label for="producut_name" class="form-label">producut name</label>
                            <input name='product_name' type="text" class="form-control" id="exampleInputEmail1"
                                aria-describedby="emailHelp" placeholder="producut name" required>
                            <!-- <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div> -->
                        </div>
 
                        <div class="mb-3">
                            <label for="product price" class="form-label">product price</label>
                            <input name='product_price' type="number" class="form-control" id="exampleInputEmail1"
                                aria-describedby="emailHelp" placeholder="User123" required>
                            <!-- <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div> -->
                        </div>
                        <div class="mb-3">
                            <label for="product_type"  class="form-label">product type</label>
                            <select type="text" name="product_type" class="form-control" id="exampleInputEmail1"
                                aria-describedby="emailHelp" required>
                                <option value="pizza">Pizza</option>
                                <option value="Burger">Burger</option>
                                <option value="Chinease">Chinease</option>
                                <option value="Maxican">Maxican</option>
                            </select>
                            <!-- <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div> -->
                        </div>
                        <div class="mb-3">
                            <label for="product_qty" class="form-label">product qty</label>
                            <input type="number" name='product_qty' class="form-control" id="exampleInputPassword1" required>
                            <!-- <p class="sm-text"><a href="forget.html">Forget password?</a></p> -->
                        </div>
                        <div class="mb-3">
                            <label for="product_desc" class="form-label">product desc</label>
                            <input type="textarea" name='product_desc' class="form-control" id="exampleInputPassword1" required>
                            <!-- <p class="sm-text"><a href="forget.html">Forget password?</a></p> -->
                        </div>
                        <div class="mb-3">
                            <label for="product_img" class="form-label">product qty</label>
                            <input type="file" name='product_img' class="form-control" id="exampleInputPassword1" required>
                            <!-- <p class="sm-text"><a href="forget.html">Forget password?</a></p> -->
                        </div>

                        <button type="submit" class="btn btn-primary " name="submit">Add</button>

                    </form>
                </div>
            <div class="col"></div>
        </div>
    </div>
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