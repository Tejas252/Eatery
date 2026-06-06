<?php
include 'assets/php/config.php';
session_start();

$adminPageTitle = 'Accept Orders';
$adminPageSubtitle = 'Review pending orders and update their status';
$adminActiveNav = 'accept';

require_once 'assets/php/admin_helpers.php';

include 'assets/php/admin_header.php';

$qr = "SELECT * FROM orders WHERE status = 'ordered' ORDER BY customer_id ASC, order_id ASC";
$res = mysqli_query($conn, $qr);
$hasOrders = $res && mysqli_num_rows($res) > 0;
$nid = 1;
?>

<section class="admin-card">
  <div class="admin-card__header">
    <h2 class="admin-card__title">Pending Orders</h2>
    <?php if ($hasOrders) : ?>
      <span class="admin-badge admin-badge--pending"><?php echo (int) mysqli_num_rows($res); ?> items</span>
    <?php endif; ?>
  </div>
  <div class="admin-card__body">
    <?php if (!$hasOrders) : ?>
      <div class="admin-empty">
        <p class="admin-empty__title">No pending orders</p>
        <p class="admin-empty__text">New customer orders will appear here for acceptance.</p>
      </div>
    <?php else : ?>
      <div class="admin-table-wrap">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Customer ID</th>
              <th>Product</th>
              <th>Qty</th>
              <th>Table</th>
              <th>Description</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($orders = mysqli_fetch_assoc($res)) :
              $productName = admin_product_name($conn, (int) $orders['product_id']);
              $showActions = $orders['customer_id'] != $nid;
              ?>
              <tr>
                <td data-label="Customer ID"><?php echo (int) $orders['customer_id']; ?></td>
                <td data-label="Product"><?php echo htmlspecialchars($productName); ?></td>
                <td data-label="Qty"><?php echo (int) $orders['qty']; ?></td>
                <td data-label="Table"><?php echo (int) $orders['table_no']; ?></td>
                <td data-label="Description"><?php echo htmlspecialchars($orders['order_desc']); ?></td>
                <td data-label="Status"><?php echo admin_status_badge($orders['status']); ?></td>
                <td class="admin-table__actions-cell" data-label="Actions">
                  <?php if ($showActions) : ?>
                    <form action="assets/php/manage_order.php" method="post" class="admin-inline-form">
                      <select name="status" class="admin-select admin-select--sm" aria-label="Update order status">
                        <option value="accepted">Accepted</option>
                        <option value="done">Done</option>
                        <option value="Ordered">Ordered</option>
                      </select>
                      <input type="hidden" name="id" value="<?php echo (int) $orders['customer_id']; ?>">
                      <input type="hidden" name="order_id" value="<?php echo (int) $orders['order_id']; ?>">
                      <input type="hidden" name="table_no" value="<?php echo (int) $orders['table_no']; ?>">
                      <button type="submit" name="change" class="admin-btn admin-btn--primary admin-btn--sm">Update</button>
                    </form>
                  <?php else : ?>
                    <span class="admin-table__muted">—</span>
                  <?php endif; ?>
                </td>
              </tr>
              <?php $nid = $orders['customer_id']; ?>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php include 'assets/php/admin_footer.php'; ?>
