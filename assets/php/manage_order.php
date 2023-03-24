<?php
include 'config.php';
session_start();
$nid = 0;
if($_SERVER["REQUEST_METHOD"]=="POST"){

    if(isset($_POST['order'])){
      
        if(isset($_SESSION['id']) && isset($_SESSION['table'])){
            $id = $_SESSION['id'];
            $desc = $_POST['order_desc'];
            $table_no = $_SESSION['table'];
            $status = $_POST['status'];

            if(isset($_SESSION['cart'])){
                $arcol = count($_SESSION['cart']);
                if($arcol > 0){
                  // echo "<h5 class='text-center'>Add</h5>";
                  // echo $arcol;
                  foreach($_SESSION['cart'] as $key => $value){

                    $pro_no = $value['no'];
                    $name = "qty_".$pro_no;
                    $qty = $_POST[$name];
                    // $qr = "select * from products  where product_no = '$pro_no'";
                    // $result = mysqli_query($conn,$qr);
                    // $prd = mysqli_fetch_assoc($result);
                    // $product_img = $prd['product_img'];
                    // $product_qty = $value['qty'];
                    // $product_price = $prd['product_price']; 
                    // $q = 0;

                    $qrr = "insert into orders (customer_id,product_id,qty,order_desc,table_no,status) values ($id,$pro_no,$qty,'$desc','$table_no','$status')";
                    $res = mysqli_query($conn,$qrr);
                    if($res){
                    $_SESSION['ordered'] = true;
                    header("location:../../index.php");
                    //   echo "<script>console.log('$table_no');</script>";
                    }else{
                    header("location:login.php");
                    }
                }}}

            
        }else{
            echo"<script>alert('First Book Table or Login'); window.location.href = '../../index.php';</script>";
        }

        
    }
  if(isset($_POST['change'])){

        $cid = $_POST['id'];
        $sts = $_POST['status'];
        $qr4 = "update orders set status = '$sts' where customer_id = '$cid'";
        $re = mysqli_query($conn,$qr4);
        if($re){
            header("location:../../admin.php");
        }else{
            echo "<script>console.log('error in update status');</script>";
        }
  }
       
  }

?>