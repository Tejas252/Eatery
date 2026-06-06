<?php
if (!isset($adminPageTitle)) {
    $adminPageTitle = 'Admin Panel';
}
if (!isset($adminPageSubtitle)) {
    $adminPageSubtitle = '';
}
if (!isset($adminActiveNav)) {
    $adminActiveNav = '';
}

require_once __DIR__ . '/admin_helpers.php';

$adminNavItems = admin_nav_items();
$adminUserLabel = admin_user_label();
$adminUserInitial = admin_user_initial();
$isLoggedIn = isset($_SESSION['login']) && $_SESSION['login'] === true;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title><?php echo htmlspecialchars($adminPageTitle); ?> | Eatery Admin</title>
  <link rel="icon" href="assets/imgs/logo3.png">
  <link rel="stylesheet" href="assets/css/theme.css">
  <link rel="stylesheet" href="assets/css/admin.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&display=swap" rel="stylesheet">
</head>
<body class="admin-body">
  <div class="admin-shell" id="adminShell">
    <aside class="admin-sidebar" id="adminSidebar" aria-label="Admin navigation">
      <div class="admin-sidebar__brand">
        <a href="admin_dashboard.php" class="admin-sidebar__logo-link">
          <img src="assets/imgs/logo3.png" alt="Eatery" class="admin-sidebar__logo">
          <span class="admin-sidebar__brand-text">
            <strong>Eatery</strong>
            <small>Admin Panel</small>
          </span>
        </a>
        <button type="button" class="admin-sidebar__close" id="adminSidebarClose" aria-label="Close menu">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
        </button>
      </div>

      <nav class="admin-sidebar__nav">
        <ul class="admin-sidebar__list">
          <?php foreach ($adminNavItems as $key => $item) : ?>
            <li>
              <a
                href="<?php echo htmlspecialchars($item['url']); ?>"
                class="admin-sidebar__link<?php echo $adminActiveNav === $key ? ' is-active' : ''; ?>"
                <?php echo $adminActiveNav === $key ? 'aria-current="page"' : ''; ?>
              >
                <span class="admin-sidebar__link-icon"><?php echo admin_nav_icon($item['icon']); ?></span>
                <span><?php echo htmlspecialchars($item['label']); ?></span>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </nav>

      <div class="admin-sidebar__footer">
        <a href="index.php" class="admin-sidebar__store-link">View Storefront</a>
      </div>
    </aside>

    <div class="admin-sidebar__backdrop" id="adminSidebarBackdrop" hidden></div>

    <div class="admin-main">
      <header class="admin-topbar">
        <div class="admin-topbar__left">
          <button type="button" class="admin-topbar__menu-btn" id="adminSidebarOpen" aria-label="Open menu">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
          </button>
          <div class="admin-topbar__titles">
            <h1 class="admin-topbar__title"><?php echo htmlspecialchars($adminPageTitle); ?></h1>
            <?php if ($adminPageSubtitle !== '') : ?>
              <p class="admin-topbar__subtitle"><?php echo htmlspecialchars($adminPageSubtitle); ?></p>
            <?php endif; ?>
          </div>
        </div>

        <div class="admin-topbar__actions">
          <div class="admin-profile" data-admin-profile>
            <button type="button" class="admin-profile__trigger" id="adminProfileTrigger" aria-haspopup="true" aria-expanded="false" aria-controls="adminProfileMenu">
              <span class="admin-profile__avatar" aria-hidden="true"><?php echo htmlspecialchars($adminUserInitial); ?></span>
              <span class="admin-profile__meta">
                <span class="admin-profile__name"><?php echo htmlspecialchars($adminUserLabel); ?></span>
                <span class="admin-profile__role">Administrator</span>
              </span>
              <svg class="admin-profile__chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
            </button>
            <div class="admin-profile__dropdown" id="adminProfileMenu" role="menu" hidden>
              <a href="index.php" class="admin-profile__link" role="menuitem">View Website</a>
              <?php if ($isLoggedIn) : ?>
                <a href="assets/php/profile.php" class="admin-profile__link" role="menuitem">My Profile</a>
                <a href="assets/php/logout.php" class="admin-profile__link admin-profile__link--danger" role="menuitem">Log Out</a>
              <?php else : ?>
                <a href="login.php" class="admin-profile__link" role="menuitem">Log In</a>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </header>

      <main class="admin-content">
