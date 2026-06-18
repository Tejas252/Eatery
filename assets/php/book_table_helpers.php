<?php

function book_table_max_guests(mysqli $conn): int
{
    $result = mysqli_query($conn, 'SELECT MAX(table_size) AS max_size FROM book_table');
    if (!$result) {
        return 5;
    }
    $row = mysqli_fetch_assoc($result);
    return max(1, (int) ($row['max_size'] ?? 5));
}

function book_table_occupied_numbers(mysqli $conn): array
{
    $occupied = [];
    $query = "SELECT DISTINCT table_no FROM orders
              WHERE LOWER(TRIM(status)) IN ('ordered', 'accepted', 'preparing', 'pending')";
    $result = mysqli_query($conn, $query);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $occupied[] = (int) $row['table_no'];
        }
    }

    return $occupied;
}

function book_table_status_key(array $table, int $guests, ?int $userTableNo, array $occupiedTables): string
{
    $tableNo = (int) $table['table_no'];
    $capacity = (int) $table['table_size'];
    $dbStatus = strtolower(trim($table['table_status'] ?? ''));

    if ($capacity < $guests) {
        return 'unavailable';
    }

    if ($userTableNo && $userTableNo === $tableNo) {
        return 'yours';
    }

    if (in_array($tableNo, $occupiedTables, true)) {
        return 'occupied';
    }

    if ($dbStatus === 'res') {
        return 'reserved';
    }

    return 'available';
}

function book_table_status_meta(string $statusKey): array
{
    $map = [
        'available' => ['label' => 'Available', 'class' => 'available'],
        'reserved' => ['label' => 'Reserved', 'class' => 'reserved'],
        'occupied' => ['label' => 'Occupied', 'class' => 'occupied'],
        'unavailable' => ['label' => 'Unavailable', 'class' => 'unavailable'],
        'yours' => ['label' => 'Your Booking', 'class' => 'yours'],
    ];

    return $map[$statusKey] ?? ['label' => 'Unavailable', 'class' => 'unavailable'];
}

function fetch_all_tables(mysqli $conn): array
{
    $result = mysqli_query($conn, 'SELECT table_no, table_size, table_status FROM book_table ORDER BY table_no ASC');
    if (!$result) {
        return [];
    }

    $tables = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $tables[] = $row;
    }

    return $tables;
}

function build_table_payload(mysqli $conn, int $guests, ?int $userTableNo = null): array
{
    $guests = max(1, $guests);
    $occupied = book_table_occupied_numbers($conn);
    $tables = fetch_all_tables($conn);
    $payload = [];

    foreach ($tables as $table) {
        $statusKey = book_table_status_key($table, $guests, $userTableNo, $occupied);
        $meta = book_table_status_meta($statusKey);

        $payload[] = [
            'table_no' => (int) $table['table_no'],
            'capacity' => (int) $table['table_size'],
            'status_key' => $statusKey,
            'status_label' => $meta['label'],
            'status_class' => $meta['class'],
            'selectable' => $statusKey === 'available',
        ];
    }

    return $payload;
}

function book_table_json(bool $success, string $message, array $data = [], int $code = 200): void
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array_merge([
        'success' => $success,
        'message' => $message,
    ], $data));
    exit;
}

function reserve_table(mysqli $conn, int $tableNo, int $guests, string $bookingDate, string $bookingTime, ?int $customerId): array
{
    if ($customerId === null) {
        return ['success' => false, 'message' => 'Please log in to book a table.', 'code' => 'auth_required'];
    }

    if (customer_has_active_booking()) {
        return [
            'success' => false,
            'message' => 'You already have Table ' . (int) $_SESSION['table'] . ' reserved. Your booking cannot be changed from here.',
            'code' => 'existing_booking',
        ];
    }

    if ($tableNo <= 0) {
        return ['success' => false, 'message' => 'Please select a valid table.', 'code' => 'invalid_table'];
    }

    if ($guests < 1 || $guests > book_table_max_guests($conn)) {
        return ['success' => false, 'message' => 'Guest count is out of range.', 'code' => 'invalid_guests'];
    }

    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $bookingDate)) {
        return ['success' => false, 'message' => 'Please choose a valid booking date.', 'code' => 'invalid_date'];
    }

    if (!preg_match('/^\d{2}:\d{2}$/', $bookingTime)) {
        return ['success' => false, 'message' => 'Please choose a valid booking time.', 'code' => 'invalid_time'];
    }

    $today = date('Y-m-d');
    if ($bookingDate < $today) {
        return ['success' => false, 'message' => 'Booking date cannot be in the past.', 'code' => 'past_date'];
    }

    $stmt = $conn->prepare('SELECT table_no, table_size, table_status FROM book_table WHERE table_no = ? LIMIT 1');
    $stmt->bind_param('i', $tableNo);
    $stmt->execute();
    $table = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$table) {
        return ['success' => false, 'message' => 'Table not found.', 'code' => 'not_found'];
    }

    if ((int) $table['table_size'] < $guests) {
        return ['success' => false, 'message' => 'This table cannot seat your party size.', 'code' => 'capacity'];
    }

    if (strtolower($table['table_status']) === 'res') {
        return ['success' => false, 'message' => 'This table is not available. Please choose another.', 'code' => 'already_reserved'];
    }

    $update = $conn->prepare("UPDATE book_table SET table_status = 'res' WHERE table_no = ? AND table_status = 'non'");
    $update->bind_param('i', $tableNo);
    $update->execute();

    if ($update->affected_rows !== 1) {
        $update->close();
        return ['success' => false, 'message' => 'Table was just booked by someone else. Please pick another table.', 'code' => 'race_condition'];
    }
    $update->close();

    $_SESSION['table'] = $tableNo;
    $_SESSION['guest'] = $guests;
    $_SESSION['booking_date'] = $bookingDate;
    $_SESSION['booking_time'] = $bookingTime;
    $_SESSION['booking_status'] = 'confirmed';

    return [
        'success' => true,
        'message' => 'Table ' . $tableNo . ' booked successfully!',
        'code' => 'booked',
        'booking' => [
            'table_no' => $tableNo,
            'guests' => $guests,
            'booking_date' => $bookingDate,
            'booking_time' => $bookingTime,
            'status' => 'confirmed',
            'capacity' => (int) $table['table_size'],
        ],
    ];
}

function get_current_booking_from_session(): ?array
{
    if (empty($_SESSION['login']) || empty($_SESSION['table'])) {
        return null;
    }

    return [
        'table_no' => (int) $_SESSION['table'],
        'guests' => (int) ($_SESSION['guest'] ?? 0),
        'booking_date' => $_SESSION['booking_date'] ?? date('Y-m-d'),
        'booking_time' => $_SESSION['booking_time'] ?? date('H:i'),
        'status' => $_SESSION['booking_status'] ?? 'confirmed',
    ];
}

function customer_has_active_booking(): bool
{
    return get_current_booking_from_session() !== null;
}

function book_table_clear_session_booking(): void
{
    unset(
        $_SESSION['table'],
        $_SESSION['guest'],
        $_SESSION['booking_date'],
        $_SESSION['booking_time'],
        $_SESSION['booking_status']
    );
}

/**
 * If admin set the booked table back to Available, end the customer's session booking.
 */
function book_table_reconcile_customer_booking(mysqli $conn): void
{
    if (!customer_has_active_booking()) {
        return;
    }

    $tableNo = (int) ($_SESSION['table'] ?? 0);
    if ($tableNo <= 0) {
        book_table_clear_session_booking();
        return;
    }

    $stmt = $conn->prepare('SELECT table_status FROM book_table WHERE table_no = ? LIMIT 1');
    if (!$stmt) {
        return;
    }

    $stmt->bind_param('i', $tableNo);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$row) {
        book_table_clear_session_booking();
        return;
    }

    $status = strtolower(trim((string) ($row['table_status'] ?? '')));
    if ($status === 'non') {
        book_table_clear_session_booking();
    }
}

function clear_booking_session_if_logged_out(): void
{
    if (!empty($_SESSION['login'])) {
        return;
    }

    book_table_clear_session_booking();
}

function get_user_table_for_display(): ?int
{
    $booking = get_current_booking_from_session();
    return $booking ? (int) $booking['table_no'] : null;
}

function enrich_booking_with_table(mysqli $conn, array $booking): array
{
    $tableNo = (int) ($booking['table_no'] ?? 0);
    if ($tableNo <= 0) {
        return $booking;
    }

    $stmt = $conn->prepare('SELECT table_size FROM book_table WHERE table_no = ? LIMIT 1');
    $stmt->bind_param('i', $tableNo);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $booking['capacity'] = $row ? (int) $row['table_size'] : null;
    return $booking;
}

function format_booking_date_display(string $date): string
{
    $time = strtotime($date);
    if ($time === false) {
        return $date;
    }
    return date('D, j M Y', $time);
}

function format_booking_time_display(string $time): string
{
    $time = trim($time);
    if (!preg_match('/^\d{2}:\d{2}$/', $time)) {
        return $time;
    }
    return date('g:i A', strtotime($time));
}
