<?php
session_start();

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth_helpers.php';

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

if (auth_is_admin_credentials($email, $password)) {
    auth_set_admin_session();
    header('Location: ../../admin_dashboard.php');
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
    auth_set_customer_session($row);
    header('Location: ../../index.php');
    exit;
}

header('Location: ../../login.php?error=invalid');
exit;
