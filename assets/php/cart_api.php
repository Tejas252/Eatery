<?php

session_start();

require_once __DIR__ . '/cart_helpers.php';

$conn = mysqli_connect('localhost', 'root', '', 'eatery');
if (!$conn) {
    cart_json_response(false, 'Unable to connect to the server.', [], 500);
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

if ($action === 'count') {
    cart_json_response(true, 'Cart count retrieved.', [
        'count' => get_cart_item_count(),
    ]);
}

if ($action === 'add') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        cart_json_response(false, 'Invalid request method.', [], 405);
    }

    $productNo = isset($_POST['product_no']) ? (int) $_POST['product_no'] : 0;
    $qty = isset($_POST['qty']) ? (int) $_POST['qty'] : 1;

    if ($productNo <= 0) {
        cart_json_response(false, 'Invalid product.', ['code' => 'invalid_product'], 400);
    }

    $result = add_product_to_cart($conn, $productNo, $qty);

    if (!$result['success']) {
        cart_json_response(false, $result['message'], [
            'code' => $result['code'] ?? 'error',
            'available' => $result['available'] ?? null,
            'count' => get_cart_item_count(),
        ], 400);
    }

    cart_json_response(true, $result['message'], [
        'code' => $result['code'],
        'count' => $result['count'],
        'product_name' => $result['product_name'],
        'qty' => $result['qty'],
    ]);
}

if ($action === 'remove') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        cart_json_response(false, 'Invalid request method.', [], 405);
    }

    $productNo = isset($_POST['product_no']) ? (int) $_POST['product_no'] : 0;
    if ($productNo <= 0) {
        cart_json_response(false, 'Invalid product.', [], 400);
    }

    if (!remove_product_from_cart($productNo)) {
        cart_json_response(false, 'Item not found in cart.', [], 404);
    }

    cart_json_response(true, 'Item removed from cart.', [
        'count' => get_cart_item_count(),
    ]);
}

cart_json_response(false, 'Unknown action.', [], 400);
