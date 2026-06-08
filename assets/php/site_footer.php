<?php

/**
 * Shared storefront footer.
 *
 * Optional before include:
 * - $footerBase (string) URL prefix, e.g. '' or '../../'
 */
$footerBase = $footerBase ?? ($navBase ?? '');

function site_footer_url(string $path): string
{
    global $footerBase;
    return ($footerBase ?? '') . $path;
}

$footerIsLoggedIn = isset($_SESSION['login']) && $_SESSION['login'] === true;
$footerYear = (int) date('Y');
?>
<footer class="site-footer" id="contact">
  <div class="site-footer__glow" aria-hidden="true"></div>
  <div class="site-footer__inner">
    <div class="site-footer__brand">
      <a href="<?php echo site_footer_url('index.php'); ?>" class="site-footer__logo-link">
        <img src="<?php echo site_footer_url('assets/imgs/logo3.png'); ?>" alt="" class="site-footer__logo">
        <span class="site-footer__brand-name">Eatery</span>
      </a>
      <p class="site-footer__tagline">Flavors for Royalty — handcrafted dishes, warm hospitality, and memorable dining in Noida.</p>
    </div>

    <nav class="site-footer__col" aria-label="Footer navigation">
      <h3 class="site-footer__heading">Explore</h3>
      <ul class="site-footer__links">
        <li><a href="<?php echo site_footer_url('index.php'); ?>#home">Home</a></li>
        <li><a href="<?php echo site_footer_url('index.php'); ?>#featured-menu">Menu</a></li>
        <li><a href="<?php echo site_footer_url('index.php'); ?>#best-selling">Best Sellers</a></li>
        <li><a href="<?php echo site_footer_url('index.php'); ?>#book-table">Book Table</a></li>
        <li><a href="<?php echo site_footer_url('index.php'); ?>#about">About</a></li>
      </ul>
    </nav>

    <div class="site-footer__col">
      <h3 class="site-footer__heading">Contact</h3>
      <ul class="site-footer__contact">
        <li>
          <span class="site-footer__contact-icon" aria-hidden="true">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M4 6h16v12H4zM4 7l8 6 8-6" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/></svg>
          </span>
          <a href="mailto:Contact@eatery.com">Contact@eatery.com</a>
        </li>
        <li>
          <span class="site-footer__contact-icon" aria-hidden="true">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M6 4h4l1 4-2 1a11 11 0 005 5l1-2 4 1v4a2 2 0 01-2 2C9.6 19 5 14.4 5 8a2 2 0 012-4z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/></svg>
          </span>
          <a href="tel:+919898252898">+91 9898252898</a>
        </li>
        <li>
          <span class="site-footer__contact-icon" aria-hidden="true">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M12 21s7-4.5 7-11a7 7 0 10-14 0c0 6.5 7 11 7 11z" stroke="currentColor" stroke-width="1.5"/><circle cx="12" cy="10" r="2.5" stroke="currentColor" stroke-width="1.5"/></svg>
          </span>
          <span>111, Platinam Hub, Noida</span>
        </li>
      </ul>
    </div>

    <div class="site-footer__col">
      <h3 class="site-footer__heading">Account</h3>
      <ul class="site-footer__links">
        <?php if ($footerIsLoggedIn) : ?>
          <li><a href="<?php echo site_footer_url('assets/php/profile.php'); ?>">My Profile</a></li>
          <li><a href="<?php echo site_footer_url('cart.php'); ?>">My Cart</a></li>
          <li><a href="<?php echo site_footer_url('assets/php/history.php'); ?>">Order History</a></li>
          <li><a href="<?php echo site_footer_url('assets/php/logout.php'); ?>">Log Out</a></li>
        <?php else : ?>
          <li><a href="<?php echo site_footer_url('login.php'); ?>">Sign In</a></li>
          <li><a href="<?php echo site_footer_url('signup.php'); ?>">Create Account</a></li>
        <?php endif; ?>
      </ul>
      <p class="site-footer__note">Open daily · Dine-in &amp; online ordering</p>
    </div>
  </div>

  <div class="site-footer__bar">
    <div class="site-footer__bar-inner">
      <p class="site-footer__copy">&copy; <?php echo $footerYear; ?> Eatery. All rights reserved.</p>
      <p class="site-footer__credit">Made with care for food lovers.</p>
    </div>
  </div>
</footer>
