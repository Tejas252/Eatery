<?php
session_start();

$adminPageTitle = 'Add Items';
$adminPageSubtitle = 'Add a new product to the menu catalog';
$adminActiveNav = 'add_items';

include 'assets/php/admin_header.php';
?>

<section class="admin-card admin-card--form">
  <div class="admin-card__header">
    <h2 class="admin-card__title">New Product</h2>
  </div>
  <div class="admin-card__body admin-card__body--padded">
    <form action="data_insert_take.php" method="post" enctype="multipart/form-data">
      <div class="admin-form-grid">
        <div class="admin-field">
          <label for="product_no" class="admin-label">Product Number</label>
          <input
            type="number"
            name="product_no"
            id="product_no"
            class="admin-input"
            placeholder="e.g. 101"
            required
          >
        </div>
        <div class="admin-field">
          <label for="product_name" class="admin-label">Product Name</label>
          <input
            type="text"
            name="product_name"
            id="product_name"
            class="admin-input"
            placeholder="Product name"
            required
          >
        </div>
        <div class="admin-field">
          <label for="product_price" class="admin-label">Price</label>
          <input
            type="number"
            name="product_price"
            id="product_price"
            class="admin-input"
            placeholder="0.00"
            min="0"
            step="0.01"
            required
          >
        </div>
        <div class="admin-field">
          <label for="product_type" class="admin-label">Category</label>
          <select name="product_type" id="product_type" class="admin-select" required>
            <option value="pizza">Pizza</option>
            <option value="Burger">Burger</option>
            <option value="Chinease">Chinease</option>
            <option value="Maxican">Maxican</option>
          </select>
        </div>
        <div class="admin-field">
          <label for="product_qty" class="admin-label">Stock Quantity</label>
          <input
            type="number"
            name="product_qty"
            id="product_qty"
            class="admin-input"
            placeholder="0"
            min="0"
            required
          >
        </div>
        <div class="admin-field">
          <label for="product_img" class="admin-label">Product Image</label>
          <label class="admin-file-upload">
            <input
              type="file"
              name="product_img"
              id="product_img"
              accept="image/jpeg,image/png,image/jpg"
              required
              data-file-label
            >
            <span class="admin-file-upload__btn">Choose image</span>
            <span class="admin-file-upload__name" data-file-name>No file chosen</span>
          </label>
        </div>
        <div class="admin-field admin-field--full">
          <label for="product_desc" class="admin-label">Description</label>
          <textarea
            name="product_desc"
            id="product_desc"
            class="admin-textarea"
            placeholder="Short product description"
            required
          ></textarea>
        </div>
      </div>
      <div class="admin-form-actions">
        <a href="products.php" class="admin-btn admin-btn--ghost">Cancel</a>
        <button type="submit" name="submit" class="admin-btn admin-btn--primary">Add Product</button>
      </div>
    </form>
  </div>
</section>

<?php include 'assets/php/admin_footer.php'; ?>
