<?php
session_start();

$authError = '';
$authSuccess = '';

if (!empty($_SESSION['auth_success'])) {
    $authSuccess = (string) $_SESSION['auth_success'];
    unset($_SESSION['auth_success']);
}

if (isset($_GET['error'])) {
    $authError = 'Invalid email or password. Please try again.';
}

if (isset($_GET['registered'])) {
    $authSuccess = 'Account created successfully. Please sign in with your credentials.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="Sign in to your Eatery account">
  <title>Login | Eatery</title>
  <link rel="icon" href="assets/imgs/logo3.png">
  <link rel="stylesheet" href="assets/css/theme.css">
  <link rel="stylesheet" href="assets/css/auth.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&display=swap" rel="stylesheet">
</head>
<body class="auth-page">
  <div class="auth-shell">
    <header class="auth-topbar">
      <a href="index.php" class="auth-brand">
        <img src="assets/imgs/logo3.png" alt="" class="auth-brand__logo">
        <span class="auth-brand__name">Eatery</span>
      </a>
      <a href="signup.php" class="auth-topbar__link">Create account</a>
    </header>

    <main class="auth-main">
      <div class="auth-layout">
        <aside class="auth-panel auth-panel--brand" aria-hidden="false">
          <img src="assets/imgs/logo3.png" alt="Eatery" class="auth-panel__logo">
          <h1 class="auth-panel__title">Welcome back</h1>
          <p class="auth-panel__text">Sign in to order your favorites, track deliveries, and manage your table reservations.</p>
          <ul class="auth-panel__features">
            <li>
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12l4 4L19 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
              Fast ordering &amp; delivery
            </li>
            <li>
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12l4 4L19 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
              Saved profile &amp; order history
            </li>
            <li>
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12l4 4L19 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
              Secure account access
            </li>
          </ul>
        </aside>

        <section class="auth-panel auth-panel--form">
          <div class="auth-form__header">
            <span class="auth-form__eyebrow">Account</span>
            <h2 class="auth-form__title">Sign in</h2>
            <p class="auth-form__subtitle">Enter your credentials to continue</p>
          </div>

          <?php if ($authSuccess !== '') : ?>
            <div class="auth-alert auth-alert--success" role="status"><?php echo htmlspecialchars($authSuccess); ?></div>
          <?php endif; ?>

          <?php if ($authError !== '') : ?>
            <div class="auth-alert auth-alert--error" role="alert"><?php echo htmlspecialchars($authError); ?></div>
          <?php endif; ?>

          <form id="loginForm" action="assets/php/verification.php" method="post" novalidate>
            <div class="auth-field">
              <label for="login-email" class="auth-label">Email address</label>
              <div class="auth-input-wrap">
                <span class="auth-input-wrap__icon" aria-hidden="true">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M4 6h16v12H4zM4 7l8 6 8-6" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/></svg>
                </span>
                <input
                  type="email"
                  name="email"
                  id="login-email"
                  class="auth-input"
                  placeholder="you@example.com"
                  autocomplete="email"
                  required
                >
              </div>
              <span class="auth-field__error" aria-live="polite"></span>
            </div>

            <div class="auth-field">
              <label for="login-password" class="auth-label">Password</label>
              <div class="auth-input-wrap">
                <span class="auth-input-wrap__icon" aria-hidden="true">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><rect x="5" y="11" width="14" height="10" rx="2" stroke="currentColor" stroke-width="1.5"/><path d="M8 11V8a4 4 0 118 0v3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                </span>
                <input
                  type="password"
                  name="password"
                  id="login-password"
                  class="auth-input auth-input--password"
                  placeholder="Enter your password"
                  autocomplete="current-password"
                  required
                >
                <button type="button" class="auth-input-wrap__toggle" data-password-toggle="login-password" aria-label="Show password" aria-pressed="false">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z" stroke="currentColor" stroke-width="1.5"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.5"/></svg>
                </button>
              </div>
              <span class="auth-field__error" aria-live="polite"></span>
            </div>

            <div class="auth-row">
              <span></span>
              <a href="assets/php/forgot.php" class="auth-link">Forgot password?</a>
            </div>

            <button type="submit" name="login" class="auth-btn">
              <span class="auth-btn__spinner" aria-hidden="true"></span>
              <span class="auth-btn__label">Sign in</span>
            </button>

            <p class="auth-footer">
              Don&rsquo;t have an account? <a href="signup.php">Sign up</a>
            </p>
          </form>
        </section>
      </div>
    </main>
  </div>

  <script src="assets/js/auth.js"></script>
</body>
</html>
