<?php
session_start();

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth_helpers.php';

function forgot_redirect(string $error = ''): void
{
    $target = '../../forgot.php';
    if ($error !== '') {
        $target .= '?error=' . urlencode($error);
    }
    header('Location: ' . $target);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['reset'])) {
    forgot_redirect('invalid');
}

$email = trim((string) ($_POST['email'] ?? ''));
$password = (string) ($_POST['password'] ?? '');
$cpassword = (string) ($_POST['cpassword'] ?? '');

if ($email === '' || $password === '' || $cpassword === '') {
    forgot_redirect('required');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    forgot_redirect('email');
}

if (auth_is_admin_email($email)) {
    forgot_redirect('admin');
}

if (strlen($password) > 20) {
    forgot_redirect('password_length');
}

if ($password !== $cpassword) {
    forgot_redirect('password');
}

$stmt = $conn->prepare('SELECT cust_id FROM customer WHERE LOWER(cust_email) = LOWER(?) LIMIT 1');
if (!$stmt) {
    forgot_redirect('server');
}

$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();
$customer = $result ? $result->fetch_assoc() : null;
$stmt->close();

if (!$customer) {
    forgot_redirect('notfound');
}

$update = $conn->prepare('UPDATE customer SET cust_password = ? WHERE cust_id = ?');
if (!$update) {
    forgot_redirect('server');
}

$custId = (int) $customer['cust_id'];
$update->bind_param('si', $password, $custId);
$ok = $update->execute();
$update->close();

if (!$ok) {
    forgot_redirect('server');
}

$_SESSION['auth_success'] = 'Password updated successfully.';
header('Location: ../../login.php');
exit;
