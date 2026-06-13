<?php

session_start();

if (!isset($_SESSION['login']) || !isset($_SESSION['id'])) {
    header('Location: ../../login.php');
    exit;
}

require_once __DIR__ . '/cart_helpers.php';
require_once __DIR__ . '/collection_helpers.php';
require_once __DIR__ . '/book_table_helpers.php';

clear_booking_session_if_logged_out();

$collections = get_collections();
$navHasActiveBooking = get_current_booking_from_session() !== null;

$userId = (int) $_SESSION['id'];
$username = $_SESSION['username'] ?? '';
$fullName = $_SESSION['name'] ?? '';
$mobile = isset($_SESSION['phone']) ? (string) $_SESSION['phone'] : '';
$email = $_SESSION['email'] ?? '';
$avatarInitial = strtoupper(substr($fullName !== '' ? $fullName : $username, 0, 1));
$avatarImg = '../../assets/imgs/prof.jpg';
$avatarFile = __DIR__ . '/../imgs/prof.jpg';
$hasAvatarImage = is_file($avatarFile);

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="Manage your Eatery account profile">
  <title>Eatery | My Profile</title>
  <link rel="icon" href="../../assets/imgs/logo3.png">
  <link rel="stylesheet" href="../../assets/css/theme.css">
  <link rel="stylesheet" href="../../assets/css/foodhut.css">
  <link rel="stylesheet" href="../../assets/css/add-to-cart.css">
  <link rel="stylesheet" href="../../assets/css/site-header.css">
  <link rel="stylesheet" href="../../assets/css/site-footer.css">
  <link rel="stylesheet" href="../../assets/css/profile.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&display=swap" rel="stylesheet">
</head>
<body class="profile-body">

  <?php
    $navBase = '../../';
    $navCollections = $collections;
    $navActiveCollection = null;
    include __DIR__ . '/site_header.php';
  ?>

  <main class="profile-page">
    <div class="profile-page__inner">

      <header class="profile-page__hero">
        <span class="profile-page__eyebrow">Your Account</span>
        <h1 class="profile-page__title">My Profile</h1>
        <p class="profile-page__subtitle">View and manage your personal information and account details.</p>
      </header>

      <div class="profile-layout">
        <aside class="profile-card profile-card--identity" aria-label="Profile summary">
          <div class="profile-avatar">
            <?php if ($hasAvatarImage) : ?>
              <img class="profile-avatar__img" src="<?php echo htmlspecialchars($avatarImg); ?>" alt="Profile photo">
            <?php else : ?>
              <span class="profile-avatar__initial" aria-hidden="true"><?php echo htmlspecialchars($avatarInitial); ?></span>
            <?php endif; ?>
            <span class="profile-avatar__ring" aria-hidden="true"></span>
          </div>
          <h2 class="profile-identity__name"><?php echo htmlspecialchars($fullName !== '' ? $fullName : $username); ?></h2>
          <p class="profile-identity__username">@<?php echo htmlspecialchars($username); ?></p>
          <div class="profile-identity__badge">User ID &middot; <?php echo $userId; ?></div>
          <nav class="profile-quick-links" aria-label="Account shortcuts">
            <a class="profile-quick-links__item" href="history.php">
              <span class="profile-quick-links__icon" aria-hidden="true">&#128203;</span>
              Order History
            </a>
            <a class="profile-quick-links__item" href="../../index.php#book-table">
              <span class="profile-quick-links__icon" aria-hidden="true">&#127869;</span>
              Table Reservations
              <?php if ($navHasActiveBooking) : ?>
                <span class="profile-quick-links__pill">Active</span>
              <?php endif; ?>
            </a>
          </nav>
        </aside>

        <section class="profile-card profile-card--details" aria-label="Profile details">
          <div class="profile-card__head">
            <div>
              <h2 class="profile-card__title">Profile Details</h2>
              <p class="profile-card__desc">Your account information is shown below. Some fields cannot be changed online.</p>
            </div>
            <button type="button" class="profile-edit-btn" id="profileEditBtn" aria-controls="profileForm">
              <span class="profile-edit-btn__icon" aria-hidden="true">&#9998;</span>
              Edit Profile
            </button>
          </div>

          <form class="profile-form" id="profileForm" novalidate>
            <div class="profile-form__grid">
              <div class="profile-field profile-field--readonly">
                <label class="profile-field__label" for="profileUserId">User ID</label>
                <input class="profile-field__input" type="text" id="profileUserId" value="<?php echo $userId; ?>" readonly tabindex="-1">
              </div>

              <div class="profile-field profile-field--readonly">
                <label class="profile-field__label" for="profileUsername">Username</label>
                <input class="profile-field__input" type="text" id="profileUsername" name="username" value="<?php echo htmlspecialchars($username); ?>" readonly autocomplete="username">
              </div>

              <div class="profile-field">
                <label class="profile-field__label" for="profileFullName">Full Name</label>
                <input class="profile-field__input" type="text" id="profileFullName" name="full_name" value="<?php echo htmlspecialchars($fullName); ?>" readonly autocomplete="name">
              </div>

              <div class="profile-field">
                <label class="profile-field__label" for="profileMobile">Mobile Number</label>
                <input class="profile-field__input" type="tel" id="profileMobile" name="mobile" value="<?php echo htmlspecialchars($mobile); ?>" readonly inputmode="tel" autocomplete="tel">
              </div>

              <div class="profile-field profile-field--full profile-field--readonly">
                <label class="profile-field__label" for="profileEmail">Email Address</label>
                <input class="profile-field__input" type="email" id="profileEmail" name="email" value="<?php echo htmlspecialchars($email); ?>" readonly autocomplete="email">
              </div>
            </div>

            <div class="profile-form__actions profile-form__actions--edit" id="profileEditActions" hidden>
              <button type="button" class="profile-btn profile-btn--ghost" id="profileCancelBtn">Cancel</button>
              <button type="submit" class="profile-btn profile-btn--primary" id="profileSaveBtn">
                <span class="profile-btn__loader" aria-hidden="true"></span>
                Save Changes
              </button>
            </div>

            <p class="profile-form__message" id="profileFormMessage" role="status" hidden></p>
          </form>
        </section>
      </div>

    </div>
  </main>

  <?php include __DIR__ . '/site_footer.php'; ?>

  <div class="cart-toast-stack" id="cartToastStack" aria-live="polite" aria-atomic="true"></div>

  <script src="../../assets/vendors/jquery/jquery-3.4.1.js"></script>
  <script src="../../assets/vendors/bootstrap/bootstrap.bundle.js"></script>
  <script src="../../assets/js/profile-menu.js"></script>
  <script src="../../assets/js/profile-page.js"></script>
</body>
</html>
