<?php

session_start();

require_once __DIR__ . '/book_table_helpers.php';

clear_booking_session_if_logged_out();

$conn = mysqli_connect('localhost', 'root', '', 'eatery');
if (!$conn) {
    book_table_json(false, 'Unable to connect to the server.', [], 500);
}

book_table_reconcile_customer_booking($conn);

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$userTable = get_user_table_for_display();
$customerId = isset($_SESSION['id']) ? (int) $_SESSION['id'] : null;
$isLoggedIn = isset($_SESSION['login']) && $_SESSION['login'] === true;

if ($action === 'tables') {
    $guests = isset($_GET['guests']) ? (int) $_GET['guests'] : (int) ($_SESSION['guest'] ?? 2);
    $guests = max(1, $guests);
    $booking = get_current_booking_from_session();
    $hasActiveBooking = $booking !== null;

    if ($hasActiveBooking) {
        book_table_json(true, 'Active reservation loaded.', [
            'has_active_booking' => true,
            'booking' => enrich_booking_with_table($conn, $booking),
            'tables' => [],
            'max_guests' => book_table_max_guests($conn),
            'is_logged_in' => $isLoggedIn,
        ]);
    }

    book_table_json(true, 'Tables loaded.', [
        'has_active_booking' => false,
        'tables' => build_table_payload($conn, $guests, $userTable),
        'max_guests' => book_table_max_guests($conn),
        'booking' => null,
        'is_logged_in' => $isLoggedIn,
    ]);
}

if ($action === 'book') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        book_table_json(false, 'Invalid request method.', [], 405);
    }

    if (!$isLoggedIn) {
        book_table_json(false, 'Please log in to book a table.', ['code' => 'auth_required'], 401);
    }

    if (customer_has_active_booking()) {
        book_table_json(false, 'You already have an active table reservation.', [
            'code' => 'existing_booking',
            'has_active_booking' => true,
            'booking' => enrich_booking_with_table($conn, get_current_booking_from_session()),
        ], 409);
    }

    $tableNo = isset($_POST['table_no']) ? (int) $_POST['table_no'] : 0;
    $guests = isset($_POST['guests']) ? (int) $_POST['guests'] : 0;
    $bookingDate = trim($_POST['booking_date'] ?? '');
    $bookingTime = trim($_POST['booking_time'] ?? '');

    if ($bookingDate === '') {
        $bookingDate = date('Y-m-d');
    }
    if ($bookingTime === '') {
        $bookingTime = date('H:i');
    }

    $result = reserve_table($conn, $tableNo, $guests, $bookingDate, $bookingTime, $customerId);

    if (!$result['success']) {
        book_table_json(false, $result['message'], [
            'code' => $result['code'] ?? 'error',
            'tables' => build_table_payload($conn, max(1, $guests), $userTable),
        ], 400);
    }

    book_table_json(true, $result['message'], [
        'code' => $result['code'],
        'has_active_booking' => true,
        'booking' => enrich_booking_with_table($conn, $result['booking']),
        'tables' => [],
    ]);
}

book_table_json(false, 'Unknown action.', [], 400);
