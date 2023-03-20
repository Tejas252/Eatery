<?php 
        include('config.php');


?>
<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Delete</title>
</head>
<body><?php
       if(isset($_POST['delete']) && isset($_POST['product_no'])){
                $prd_no = $_POST['product_no'];
                $qr = "delete from products where product_no = $prd_no";
                if(mysqli_query($conn,$qr)){
                        echo"okay";
                } 


       } 
       ?>
</body>
</html>