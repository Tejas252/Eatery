<?php

    session_start();
    unset($_SESSION['login']);
    unset($_SESSION['id']);
    unset($_SESSION['username']);
    unset($_SESSION['name']);
    unset($_SESSION['email']);
    unset($_SESSION['phone']);
    unset($_SESSION['table']);
    unset($_SESSION['guest']);
    unset($_SESSION['booking_date']);
    unset($_SESSION['booking_time']);
    unset($_SESSION['booking_status']);

    header('location:../../index.php');

?>