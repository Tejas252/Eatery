<?php
include 'assets/php/config.php';
session_start();

$adminPageTitle = 'Order History';
$adminPageSubtitle = 'Search and browse all past orders';
$adminActiveNav = 'history';

require_once 'assets/php/admin_helpers.php';

$isSearch = $_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search']) && isset($_GET['cid']);
$searchCid = $isSearch ? (int) $_GET['cid'] : 0;

if ($isSearch) {
    $qr = "SELECT * FROM orders WHERE customer_id = $searchCid ORDER BY oreder_time DESC";
} else {
    $qr = 'SELECT * FROM orders ORDER BY oreder_time DESC';
}

$res = mysqli_query($conn, $qr);
$hasOrders = $res && mysqli_num_rows($res) > 0;

include 'assets/php/admin_header.php';
?>

<section class="admin-card">
  <div class="admin-card__header">
    <h2 class="admin-card__title">
      <?php echo $isSearch ? 'Search Results · Customer #' . $searchCid : 'All Orders'; ?>
    </h2>
    <form action="" method="get" class="admin-search">
      <input
        type="number"
        name="cid"
        class="admin-input"
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
        <table class="admin-table">
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
            <?php while ($orders = mysqli_fetch_assoc($res)) :
              $productName = admin_product_name($conn, (int) $orders['product_id']);
              ?>
              <tr>
                <td data-label="Customer ID"><?php echo (int) $orders['customer_id']; ?></td>
                <td data-label="Product"><?php echo htmlspecialchars($productName); ?></td>
                <td data-label="Qty"><?php echo (int) $orders['qty']; ?></td>
                <td data-label="Table"><?php echo (int) $orders['table_no']; ?></td>
                <td data-label="Description"><?php echo htmlspecialchars($orders['order_desc']); ?></td>
                <td data-label="Status"><?php echo admin_status_badge($orders['status']); ?></td>
                <td data-label="Time"><?php echo htmlspecialchars($orders['oreder_time']); ?></td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php include 'assets/php/admin_footer.php'; ?>
