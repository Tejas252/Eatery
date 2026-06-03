<?php

function history_status_meta(string $status): array
{
    $status = strtolower(trim($status));

    $map = [
        'ordered' => ['label' => 'Pending', 'class' => 'pending'],
        'accepted' => ['label' => 'Processing', 'class' => 'processing'],
        'deliverd' => ['label' => 'Processing', 'class' => 'processing'],
        'done' => ['label' => 'Completed', 'class' => 'completed'],
        'cancelled' => ['label' => 'Cancelled', 'class' => 'cancelled'],
    ];

    return $map[$status] ?? ['label' => ucfirst($status), 'class' => 'pending'];
}

function history_payment_meta(string $status): array
{
    $status = strtolower(trim($status));

    if ($status === 'done') {
        return ['label' => 'Paid', 'class' => 'paid'];
    }

    if ($status === 'cancelled') {
        return ['label' => 'Refunded', 'class' => 'refunded'];
    }

    return ['label' => 'Pending', 'class' => 'pending'];
}

function fetch_customer_orders(mysqli $conn, int $customerId): array
{
    $stmt = $conn->prepare(
        'SELECT o.order_id, o.product_id, o.qty, o.order_desc, o.table_no,
                o.oreder_time, o.status, o.total,
                p.product_name, p.product_price, p.product_img, p.product_type
         FROM orders o
         LEFT JOIN products p ON p.product_no = o.product_id
         WHERE o.customer_id = ?
         ORDER BY o.oreder_time DESC, o.order_id DESC'
    );

    if (!$stmt) {
        return [];
    }

    $stmt->bind_param('i', $customerId);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    $stmt->close();

    return $rows;
}

function group_order_rows(array $rows): array
{
    $groups = [];

    foreach ($rows as $row) {
        $key = $row['oreder_time'] . '|' . $row['table_no'] . '|' . strtolower($row['status']);

        if (!isset($groups[$key])) {
            $groups[$key] = [
                'order_number' => (int) $row['order_id'],
                'order_time' => $row['oreder_time'],
                'table_no' => (int) $row['table_no'],
                'status' => $row['status'],
                'total' => (int) $row['total'],
                'items' => [],
            ];
        }

        $groups[$key]['order_number'] = min($groups[$key]['order_number'], (int) $row['order_id']);

        $price = (int) ($row['product_price'] ?? 0);
        $qty = (int) ($row['qty'] ?? 0);

        $groups[$key]['items'][] = [
            'order_id' => (int) $row['order_id'],
            'name' => $row['product_name'] ?? 'Unknown item',
            'qty' => $qty,
            'price' => $price,
            'line_total' => $price * $qty,
            'desc' => $row['order_desc'] ?? '',
            'img' => $row['product_img'] ?? '',
            'type' => $row['product_type'] ?? '',
        ];
    }

    return array_values($groups);
}

function format_order_date(string $datetime): string
{
    $time = strtotime($datetime);
    if ($time === false) {
        return $datetime;
    }
    return date('M j, Y · g:i A', $time);
}
