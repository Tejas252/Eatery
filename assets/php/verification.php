<?php

    include('config.php');
    // $conn = mysqli_connect('localhost','root','','eatery');
    session_start();
    $email = $_POST['email'];
    $password = $_POST['password'];

    if(strcmp($email,"rumit10@gmail.com") == 0 && strcmp($password ,"123456") == 0 ){
        $_SESSION['id'] = $row['cust_id'];
        $_SESSION['username'] = $row['cust_username'];
        $_SESSION['name'] = $row['cust_name'];
        $_SESSION['phone'] = $row['cust_mobile'];
        $_SESSION['email'] = $row['cust_email'];
        $_SESSION['login'] = true;
        echo "<script>  window.location.href='../../admin.php';";
        // // header("location:data_insert.php");
        echo"</script>";   
    }else{
        $query = "select * from customer where cust_email = '$email' and cust_password='$password'";

        $result = mysqli_query($conn, $query);  
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);  
        $count = mysqli_num_rows($result); 
        // echo $result;
        // print($result); 
            if($count==1){
                
                // echo "conn";
                // if(isset($_POST['login'])){
                    // session_start();
                    // $user = $_POST['email'];
                    
                    $_SESSION['id'] = $row['cust_id'];
                    $_SESSION['username'] = $row['cust_username'];
                    $_SESSION['name'] = $row['cust_name'];
                    $_SESSION['phone'] = $row['cust_mobile'];
                    $_SESSION['email'] = $row['cust_email'];
                    $_SESSION['login'] = true;
                    // $qr = "";
                    // echo strcmp($email,"rumit10@gmail.com");
                    // $_SESSION['username'] = $res['cust_username'];
                    // $_SESSION['username'] = $res['cust_username'];
                    header("location:../../index.php"); 
    
                // }
            }else{
                // echo "not";
              
                echo "<script> alert('Username & Password not mathched'); window.location.href='../../login.php';";
                // // header("location:data_insert.php");
                echo"</script>";
                // echo"window.location.href = '../../login.html'";
                // header("location:../../login.html");
            }
    }

    
       


?>