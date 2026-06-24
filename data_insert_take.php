<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>

<body>
  <!-- <input type="tex" onclick='click()'> -->
  <?php
  if (session_status() === PHP_SESSION_NONE) {
    session_start();
  }

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
  // echo "Connected successfully";
  
  // $fname = $_GET['f_name'];
// $lname = $_GET['l_name'];
// // $qr = "insert into data values ('$fname','$lname')";
// $qr1 = "create table rumit (name varchar(10),lo varchar(10)) ";
// mysqli_query($conn, $qr1);
  


  if (isset($_POST['submit']) || isset($_POST['create'])) {
    require_once __DIR__ . '/assets/php/auth_helpers.php';
    admin_require_auth();
    require_once __DIR__ . '/assets/php/product_admin_helpers.php';
    product_admin_add_form_redirect('Please use the Add Product form to create products.');
  }


  // print_r($product_img);
  






  ?>
</body>

</html>