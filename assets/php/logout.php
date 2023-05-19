<?php

    session_start();
    unset($_SESSION['login']);
    unset($_SESSION['id']);
    unset($_SESSION['username']);
    unset($_SESSION['name']);
    unset($_SESSION['email']);
    unset($_SESSION['phone']);

    header('location:../../index.php');

?>