<?php
include 'assets/php/config.php';
session_start();

$adminPageTitle = 'Table Reservations';
$adminPageSubtitle = 'Monitor table availability and capacity';
$adminActiveNav = 'reservations';

require_once 'assets/php/admin_helpers.php';

$qr = 'SELECT table_no, table_size, table_status FROM book_table ORDER BY table_no ASC';
$res = mysqli_query($conn, $qr);
$hasTables = $res && mysqli_num_rows($res) > 0;

$available = admin_count_query($conn, "SELECT COUNT(*) FROM book_table WHERE table_status = 'non'");
$occupied = $hasTables ? mysqli_num_rows($res) - $available : 0;

include 'assets/php/admin_header.php';
?>

<section class="admin-stats">
  <article class="admin-stat-card admin-stat-card--accent">
    <span class="admin-stat-card__label">Available</span>
    <span class="admin-stat-card__value"><?php echo (int) $available; ?></span>
    <span class="admin-stat-card__hint">Ready to book</span>
  </article>
  <article class="admin-stat-card">
    <span class="admin-stat-card__label">Occupied</span>
    <span class="admin-stat-card__value"><?php echo (int) $occupied; ?></span>
    <span class="admin-stat-card__hint">Currently in use</span>
  </article>
  <article class="admin-stat-card">
    <span class="admin-stat-card__label">Total Tables</span>
    <span class="admin-stat-card__value"><?php echo $hasTables ? (int) mysqli_num_rows($res) : 0; ?></span>
    <span class="admin-stat-card__hint">In restaurant</span>
  </article>
</section>

<section class="admin-card">
  <div class="admin-card__header">
    <h2 class="admin-card__title">All Tables</h2>
  </div>
  <div class="admin-card__body">
    <?php if (!$hasTables) : ?>
      <div class="admin-empty">
        <p class="admin-empty__title">No tables configured</p>
        <p class="admin-empty__text">Table data will appear here once configured in the database.</p>
      </div>
    <?php else : ?>
      <?php mysqli_data_seek($res, 0); ?>
      <div class="admin-table-wrap">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Table No</th>
              <th>Capacity</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($table = mysqli_fetch_assoc($res)) : ?>
              <tr>
                <td data-label="Table No"><?php echo (int) $table['table_no']; ?></td>
                <td data-label="Capacity"><?php echo (int) $table['table_size']; ?> guests</td>
                <td data-label="Status"><?php echo admin_table_status_badge($table['table_status']); ?></td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php include 'assets/php/admin_footer.php'; ?>
