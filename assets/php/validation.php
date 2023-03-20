<?php
    include("config.php");
    // $conn = mysqli_connect('localhost','root','','eatery');
  


   if(isset($_POST['submit']) ){
        if(isset($_POST['terms'])){
            $email = $_POST['Email'];
            $username = $_POST['username'];
            $name = $_POST['name'];
            $phone = $_POST['phone'];
            $password= $_POST['password'];
            $cpassword = $_POST['cpassword'];
        
                if($password == $cpassword){
                    // echo $phone;
                    $query = "insert into customer (cust_email,cust_username,cust_name,cust_mobile,cust_password) values ('$email','$username','$name',$phone,'$password')"; 
                    $qua= mysqli_query($conn,$query);
                    if(!$qua)
                    {
                        echo mysqli_error($conn);
                    }else{
                        
                        header("location:../../login.php");
                    }
                }else{
                    echo"<script>alert('Both password is different'); window.location.href = '../../signup.php';</script>";
                }
        }else{
            echo"<script>alert('Accept Terms & Condition'); window.location.href = '../../signup.php';</script>";
        }
   
        
   }

   if(isset($_POST['forgot'])){
        $email = $_POST['Email'];
        $phone = $_POST['phone'];
        $password= $_POST['password'];
        $cpassword = $_POST['cpassword'];
        if($password == $cpassword){

            $qr = "update customer set cust_password = '$password' where cust_email = '$email' && cust_mobile = $phone";
            $res = mysqli_query($conn,$qr);
            if($res){
                header('location:../../login.php');
            }
        }else{
            echo"<script>alert('Both password is different'); window.location.href = '../../signup.php';</script>";
        }
   }


?>