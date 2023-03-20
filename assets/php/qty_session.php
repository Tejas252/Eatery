
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php 

session_start();
// session_destroy();   
if($_SERVER["REQUEST_METHOD"]=="POST"){
    if(isset($_POST["submit"])){

        if(isset($_SESSION['cart'])){
            $nos = array_column($_SESSION['cart'],'no');

            if(in_array($_POST['product_no'],$nos)){
                echo"<script> alert('Already Added'); window.location.href='../../index.php#Menu';";
                // // header("location:data_insert.php");
                echo"</script>";
            }else{
                $count = count($_SESSION['cart']);
                $_SESSION['cart'][$count] = array('no' => $_POST['product_no'] , 'qty' => $_POST['qty']);
                // print_r($_SESSION['cart']);
                echo"<script> alert('Item Added'); window.location.href='../../index.php#Menu';";
                // // header("location:data_insert.php");
                echo"</script>";
            }
            
        }else{
            $_SESSION['cart'][0] = array('no' => $_POST['product_no'] , 'qty' => $_POST['qty']);
            // print_r($_SESSION['cart']);
            // echo 'hey';
            echo"<script> alert('Item Added'); window.location.href='../../index.php#Menu';";
            // // header("location:data_insert.php");
            echo"</script>";
        }
        // $_SESSION['product_no'] = $_POST['product_no'];
        // $_SESSION['qty'] = $_POST['qty'];
        // echo $_POST['product_no'];
        // echo $_SESSION['qty'];
        // header("location:../../index.php#Menu");
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
    
</body>
</html>

