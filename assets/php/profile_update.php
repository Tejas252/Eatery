<?php

session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

if (!isset($_SESSION['login']) || !isset($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Please log in to update your profile.']);
    exit;
}

$fullName = trim($_POST['full_name'] ?? '');
$mobile = trim($_POST['mobile'] ?? '');
$customerId = (int) $_SESSION['id'];

if ($fullName === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Full name is required.']);
    exit;
}

if (strlen($fullName) > 20) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Full name must be 20 characters or fewer.']);
    exit;
}

if ($mobile !== '' && !preg_match('/^\d{10}$/', $mobile)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Mobile number must be exactly 10 digits.']);
    exit;
}

$mobileParam = $mobile === '' ? null : $mobile;

$stmt = $conn->prepare('UPDATE customer SET cust_name = ?, cust_mobile = ? WHERE cust_id = ? LIMIT 1');
$stmt->bind_param('ssi', $fullName, $mobileParam, $customerId);
$ok = $stmt->execute();
$affected = $stmt->affected_rows;
$stmt->close();

if (!$ok) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Could not save profile. Please try again.']);
    exit;
}

$_SESSION['name'] = $fullName;
$_SESSION['phone'] = $mobile;

echo json_encode([
    'success' => true,
    'message' => 'Profile updated successfully.',
    'profile' => [
        'full_name' => $fullName,
        'mobile' => $mobile,
    ],
]);
