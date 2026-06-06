<?php
include 'assets/php/config.php';
session_start();

$adminPageTitle = 'Customers';
$adminPageSubtitle = 'View registered customer accounts';
$adminActiveNav = 'customers';

require_once 'assets/php/admin_helpers.php';

$qr = 'SELECT cust_id, cust_username, cust_name, cust_mobile, cust_email FROM customer ORDER BY cust_id ASC';
$res = mysqli_query($conn, $qr);
$hasCustomers = $res && mysqli_num_rows($res) > 0;

include 'assets/php/admin_header.php';
?>

<section class="admin-card">
  <div class="admin-card__header">
    <h2 class="admin-card__title">Registered Customers</h2>
    <?php if ($hasCustomers) : ?>
      <span class="admin-badge admin-badge--default"><?php echo (int) mysqli_num_rows($res); ?> users</span>
    <?php endif; ?>
  </div>
  <div class="admin-card__body">
    <?php if (!$hasCustomers) : ?>
      <div class="admin-empty">
        <p class="admin-empty__title">No customers yet</p>
        <p class="admin-empty__text">Registered users will appear here.</p>
      </div>
    <?php else : ?>
      <div class="admin-table-wrap">
        <table class="admin-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Username</th>
              <th>Name</th>
              <th>Mobile</th>
              <th>Email</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($customer = mysqli_fetch_assoc($res)) : ?>
              <tr>
                <td data-label="ID"><?php echo (int) $customer['cust_id']; ?></td>
                <td data-label="Username"><?php echo htmlspecialchars($customer['cust_username']); ?></td>
                <td data-label="Name"><?php echo htmlspecialchars($customer['cust_name'] ?? '—'); ?></td>
                <td data-label="Mobile"><?php echo htmlspecialchars((string) ($customer['cust_mobile'] ?? '—')); ?></td>
                <td data-label="Email"><?php echo htmlspecialchars($customer['cust_email']); ?></td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php include 'assets/php/admin_footer.php'; ?>
