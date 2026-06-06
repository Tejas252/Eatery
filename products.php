<?php
include 'assets/php/config.php';
session_start();

$adminPageTitle = 'Products';
$adminPageSubtitle = 'Manage menu inventory and stock levels';
$adminActiveNav = 'products';

require_once 'assets/php/admin_helpers.php';

$qr = 'SELECT * FROM products ORDER BY product_id ASC';
$res = mysqli_query($conn, $qr);
$hasProducts = $res && mysqli_num_rows($res) > 0;

include 'assets/php/admin_header.php';
?>

<section class="admin-card">
  <div class="admin-card__header">
    <h2 class="admin-card__title">Product Inventory</h2>
    <a href="data_insert.php" class="admin-btn admin-btn--primary admin-btn--sm">Add Product</a>
  </div>
  <div class="admin-card__body">
    <?php if (!$hasProducts) : ?>
      <div class="admin-empty">
        <p class="admin-empty__title">No products yet</p>
        <p class="admin-empty__text">Add your first menu item to get started.</p>
        <a href="data_insert.php" class="admin-btn admin-btn--primary" style="margin-top:1rem;">Add Product</a>
      </div>
    <?php else : ?>
      <div class="admin-table-wrap">
        <table class="admin-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Image</th>
              <th>Name</th>
              <th>Category</th>
              <th>Price</th>
              <th>Stock</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($product = mysqli_fetch_assoc($res)) : ?>
              <tr>
                <td data-label="ID"><?php echo (int) $product['product_id']; ?></td>
                <td data-label="Image">
                  <img
                    src="assets/uploads/<?php echo htmlspecialchars($product['product_img']); ?>"
                    alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                    class="admin-table__product-img"
                    loading="lazy"
                  >
                </td>
                <td data-label="Name"><?php echo htmlspecialchars($product['product_name']); ?></td>
                <td data-label="Category">
                  <span class="admin-badge admin-badge--default"><?php echo htmlspecialchars($product['product_type']); ?></span>
                </td>
                <td data-label="Price"><?php echo admin_format_currency($product['product_price']); ?></td>
                <td data-label="Stock"><?php echo admin_stock_badge((int) $product['product_qty']); ?></td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php include 'assets/php/admin_footer.php'; ?>
