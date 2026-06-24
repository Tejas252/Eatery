<?php

/** Order status constants (DB values — deliverd kept for backward compatibility). */
function order_statuses(): array
{
    return ['ordered', 'accepted', 'preparing', 'deliverd', 'done', 'cancelled'];
}

function order_status_priority(string $status): int
{
    $map = [
        'cancelled' => 0,
        'ordered' => 1,
        'accepted' => 2,
        'preparing' => 3,
        'deliverd' => 4,
        'delivered' => 4,
        'done' => 5,
    ];

    return $map[strtolower(trim($status))] ?? 0;
}

function order_normalize_status(string $status): string
{
    $key = strtolower(trim($status));
    if ($key === 'delivered') {
        return 'deliverd';
    }
    if ($key === 'ordered' || $key === 'pending') {
        return 'ordered';
    }
    return $key;
}

function order_status_meta(string $status): array
{
    $key = order_normalize_status($status);
    $map = [
        'ordered' => ['label' => 'New Order', 'class' => 'pending', 'admin_class' => 'pending'],
        'accepted' => ['label' => 'Accepted', 'class' => 'processing', 'admin_class' => 'accepted'],
        'preparing' => ['label' => 'Preparing', 'class' => 'processing', 'admin_class' => 'accepted'],
        'deliverd' => ['label' => 'Delivered', 'class' => 'processing', 'admin_class' => 'delivering'],
        'done' => ['label' => 'Completed', 'class' => 'completed', 'admin_class' => 'done'],
        'cancelled' => ['label' => 'Cancelled', 'class' => 'cancelled', 'admin_class' => 'danger'],
    ];

    return $map[$key] ?? ['label' => ucfirst($key), 'class' => 'pending', 'admin_class' => 'default'];
}

function order_allowed_transitions(string $currentStatus): array
{
    $current = order_normalize_status($currentStatus);
    $map = [
        'ordered' => [
            'accepted' => 'Accept order',
            'cancelled' => 'Cancel order',
        ],
        'accepted' => [
            'preparing' => 'Start preparing',
            'deliverd' => 'Mark as delivered',
        ],
        'preparing' => [
            'deliverd' => 'Mark as delivered',
        ],
        'deliverd' => [
            'done' => 'Complete order',
        ],
    ];

    return $map[$current] ?? [];
}

function order_admin_redirect_for_status(string $newStatus): string
{
    $status = order_normalize_status($newStatus);

    if ($status === 'ordered') {
        return '../../admin.php';
    }
    if (in_array($status, ['accepted', 'preparing', 'deliverd'], true)) {
        return '../../admin_ord.php';
    }
    if ($status === 'done' || $status === 'cancelled') {
        return '../../admin_ord.php';
    }

    return '../../admin.php';
}

function order_admin_flash_message(string $newStatus): string
{
    $meta = order_status_meta($newStatus);
    return 'Order updated to "' . $meta['label'] . '" successfully.';
}

/** Statuses that still block a table from being marked available in admin. */
function order_table_active_statuses(): array
{
    return ['ordered', 'accepted', 'preparing'];
}

function order_table_has_active_orders(mysqli $conn, int $tableNo): bool
{
    if ($tableNo <= 0) {
        return false;
    }

    $stmt = $conn->prepare(
        "SELECT COUNT(*) FROM orders
         WHERE table_no = ?
           AND LOWER(TRIM(status)) IN ('ordered', 'accepted', 'preparing', 'pending')"
    );
    if (!$stmt) {
        return false;
    }

    $stmt->bind_param('i', $tableNo);
    $stmt->execute();
    $count = (int) ($stmt->get_result()->fetch_row()[0] ?? 0);
    $stmt->close();

    return $count > 0;
}

function order_resolve_table_no(mysqli $conn, int $customerId, int $fromOrderId, ?string $orderTime = null): int
{
    if ($orderTime !== null) {
        $stmt = $conn->prepare(
            'SELECT table_no FROM orders WHERE customer_id = ? AND order_id >= ? AND oreder_time = ? ORDER BY order_id ASC LIMIT 1'
        );
        if (!$stmt) {
            return 0;
        }
        $stmt->bind_param('iis', $customerId, $fromOrderId, $orderTime);
    } else {
        $stmt = $conn->prepare(
            'SELECT table_no FROM orders WHERE customer_id = ? AND order_id >= ? ORDER BY order_id ASC LIMIT 1'
        );
        if (!$stmt) {
            return 0;
        }
        $stmt->bind_param('ii', $customerId, $fromOrderId);
    }

    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    return $row ? (int) $row['table_no'] : 0;
}

function order_fetch_product_name(mysqli $conn, int $productRef): string
{
    $productRef = (int) $productRef;
    if ($productRef <= 0) {
        return 'Unknown product';
    }

    $stmt = $conn->prepare(
        'SELECT product_name FROM products WHERE product_no = ? OR product_id = ? LIMIT 1'
    );
    if (!$stmt) {
        return 'Unknown product';
    }

    $stmt->bind_param('ii', $productRef, $productRef);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result ? $result->fetch_assoc() : null;
    $stmt->close();

    return $row['product_name'] ?? 'Unknown product';
}

function order_fetch_rows(mysqli $conn, string $whereSql, string $orderSql = 'ORDER BY customer_id ASC, order_id ASC'): array
{
    $query = "SELECT * FROM orders WHERE $whereSql $orderSql";
    $result = mysqli_query($conn, $query);
    if (!$result) {
        return [];
    }

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function order_batch_key(array $row): string
{
    return (int) $row['customer_id'] . '|' . $row['oreder_time'] . '|' . (int) $row['table_no'];
}

function order_is_first_in_customer_batch(array $row, ?int $previousCustomerId): bool
{
    return $previousCustomerId === null || (int) $row['customer_id'] !== $previousCustomerId;
}

function order_is_first_in_batch(array $row, ?string $previousBatchKey): bool
{
    return $previousBatchKey === null || order_batch_key($row) !== $previousBatchKey;
}

function order_update_batch_status(
    mysqli $conn,
    int $customerId,
    int $fromOrderId,
    string $newStatus,
    ?int $tableNo = null,
    ?string $orderTime = null
): bool {
    $newStatus = order_normalize_status($newStatus);
    $customerId = (int) $customerId;
    $fromOrderId = (int) $fromOrderId;

    if ($orderTime !== null) {
        $orderTimeEsc = mysqli_real_escape_string($conn, $orderTime);
        $stmt = $conn->prepare(
            "UPDATE orders SET status = ? WHERE customer_id = ? AND order_id >= ? AND oreder_time = ? AND status NOT IN ('done', 'cancelled')"
        );
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param('siis', $newStatus, $customerId, $fromOrderId, $orderTimeEsc);
    } else {
        $stmt = $conn->prepare(
            "UPDATE orders SET status = ? WHERE customer_id = ? AND order_id >= ? AND status NOT IN ('done', 'cancelled')"
        );
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param('sii', $newStatus, $customerId, $fromOrderId);
    }

    $ok = $stmt->execute();
    $stmt->close();

    if (!$ok) {
        return false;
    }

    return true;
}

function order_validate_transition(string $currentStatus, string $newStatus): bool
{
    $current = order_normalize_status($currentStatus);
    $new = order_normalize_status($newStatus);
    $allowed = order_allowed_transitions($current);

    return isset($allowed[$new]);
}

function order_get_batch_current_status(mysqli $conn, int $customerId, int $fromOrderId): ?string
{
    $customerId = (int) $customerId;
    $fromOrderId = (int) $fromOrderId;
    $result = mysqli_query(
        $conn,
        "SELECT status FROM orders WHERE customer_id = $customerId AND order_id >= $fromOrderId ORDER BY order_id ASC LIMIT 1"
    );
    if (!$result) {
        return null;
    }
    $row = mysqli_fetch_assoc($result);

    return $row ? order_normalize_status($row['status']) : null;
}

function order_action_button_variant(string $status): string
{
    $key = order_normalize_status($status);
    $map = [
        'accepted' => 'success',
        'cancelled' => 'danger',
        'preparing' => 'primary',
        'deliverd' => 'primary',
        'done' => 'success',
    ];

    return $map[$key] ?? 'ghost';
}

function order_action_short_label(string $status, string $fallback): string
{
    $key = order_normalize_status($status);
    $map = [
        'accepted' => 'Accept',
        'cancelled' => 'Cancel',
        'preparing' => 'Prepare',
        'deliverd' => 'Delivered',
        'done' => 'Complete',
    ];

    return $map[$key] ?? $fallback;
}

function order_action_icon(string $status): string
{
    $key = order_normalize_status($status);
    $icons = [
        'accepted' => '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12l4 4L19 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        'cancelled' => '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>',
        'preparing' => '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3v18M3 12h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>',
        'deliverd' => '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M3 7h11v8H3zM14 10h4l3 3v2h-7V10z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/></svg>',
        'done' => '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.5"/><path d="M8 12l2.5 2.5L16 9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
    ];

    return $icons[$key] ?? '';
}

function render_admin_order_actions(array $order, array $transitions, string $returnTo): void
{
    if (!$transitions) {
        echo '<span class="admin-table__muted">—</span>';
        return;
    }

    echo '<div class="admin-order-actions">';

    foreach ($transitions as $value => $label) {
        $variant = order_action_button_variant($value);
        $shortLabel = order_action_short_label($value, $label);
        $icon = order_action_icon($value);
        ?>
        <form
          action="assets/php/manage_order.php"
          method="post"
          class="admin-order-actions__form"
        >
          <input type="hidden" name="status" value="<?php echo htmlspecialchars($value); ?>">
          <input type="hidden" name="id" value="<?php echo (int) $order['customer_id']; ?>">
          <input type="hidden" name="order_id" value="<?php echo (int) $order['order_id']; ?>">
          <input type="hidden" name="table_no" value="<?php echo (int) $order['table_no']; ?>">
          <input type="hidden" name="order_time" value="<?php echo htmlspecialchars($order['oreder_time']); ?>">
          <input type="hidden" name="return_to" value="<?php echo htmlspecialchars($returnTo); ?>">
          <button
            type="submit"
            name="change"
            class="admin-btn admin-btn--sm admin-btn--<?php echo htmlspecialchars($variant); ?>"
            title="<?php echo htmlspecialchars($label); ?>"
          >
            <?php echo $icon; ?>
            <span><?php echo htmlspecialchars($shortLabel); ?></span>
          </button>
        </form>
        <?php
    }

    echo '</div>';
}
