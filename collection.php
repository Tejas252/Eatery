<?php

session_start();
include('data_insert_take.php');
require_once('assets/php/cart_helpers.php');
require_once('assets/php/collection_helpers.php');
require_once('assets/php/collection_render.php');
require_once('assets/php/book_table_helpers.php');

clear_booking_session_if_logged_out();

$slug = trim($_GET['slug'] ?? '');
$collection = get_collection_by_slug($slug);

if (!$collection) {
    header('Location: index.php');
    exit;
}

$collections = get_collections();
$products = fetch_collection_products($conn, $collection);
$productCount = count($products);
$pageTitle = $collection['title'] . ' | Eatery';
$navHasActiveBooking = get_current_booking_from_session() !== null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="<?php echo htmlspecialchars($collection['description']); ?>">
  <title><?php echo htmlspecialchars($pageTitle); ?></title>
  <link rel="stylesheet" href="assets/vendors/themify-icons/css/themify-icons.css">
  <link rel="stylesheet" href="assets/css/theme.css">
  <link rel="stylesheet" href="assets/css/foodhut.css">
  <link rel="stylesheet" href="assets/css/menu.css">
  <link rel="stylesheet" href="assets/css/collections.css">
  <link rel="stylesheet" href="assets/css/add-to-cart.css">
  <link rel="stylesheet" href="assets/css/site-header.css">
  <link rel="stylesheet" href="assets/css/site-footer.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&display=swap" rel="stylesheet">
  <style>
    button img { height: 20px; width: 20px; }
    .collection-page,
    .collection-page .product-card {
      font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }
  </style>
</head>
<body class="collection-page" id="top">

  <?php
    $navBase = '';
    $navCollections = $collections;
    $navActiveCollection = $slug;
    include('assets/php/site_header.php');
  ?>

  <main class="collection-page__main">
    <div class="collection-page__inner">
      <header class="collection-page__hero">
        <p class="collection-page__breadcrumb">
          <a href="index.php">Home</a> / <?php echo htmlspecialchars($collection['nav_label']); ?>
        </p>
        <span class="collection-page__eyebrow"><?php echo htmlspecialchars($collection['eyebrow']); ?></span>
        <h1 class="collection-page__title"><?php echo htmlspecialchars($collection['title']); ?></h1>
        <p class="collection-page__desc"><?php echo htmlspecialchars($collection['description']); ?></p>
        <p class="collection-page__meta"><?php echo $productCount; ?> product<?php echo $productCount === 1 ? '' : 's'; ?> available</p>
      </header>

      <?php render_product_grid($conn, $products); ?>

      <a class="collection-page__back" href="index.php#collection-<?php echo htmlspecialchars($collection['slug']); ?>">&larr; Back to Home</a>
    </div>
  </main>

  <?php include('assets/php/site_footer.php'); ?>

  <?php include('assets/php/cart_modal.php'); ?>

  <script src="assets/vendors/jquery/jquery-3.4.1.js"></script>
  <script src="assets/vendors/bootstrap/bootstrap.bundle.js"></script>
  <script src="assets/js/add-to-cart.js"></script>
  <script src="assets/js/profile-menu.js"></script>
</body>
</html>
