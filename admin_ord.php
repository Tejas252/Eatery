<?php
include 'assets/php/config.php';
session_start();

$adminPageTitle = 'Deliver Orders';
$adminPageSubtitle = 'Manage accepted, preparing, and delivered orders';
$adminActiveNav = 'deliver';

require_once 'assets/php/admin_helpers.php';
require_once 'assets/php/order_helpers.php';

include 'assets/php/admin_header.php';

$rows = order_fetch_rows($conn, "status IN ('accepted', 'preparing', 'deliverd')");
$hasOrders = count($rows) > 0;
$previousBatchKey = null;
?>

<section class="admin-card">
  <div class="admin-card__header">
    <h2 class="admin-card__title">Active Orders</h2>
    <?php if ($hasOrders) : ?>
      <span class="admin-badge admin-badge--accepted"><?php echo count($rows); ?> items</span>
    <?php endif; ?>
  </div>
  <div class="admin-card__body">
    <?php if (!$hasOrders) : ?>
      <div class="admin-empty">
        <p class="admin-empty__title">No active orders</p>
        <p class="admin-empty__text">Accepted orders will appear here for preparation and delivery.</p>
      </div>
    <?php else : ?>
      <div class="admin-table-wrap">
        <table class="admin-table admin-table--active-orders">
          <thead>
            <tr>
              <th>Customer ID</th>
              <th>Product</th>
              <th>Qty</th>
              <th>Table</th>
              <th>Description</th>
              <th>Total</th>
              <th>Status</th>
              <th class="admin-table__actions-col">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($rows as $order) :
              $showActions = order_is_first_in_batch($order, $previousBatchKey);
              $previousBatchKey = order_batch_key($order);
              $transitions = order_allowed_transitions($order['status']);
              ?>
              <tr class="<?php echo $showActions ? 'admin-table__row--batch-start' : 'admin-table__row--batch-item'; ?>">
                <td data-label="Customer ID"><?php echo (int) $order['customer_id']; ?></td>
                <td data-label="Product"><?php echo htmlspecialchars(order_fetch_product_name($conn, (int) $order['product_id'])); ?></td>
                <td data-label="Qty"><?php echo (int) $order['qty']; ?></td>
                <td data-label="Table"><?php echo (int) $order['table_no']; ?></td>
                <td data-label="Description"><?php echo htmlspecialchars($order['order_desc'] ?: '—'); ?></td>
                <td data-label="Total">
                  <?php if ($showActions) : ?>
                    <?php echo admin_format_currency($order['total']); ?>
                  <?php else : ?>
                    <span class="admin-table__muted">—</span>
                  <?php endif; ?>
                </td>
                <td data-label="Status"><?php echo admin_status_badge($order['status']); ?></td>
                <td class="admin-table__actions-cell" data-label="Actions">
                  <?php if ($showActions && $transitions) :
                    render_admin_order_actions($order, $transitions, '../../admin_ord.php');
                  else : ?>
                    <span class="admin-table__muted">—</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php include 'assets/php/admin_footer.php'; ?>
