<?php
include 'assets/php/config.php';
session_start();

$adminPageTitle = 'Products';
$adminPageSubtitle = 'Manage menu inventory, pricing, and stock levels';
$adminActiveNav = 'products';
$adminExtraCss = ['assets/css/admin-products.css'];
$adminExtraJs = ['assets/js/admin-products.js'];

require_once 'assets/php/admin_helpers.php';
require_once 'assets/php/product_admin_helpers.php';

$filters = [
    'q' => trim((string) ($_GET['q'] ?? '')),
    'category' => trim((string) ($_GET['category'] ?? '')),
    'stock' => trim((string) ($_GET['stock'] ?? '')),
    'sort' => trim((string) ($_GET['sort'] ?? 'name_asc')),
    'page' => max(1, (int) ($_GET['page'] ?? 1)),
    'per_page' => 10,
];

$list = product_admin_fetch_list($conn, $filters);
$products = $list['rows'];
$categories = product_admin_categories($conn);
$queryParams = product_admin_query_params();
$hasProducts = $list['total'] > 0;

include 'assets/php/admin_header.php';
?>

<section class="admin-card">
  <div class="admin-card__header">
    <h2 class="admin-card__title">Product Inventory</h2>
    <a href="data_insert.php" class="admin-btn admin-btn--primary admin-btn--sm">+ Add Product</a>
  </div>

  <form class="admin-products-toolbar" method="get" action="products.php">
    <div class="admin-products-toolbar__filters">
      <input
        type="search"
        name="q"
        class="admin-input admin-products-toolbar__search"
        placeholder="Search products..."
        value="<?php echo htmlspecialchars($filters['q']); ?>"
        aria-label="Search products"
      >
      <select name="category" class="admin-select" aria-label="Filter by category">
        <option value="">All categories</option>
        <?php foreach ($categories as $category) : ?>
          <option value="<?php echo htmlspecialchars($category); ?>" <?php echo strcasecmp($filters['category'], $category) === 0 ? 'selected' : ''; ?>>
            <?php echo htmlspecialchars($category); ?>
          </option>
        <?php endforeach; ?>
      </select>
      <select name="stock" class="admin-select" aria-label="Filter by stock">
        <option value="">All stock</option>
        <option value="in" <?php echo $filters['stock'] === 'in' ? 'selected' : ''; ?>>In stock</option>
        <option value="low" <?php echo $filters['stock'] === 'low' ? 'selected' : ''; ?>>Low stock</option>
        <option value="out" <?php echo $filters['stock'] === 'out' ? 'selected' : ''; ?>>Out of stock</option>
      </select>
      <select name="sort" class="admin-select" aria-label="Sort products">
        <option value="name_asc" <?php echo $filters['sort'] === 'name_asc' ? 'selected' : ''; ?>>Name A–Z</option>
        <option value="name_desc" <?php echo $filters['sort'] === 'name_desc' ? 'selected' : ''; ?>>Name Z–A</option>
        <option value="price_asc" <?php echo $filters['sort'] === 'price_asc' ? 'selected' : ''; ?>>Price low–high</option>
        <option value="price_desc" <?php echo $filters['sort'] === 'price_desc' ? 'selected' : ''; ?>>Price high–low</option>
        <option value="stock_asc" <?php echo $filters['sort'] === 'stock_asc' ? 'selected' : ''; ?>>Stock low–high</option>
        <option value="stock_desc" <?php echo $filters['sort'] === 'stock_desc' ? 'selected' : ''; ?>>Stock high–low</option>
      </select>
      <button type="submit" class="admin-btn admin-btn--ghost admin-btn--sm">Apply</button>
      <?php if ($filters['q'] !== '' || $filters['category'] !== '' || $filters['stock'] !== '') : ?>
        <a href="products.php" class="admin-btn admin-btn--ghost admin-btn--sm">Reset</a>
      <?php endif; ?>
    </div>
  </form>

  <div class="admin-products-meta">
    <span><?php echo (int) $list['total']; ?> product<?php echo $list['total'] === 1 ? '' : 's'; ?> total</span>
    <?php if ($list['pages'] > 1) : ?>
      <span>Page <?php echo (int) $list['page']; ?> of <?php echo (int) $list['pages']; ?></span>
    <?php endif; ?>
  </div>

  <div class="admin-card__body">
    <?php if (!$hasProducts) : ?>
      <div class="admin-empty">
        <p class="admin-empty__title">No products found</p>
        <p class="admin-empty__text"><?php echo ($filters['q'] !== '' || $filters['category'] !== '' || $filters['stock'] !== '') ? 'Try adjusting your search or filters.' : 'Add your first menu item to get started.'; ?></p>
        <a href="data_insert.php" class="admin-btn admin-btn--primary" style="margin-top:1rem;">Add Product</a>
      </div>
    <?php else : ?>
      <div class="admin-table-wrap">
        <table class="admin-table admin-table--products">
          <thead>
            <tr>
              <th>ID</th>
              <th>Image</th>
              <th>Product</th>
              <th>Category</th>
              <th>Price</th>
              <th>Stock</th>
              <th class="admin-table__actions-col">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($products as $product) :
              $productJson = product_admin_json_attr($product);
              ?>
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
                <td data-label="Product">
                  <div class="admin-product-cell">
                    <span class="admin-product-cell__name"><?php echo htmlspecialchars($product['product_name']); ?></span>
                    <span class="admin-product-cell__sku">SKU #<?php echo (int) $product['product_no']; ?></span>
                  </div>
                </td>
                <td data-label="Category">
                  <span class="admin-badge admin-badge--default"><?php echo htmlspecialchars($product['product_type']); ?></span>
                </td>
                <td data-label="Price"><?php echo admin_format_currency($product['product_price']); ?></td>
                <td data-label="Stock">
                  <div class="admin-product-stock">
                    <?php echo admin_stock_badge((int) $product['product_qty']); ?>
                    <form action="assets/php/manage_product.php" method="post" class="admin-stock-form">
                      <input type="hidden" name="product_id" value="<?php echo (int) $product['product_id']; ?>">
                      <?php product_admin_render_return_fields($queryParams); ?>
                      <input type="number" name="product_qty" class="admin-input" value="<?php echo (int) $product['product_qty']; ?>" min="0" aria-label="Stock quantity">
                      <button type="submit" name="stock" class="admin-btn admin-btn--ghost admin-btn--sm">Save</button>
                    </form>
                  </div>
                </td>
                <td class="admin-table__actions-cell" data-label="Actions">
                  <div class="admin-product-actions">
                    <button type="button" class="admin-btn admin-btn--sm admin-btn--view" data-product-view data-product="<?php echo $productJson; ?>">View</button>
                    <button type="button" class="admin-btn admin-btn--sm admin-btn--edit" data-product-edit data-product="<?php echo $productJson; ?>">Edit</button>
                    <button type="button" class="admin-btn admin-btn--sm admin-btn--danger" data-product-delete data-product="<?php echo $productJson; ?>">Delete</button>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <?php if ($list['pages'] > 1) : ?>
        <nav class="admin-pagination" aria-label="Product pagination">
          <?php
            $currentPage = (int) $list['page'];
            $totalPages = (int) $list['pages'];
            $prevDisabled = $currentPage <= 1 ? ' is-disabled' : '';
            $nextDisabled = $currentPage >= $totalPages ? ' is-disabled' : '';
          ?>
          <a class="admin-pagination__btn<?php echo $prevDisabled; ?>" href="<?php echo htmlspecialchars(product_admin_pagination_url($queryParams, max(1, $currentPage - 1))); ?>">Prev</a>
          <?php for ($p = max(1, $currentPage - 2); $p <= min($totalPages, $currentPage + 2); $p++) : ?>
            <a class="admin-pagination__btn<?php echo $p === $currentPage ? ' is-active' : ''; ?>" href="<?php echo htmlspecialchars(product_admin_pagination_url($queryParams, $p)); ?>"><?php echo $p; ?></a>
          <?php endfor; ?>
          <a class="admin-pagination__btn<?php echo $nextDisabled; ?>" href="<?php echo htmlspecialchars(product_admin_pagination_url($queryParams, min($totalPages, $currentPage + 1))); ?>">Next</a>
        </nav>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</section>

<div class="admin-modal" id="productViewModal" hidden>
  <div class="admin-modal__backdrop" data-modal-close></div>
  <div class="admin-modal__dialog admin-modal__dialog--view" role="dialog" aria-modal="true" aria-labelledby="viewModalTitle">
    <div class="admin-modal__header">
      <h3 class="admin-modal__title" id="viewModalTitle">Product Details</h3>
      <button type="button" class="admin-modal__close" data-modal-close aria-label="Close">&times;</button>
    </div>
    <div class="admin-modal__body">
      <div class="admin-view-product">
        <img src="" alt="" class="admin-view-product__img" id="viewProductImg">
        <div class="admin-view-product__details">
          <strong id="viewProductName"></strong>
          <span class="admin-table__muted" id="viewProductSku"></span>
          <div class="admin-view-product__row"><span>Category</span><strong id="viewProductCategory"></strong></div>
          <div class="admin-view-product__row"><span>Price</span><strong id="viewProductPrice"></strong></div>
          <div class="admin-view-product__row"><span>Stock</span><strong id="viewProductStock"></strong></div>
          <div class="admin-view-product__row"><span>Caption</span><strong id="viewProductDesc"></strong></div>
        </div>
      </div>
    </div>
    <div class="admin-modal__footer">
      <button type="button" class="admin-btn admin-btn--ghost" data-modal-close>Close</button>
      <a href="#" class="admin-btn admin-btn--primary" id="viewProductLink" target="_blank" rel="noopener">View on Storefront</a>
    </div>
  </div>
</div>

<div class="admin-modal" id="productEditModal" hidden>
  <div class="admin-modal__backdrop" data-modal-close></div>
  <div class="admin-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="editModalTitle">
    <div class="admin-modal__header">
      <h3 class="admin-modal__title" id="editModalTitle">Edit Product</h3>
      <button type="button" class="admin-modal__close" data-modal-close aria-label="Close">&times;</button>
    </div>
    <form id="productEditForm" action="assets/php/manage_product.php" method="post" enctype="multipart/form-data">
      <div class="admin-modal__body">
        <input type="hidden" name="product_id" value="">
        <?php product_admin_render_return_fields($queryParams); ?>
        <div class="admin-edit-preview">
          <img src="" alt="" id="editProductPreview">
          <div>
            <strong>Edit product</strong>
            <p class="admin-table__muted" id="editProductSku"></p>
          </div>
        </div>
        <div class="admin-form-grid">
          <div class="admin-field">
            <label class="admin-label" for="edit_name">Product Name</label>
            <input type="text" class="admin-input admin-input--no-icon" id="edit_name" name="product_name" maxlength="20" required>
          </div>
          <div class="admin-field">
            <label class="admin-label" for="edit_type">Category</label>
            <select class="admin-select" id="edit_type" name="product_type" required>
              <?php foreach ($categories as $category) : ?>
                <option value="<?php echo htmlspecialchars($category); ?>"><?php echo htmlspecialchars($category); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="admin-field">
            <label class="admin-label" for="edit_price">Price</label>
            <input type="number" class="admin-input admin-input--no-icon" id="edit_price" name="product_price" min="0" required>
          </div>
          <div class="admin-field">
            <label class="admin-label" for="edit_qty">Stock Quantity</label>
            <input type="number" class="admin-input admin-input--no-icon" id="edit_qty" name="product_qty" min="0" required>
          </div>
          <div class="admin-field admin-field--full">
            <label class="admin-label" for="edit_desc">Product Caption</label>
            <textarea class="admin-textarea" id="edit_desc" name="product_desc" maxlength="100" placeholder="Short caption shown on menu cards" required></textarea>
          </div>
          <div class="admin-field admin-field--full">
            <label class="admin-label" for="edit_img">Replace Image (optional)</label>
            <input type="file" class="admin-input admin-input--no-icon" id="edit_img" name="product_img" accept="image/jpeg,image/png,image/jpg">
          </div>
        </div>
      </div>
      <div class="admin-modal__footer">
        <button type="button" class="admin-btn admin-btn--ghost" data-modal-close>Cancel</button>
        <button type="submit" name="update" class="admin-btn admin-btn--primary">Save Changes</button>
      </div>
    </form>
  </div>
</div>

<div class="admin-modal" id="productDeleteModal" hidden>
  <div class="admin-modal__backdrop" data-modal-close></div>
  <div class="admin-modal__dialog admin-modal__dialog--view" role="dialog" aria-modal="true" aria-labelledby="deleteModalTitle">
    <div class="admin-modal__header">
      <h3 class="admin-modal__title" id="deleteModalTitle">Delete Product</h3>
      <button type="button" class="admin-modal__close" data-modal-close aria-label="Close">&times;</button>
    </div>
    <form action="assets/php/manage_product.php" method="post">
      <div class="admin-modal__body">
        <input type="hidden" name="product_id" id="deleteProductId" value="">
        <?php product_admin_render_return_fields($queryParams); ?>
        <p>Are you sure you want to delete <strong id="deleteProductName"></strong>? This action cannot be undone.</p>
      </div>
      <div class="admin-modal__footer">
        <button type="button" class="admin-btn admin-btn--ghost" data-modal-close>Cancel</button>
        <button type="submit" name="delete" class="admin-btn admin-btn--danger">Delete Product</button>
      </div>
    </form>
  </div>
</div>

<?php include 'assets/php/admin_footer.php'; ?>
