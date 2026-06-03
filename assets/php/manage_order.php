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
                  $orderOk = true;
                  foreach($_SESSION['cart'] as $key => $value){

                    $pro_no = $value['no'];
                    $name = "qty_".$pro_no;
                    $qty = $_POST[$name];
                    $total = $_POST['total'];

                    $qrr = "insert into orders (customer_id,product_id,qty,order_desc,table_no,status,total) values ($id,$pro_no,$qty,'$desc','$table_no','$status',$total)";
                    $res = mysqli_query($conn,$qrr);

                    $qrr1 = "update products set product_qty = product_qty-$qty where product_no = $pro_no";
                    mysqli_query($conn,$qrr1);

                    if(!$res){
                      $orderOk = false;
                      break;
                    }
                  }

                  if($orderOk){
                    $_SESSION['ordered'] = true;
                    $_SESSION['cart'] = [];
                    header("location:../../cart.php");
                    exit;
                  }
                  header("location:login.php");
                  exit;
                }}

            
        }else{
            echo"<script>alert('First Book Table or Login'); window.location.href = '../../index.php';</script>";
        }

        
    }
  if(isset($_POST['change'])){
        if($_POST['status'] == 'deliverd' ){
            $cid = $_POST['id'];
            $sts = $_POST['status'];
            $o_id = $_POST['order_id'];
            $qr4 = "update orders set status = '$sts' where customer_id = '$cid' && order_id >= $o_id";
            $re = mysqli_query($conn,$qr4);
            if($re){
                header("location:../../admin_ord.php");
            }else{
                echo "<script>console.log('error in update status');</script>";
            }
        }else{
            if($_POST['status'] == 'done'){
                $cid = $_POST['id'];
                $sts = $_POST['status'];
                $o_id = $_POST['order_id']; 
                $t_no = $_POST['table_no']; 
                $qr4 = "update orders set status = '$sts' where customer_id = '$cid' && order_id >= $o_id";
                $qr5 = "update book_table set table_status = 'non' where table_no = '$t_no' ";
                $re = mysqli_query($conn,$qr4);
                $res = mysqli_query($conn,$qr5);
                if($re && $res){
                    header("location:../../admin.php");
                }else{
                    echo "<script>console.log('error in update status');</script>";
                }  
            }else{
        $cid = $_POST['id'];
        $sts = $_POST['status'];
        $o_id = $_POST['order_id'];
        $qr4 = "update orders set status = '$sts' where customer_id = '$cid' && order_id >= $o_id";
        $re = mysqli_query($conn,$qr4);
        if($re){
            header("location:../../admin.php");
        }else{
            echo "<script>console.log('error in update status');</script>";
        }
    }
    }
  }
  if(isset($_POST['remove'])){
    foreach($_SESSION['cart'] as $key => $value){
        if($value['no'] == $_POST['no']){
            // echo "removed";
            unset($_SESSION['cart'][$key]);
            $_SESSION['cart'] = array_values($_SESSION['cart']);
            echo"<script> alert('Removed'); window.location.href='../../cart.php';";
            // // header("location:data_insert.php");
            echo"</script>";
        }
        
    }

}
       
  }

?>