<?php

require_once __DIR__ . '/admin_helpers.php';

function table_admin_normalize_status(string $status): string
{
    $key = strtolower(trim($status));

    if (in_array($key, ['non', 'available', 'avail'], true)) {
        return 'non';
    }

    if (in_array($key, ['res', 'reserved', 'booked'], true)) {
        return 'res';
    }

    return $key;
}

function table_admin_status_options(): array
{
    return [
        'non' => 'Available',
        'res' => 'Reserved',
    ];
}

function table_admin_stats(mysqli $conn): array
{
    return [
        'available' => admin_count_query($conn, "SELECT COUNT(*) FROM book_table WHERE table_status = 'non'"),
        'reserved' => admin_count_query($conn, "SELECT COUNT(*) FROM book_table WHERE table_status = 'res'"),
        'total' => admin_count_query($conn, 'SELECT COUNT(*) FROM book_table'),
    ];
}

function table_admin_fetch_all(mysqli $conn): array
{
    $result = mysqli_query($conn, 'SELECT table_no, table_size, table_status FROM book_table ORDER BY table_no ASC');
    if (!$result) {
        return [];
    }

    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }

    return $rows;
}

function table_admin_get(mysqli $conn, int $tableNo): ?array
{
    $stmt = $conn->prepare('SELECT table_no, table_size, table_status FROM book_table WHERE table_no = ? LIMIT 1');
    if (!$stmt) {
        return null;
    }

    $stmt->bind_param('i', $tableNo);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    return $row ?: null;
}

function table_admin_table_no_exists(mysqli $conn, int $tableNo, ?int $exclude = null): bool
{
    if ($exclude !== null && $tableNo === $exclude) {
        return false;
    }

    $stmt = $conn->prepare('SELECT table_no FROM book_table WHERE table_no = ? LIMIT 1');
    if (!$stmt) {
        return true;
    }

    $stmt->bind_param('i', $tableNo);
    $stmt->execute();
    $exists = (bool) $stmt->get_result()->fetch_assoc();
    $stmt->close();

    return $exists;
}

function table_admin_has_active_orders(mysqli $conn, int $tableNo): bool
{
    require_once __DIR__ . '/order_helpers.php';
    return order_table_has_active_orders($conn, $tableNo);
}

function table_admin_is_reserved(array $table): bool
{
    return table_admin_normalize_status((string) ($table['table_status'] ?? '')) === 'res';
}

function table_admin_delete_allowed(mysqli $conn, array $table): array
{
    if (table_admin_is_reserved($table)) {
        return [
            'allowed' => false,
            'reason' => 'This table is reserved. Set it to Available or cancel the reservation before deleting.',
        ];
    }

    if (table_admin_has_active_orders($conn, (int) $table['table_no'])) {
        return [
            'allowed' => false,
            'reason' => 'This table has active orders. Complete or cancel those orders before deleting.',
        ];
    }

    return ['allowed' => true, 'reason' => ''];
}

function table_admin_validate_payload(array $data, bool $isUpdate, ?int $currentTableNo = null): array
{
    $errors = [];
    $tableNo = (int) ($data['table_no'] ?? 0);
    $capacity = (int) ($data['table_size'] ?? 0);
    $status = table_admin_normalize_status((string) ($data['table_status'] ?? 'non'));

    if ($tableNo < 1 || $tableNo > 999) {
        $errors[] = 'Table number must be between 1 and 999.';
    }

    if ($capacity < 1 || $capacity > 99) {
        $errors[] = 'Capacity must be between 1 and 99 guests.';
    }

    if (!in_array($status, ['non', 'res'], true)) {
        $errors[] = 'Please choose a valid status.';
    }

    if ($isUpdate && $currentTableNo !== null && $tableNo !== $currentTableNo) {
        $errors[] = 'Table number cannot be changed. Delete and recreate the table if needed.';
    }

    return $errors;
}

function table_admin_payload(array $table): array
{
    $status = table_admin_normalize_status((string) $table['table_status']);

    return [
        'table_no' => (int) $table['table_no'],
        'table_size' => (int) $table['table_size'],
        'table_status' => $status,
        'status_label' => table_admin_status_options()[$status] ?? ucfirst($status),
        'can_delete' => !table_admin_is_reserved($table),
    ];
}

function table_admin_json_attr(array $table): string
{
    return htmlspecialchars(json_encode(table_admin_payload($table), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP), ENT_QUOTES, 'UTF-8');
}

function table_admin_redirect(string $message = '', string $type = 'success'): void
{
    if ($message !== '') {
        $_SESSION['admin_order_notice'] = ['type' => $type, 'message' => $message];
    }

    header('Location: ../../admin_reservations.php');
    exit;
}
