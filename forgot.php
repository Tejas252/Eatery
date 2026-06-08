<?php
session_start();

require_once __DIR__ . '/assets/php/auth_helpers.php';

if (auth_is_admin()) {
    header('Location: admin_dashboard.php');
    exit;
}

if (auth_is_customer()) {
    header('Location: index.php');
    exit;
}

$authError = '';
if (isset($_GET['error'])) {
    $errorKey = (string) $_GET['error'];
    $errorMessages = [
        'required' => 'Please fill in all required fields.',
        'email' => 'Enter a valid email address.',
        'password' => 'Passwords do not match. Please try again.',
        'password_length' => 'Password must be 20 characters or fewer.',
        'notfound' => 'No account found with that email address.',
        'admin' => 'Administrator passwords cannot be reset from this page.',
        'server' => 'Password update failed. Please try again in a moment.',
        'invalid' => 'Invalid request. Please try again.',
    ];
    $authError = $errorMessages[$errorKey] ?? 'Password reset failed. Please try again.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="Reset your Eatery account password">
  <title>Forgot Password | Eatery</title>
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
      <a href="login.php" class="auth-topbar__link">Sign in</a>
    </header>

    <main class="auth-main">
      <div class="auth-layout">
        <aside class="auth-panel auth-panel--brand" aria-hidden="false">
          <img src="assets/imgs/logo3.png" alt="Eatery" class="auth-panel__logo">
          <h1 class="auth-panel__title">Reset your password</h1>
          <p class="auth-panel__text">Enter your registered email and choose a new password to regain access to your account.</p>
          <ul class="auth-panel__features">
            <li>
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12l4 4L19 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
              Email verification before update
            </li>
            <li>
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12l4 4L19 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
              Secure password confirmation
            </li>
            <li>
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12l4 4L19 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
              Sign in immediately after reset
            </li>
          </ul>
        </aside>

        <section class="auth-panel auth-panel--form">
          <div class="auth-form__header">
            <span class="auth-form__eyebrow">Account recovery</span>
            <h2 class="auth-form__title">Forgot password</h2>
            <p class="auth-form__subtitle">Use your registered email to set a new password</p>
          </div>

          <?php if ($authError !== '') : ?>
            <div class="auth-alert auth-alert--error" role="alert"><?php echo htmlspecialchars($authError); ?></div>
          <?php endif; ?>

          <form id="forgotForm" action="assets/php/forgot_password.php" method="post" novalidate>
            <div class="auth-field">
              <label for="forgot-email" class="auth-label">Email address</label>
              <div class="auth-input-wrap">
                <span class="auth-input-wrap__icon" aria-hidden="true">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M4 6h16v12H4zM4 7l8 6 8-6" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/></svg>
                </span>
                <input
                  type="email"
                  name="email"
                  id="forgot-email"
                  class="auth-input"
                  placeholder="you@example.com"
                  autocomplete="email"
                  required
                >
              </div>
              <span class="auth-field__error" aria-live="polite"></span>
            </div>

            <div class="auth-field">
              <label for="forgot-password" class="auth-label">New password</label>
              <div class="auth-input-wrap">
                <span class="auth-input-wrap__icon" aria-hidden="true">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><rect x="5" y="11" width="14" height="10" rx="2" stroke="currentColor" stroke-width="1.5"/><path d="M8 11V8a4 4 0 118 0v3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                </span>
                <input
                  type="password"
                  name="password"
                  id="forgot-password"
                  class="auth-input auth-input--password"
                  placeholder="Enter new password (max 20)"
                  maxlength="20"
                  autocomplete="new-password"
                  required
                >
                <button type="button" class="auth-input-wrap__toggle" data-password-toggle="forgot-password" aria-label="Show password" aria-pressed="false">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z" stroke="currentColor" stroke-width="1.5"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.5"/></svg>
                </button>
              </div>
              <span class="auth-field__error" aria-live="polite"></span>
            </div>

            <div class="auth-field">
              <label for="forgot-cpassword" class="auth-label">Confirm new password</label>
              <div class="auth-input-wrap">
                <span class="auth-input-wrap__icon" aria-hidden="true">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><rect x="5" y="11" width="14" height="10" rx="2" stroke="currentColor" stroke-width="1.5"/><path d="M8 11V8a4 4 0 118 0v3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                </span>
                <input
                  type="password"
                  name="cpassword"
                  id="forgot-cpassword"
                  class="auth-input auth-input--password"
                  placeholder="Confirm new password"
                  maxlength="20"
                  autocomplete="new-password"
                  required
                >
                <button type="button" class="auth-input-wrap__toggle" data-password-toggle="forgot-cpassword" aria-label="Show password" aria-pressed="false">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z" stroke="currentColor" stroke-width="1.5"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.5"/></svg>
                </button>
              </div>
              <span class="auth-field__error" aria-live="polite"></span>
            </div>

            <button type="submit" name="reset" class="auth-btn">
              <span class="auth-btn__spinner" aria-hidden="true"></span>
              <span class="auth-btn__label">Update password</span>
            </button>

            <p class="auth-footer">
              Remember your password? <a href="login.php">Sign in</a>
            </p>
          </form>
        </section>
      </div>
    </main>
  </div>

  <script src="assets/js/auth.js"></script>
</body>
</html>
