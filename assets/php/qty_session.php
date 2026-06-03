<?php

session_start();

require_once __DIR__ . '/cart_helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../index.php#Menu');
    exit;
}

if (isset($_POST['submit'])) {
    $conn = mysqli_connect('localhost', 'root', '', 'eatery');
    if (!$conn) {
        echo "<script>alert('Server error. Please try again.'); window.location.href='../../index.php#Menu';</script>";
        exit;
    }

    $productNo = isset($_POST['product_no']) ? (int) $_POST['product_no'] : 0;
    $qty = isset($_POST['qty']) ? (int) $_POST['qty'] : 1;
    $result = add_product_to_cart($conn, $productNo, $qty);
    $message = addslashes($result['message']);
    $target = '../../index.php#Menu';

    echo "<script>alert('{$message}'); window.location.href='{$target}';</script>";
    exit;
}

if (isset($_POST['remove'])) {
    foreach ($_SESSION['cart'] as $key => $value) {
        if ($value['no'] == $_POST['no']) {
            unset($_SESSION['cart'][$key]);
            $_SESSION['cart'] = array_values($_SESSION['cart']);
            echo "<script> alert('Removed'); window.location.href='../../cart.php';</script>";
            exit;
        }
    }
}

header('Location: ../../cart.php');
exit;
