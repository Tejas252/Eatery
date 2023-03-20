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

  <!-- <link rel="stylesheet" href="cart.css"> -->
</head>

<body data-spy="scroll" data-target=".navbar" data-offset="40" id="home">

  <!-- Navbar -->
  <nav class="custom-navbar navbar navbar-expand-lg navbar-dark fixed-top " data-spy="affix" data-offset-top="10">
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
          <a class="nav-link" href="<?php if($_SESSION['login']){echo'assets/php/logout.php';}else{echo 'login.php';} ?>"><?php if($_SESSION['login']){echo'Log-out';}else{echo 'Login';} ?></a>
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
  <header id="home" class="header">
    <div class="overlay text-white text-center" id=>
      <h1 class="display-2 font-weight-bold my-3">Eatery</h1>
      <h2 class="display-4 mb-5"> Flavors for Royalty </h2>
      <a class="btn btn-lg btn-primary" href="#Menu">View Our Menu</a>
    </div>
  </header>

  <!-- book a table Section  -->

  <div class="container-fluid has-bg-overlay text-center text-light has-height-lg middle-items" id="book-table">
    <div class="">
      <h2 class="section-title mb-3">BOOK A TABLE</h2>
      <div class="row">
        <div class="col-md-3"></div>
        <div class="col-sm-6 col-md-6 col-xs-12 my-2">
          <form action="#book-table" method="post">
            <input type="number" value="<?php if(isset($_SESSION['tt'])){echo $_SESSION['tt']; } ?>" name="table_guests" onchange="check()" id="booktable" class="form-control form-control-lg custom-form-control"
            placeholder="NUMBER OF GUESTS" min="1" max="5" required>
            <button type="submit" onclick="clk()" href="#book-table" class="btn btn-lg btn-primary my-4" name="find" id="rounded-btn">FIND TABLE</button>
          </form> 
        </div>
        <div class="col-md-3"></div>
      </div>
      <script>

          function check() {
            let g = document.getElementById('booktable').value;
            if(g>5){
              alert('only 5 members');
              document.getElementById('booktable').value = 5;
              
            }
            
          }
          
        </script>
      <div class="row">
        <div class="col-md-3"></div>
        <div class="col-sm-6 col-md-6 col-xs-12 my-2">
          <form action="" method="post">
          <select type="number" name="table_no" id="table_nn" class="form-control form-control-lg custom-form-control"
            placeholder="TABLE NUMBER" disabled required>
             <?php 
              
                          // $size = <scri></scri>
                  if($_SERVER["REQUEST_METHOD"]=="POST"){
                    if(isset($_POST['find']) && isset($_POST['table_guests'])){
                      // echo"hello";
                      $_SESSION['tt'] = $_POST['table_guests'];
                      $g = $_POST['table_guests'];
                      $qrr = "select * from book_table where table_status = 'non' && table_size >= $g";
                      $resss = mysqli_query($conn,$qrr);
                      $cnt = mysqli_num_rows($resss);
                      if($cnt>0){
                        while($table = mysqli_fetch_assoc($resss)){
                          $no = $table['table_no'];
                          echo " <option name='table_no' class='btn-primary' value=.$no.>Table : $no</option>";
                          
                        }   
                      }else{
                        echo " <option class='btn-primary' disabled> Not Available </option>";
                      }
                     
                    }else{
                      echo " <option class='btn-primary' disabled > Enter Number of Guests </option>";
                    }
                  
                  }
                      

                          

                  ?> 
            
           
          </select><?php      if(isset($_SESSION['tt'])){
                                                echo "<script>
                                                let g1 = parseInt(".$_SESSION['tt'].");
                                                console.log(g1);
                                                if(g1>0){
                                                  document.getElementById('table_nn').disabled = false;
                                                }
                                                console.log(g1);
                                                </script>

                                                "; 
                                              }
                             
                              
                              ?>
        </div>
        <div class="col-md-3"></div>
      </div>
      <div class="row">
        <div class="col-md-3"></div>
        <div class="col-sm-6 col-md-6 col-xs-12 my-2">
                <h6 class="" style="color: #ff214f;">Note: First Input Number of Guests</h6>
          </div>
          <div class="col-md-3"></div>
      </div>

      <button name="book" class="btn btn-lg btn-primary mb-4" id="rounded-btn">BOOK TABLE</button>
      </form>
    </div>
  </div>



  
  <div class="d-flex flex-column">

    <!--  Menu Section  -->
    <div id="Menu" class="text-center bg-dark text-light has-height-md middle-items wow fadeIn">
    <h2 class="my-3">OUR MENU</h2>
  </div>
    <!-- qty-form -->

    <div class="container-fluid qty_form mt-2" id="qty_form">
      <div class="row">
        <div class="col"></div>
        <div class="col-md-3 col-sm-6 col-xs-10">
          <form class="py-4 pl-4 pr-4" target="_self" action="assets/php/qty_session.php" method="post">
            <h4 class="mb-4" id="form_prd_name" style="color: #ff214f"></h4>

            <div class="mb-3">
              <label for="exampleInputEmail1" class="form-label">Product id</label>
              <input name="product_no" type="number" class="form-control" id="product_no" aria-describedby="emailHelp"
                readonly>

            </div>
            <div class="mb-3">
              <label for="" class="form-label">Quantity</label>
              <input name="qty" type="number" class="form-control" id="exampleInputPassword1" min="1" max="10" required>

            </div>

            <button name = "submit" type="submit" class="btn btn-primary " onclick="submit()">submit</button>

          </form>
        </div>
        <div class="col"></div>
      </div>
    </div>
  
    <h2 class=" mb-3 ml-3" style="display:block;">pizza<button value="pizza" onclick="shw(this.value)" class="ml-3 btn btn-primary">🔻</button> </h5>
    
    <div id="pizza" class="prdlist">
      <?php
        include('data_insert_take.php');
        $qr2 = "select * from products where product_type = 'pizza'";
        $result = mysqli_query($conn, $qr2);
        if (mysqli_num_rows($result) > 0) {
          while ($image = mysqli_fetch_assoc($result)) {
            // echo '<div class="col-sm-6 col-lg-3 Menu-item wow fadeIn">';
            // echo '<a href="#menu" class="Menu-overlay">';
            // echo '<img src="assets/uploads/'.$image['product_img'].'" alt="template by DevCRID http://www.devcrud.com/" class="Menu-img">';
            // echo '<i class="Menu-icon ti-plus"></i>';
            // echo ' </a></div>';
        
            echo '<div class="prd">';
            echo '<img src="assets/uploads/' . $image['product_img'] . '" alt="hey" class="ml-2 my-2">';
            echo '<div class="prd_det ml-2 my-2">';
            echo '<h5 id="prd_name"><b>' . $image['product_name'] . '</b></h5>';
            echo '<p class="small_text">' . $image['product_type'] . '</p>';
            echo '<p class="small_text">' . $image['product_desc'] . '</p>';
            echo '<h6><b>Price : ' . $image['product_price'] . '</b></h6>';
            echo '</div>';
            echo '<div class="add ml-1 mr-2">';
            echo '<a class = "prod" href="#menu" onclick="addto(this.name)" name="' . $image['product_no'] . '_' . $image['product_name'] . '">+</a>';
            echo '</div></div>';
            // unset($_POST['submit']);
          }
        }else{
          echo "<div class='ml-5'><p>It will Add soon</p></div>";
        } 
        ?>
    </div>
    <hr>
    <h2 class=" mb-3 ml-3" style="display:block;">Burgers<button value="burger" onclick="shw(this.value)" class="ml-3 btn btn-primary">🔻</button> </h5>
    <div id="burger" class="prdlist hide">
      <?php
        include('data_insert_take.php');
        $qr2 = "select * from products where product_type = 'Burger'";
        $result = mysqli_query($conn, $qr2);
        if (mysqli_num_rows($result) > 0) {
          while ($image = mysqli_fetch_assoc($result)) {
            // echo '<div class="col-sm-6 col-lg-3 Menu-item wow fadeIn">';
            // echo '<a href="#menu" class="Menu-overlay">';
            // echo '<img src="assets/uploads/'.$image['product_img'].'" alt="template by DevCRID http://www.devcrud.com/" class="Menu-img">';
            // echo '<i class="Menu-icon ti-plus"></i>';
            // echo ' </a></div>';
        
            echo '<div class="prd">';
            echo '<img src="assets/uploads/' . $image['product_img'] . '" alt="hey" class="ml-2 my-2">';
            echo '<div class="prd_det ml-2 my-2">';
            echo '<h5 id="prd_name"><b>' . $image['product_name'] . '</b></h5>';
            echo '<p class="small_text">' . $image['product_type'] . '</p>';
            echo '<p class="small_text">' . $image['product_desc'] . '</p>';
            echo '<h6><b>Price : ' . $image['product_price'] . '</b></h6>';
            echo '</div>';
            echo '<div class="add ml-1 mr-2">';
            echo '<a class = "prod" href="#menu" onclick="addto(this.name)" name="' . $image['product_no'] . '_' . $image['product_name'] . '">+</a>';
            echo '</div></div>';
            // unset($_POST['submit']);
          }
        }else{
          echo "<div class='ml-5'><p>It will Add soon</p></div>";
        }
        ?>
    </div>
        <hr>
        <script>
          var prd = [];
          function addto(d) {

            var arr = d.split("_");
            console.log(arr);
            document.getElementById('product_no').value = arr[0];
            // let name = document.getElementById('prd_name').value;
            document.getElementById('form_prd_name').innerText = arr[1]; 
            document.getElementById('qty_form').classList.add('show');
            window.location.href = "index.php#Menu";
            // if(prd.includes(d)){

            // }else{
            //   prd.push(d);
            //   console.log(prd);
            // }
          }
          function submit(){
            document.getElementById('qty_form').classList.remove('show'); 
          }

          function shw(i) {
            document.getElementById(i).classList.toggle('hide');
            // document.getElementById(i).
          }
        </script>

  </div>
  
  <!--  About Section  -->
  <div id="about" class="container-fluid wow fadeIn" id="about" data-wow-duration="1.5s">
    <div class="row">
      <!-- <div class="col-lg-6 has-img-bg"></div> -->
      <div class="col">
        <div class="row justify-content-center">
          <div class="col-sm-8 py-5 my-5">
            <!-- <div class="col-11"> -->
            <h2 class="mb-4">About Us</h2>
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consectetur, quisquam accusantium nostrum modi,
              nemo, officia veritatis ipsum facere maxime assumenda voluptatum enim! Labore maiores placeat impedit,
              vero sed est voluptas!Lorem ipsum dolor sit amet, consectetur adipisicing elit. Expedita alias dicta?</p>

          </div>
        </div>
      </div>
    </div>
  </div>


  
  <!-- page footer  -->
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


  <!-- core  -->
  <script src="assets/vendors/jquery/jquery-3.4.1.js"></scrip >
      <script src="assets/vendors/bootstrap/bootstrap.bundle.js"></script>

  <!-- bootstrap affix -->
  <script src="assets/vendors/bootstrap/bootstrap.affix.js"></script>

  <!-- wow.js -->
  <script src="assets/vendors/wow/wow.js"></script>

  // <!-- google maps -->
  // < async defer
  //   src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCtme10pzgKSPeJVJrG1O3tjR6lk98o4w8&callback=initMap">

  <!-- FoodHut js -->
  <script src="assets/js/foodhut.js"></script>




</body>

</html>




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
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
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
          <a class="nav-link" href="<?php if($_SESSION['login']){echo'assets/php/logout.php';}else{echo 'login.php';} ?>"><?php if($_SESSION['login']){echo'Log-out';}else{echo 'Login';} ?></a>
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

    <!-- header -->
    <header id="home" class="header">
    <div class="overlay text-white text-center" id=>
    <div class="container-fluid form" >
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
                <form class="py-4 pl-4 pr-4"  target="_self" action="assets/php/verification.php" method="post">
                    <h1 class="mb-4">Login</h1>
                    <div class="mb-3">
                      <label for="exampleInputEmail1" class="form-label">Email</label>
                      <input name="email" type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="xyz@gmail.com">
                      <!-- <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div> -->
                    </div>
                    <div class="mb-3">
                      <label for="exampleInputPassword1" class="form-label">Password</label>
                      <input name="password" type="password" class="form-control" id="exampleInputPassword1">
                      <p class="sm-text"><a href="forget.html">Forget password?</a></p>
                    </div>
                    <!-- <div class="mb-3 form-check">
                      <input type="checkbox" class="form-check-input" id="exampleCheck1">
                      <label class="form-check-label" for="exampleCheck1">Check me out</label>
                    </div> -->
                    <button type="submit" class="btn btn1 btn-primary " name="login">Login</button>
                    <p class="sm-text mt-2">Don't have an account?<a href="signup.php"> Sign Up</a></p>
                  </form>
            </div>
            <div class="col"></div>
        </div>
      </div>
    </div>
  </header>
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




// profile

 <?php
include('config.php'); 
session_start(); 
?>

 <html>
    <head>
        <style>
           .text-right {
            color: #f8f9fa;
            }
            body {
    background: rgb(99, 39, 120)
}


 


/* .back:hover {
    color: #682773;
    cursor: pointer
} */

.labels ,.d,.text-black-50 .d {
    font-size: 20px;
    color: #f8f9fa;
    
}


body{
    /* background: rgba(0,0,0,0.5) url('../photo/bg_2.jpg') no-repeat; */
    background-blend-mode: darken !important;   
    background: rgba(0,0,0,0.7) url('../imgs/main.jpg') no-repeat !important;
   opacity: 10px  !important;
}
.rounded{
    border: 2px solid white;
    border-radius: 10px;
}
/* .btn{
    background-color: #f8f9fa ;
    
} */
.btn.profile-button{
    color: #fff;
    background-color: #ff214f;
    border-color: #ff214f;
}
.btn.profile-button:hover{
    background-color: #ff214f;
    color: #fff;
}
.form-control{
    background-color: #495057 !important;
    /* background-clip: padding-box; */
    border: 1px solid #343a40 !important;
    border-radius: 0.2rem !important;
    color: white!important;
}
::placeholder{
    color: #fff !important  ;
}
.go_back{
    text-decoration: none;
    color:#fff;
}

        </style>
         <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
    <link rel="stylesheet">
    <link rel="stylesheet" >
    

    </head>
 <body>
 
 <div class="container rounded mt-5 mb-5" >
 <button class="btn profile-button ml-5 my-3" type="button"><a class="go_back" href="../../index.php#Home"> Go Back </a></button>
    
    <div class="row ">
        <div class="col-md-3 border-right">
            <div class="d-flex flex-column align-items-center text-center p-3 py-5"><img class="rounded-circle mt-5" width="150px" src="https://st3.depositphotos.com/15648834/17930/v/600/depositphotos_179308454-stock-illustration-unknown-person-silhouette-glasses-profile.jpg"><span class="font-weight-bold  d " ><?php echo "ID:".$_SESSION['id']; ?></span></div>
        </div>
        <div class="col-md-5 border-right">
            <div class="p-3 py-5">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="text-right">Profile Settings</h4>
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
                <div class="mt-5 text-center">
                    <button class="btn profile-button mr-5" type="button">Save Profile</button>
                    <button class="btn profile-button ml-5" type="button">Edit Profile</button>

                </div>
                
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

 </body>
 </html>



