<?php
session_start();

require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../login.php');
    exit;
}

$email = trim((string) ($_POST['email'] ?? ''));
$password = (string) ($_POST['password'] ?? '');

if ($email === '' || $password === '') {
    header('Location: ../../login.php?error=invalid');
    exit;
}

if (strcmp($email, 'rumit10@gmail.com') === 0 && strcmp($password, '123456') === 0) {
    $_SESSION['id'] = 0;
    $_SESSION['username'] = 'admin';
    $_SESSION['name'] = 'Administrator';
    $_SESSION['email'] = $email;
    $_SESSION['login'] = true;
    header('Location: ../../admin.php');
    exit;
}

$emailEsc = mysqli_real_escape_string($conn, $email);
$passwordEsc = mysqli_real_escape_string($conn, $password);
$query = "SELECT * FROM customer WHERE cust_email = '$emailEsc' AND cust_password = '$passwordEsc' LIMIT 1";
$result = mysqli_query($conn, $query);

if (!$result) {
    header('Location: ../../login.php?error=invalid');
    exit;
}

$row = mysqli_fetch_array($result, MYSQLI_ASSOC);

if ($row) {
    $_SESSION['id'] = $row['cust_id'];
    $_SESSION['username'] = $row['cust_username'];
    $_SESSION['name'] = $row['cust_name'];
    $_SESSION['phone'] = $row['cust_mobile'];
    $_SESSION['email'] = $row['cust_email'];
    $_SESSION['login'] = true;
    header('Location: ../../index.php');
    exit;
}

header('Location: ../../login.php?error=invalid');
exit;
