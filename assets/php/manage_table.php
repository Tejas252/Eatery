<?php
session_start();

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth_helpers.php';
require_once __DIR__ . '/table_admin_helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    table_admin_redirect('', 'success');
}

admin_require_auth();
if (isset($_POST['create'])) {
    $errors = table_admin_validate_payload($_POST, false);
    if ($errors) {
        table_admin_redirect(implode(' ', $errors), 'error');
    }

    $tableNo = (int) $_POST['table_no'];
    $capacity = (int) $_POST['table_size'];
    $status = table_admin_normalize_status((string) ($_POST['table_status'] ?? 'non'));

    if (table_admin_table_no_exists($conn, $tableNo)) {
        table_admin_redirect('Table number already exists. Choose a unique table number.', 'error');
    }

    $stmt = $conn->prepare('INSERT INTO book_table (table_no, table_size, table_status) VALUES (?, ?, ?)');
    if (!$stmt) {
        table_admin_redirect('Failed to create table.', 'error');
    }

    $stmt->bind_param('iis', $tableNo, $capacity, $status);
    $ok = $stmt->execute();
    $stmt->close();

    table_admin_redirect(
        $ok ? 'Table ' . $tableNo . ' added successfully.' : 'Failed to create table.',
        $ok ? 'success' : 'error'
    );
}

if (isset($_POST['update'])) {
    $originalNo = (int) ($_POST['original_table_no'] ?? 0);
    $table = table_admin_get($conn, $originalNo);

    if (!$table) {
        table_admin_redirect('Table not found.', 'error');
    }

    $errors = table_admin_validate_payload($_POST, true, $originalNo);
    if ($errors) {
        table_admin_redirect(implode(' ', $errors), 'error');
    }

    $tableNo = (int) $_POST['table_no'];
    $capacity = (int) $_POST['table_size'];
    $status = table_admin_normalize_status((string) ($_POST['table_status'] ?? 'non'));

    if ($status === 'non' && table_admin_is_reserved($table) && table_admin_has_active_orders($conn, $originalNo)) {
        table_admin_redirect('Cannot set table to Available while it has pending, accepted, or preparing orders.', 'error');
    }

    $stmt = $conn->prepare('UPDATE book_table SET table_size = ?, table_status = ? WHERE table_no = ?');
    if (!$stmt) {
        table_admin_redirect('Failed to update table.', 'error');
    }

    $stmt->bind_param('isi', $capacity, $status, $originalNo);
    $ok = $stmt->execute();
    $stmt->close();

    table_admin_redirect(
        $ok ? 'Table ' . $tableNo . ' updated successfully.' : 'Failed to update table.',
        $ok ? 'success' : 'error'
    );
}

if (isset($_POST['delete'])) {
    $tableNo = (int) ($_POST['table_no'] ?? 0);
    $table = table_admin_get($conn, $tableNo);

    if (!$table) {
        table_admin_redirect('Table not found.', 'error');
    }

    $deleteCheck = table_admin_delete_allowed($conn, $table);
    if (!$deleteCheck['allowed']) {
        table_admin_redirect($deleteCheck['reason'], 'error');
    }

    $stmt = $conn->prepare('DELETE FROM book_table WHERE table_no = ?');
    if (!$stmt) {
        table_admin_redirect('Failed to delete table.', 'error');
    }

    $stmt->bind_param('i', $tableNo);
    $ok = $stmt->execute();
    $stmt->close();

    table_admin_redirect(
        $ok ? 'Table ' . $tableNo . ' deleted successfully.' : 'Failed to delete table.',
        $ok ? 'success' : 'error'
    );
}

table_admin_redirect('Invalid table request.', 'error');
