<?php

function admin_nav_items(): array
{
    return [
        'dashboard' => [
            'label' => 'Dashboard',
            'url' => 'admin_dashboard.php',
            'icon' => 'grid',
        ],
        'add_items' => [
            'label' => 'Add Items',
            'url' => 'data_insert.php',
            'icon' => 'plus',
        ],
        'accept' => [
            'label' => 'Accept Orders',
            'url' => 'admin.php',
            'icon' => 'inbox',
        ],
        'deliver' => [
            'label' => 'Deliver Orders',
            'url' => 'admin_ord.php',
            'icon' => 'truck',
        ],
        'history' => [
            'label' => 'Order History',
            'url' => 'admin_history.php',
            'icon' => 'clock',
        ],
        'products' => [
            'label' => 'Products',
            'url' => 'products.php',
            'icon' => 'box',
        ],
        'reservations' => [
            'label' => 'Table Reservations',
            'url' => 'admin_reservations.php',
            'icon' => 'calendar',
        ],
        'customers' => [
            'label' => 'Customers',
            'url' => 'admin_customers.php',
            'icon' => 'users',
        ],
    ];
}

function admin_user_label(): string
{
    if (!empty($_SESSION['name'])) {
        return (string) $_SESSION['name'];
    }
    if (!empty($_SESSION['username'])) {
        return (string) $_SESSION['username'];
    }
    return 'Administrator';
}

function admin_user_initial(): string
{
    return strtoupper(substr(admin_user_label(), 0, 1));
}

function admin_count_query(mysqli $conn, string $sql): int
{
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        return 0;
    }
    $row = mysqli_fetch_row($result);
    return (int) ($row[0] ?? 0);
}

function admin_dashboard_stats(mysqli $conn): array
{
    return [
        'pending' => admin_count_query($conn, "SELECT COUNT(*) FROM orders WHERE status = 'ordered'"),
        'accepted' => admin_count_query($conn, "SELECT COUNT(*) FROM orders WHERE status = 'accepted'"),
        'delivering' => admin_count_query($conn, "SELECT COUNT(*) FROM orders WHERE status = 'deliverd'"),
        'completed' => admin_count_query($conn, "SELECT COUNT(*) FROM orders WHERE status = 'done'"),
        'products' => admin_count_query($conn, 'SELECT COUNT(*) FROM products'),
        'customers' => admin_count_query($conn, 'SELECT COUNT(*) FROM customer'),
        'tables' => admin_count_query($conn, "SELECT COUNT(*) FROM book_table WHERE table_status = 'non'"),
    ];
}

function admin_product_name(mysqli $conn, int $productId): string
{
    $productId = (int) $productId;
    $result = mysqli_query($conn, "SELECT product_name FROM products WHERE product_id = $productId LIMIT 1");
    if (!$result) {
        return 'Unknown product';
    }
    $row = mysqli_fetch_assoc($result);
    return $row['product_name'] ?? 'Unknown product';
}

function admin_status_badge(string $status): string
{
    $key = strtolower(trim($status));
    $map = [
        'ordered' => ['label' => 'Pending', 'class' => 'pending'],
        'accepted' => ['label' => 'Accepted', 'class' => 'accepted'],
        'deliverd' => ['label' => 'Delivering', 'class' => 'delivering'],
        'delivered' => ['label' => 'Delivered', 'class' => 'delivering'],
        'done' => ['label' => 'Completed', 'class' => 'done'],
    ];
    $meta = $map[$key] ?? ['label' => ucfirst($status), 'class' => 'default'];

    return '<span class="admin-badge admin-badge--' . htmlspecialchars($meta['class']) . '">'
        . htmlspecialchars($meta['label']) . '</span>';
}

function admin_nav_icon(string $icon): string
{
    $icons = [
        'grid' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="3" y="3" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.5"/></svg>',
        'plus' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>',
        'inbox' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 4h16v16H4zM4 9h16M9 14h6" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/></svg>',
        'truck' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M3 7h11v8H3zM14 10h4l3 3v2h-7v-5zM7 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3zM18 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/></svg>',
        'clock' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.5"/><path d="M12 7v5l3 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>',
        'box' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 7l8-4 8 4-8 4-8-4zM4 7v10l8 4 8-4V7" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/></svg>',
        'calendar' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2" stroke="currentColor" stroke-width="1.5"/><path d="M8 3v4M16 3v4M3 10h18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>',
        'users' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="9" cy="8" r="3" stroke="currentColor" stroke-width="1.5"/><path d="M3 19c0-3 2.5-5 6-5s6 2 6 5M16 8a3 3 0 110 6M19 19c0-2.2-1.8-4-4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>',
    ];

    return $icons[$icon] ?? $icons['grid'];
}

function admin_stock_badge(int $qty): string
{
    if ($qty <= 0) {
        return '<span class="admin-badge admin-badge--danger">Out of stock</span>';
    }
    if ($qty <= 10) {
        return '<span class="admin-badge admin-badge--warning">Low · ' . $qty . '</span>';
    }
    return '<span class="admin-badge admin-badge--success">In stock · ' . $qty . '</span>';
}

function admin_table_status_badge(string $status): string
{
    $key = strtolower(trim($status));
    if ($key === 'non' || $key === 'available') {
        return '<span class="admin-badge admin-badge--success">Available</span>';
    }
    if ($key === 'booked' || $key === 'occupied') {
        return '<span class="admin-badge admin-badge--warning">Occupied</span>';
    }
    return '<span class="admin-badge admin-badge--default">' . htmlspecialchars(ucfirst($status)) . '</span>';
}

function admin_format_currency($amount): string
{
    return '$' . number_format((float) $amount, 2);
}
