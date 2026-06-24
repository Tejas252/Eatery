<?php

/**
 * Renders the main site navbar.
 *
 * Expected variables (set before include):
 * - $navBase (string) URL prefix, e.g. '' or '../../'
 * - $navCollections (array) from get_collections()
 * - $navActiveCollection (string|null) current collection slug
 * - $navHasActiveBooking (bool) whether user has an active table booking
 */
function site_nav_url(string $path): string
{
    global $navBase;
    return ($navBase ?? '') . $path;
}

function site_nav_is_logged_in(): bool
{
    return isset($_SESSION['login']) && $_SESSION['login'] === true;
}

function site_nav_user_label(): string
{
    if (!empty($_SESSION['name'])) {
        return (string) $_SESSION['name'];
    }
    if (!empty($_SESSION['username'])) {
        return (string) $_SESSION['username'];
    }
    return 'My Account';
}

$navBase = $navBase ?? '';
$navCollections = $navCollections ?? (function_exists('get_collections') ? get_collections() : []);
$navActiveCollection = $navActiveCollection ?? null;
$navHasActiveBooking = $navHasActiveBooking ?? false;
$navIsLoggedIn = site_nav_is_logged_in();
$navCartCount = function_exists('get_cart_item_count') ? get_cart_item_count() : 0;
$navUserLabel = site_nav_user_label();
$navUserEmail = !empty($_SESSION['email']) ? (string) $_SESSION['email'] : '';
?>
<nav class="site-nav custom-navbar navbar navbar-expand-lg navbar-dark fixed-top" data-spy="affix" data-offset-top="10">
  <button class="navbar-toggler site-nav__toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse site-nav__collapse" id="navbarSupportedContent">
    <ul class="navbar-nav site-nav__links site-nav__links--start">
      <li class="nav-item">
        <a class="nav-link" href="<?php echo site_nav_url('index.php'); ?>#home">Home</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?php echo site_nav_url('index.php'); ?>#book-table">Book Table</a>
      </li>
      <?php foreach ($navCollections as $navCollection) : ?>
        <li class="nav-item">
          <a
            class="nav-link<?php echo ($navActiveCollection === ($navCollection['slug'] ?? '')) ? ' active' : ''; ?>"
            href="<?php echo htmlspecialchars(site_nav_url(collection_page_url($navCollection['slug']))); ?>"
          >
            <?php echo htmlspecialchars($navCollection['nav_label']); ?>
          </a>
        </li>
      <?php endforeach; ?>
      <li class="nav-item">
        <a class="nav-link" href="<?php echo site_nav_url('index.php'); ?>#about">About</a>
      </li>
      <?php if (!$navIsLoggedIn) : ?>
        <li class="nav-item site-nav__login-item">
          <a class="nav-link site-nav__login-link" href="<?php echo site_nav_url('login.php'); ?>">Login</a>
        </li>
      <?php endif; ?>
    </ul>

    <a class="navbar-brand site-nav__brand m-auto" href="<?php echo site_nav_url('index.php'); ?>">
      <img src="<?php echo site_nav_url('assets/imgs/logo3.png'); ?>" class="brand-img site-nav__logo" alt="Eatery">
      <span class="brand-txt">Eatery</span>
    </a>

    <ul class="navbar-nav site-nav__actions">
      <li class="nav-item">
        <a href="<?php echo site_nav_url('cart.php'); ?>" class="site-nav__icon-btn cart-link" aria-label="View cart">
          <img src="<?php echo site_nav_url('assets/imgs/cart.png'); ?>" class="nav-cart-icon" alt="">
          <span class="cart-badge<?php echo $navCartCount > 0 ? '' : ' is-empty'; ?>" id="cart-count"><?php echo (int) $navCartCount; ?></span>
        </a>
      </li>
      <li class="nav-item site-nav__profile-item">
        <?php if ($navIsLoggedIn) : ?>
          <div class="nav-profile" data-profile-menu>
            <button
              type="button"
              class="nav-profile__trigger site-nav__icon-btn"
              id="navProfileTrigger"
              aria-haspopup="true"
              aria-expanded="false"
              aria-controls="navProfileMenu"
              aria-label="Open account menu"
            >
              <img src="<?php echo site_nav_url('assets/imgs/user.png'); ?>" class="nav-cart-icon" alt="">
              <svg class="nav-profile__chevron" width="10" height="10" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </button>
            <div class="nav-profile__dropdown" id="navProfileMenu" role="menu" aria-labelledby="navProfileTrigger" hidden>
              <div class="nav-profile__head">
                <span class="nav-profile__avatar" aria-hidden="true"><?php echo strtoupper(substr($navUserLabel, 0, 1)); ?></span>
                <div class="nav-profile__meta">
                  <span class="nav-profile__name"><?php echo htmlspecialchars($navUserLabel); ?></span>
                  <?php if ($navUserEmail !== '') : ?>
                    <span class="nav-profile__email"><?php echo htmlspecialchars($navUserEmail); ?></span>
                  <?php endif; ?>
                </div>
              </div>
              <ul class="nav-profile__list">
                <li>
                  <a class="nav-profile__link" href="<?php echo site_nav_url('assets/php/profile.php'); ?>" role="menuitem">
                    <span class="nav-profile__link-icon" aria-hidden="true">&#128100;</span>
                    My Profile
                  </a>
                </li>
                <li>
                  <a class="nav-profile__link" href="<?php echo site_nav_url('assets/php/history.php'); ?>" role="menuitem">
                    <span class="nav-profile__link-icon" aria-hidden="true">&#128203;</span>
                    Order History
                  </a>
                </li>
                <li>
                  <a class="nav-profile__link" href="<?php echo site_nav_url('index.php'); ?>#book-table" role="menuitem">
                    <span class="nav-profile__link-icon" aria-hidden="true">&#127869;</span>
                    Table Reservations
                    <?php if ($navHasActiveBooking) : ?>
                      <span class="nav-profile__pill">Active</span>
                    <?php endif; ?>
                  </a>
                </li>
              </ul>
              <div class="nav-profile__footer">
                <a class="nav-profile__logout" href="<?php echo site_nav_url('assets/php/logout.php'); ?>" role="menuitem">
                  <span class="nav-profile__link-icon" aria-hidden="true">&#9099;</span>
                  Log Out
                </a>
              </div>
            </div>
          </div>
        <?php else : ?>
          <a href="<?php echo site_nav_url('login.php'); ?>" class="site-nav__icon-btn" aria-label="Sign in">
            <img src="<?php echo site_nav_url('assets/imgs/user.png'); ?>" class="nav-cart-icon" alt="">
          </a>
        <?php endif; ?>
      </li>
    </ul>
  </div>
</nav>
