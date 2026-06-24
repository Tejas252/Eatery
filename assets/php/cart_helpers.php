<?php

function get_cart_item_count(): int
{
    if (empty($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        return 0;
    }

    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total += (int) ($item['qty'] ?? 0);
    }

    return $total;
}

function cart_json_response(bool $success, string $message, array $data = [], int $statusCode = 200): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array_merge([
        'success' => $success,
        'message' => $message,
    ], $data));
    exit;
}

function get_product_by_no(mysqli $conn, int $productNo): ?array
{
    $stmt = $conn->prepare('SELECT product_no, product_name, product_price, product_qty, product_img FROM products WHERE product_no = ? LIMIT 1');
    if (!$stmt) {
        return null;
    }

    $stmt->bind_param('i', $productNo);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();

    return $product ?: null;
}

function get_cart_qty_for_product(int $productNo): int
{
    if (empty($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        return 0;
    }

    foreach ($_SESSION['cart'] as $item) {
        if ((int) ($item['no'] ?? 0) === $productNo) {
            return (int) ($item['qty'] ?? 0);
        }
    }

    return 0;
}

function add_product_to_cart(mysqli $conn, int $productNo, int $qty): array
{
    $qty = max(1, min(10, $qty));
    $product = get_product_by_no($conn, $productNo);

    if (!$product) {
        return [
            'success' => false,
            'message' => 'Product not found.',
            'code' => 'not_found',
        ];
    }

    $stock = (int) $product['product_qty'];
    if ($stock <= 0) {
        return [
            'success' => false,
            'message' => 'Sorry, this item is out of stock.',
            'code' => 'out_of_stock',
        ];
    }

    $existingQty = get_cart_qty_for_product($productNo);
    $maxAllowed = min($stock, 10);
    $requestedTotal = $existingQty + $qty;

    if ($existingQty >= $maxAllowed) {
        return [
            'success' => false,
            'message' => 'Maximum available quantity is already in your cart.',
            'code' => 'max_in_cart',
            'available' => 0,
        ];
    }

    if ($requestedTotal > $maxAllowed) {
        $available = $maxAllowed - $existingQty;
        return [
            'success' => false,
            'message' => 'Only ' . $available . ' more can be added (' . $stock . ' in stock).',
            'code' => 'insufficient_stock',
            'available' => $available,
        ];
    }

    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $updated = false;
    foreach ($_SESSION['cart'] as $key => $item) {
        if ((int) ($item['no'] ?? 0) === $productNo) {
            $_SESSION['cart'][$key]['qty'] = $requestedTotal;
            $updated = true;
            break;
        }
    }

    if (!$updated) {
        $_SESSION['cart'][] = [
            'no' => $productNo,
            'qty' => $qty,
        ];
    }

    unset($_SESSION['ordered']);

    return [
        'success' => true,
        'message' => $updated ? 'Cart updated successfully.' : 'Added to cart successfully.',
        'code' => $updated ? 'updated' : 'added',
        'count' => get_cart_item_count(),
        'product_name' => $product['product_name'],
        'qty' => $updated ? $requestedTotal : $qty,
    ];
}

function remove_product_from_cart(int $productNo): bool
{
    if (empty($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        return false;
    }

    foreach ($_SESSION['cart'] as $key => $item) {
        if ((int) ($item['no'] ?? 0) === $productNo) {
            unset($_SESSION['cart'][$key]);
            $_SESSION['cart'] = array_values($_SESSION['cart']);
            return true;
        }
    }

    return false;
}
