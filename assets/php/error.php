<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
</head>
<body>
    <?php 

    session_start();
    if($_SESSION['login'] == false){
        // echo"hello";
        echo"<script>alert('Login Required'); window.location.href = '../../index.php';</script>";
        // header('location:../../index.php');
    }else{
        echo "hhh";
    }

    ?>    
</body>
</html>

