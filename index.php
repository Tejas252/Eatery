<!-- restaurant management system -->


<?php
// if(isset($_SESSION['login'])){
  
//   // session_destroy();
// }
session_start();
include('data_insert_take.php');
require_once('assets/php/cart_helpers.php');
require_once('assets/php/book_table_helpers.php');
require_once('assets/php/collection_helpers.php');
require_once('assets/php/collection_render.php');

$accessDeniedNotice = '';
if (!empty($_SESSION['access_denied_notice'])) {
    $accessDeniedNotice = (string) $_SESSION['access_denied_notice'];
    unset($_SESSION['access_denied_notice']);
}

clear_booking_session_if_logged_out();
book_table_reconcile_customer_booking($conn);

$collections = get_collections();

$bestSellingProducts = fetch_best_selling_products($conn, 8);
$featuredMenuProducts = enrich_products_with_ratings(
    $conn,
    fetch_featured_menu_products($conn, 12)
);

$bookMaxGuests = book_table_max_guests($conn);
$bookCurrentGuests = isset($_SESSION['guest']) ? (int) $_SESSION['guest'] : 2;
$bookCurrentBooking = get_current_booking_from_session();
$bookIsLoggedIn = isset($_SESSION['login']) && $_SESSION['login'] === true;
$hasActiveBooking = $bookCurrentBooking !== null;

if ($hasActiveBooking) {
    $bookCurrentBooking = enrich_booking_with_table($conn, $bookCurrentBooking);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Start your development with FoodHut landing page.">
    <meta name="author" content="Devcrud">
    <title>Eatery | Restaurant &amp; Online Ordering</title>
   
    <!-- font icons -->
    <link rel="stylesheet" href="assets/vendors/themify-icons/css/themify-icons.css">

    <link rel="stylesheet" href="assets/vendors/animate/animate.css">

    <!-- Bootstrap + FoodHut main styles -->
	<link rel="stylesheet" href="assets/css/theme.css">
	<link rel="stylesheet" href="assets/css/foodhut.css">
  <link rel="stylesheet" href="assets/css/menu.css">
  <link rel="stylesheet" href="assets/css/collections.css">
  <link rel="stylesheet" href="assets/css/featured-menu.css">
  <link rel="stylesheet" href="assets/css/add-to-cart.css">
  <link rel="stylesheet" href="assets/css/site-header.css">
  <link rel="stylesheet" href="assets/css/book-table.css">
  <link rel="stylesheet" href="assets/css/about.css">
  <link rel="stylesheet" href="assets/css/site-footer.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet">
  <style>
    button img{
      height:20px;
      width:20px;
    }
    .menu-section,
    .menu-section .product-card,
    .collections-home,
    .collection-section,
    .featured-menu,
    .home-hero {
      font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }
  </style>
</head>
<body data-spy="scroll" data-target=".navbar" data-offset="40" id="home">
    
    <?php
      $navBase = '';
      $navCollections = $collections;
      $navActiveCollection = null;
      $navHasActiveBooking = $hasActiveBooking;
      include('assets/php/site_header.php');
    ?>
    <?php if ($accessDeniedNotice !== '') : ?>
      <div class="container" style="padding-top:1rem;">
        <div role="alert" style="padding:0.85rem 1rem;border-radius:8px;border:1px solid rgba(239,68,68,0.35);background:rgba(239,68,68,0.12);color:#fecaca;">
          <?php echo htmlspecialchars($accessDeniedNotice); ?>
        </div>
      </div>
    <?php endif; ?>
    <!-- Hero -->
    <header id="home" class="home-hero">
      <div class="home-hero__inner wow fadeIn">
        <span class="home-hero__eyebrow">Welcome to Eatery</span>
        <h1 class="home-hero__title">Flavors for Royalty</h1>
        <p class="home-hero__text">Discover our best-selling dishes and curated collections — from handcrafted pizzas to signature burgers, made fresh and served with care.</p>
        <div class="home-hero__actions">
          <a class="home-hero__btn home-hero__btn--primary" href="#best-selling">Best Sellers</a>
          <a class="home-hero__btn home-hero__btn--ghost" href="#featured-menu">Browse Menu</a>
        </div>
      </div>
    </header>

    <div class="collections-home">
      <div class="collections-home__inner">
        <?php
          render_collection_carousel_section([
            'carousel_id' => 'best-selling',
            'section_id' => 'best-selling',
            'title' => 'Best Selling',
            'eyebrow' => 'Customer Favorites',
            'description' => 'Our most ordered dishes, loved by guests and perfect for your next visit.',
            'theme' => 'bestselling',
            'cta_url' => collection_page_url('pizza'),
            'cta_label' => 'Explore Collections',
          ], $bestSellingProducts, $conn);
        ?>
      </div>
    </div>

    <?php render_featured_menu_section($conn, $featuredMenuProducts); ?>

  <section
    class="book-table-section"
    id="book-table"
    data-logged-in="<?php echo $bookIsLoggedIn ? '1' : '0'; ?>"
    data-max-guests="<?php echo (int) $bookMaxGuests; ?>"
    data-has-active-booking="<?php echo $hasActiveBooking ? '1' : '0'; ?>"
  >
    <div class="book-table-section__inner">
      <header class="book-table-section__header">
        <span class="book-table-section__eyebrow">Reservations</span>
        <h2 class="book-table-section__title">Book a Table</h2>
        <p class="book-table-section__subtitle" id="bookTableSubtitle">
          <?php if ($hasActiveBooking) : ?>
            Your table is reserved. Browse the menu when you are ready.
          <?php else : ?>
            Choose your party size, pick an available table, and confirm your reservation in seconds.
          <?php endif; ?>
        </p>
      </header>

      <div class="book-table-layout" id="bookTableBookingFlow" <?php echo $hasActiveBooking ? 'hidden' : ''; ?>>
        <aside class="book-table-panel" aria-label="Booking details">
          <h3 class="book-table-panel__title">Reservation Details</h3>

          <div class="book-table-field">
            <label for="bookingGuests">Number of guests</label>
            <div class="book-table-guest-stepper">
              <button type="button" id="bookingGuestsDecrease" aria-label="Decrease guests">−</button>
              <input type="number" id="bookingGuests" value="<?php echo max(1, min($bookMaxGuests, $bookCurrentGuests)); ?>" min="1" max="<?php echo (int) $bookMaxGuests; ?>" inputmode="numeric">
              <button type="button" id="bookingGuestsIncrease" aria-label="Increase guests">+</button>
            </div>
          </div>

          <?php if (!$bookIsLoggedIn) : ?>
            <p class="book-table-login-note">Please <a href="login.php">log in</a> to reserve a table.</p>
          <?php endif; ?>

          <div class="book-table-actions">
            <button type="button" class="book-table-btn book-table-btn--primary" id="bookTableSubmit" <?php echo $bookIsLoggedIn ? '' : 'disabled'; ?>>
              <span class="book-table-btn__loader" aria-hidden="true"></span>
              <span>Confirm Booking</span>
            </button>
            <button type="button" class="book-table-btn book-table-btn--ghost" id="bookTableRefresh">Refresh Availability</button>
          </div>

          <div class="book-table-message" id="bookTableMessage" role="status"></div>
        </aside>

        <div class="book-table-floor" aria-label="Restaurant floor plan">
          <div class="book-table-floor__head">
            <h3 class="book-table-floor__title">Live Table Availability</h3>
            <div class="book-table-legend">
              <span class="book-table-legend__item"><span class="book-table-legend__dot book-table-legend__dot--available"></span> Available</span>
              <span class="book-table-legend__item"><span class="book-table-legend__dot book-table-legend__dot--reserved"></span> Reserved</span>
              <span class="book-table-legend__item"><span class="book-table-legend__dot book-table-legend__dot--occupied"></span> Occupied</span>
              <span class="book-table-legend__item"><span class="book-table-legend__dot book-table-legend__dot--unavailable"></span> Unavailable</span>
            </div>
          </div>
          <div class="book-table-grid" id="bookTableGrid"></div>
        </div>
      </div>

      <div
        class="book-table-active"
        id="bookTableActiveReservation"
        aria-label="Your current reservation"
        <?php echo $hasActiveBooking ? '' : 'hidden'; ?>
      >
        <div class="book-table-active__bar">
          <span class="book-table-active__indicator" aria-hidden="true"></span>
          <p class="book-table-active__summary">
            <strong>Table <span data-field="table-no"><?php echo $hasActiveBooking ? (int) $bookCurrentBooking['table_no'] : ''; ?></span></strong>
            <span class="book-table-active__sep" aria-hidden="true">·</span>
            <span data-field="capacity"><?php echo ($hasActiveBooking && !empty($bookCurrentBooking['capacity'])) ? (int) $bookCurrentBooking['capacity'] . ' seats' : ''; ?></span>
            <span class="book-table-active__sep" aria-hidden="true">·</span>
            <span data-field="guests"><?php echo $hasActiveBooking ? (int) $bookCurrentBooking['guests'] . ' guest' . ((int) $bookCurrentBooking['guests'] === 1 ? '' : 's') : ''; ?></span>
            <span class="book-table-active__badge" data-field="status"><?php echo $hasActiveBooking ? 'Confirmed' : ''; ?></span>
          </p>
          <a class="book-table-active__link" href="#featured-menu">Browse Menu</a>
        </div>
      </div>
    </div>
  </section>



  <?php include('assets/php/about_section.php'); ?>

  <?php include('assets/php/site_footer.php'); ?>

    <?php include('assets/php/cart_modal.php'); ?>

	<!-- core  -->
    <script src="assets/vendors/jquery/jquery-3.4.1.js"></script>
    <script src="assets/vendors/bootstrap/bootstrap.bundle.js"></script>

    <!-- bootstrap affix -->
    <script src="assets/vendors/bootstrap/bootstrap.affix.js"></script>

    <!-- wow.js -->
    <script src="assets/vendors/wow/wow.js"></script>
    
    <!-- google maps -->
    <!-- <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCtme10pzgKSPeJVJrG1O3tjR6lk98o4w8&callback=initMap"></script> -->

    <!-- FoodHut js -->
    <script src="assets/js/foodhut.js"></script>
    <script src="assets/js/menu-carousel.js"></script>
    <script src="assets/js/featured-menu.js"></script>
    <script src="assets/js/add-to-cart.js"></script>
    <script src="assets/js/profile-menu.js"></script>
    <script src="assets/js/book-table.js"></script>
    <script src="assets/js/about.js"></script>

</body>
</html>
