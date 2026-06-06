<?php
include 'assets/php/config.php';
session_start();

$adminPageTitle = 'Order History';
$adminPageSubtitle = 'Search and browse all past orders';
$adminActiveNav = 'history';

require_once 'assets/php/admin_helpers.php';
require_once 'assets/php/order_helpers.php';

$isSearch = $_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search']) && isset($_GET['cid']);
$searchCid = $isSearch ? (int) $_GET['cid'] : 0;

if ($isSearch) {
    $rows = order_fetch_rows($conn, "customer_id = $searchCid", 'ORDER BY oreder_time DESC, order_id DESC');
} else {
    $rows = order_fetch_rows($conn, '1=1', 'ORDER BY oreder_time DESC, order_id DESC');
}

$hasOrders = count($rows) > 0;

include 'assets/php/admin_header.php';
?>

<section class="admin-card">
  <div class="admin-card__header admin-card__header--stack">
    <h2 class="admin-card__title">
      <?php echo $isSearch ? 'Search Results · Customer #' . $searchCid : 'All Orders'; ?>
    </h2>
    <form action="" method="get" class="admin-search admin-search--inline">
      <input
        type="number"
        name="cid"
        class="admin-input admin-input--search"
        placeholder="Customer ID"
        value="<?php echo $isSearch ? $searchCid : ''; ?>"
        min="1"
        required
        aria-label="Customer ID"
      >
      <button type="submit" name="search" value="1" class="admin-btn admin-btn--primary admin-btn--sm">Search</button>
      <?php if ($isSearch) : ?>
        <a href="admin_history.php" class="admin-btn admin-btn--ghost admin-btn--sm">Clear</a>
      <?php endif; ?>
    </form>
  </div>
  <div class="admin-card__body">
    <?php if ($isSearch && !$hasOrders) : ?>
      <div class="admin-empty">
        <p class="admin-empty__title">No orders found</p>
        <p class="admin-empty__text">No order history exists for customer #<?php echo $searchCid; ?>.</p>
      </div>
    <?php elseif (!$hasOrders) : ?>
      <div class="admin-empty">
        <p class="admin-empty__title">No order history</p>
        <p class="admin-empty__text">Completed and past orders will appear here.</p>
      </div>
    <?php else : ?>
      <div class="admin-table-wrap">
        <table class="admin-table admin-table--history">
          <thead>
            <tr>
              <th>Customer ID</th>
              <th>Product</th>
              <th>Qty</th>
              <th>Table</th>
              <th>Description</th>
              <th>Status</th>
              <th>Time</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($rows as $order) : ?>
              <tr>
                <td data-label="Customer ID"><?php echo (int) $order['customer_id']; ?></td>
                <td data-label="Product"><?php echo htmlspecialchars(order_fetch_product_name($conn, (int) $order['product_id'])); ?></td>
                <td data-label="Qty"><?php echo (int) $order['qty']; ?></td>
                <td data-label="Table"><?php echo (int) $order['table_no']; ?></td>
                <td data-label="Description"><?php echo htmlspecialchars($order['order_desc']); ?></td>
                <td data-label="Status"><?php echo admin_status_badge($order['status']); ?></td>
                <td data-label="Time"><?php echo htmlspecialchars($order['oreder_time']); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php include 'assets/php/admin_footer.php'; ?>
