<?php
session_start();

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/product_admin_helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    product_admin_redirect('', 'error', product_admin_query_params());
}

$returnQuery = product_admin_query_params_from_request();

if (isset($_POST['delete'])) {
    $productId = (int) ($_POST['product_id'] ?? 0);
    $product = product_admin_get($conn, $productId);

    if (!$product) {
        product_admin_redirect('Product not found.', 'error', $returnQuery);
    }

    $productNo = (int) $product['product_no'];
    $orderCheck = mysqli_query($conn, "SELECT COUNT(*) FROM orders WHERE product_id = $productNo");
    $orderCount = $orderCheck ? (int) mysqli_fetch_row($orderCheck)[0] : 0;

    $stmt = $conn->prepare('DELETE FROM products WHERE product_id = ?');
    if (!$stmt) {
        product_admin_redirect('Failed to delete product.', 'error', $returnQuery);
    }

    $stmt->bind_param('i', $productId);
    $ok = $stmt->execute();
    $stmt->close();

    if (!$ok) {
        product_admin_redirect('Failed to delete product.', 'error', $returnQuery);
    }

    product_admin_delete_image_file($product['product_img']);
    $message = 'Product deleted successfully.';
    if ($orderCount > 0) {
        $message .= " ($orderCount past order record(s) still reference this item.)";
    }
    product_admin_redirect($message, 'success', $returnQuery);
}

if (isset($_POST['stock'])) {
    $productId = (int) ($_POST['product_id'] ?? 0);
    $qty = (int) ($_POST['product_qty'] ?? -1);

    if ($productId <= 0 || $qty < 0) {
        product_admin_redirect('Invalid stock update.', 'error', $returnQuery);
    }

    $stmt = $conn->prepare('UPDATE products SET product_qty = ? WHERE product_id = ?');
    if (!$stmt) {
        product_admin_redirect('Failed to update stock.', 'error', $returnQuery);
    }

    $stmt->bind_param('ii', $qty, $productId);
    $ok = $stmt->execute();
    $stmt->close();

    product_admin_redirect($ok ? 'Stock updated successfully.' : 'Failed to update stock.', $ok ? 'success' : 'error', $returnQuery);
}

if (isset($_POST['update'])) {
    $productId = (int) ($_POST['product_id'] ?? 0);
    $product = product_admin_get($conn, $productId);

    if (!$product) {
        product_admin_redirect('Product not found.', 'error', $returnQuery);
    }

    $errors = product_admin_validate_payload($_POST, true);
    if ($errors) {
        product_admin_redirect(implode(' ', $errors), 'error', $returnQuery);
    }

    $name = trim((string) $_POST['product_name']);
    $type = trim((string) $_POST['product_type']);
    $desc = trim((string) $_POST['product_desc']);
    $price = (int) $_POST['product_price'];
    $qty = (int) $_POST['product_qty'];
    $imageName = $product['product_img'];

    if (!empty($_FILES['product_img']['name'])) {
        $upload = product_admin_upload_image($_FILES['product_img']);
        if (!$upload['ok']) {
            product_admin_redirect($upload['message'], 'error', $returnQuery);
        }
        product_admin_delete_image_file($product['product_img']);
        $imageName = $upload['filename'];
    }

    $stmt = $conn->prepare(
        'UPDATE products SET product_name = ?, product_price = ?, product_type = ?, product_qty = ?, product_desc = ?, product_img = ? WHERE product_id = ?'
    );
    if (!$stmt) {
        product_admin_redirect('Failed to update product.', 'error', $returnQuery);
    }

    $stmt->bind_param('sisissi', $name, $price, $type, $qty, $desc, $imageName, $productId);
    $ok = $stmt->execute();
    $stmt->close();

    product_admin_redirect($ok ? 'Product updated successfully.' : 'Failed to update product.', $ok ? 'success' : 'error', $returnQuery);
}

product_admin_redirect('Invalid product action.', 'error', $returnQuery);
