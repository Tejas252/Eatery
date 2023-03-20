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
  


  if (isset($_POST['submit'])) {
    $product_no = $_POST['product_no'];
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_type = $_POST['product_type'];
    $product_qty = $_POST['product_qty'];
    $product_desc = $_POST['product_desc'];
    $product_img = $_FILES['product_img'];
    $img_name = $_FILES['product_img']['name'];
    $img_size = $_FILES['product_img']['size'];
    $tmp_name = $_FILES['product_img']['tmp_name'];
    $error = $_FILES['product_img']['error'];

    $qr = "select product_no from products";
    $res = mysqli_query($conn, $qr);
    $row = mysqli_num_rows($res);
    if (mysqli_num_rows($res) > 0) {
      while ($num = mysqli_fetch_assoc($res)) {
        echo $_POST['product_no'];
        echo'<br>';
        echo $num['product_no'];
        if ($num['product_no'] == $_POST['product_no']) {
          // echo "<a href='data_insert.php'>Go Back</a>";
          echo "<script> alert('Product no is same'); window.location.href='/eatery/public_html/data_insert.php';";
          // // header("location:data_insert.php");
          echo"</script>";
          echo"if";
            break;
        }else{
          echo"else";
          continue;
        }
      }
    }
            if ($error === 0) 
            {
              if ($img_size < 12500000) {
                  $img_ex = pathinfo($img_name, PATHINFO_EXTENSION);
                  $img_ex_lc = strtolower($img_ex);

                  $allowed_exs = array("jpg", "jpeg", "png");

                  if (in_array($img_ex_lc, $allowed_exs)) {
                    $new_img_name = uniqid("IMG-", true) . '.' . $img_ex_lc;
                    $img_upload_path = 'assets/uploads/' . $new_img_name;
                    move_uploaded_file($tmp_name, $img_upload_path);
                    $qr1 = "insert into products(product_no,product_name,product_price,product_type,product_qty,product_desc,product_img) values('$product_no','$product_name','$product_price','$product_type','$product_qty','$product_desc','$new_img_name'  )";
                    mysqli_query($conn, $qr1);
                   
                    header("location:index.php");

                  }
                  // echo($img_ex);
            } else {
              echo "file to large <a href='data_insert.php'>go back</a>";
              // header("location:<a href='data_insert.php'>go back</a>");
            }

          } else {
            echo "img error <a href='data_insert.php'>go back</a> ";
          }

  }


  // print_r($product_img);
  






  ?>
</body>

</html>