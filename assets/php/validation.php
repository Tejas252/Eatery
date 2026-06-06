<?php
session_start();

require_once __DIR__ . '/config.php';

function auth_redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function auth_signup_redirect(string $error = '', string $message = ''): void
{
    if ($error !== '') {
        auth_redirect('../../signup.php?error=' . urlencode($error));
    }
    if ($message !== '') {
        $_SESSION['auth_notice'] = $message;
    }
    auth_redirect('../../signup.php');
}

function auth_login_redirect(string $query = ''): void
{
    $target = '../../login.php';
    if ($query !== '') {
        $target .= '?' . ltrim($query, '?');
    }
    auth_redirect($target);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    auth_redirect('../../signup.php');
}

if (isset($_POST['forgot'])) {
    $email = trim((string) ($_POST['Email'] ?? ''));
    $phone = trim((string) ($_POST['phone'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $cpassword = (string) ($_POST['cpassword'] ?? '');

    if ($email === '' || $phone === '' || $password === '' || $cpassword === '') {
        auth_redirect('../../assets/php/forgot.php?error=required');
    }

    if ($password !== $cpassword) {
        auth_redirect('../../assets/php/forgot.php?error=password');
    }

    $emailEsc = mysqli_real_escape_string($conn, $email);
    $phoneEsc = mysqli_real_escape_string($conn, $phone);
    $passwordEsc = mysqli_real_escape_string($conn, $password);

    $qr = "UPDATE customer SET cust_password = '$passwordEsc'
           WHERE cust_email = '$emailEsc' AND cust_mobile = '$phoneEsc'";
    $res = mysqli_query($conn, $qr);

    if ($res && mysqli_affected_rows($conn) > 0) {
        $_SESSION['auth_success'] = 'Password updated successfully. Please sign in.';
        auth_login_redirect('registered=1');
    }

    auth_redirect('../../assets/php/forgot.php?error=notfound');
}

if (!isset($_POST['submit'])) {
    auth_signup_redirect('invalid', '');
}

if (!isset($_POST['terms'])) {
    auth_signup_redirect('terms');
}

$email = trim((string) ($_POST['Email'] ?? ''));
$username = trim((string) ($_POST['username'] ?? ''));
$name = trim((string) ($_POST['name'] ?? ''));
$phone = trim((string) ($_POST['phone'] ?? ''));
$password = (string) ($_POST['password'] ?? '');
$cpassword = (string) ($_POST['cpassword'] ?? '');

if ($email === '' || $username === '' || $name === '' || $phone === '' || $password === '' || $cpassword === '') {
    auth_signup_redirect('required');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    auth_signup_redirect('email');
}

if (strlen($username) > 8) {
    auth_signup_redirect('username');
}

if (strlen($name) > 20) {
    auth_signup_redirect('name');
}

if (strlen($password) > 20) {
    auth_signup_redirect('password_length');
}

if (!preg_match('/^\d{10,15}$/', $phone)) {
    auth_signup_redirect('phone');
}

if ($password !== $cpassword) {
    auth_signup_redirect('password');
}

$emailEsc = mysqli_real_escape_string($conn, $email);
$usernameEsc = mysqli_real_escape_string($conn, $username);
$nameEsc = mysqli_real_escape_string($conn, $name);
$phoneEsc = mysqli_real_escape_string($conn, $phone);
$passwordEsc = mysqli_real_escape_string($conn, $password);

$duplicateCheck = mysqli_query(
    $conn,
    "SELECT cust_id FROM customer WHERE cust_email = '$emailEsc' OR cust_username = '$usernameEsc' LIMIT 1"
);

if (!$duplicateCheck) {
    auth_signup_redirect('server');
}

if (mysqli_num_rows($duplicateCheck) > 0) {
    auth_signup_redirect('duplicate');
}

$query = "INSERT INTO customer (cust_email, cust_username, cust_name, cust_mobile, cust_password)
          VALUES ('$emailEsc', '$usernameEsc', '$nameEsc', '$phoneEsc', '$passwordEsc')";
$result = mysqli_query($conn, $query);

if (!$result) {
    auth_signup_redirect('server');
}

$_SESSION['auth_success'] = 'Your account was created successfully. Please sign in.';
auth_login_redirect('registered=1');
