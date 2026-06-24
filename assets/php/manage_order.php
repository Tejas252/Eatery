<?php
session_start();

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth_helpers.php';
require_once __DIR__ . '/order_helpers.php';

function order_manage_redirect(string $path, string $message = '', string $type = 'success'): void
{
    if ($message !== '') {
        $_SESSION['admin_order_notice'] = ['type' => $type, 'message' => $message];
    }
    header('Location: ' . $path);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../index.php');
    exit;
}

if (isset($_POST['order'])) {
    if (!isset($_SESSION['id']) || !isset($_SESSION['table'])) {
        echo "<script>alert('First Book Table or Login'); window.location.href='../../index.php';</script>";
        exit;
    }

    if (!isset($_SESSION['cart']) || count($_SESSION['cart']) === 0) {
        header('Location: ../../cart.php');
        exit;
    }

    $id = (int) $_SESSION['id'];
    $desc = mysqli_real_escape_string($conn, (string) ($_POST['order_desc'] ?? ''));
    $table_no = (int) $_SESSION['table'];
    $status = order_normalize_status((string) ($_POST['status'] ?? 'ordered'));
    if ($status !== 'ordered') {
        $status = 'ordered';
    }

    $orderOk = true;
    foreach ($_SESSION['cart'] as $value) {
        $pro_no = (int) $value['no'];
        $qtyName = 'qty_' . $pro_no;
        $qty = (int) ($_POST[$qtyName] ?? 0);
        $total = (int) ($_POST['total'] ?? 0);

        $insert = mysqli_query(
            $conn,
            "INSERT INTO orders (customer_id, product_id, qty, order_desc, table_no, status, total)
             VALUES ($id, $pro_no, $qty, '$desc', $table_no, '$status', $total)"
        );
        mysqli_query($conn, "UPDATE products SET product_qty = product_qty - $qty WHERE product_no = $pro_no");

        if (!$insert) {
            $orderOk = false;
            break;
        }
    }

    if ($orderOk) {
        $_SESSION['ordered'] = true;
        $_SESSION['cart'] = [];
        header('Location: ../../cart.php');
        exit;
    }

    header('Location: ../../login.php');
    exit;
}

if (isset($_POST['change'])) {
    admin_require_auth();

    $customerId = (int) ($_POST['id'] ?? 0);
    $orderId = (int) ($_POST['order_id'] ?? 0);
    $tableNo = isset($_POST['table_no']) ? (int) $_POST['table_no'] : null;
    $orderTime = isset($_POST['order_time']) ? (string) $_POST['order_time'] : null;
    $newStatus = order_normalize_status((string) ($_POST['status'] ?? ''));
    $returnPath = (string) ($_POST['return_to'] ?? '');

    if ($customerId <= 0 || $orderId <= 0 || $newStatus === '') {
        order_manage_redirect('../../admin.php', 'Invalid order update request.', 'error');
    }

    $currentStatus = order_get_batch_current_status($conn, $customerId, $orderId);
    if ($currentStatus === null) {
        order_manage_redirect('../../admin.php', 'Order not found.', 'error');
    }

    if (!order_validate_transition($currentStatus, $newStatus)) {
        $redirect = $returnPath !== '' ? $returnPath : order_admin_redirect_for_status($currentStatus);
        order_manage_redirect(
            $redirect,
            'That status change is not allowed for this order.',
            'error'
        );
    }

    if (!order_update_batch_status($conn, $customerId, $orderId, $newStatus, $tableNo, $orderTime)) {
        $redirect = $returnPath !== '' ? $returnPath : order_admin_redirect_for_status($currentStatus);
        order_manage_redirect($redirect, 'Failed to update order status.', 'error');
    }

    $redirect = $returnPath !== '' ? $returnPath : order_admin_redirect_for_status($newStatus);
    order_manage_redirect($redirect, order_admin_flash_message($newStatus));
}

if (isset($_POST['remove'])) {
    foreach ($_SESSION['cart'] as $key => $value) {
        if ($value['no'] == $_POST['no']) {
            unset($_SESSION['cart'][$key]);
            $_SESSION['cart'] = array_values($_SESSION['cart']);
            echo "<script>alert('Removed'); window.location.href='../../cart.php';</script>";
            exit;
        }
    }
}

order_manage_redirect('../../admin.php');
