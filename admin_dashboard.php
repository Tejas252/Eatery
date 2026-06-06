<?php
include 'assets/php/config.php';
session_start();

$adminPageTitle = 'Dashboard';
$adminPageSubtitle = 'Overview of orders, products, and reservations';
$adminActiveNav = 'dashboard';

require_once 'assets/php/admin_helpers.php';
$stats = admin_dashboard_stats($conn);

include 'assets/php/admin_header.php';
?>

<section class="admin-stats" aria-label="Key metrics">
  <article class="admin-stat-card admin-stat-card--accent">
    <span class="admin-stat-card__label">Pending Orders</span>
    <span class="admin-stat-card__value"><?php echo (int) $stats['pending']; ?></span>
    <span class="admin-stat-card__hint">Awaiting acceptance</span>
  </article>
  <article class="admin-stat-card">
    <span class="admin-stat-card__label">Accepted</span>
    <span class="admin-stat-card__value"><?php echo (int) $stats['accepted']; ?></span>
    <span class="admin-stat-card__hint">Ready for delivery</span>
  </article>
  <article class="admin-stat-card">
    <span class="admin-stat-card__label">Delivering</span>
    <span class="admin-stat-card__value"><?php echo (int) $stats['delivering']; ?></span>
    <span class="admin-stat-card__hint">Out for delivery</span>
  </article>
  <article class="admin-stat-card">
    <span class="admin-stat-card__label">Completed</span>
    <span class="admin-stat-card__value"><?php echo (int) $stats['completed']; ?></span>
    <span class="admin-stat-card__hint">Finished orders</span>
  </article>
</section>

<section class="admin-stats" aria-label="Inventory metrics">
  <article class="admin-stat-card">
    <span class="admin-stat-card__label">Products</span>
    <span class="admin-stat-card__value"><?php echo (int) $stats['products']; ?></span>
    <span class="admin-stat-card__hint">In catalog</span>
  </article>
  <article class="admin-stat-card">
    <span class="admin-stat-card__label">Customers</span>
    <span class="admin-stat-card__value"><?php echo (int) $stats['customers']; ?></span>
    <span class="admin-stat-card__hint">Registered users</span>
  </article>
  <article class="admin-stat-card">
    <span class="admin-stat-card__label">Available Tables</span>
    <span class="admin-stat-card__value"><?php echo (int) $stats['tables']; ?></span>
    <span class="admin-stat-card__hint">Open for booking</span>
  </article>
</section>

<section class="admin-card">
  <div class="admin-card__header">
    <h2 class="admin-card__title">Quick Actions</h2>
  </div>
  <div class="admin-card__body admin-card__body--padded">
    <div class="admin-quick-links">
      <a href="admin.php" class="admin-quick-link">
        <strong>Accept Orders</strong>
        <span>Review and approve incoming orders</span>
      </a>
      <a href="admin_ord.php" class="admin-quick-link">
        <strong>Deliver Orders</strong>
        <span>Update delivery and completion status</span>
      </a>
      <a href="data_insert.php" class="admin-quick-link">
        <strong>Add Product</strong>
        <span>Add new items to the menu</span>
      </a>
      <a href="products.php" class="admin-quick-link">
        <strong>Manage Products</strong>
        <span>View inventory and stock levels</span>
      </a>
      <a href="admin_history.php" class="admin-quick-link">
        <strong>Order History</strong>
        <span>Search and browse past orders</span>
      </a>
      <a href="admin_reservations.php" class="admin-quick-link">
        <strong>Table Reservations</strong>
        <span>Monitor table availability</span>
      </a>
    </div>
  </div>
</section>

<?php include 'assets/php/admin_footer.php'; ?>
