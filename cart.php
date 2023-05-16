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
          <input type="button" value="Home"  class="nav-link te"  href="" onclick="home(this.name)" name="index.php#Home">
        </li>
        <li class="nav-item">
        <input type="button" value="Book-table"  class="nav-link te"  href="" onclick="home(this.name)" name="index.php#book-table">
        </li>
        <li class="nav-item">
        <input type="button" value="Menu"  class="nav-link te"  href="" onclick="home(this.name)" name="index.php#Menu">

        </li>
        <li class="nav-item">
          <a class="nav-link" href="#About">About</a>
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
  <form action="assets/php/manage_order.php" method="post">
    <header class="container-fluid">
      <div class="row" id="cart-container">
        <div class="col-md-3 cart-left"></div>
        <div class="col-sm-6 col-md-6 col-xs-12 my-2 cart-main cart-middle">

                    <!-- crt header -->
                  
                  <div class="container-fluid text-c" id="cart-head">
                    <div class="row d-flex cart-head">
                      <div class="col-4">
                        <h5>Product</h5>
                      </div>
                      <div class="col-4">
                        <h5>Quantity</h5>
                      </div>
                      <div class="col-4">
                        <h5>Price</h5>
                      </div>
                    </div>
                  </div> 
                   
                   <!-- cart-product  -->

                   <?php 
                          $servername = "localhost";
                          $username = "root";
                          $password = "";
                          $database = "eatery";
                        
                          // Create connection
                          $conn = new mysqli($servername, $username, $password, $database);
                        
                          // Check connection
                          if ($conn->connect_error) {
                            die("Connection failed: " . $conn->connect_error);
                          }
                    if(isset($_SESSION['cart'])){
                      $arcol = count($_SESSION['cart']);
                      if($arcol > 0){
                        // echo "<h5 class='text-center'>Add</h5>";
                        // echo $arcol;
                        foreach($_SESSION['cart'] as $key => $value){

                          $pro_no = $value['no'];
                        
                          $qr = "select * from products  where product_no = '$pro_no'";
                          $result = mysqli_query($conn,$qr);
                          $prd = mysqli_fetch_assoc($result);
                          $product_name = $prd['product_name'];
                          $product_img = $prd['product_img'];
                          $product_qty = $value['qty'];
                          $product_price = $prd['product_price']; 
                          $q = 0;


                        // if(isset($_POST["submit"])){
                        //   $_SESSION['product_no'] = $_POST['product_no'];
                        //   $_SESSION['qty'] = $_POST['qty'];
                        //   // echo $_POST['product_no'];
                        //   // echo $_SESSION['qty'];
                        //   // header("location:../../index.php#Menu");
                        // } 
                        

                  ?>
                  
                  <div class="container-fluid">
                    <div class="row d-flex cart-head">
                      <div class="col-4 cart-prd">
                        <img class="prd-img" src="assets/uploads/<?php echo $product_img?>" alt="hey">
                        <div class="prd-desc">
                          <h6><?php echo $product_name; ?></h6>
                          <span>Price:</span>
                          <p id="pr"> <?php echo $product_price ?> /-</p>
                          <div>
                          <form action="assets/php/qty_session.php" method="post">
                            <input type="hidden" name="no" value="<?php echo $value['no']; ?>">
                            <input type="submit" name="remove" value="Remove"></input>
                          </form>
                          </div>
                          
                          
                        </div>
                        
                      </div>
                      <div class="col-4">
                        <!-- <h5></h5> -->
                        <div class="textbox text-l">
                          <input class="text-box" name="qty_<?php echo $pro_no ?>" id="qty<?php echo $pro_no;?>_<?php echo $product_price;?>" type="number" onchange="total(this.id)" value="<?php echo $product_qty;?>" min="1" max="10" requierd>
                        </div>
                        
                      </div>
                      <div class="col-4 text-l">
                        <h5 id="qty<?php echo $pro_no;?>" class="sub_tot"><?php echo ($product_price*$product_qty);?></h5>
                      </div>
                    </div>
                  </div> 
                  

                   <?php } }else{echo "<h5 class='text-center'>Add products</h5>";}}?>
              
                  
                  <!-- cart-total  -->
                  <div class="row">
                    <div class="col-3"></div>
                    <div class="col-9">
                      <hr>
                        <div class="container-fluid">
                          <div class="row">
                            <div class="col-6">
                              <div class="container-fluid text-l">
                                <div class="row">
                                  <div class="col">Sub Total:</div>
                                </div>
                                <div class="row">
                                  <div class="col">Tax:</div>
                                </div>
                                <div class="row">
                                  <div class="col">Total:</div>
                                </div>
                              </div>
                            </div>
                            <div class="col-6">
                              <div class="container-fluid text-l">
                                <div class="row">
                                  <div class="col" id="sub_sub_total">000</div>
                                </div>
                                <div class="row">
                                  <div class="col" id="tax">000</div>
                                </div>
                                <div class="row">
                                  <div class="col"id="final_total">0000</div>
                                </div>
                              </div>
                             
                            </div>
                          </div>
                        </div>
                    </div>
                  </div>
        </div>
        <div class="col-md-3 cart-right"></div>
       
      </div>
      <?php// $_SESSION['ordered'] = false; ?>
      <?php if(!isset($_SESSION['ordered'])){ ?>
                

         
        
                              <div class="container-fluid " >
                                  <div class="row">
                                    <div class="col-12 text-center d-flex align-items-center justify-content-center">
                                      <label for="order-desc" class="" >Order Suggestion</label>
                                      <textarea name="order_desc" id="order_desc" cols="30" rows="1" class="" onchange="order_desc()" style="margin-left : 4px;"></textarea>
                                   </div>
                                  </div>
                              </div>
                              
                              <div class="container-fluid mt-3">
                                  <div class="row">
                                    <div class="col-12 text-center">
                                     
                                      <input type="hidden" name="cust_id" value="<?php if(isset($_SESSION['id'])){echo $_SESSION['id'];} ?>">
                                      <input type="hidden" id="" name="status" value="ordered">
                                      <button type="submit" name="order" class="btn btn-primary w-25 mb-3">Make Order</button>
                                      <a href="assets/php/history.php">📝</a>
                                   </div>
                                  </div>
                              </div>
      
      
       <?php }else {?>
                                <div class="container-fluid mt-3">
                                  <div class="row">
                                    <div class="col-12 text-center">


                                      <button  name="order" class="btn btn-primary w-25 mb-3">Ordered</button>
                                      <?php
                                      $id = $_SESSION['id'];
                                      $or = 'ordered';
                                      $sql = "select * from orders where customer_id = $id && status = 'ordered' || status = 'accepted' || status = 'Deliverd'";
                                      $res = mysqli_query($conn,$sql);
                                      // print_r($res);
                                      $orders = mysqli_fetch_assoc($res);     
                                      if(isset($orders['status'])){                       
                                      ?>
                                      <p  name="order" class="btn btn-primary w-25 mb-3"><?php echo $orders['status']; }?></p>
                                      <a href="assets/php/history.php">📝</a>

                                   </div>
                                  </div>
                                </div>
     

      <?php  } ?>      
                    
                              <!-- <div class="container-fluid mt-3">
                                  <div class="row">
                                    <div class="col-12 text-center">
                                      <h5><a href="index.php">Go Back</a></h5>
                                   </div>
                                  </div>
                              </div> -->
    </header>
    </form>
    <script>
                    // var prd_qty = ;
                    // document.getElementById('qty').value = prd_qty;
                    // var sub_total = ( prd_qty * );
                    // document.getElementById('sub_total').innerText = sub_total;
                    let prd_tot1 = document.getElementsByClassName('sub_tot');
                      let p1=0;
                    let sub1=0;
                    let sum1=0;


                    while(p1 < (prd_tot1.length )){
                        // console.log(document.getElementsByClassName('sub_tot')[p].innerText);
                        sub1 = parseInt(document.getElementsByClassName('sub_tot')[p1].innerText);

                        console.log(typeof(sub1));
                        sum1 =sum1 + sub1 ;
                        // console.log(sum1);
                        p1++;

                      }

                      document.getElementById("sub_sub_total").innerText = sum1; 
                      let tax1 = (3*sum1)/100;
                      document.getElementById("tax").innerText = tax1; 
                      document.getElementById("final_total").innerText = (sum1 + parseInt(document.getElementById("tax").innerText)); 
                      // document.getElementById('sub_sub_total').innnerText = sum;
                      document.getElementById("sub_sub_total").innerText = sum1;       

                      let sub = 0;

                    function order_desc() {
                        let val = document.getElementById('order_desc').value;
                        document.getElementById('desc').value = val;
                        
                        
                        // alert(val);
                        // return val;
                    }
                    function total(id){
                      // console.log(id);
                      var ids = id.split('_');
                      // console.log(ids);
                      var qty = document.getElementById(id).value;
                      var price =  ids[1];
                      var sub_total = ( qty * price);
                      document.getElementById(ids[0]).innerText = sub_total; 
                      // console.log('hey');

                      let prd_tot = document.getElementsByClassName('sub_tot');
                      let sum = 0;
                      let  p = 0;
                      // console.log(prd_tot.length);
                      while(p < (prd_tot.length )){
                        // console.log(document.getElementsByClassName('sub_tot')[p].innerText);
                        sub = parseInt(document.getElementsByClassName('sub_tot')[p].innerText);

                        console.log(typeof(sub));
                        sum =sum + sub ;
                        console.log(sum);
                        p++;

                      }
                      // document.getElementById('sub_sub_total').innnerText = sum;
                      document.getElementById("sub_sub_total").innerText = sum; 
                      let tax = (3*sum)/100;
                      document.getElementById("tax").innerText = tax; 
                      document.getElementById("final_total").innerText = (sum + parseInt(document.getElementById("tax").innerText)); 

                        
                      
                    }
                  </script>
      
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






