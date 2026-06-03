<!-- restaurant management system -->


<?php
// if(isset($_SESSION['login'])){
  
//   // session_destroy();
// }
session_start();
include('data_insert_take.php');
require_once('assets/php/cart_helpers.php');
require_once('assets/php/book_table_helpers.php');

clear_booking_session_if_logged_out();

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
    <title>FoodHut | Free Bootstrap 4.3.x template</title>
   
    <!-- font icons -->
    <link rel="stylesheet" href="assets/vendors/themify-icons/css/themify-icons.css">

    <link rel="stylesheet" href="assets/vendors/animate/animate.css">

    <!-- Bootstrap + FoodHut main styles -->
	<link rel="stylesheet" href="assets/css/foodhut.css">
  <link rel="stylesheet" href="assets/css/menu.css">
  <link rel="stylesheet" href="assets/css/add-to-cart.css">
  <link rel="stylesheet" href="assets/css/book-table.css">
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
    .menu-section .menu-category__title {
      font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }
  </style>
</head>
<body data-spy="scroll" data-target=".navbar" data-offset="40" id="home">
    
    <!-- Navbar -->
    <nav class="custom-navbar navbar navbar-expand-lg navbar-dark fixed-top" data-spy="affix" data-offset-top="10">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" href="#home">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#book-table">Book-Table</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#Menu">Menu</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#about">About</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?php if(isset($_SESSION['login'])){echo'assets/php/logout.php';}else{echo 'login.php';} ?>"><?php if(isset($_SESSION['login'])){echo'Log-out';}else{echo 'Login';} ?></a>
        </li>
      </ul>
      <a class="navbar-brand m-auto" href="#">
        <img src="assets/imgs/logo3.png" class="brand-img" alt="">
        <span class="brand-txt">Eatery</span>
      </a>
      <ul class="navbar-nav">
       
        <li class="nav-item cart-btn">
          <a href="cart.php" class="btn btn-primary ml-xl-4 cart-btn cart-link" aria-label="View cart">
            <img src="assets/imgs/cart.png" class="nav-cart-icon" alt="">
            <span class="cart-badge<?php echo get_cart_item_count() > 0 ? '' : ' is-empty'; ?>" id="cart-count"><?php echo get_cart_item_count(); ?></span>
          </a>
          <a href="<?php if(isset($_SESSION['login'])){echo'assets/php/profile.php';}else{echo'assets/php/error.php';} ?>" class="btn btn-primary ml-xl-4 cart-btn"><img src="assets/imgs/user.png" class="nav-cart-icon" alt=""></a>
        </li> 
      </ul>
    </div>
    </nav>
    <!-- header -->
    <header id="home" class="header">
    <div class="overlay text-white text-center" id=>
      <!-- <h1 class="display-2 font-weight-bold my-3">Eatery</h1> -->
      <h2 class="display-4 mb-3"> Flavors for Royalty </h2>
      <p class="display-5 mb-5 w-50 text-justify font-italic font-weight-normal">Welcome to Eatery, where we serve delicious food with impeccable service in a warm and inviting atmosphere. Our menu features a wide variety of dishes inspired by global cuisine, ranging from traditional favorites to modern interpretations. Whether you are in the mood for something savory or sweet, we have something for everyone.</p>
      <a class="btn btn-lg btn-primary" href="#Menu">View Our Menu</a>
    </div>
  </header>

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
          <a class="book-table-active__link" href="#Menu">Browse Menu</a>
        </div>
      </div>
    </div>
  </section>



  
  <div class="d-flex flex-column">

    <!--  Menu Section  -->
    <div id="Menu" class="text-center bg-dark text-light has-height-md middle-items wow fadeIn">
    <h2 class="my-3">OUR MENU</h2>
  </div>
  
    <?php include('assets/php/product_card.php'); ?>

    <div class="menu-section">
      <div class="menu-section__inner">
        <?php
          render_product_carousel(
            'pizza',
            'Pizza',
            'Handcrafted Favorites',
            "SELECT * FROM products WHERE LOWER(product_type) = 'pizza' AND product_qty > 0 ORDER BY product_no ASC",
            $conn
          );

          render_product_carousel(
            'burger',
            'Burgers',
            'Signature Classics',
            "SELECT * FROM products WHERE LOWER(product_type) = 'burger' AND product_qty > 0 ORDER BY product_no ASC",
            $conn,
            true
          );
        ?>
      </div>
    </div>
        <script>
          function shw(i) {
            var carousel = document.getElementById(i);
            var section = document.getElementById(i + '-section');
            if (carousel) {
              carousel.classList.toggle('hide');
            }
            if (section) {
              section.classList.toggle('is-collapsed');
              var toggle = section.querySelector('.menu-category__toggle');
              if (toggle) {
                toggle.setAttribute('aria-expanded', section.classList.contains('is-collapsed') ? 'false' : 'true');
              }
            }
          }
        </script>

  </div>
  
  <!--  About Section  -->
  <div id="about" class="container-fluid wow fadeIn" id="about" data-wow-duration="1.5s">
    <div class="row">
      <!-- <div class="col-lg-6 has-img-bg"></div> -->
      <div class="col">
        <div class="row justify-content-center">
          <div class="col-sm-8 py-5 my-5">
            <!-- <div class="col-11"> -->
            <h2 class="mb-4">About Us</h2>
            <p>Back in 1954, a man named Ray Kroc discovered a small burger restaurant in California, and wrote the first page of our history. From humble beginnings as a small restaurant, we're proud to have become one of the world's leading food service brands with more than 36,000 restaurants in more than 100 countries</p>

          </div>
        </div>
      </div>
    </div>
  </div>


  
  <!-- page footer  -->
  <div class="container-fluid bg-dark text-light has-height-md middle-items border-top text-center wow fadeIn">
    <div class="row">
      <div class="col-sm-4">
        <h3>EMAIL US</h3>
        <P class="text-muted">Contact@eatery.com</P>
      </div>
      <div class="col-sm-4">
        <h3>CALL US</h3>
        <P class="text-muted">+91 9898252898</P>
      </div>
      <div class="col-sm-4">
        <h3>FIND US</h3>
        <P class="text-muted">111, Platinam hub,Noida</P>
      </div>
    </div>
  </div>

    <!-- end of page footer -->

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
    <script src="assets/js/add-to-cart.js"></script>
    <script src="assets/js/book-table.js"></script>

</body>
</html>
