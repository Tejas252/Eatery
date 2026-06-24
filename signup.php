<?php
session_start();

$authError = '';
if (isset($_GET['error'])) {
    $errorKey = (string) $_GET['error'];
    $errorMessages = [
        'terms' => 'Please accept the Terms & Conditions to continue.',
        'password' => 'Passwords do not match. Please try again.',
        'required' => 'Please fill in all required fields.',
        'email' => 'Enter a valid email address.',
        'username' => 'Username must be 8 characters or fewer.',
        'name' => 'Name must be 20 characters or fewer.',
        'password_length' => 'Password must be 20 characters or fewer.',
        'phone' => 'Enter a valid phone number (10–15 digits).',
        'duplicate' => 'An account with this email or username already exists.',
        'server' => 'Registration failed. Please try again in a moment.',
        'invalid' => 'Invalid registration request. Please try again.',
    ];
    $authError = $errorMessages[$errorKey] ?? 'Registration failed. Please try again.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="Create your Eatery account">
  <title>Sign Up | Eatery</title>
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
      <div class="auth-layout auth-layout--signup">
        <aside class="auth-panel auth-panel--brand">
          <img src="assets/imgs/logo3.png" alt="Eatery" class="auth-panel__logo">
          <h1 class="auth-panel__title">Join Eatery</h1>
          <p class="auth-panel__text">Create an account to book tables, place orders, and enjoy flavors made for royalty.</p>
          <ul class="auth-panel__features">
            <li>
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12l4 4L19 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
              One account for orders &amp; reservations
            </li>
            <li>
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12l4 4L19 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
              Quick checkout every time
            </li>
            <li>
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12l4 4L19 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
              Personalized dining experience
            </li>
          </ul>
        </aside>

        <section class="auth-panel auth-panel--form">
          <div class="auth-form__header">
            <span class="auth-form__eyebrow">Registration</span>
            <h2 class="auth-form__title">Create account</h2>
            <p class="auth-form__subtitle">Fill in your details to get started</p>
          </div>

          <?php if ($authError !== '') : ?>
            <div class="auth-alert auth-alert--error" role="alert"><?php echo htmlspecialchars($authError); ?></div>
          <?php endif; ?>

          <form id="signupForm" action="assets/php/validation.php" method="post" novalidate>
            <input type="hidden" name="submit" value="1">
            <div class="auth-form-grid">
              <div class="auth-field auth-field--half">
                <label for="signup-email" class="auth-label">Email</label>
                <div class="auth-input-wrap">
                  <span class="auth-input-wrap__icon" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M4 6h16v12H4zM4 7l8 6 8-6" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/></svg>
                  </span>
                  <input type="email" name="Email" id="signup-email" class="auth-input" placeholder="you@example.com" autocomplete="email" required>
                </div>
                <span class="auth-field__error" aria-live="polite"></span>
              </div>

              <div class="auth-field auth-field--half">
                <label for="signup-username" class="auth-label">Username</label>
                <div class="auth-input-wrap">
                  <span class="auth-input-wrap__icon" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="3.5" stroke="currentColor" stroke-width="1.5"/><path d="M5 20c0-3.5 3-6 7-6s7 2.5 7 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                  </span>
                  <input type="text" name="username" id="signup-username" class="auth-input" placeholder="Username (max 8)" maxlength="8" autocomplete="username" required>
                </div>
                <span class="auth-field__error" aria-live="polite"></span>
              </div>

              <div class="auth-field auth-field--half">
                <label for="signup-name" class="auth-label">Full name</label>
                <div class="auth-input-wrap">
                  <span class="auth-input-wrap__icon" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M4 19h16M8 7h8M10 11h4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                  </span>
                  <input type="text" name="name" id="signup-name" class="auth-input" placeholder="Your name" maxlength="20" autocomplete="name" required>
                </div>
                <span class="auth-field__error" aria-live="polite"></span>
              </div>

              <div class="auth-field auth-field--half">
                <label for="signup-phone" class="auth-label">Phone number</label>
                <div class="auth-input-wrap">
                  <span class="auth-input-wrap__icon" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M6 4h4l1 4-2 1a11 11 0 005 5l1-2 4 1v4a2 2 0 01-2 2C9.6 19 5 14.4 5 8a2 2 0 012-4z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/></svg>
                  </span>
                  <input type="tel" name="phone" id="signup-phone" class="auth-input" placeholder="Phone number" autocomplete="tel" required>
                </div>
                <span class="auth-field__error" aria-live="polite"></span>
              </div>

              <div class="auth-field auth-field--half">
                <label for="signup-password" class="auth-label">Password</label>
                <div class="auth-input-wrap">
                  <span class="auth-input-wrap__icon" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><rect x="5" y="11" width="14" height="10" rx="2" stroke="currentColor" stroke-width="1.5"/><path d="M8 11V8a4 4 0 118 0v3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                  </span>
                  <input type="password" name="password" id="signup-password" class="auth-input auth-input--password" placeholder="Create password (max 20)" maxlength="20" autocomplete="new-password" required>
                  <button type="button" class="auth-input-wrap__toggle" data-password-toggle="signup-password" aria-label="Show password" aria-pressed="false">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z" stroke="currentColor" stroke-width="1.5"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.5"/></svg>
                  </button>
                </div>
                <span class="auth-field__error" aria-live="polite"></span>
              </div>

              <div class="auth-field auth-field--half">
                <label for="signup-cpassword" class="auth-label">Confirm password</label>
                <div class="auth-input-wrap">
                  <span class="auth-input-wrap__icon" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><rect x="5" y="11" width="14" height="10" rx="2" stroke="currentColor" stroke-width="1.5"/><path d="M8 11V8a4 4 0 118 0v3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                  </span>
                  <input type="password" name="cpassword" id="signup-cpassword" class="auth-input auth-input--password" placeholder="Confirm password" maxlength="20" autocomplete="new-password" required>
                  <button type="button" class="auth-input-wrap__toggle" data-password-toggle="signup-cpassword" aria-label="Show password" aria-pressed="false">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z" stroke="currentColor" stroke-width="1.5"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.5"/></svg>
                  </button>
                </div>
                <span class="auth-field__error" aria-live="polite"></span>
              </div>
            </div>

            <div class="auth-field">
              <div class="auth-terms">
                <input type="checkbox" name="terms" id="signup-terms" class="auth-terms__input" value="1">
                <label for="signup-terms" class="auth-terms__label">
                  I agree to the <a href="index.php#about">Terms &amp; Conditions</a> and understand how my information is used.
                </label>
              </div>
              <span class="auth-field__error" aria-live="polite"></span>
            </div>

            <button type="submit" class="auth-btn">
              <span class="auth-btn__spinner" aria-hidden="true"></span>
              <span class="auth-btn__label">Create account</span>
            </button>

            <p class="auth-footer">
              Already have an account? <a href="login.php">Sign in</a>
            </p>
          </form>
        </section>
      </div>
    </main>
  </div>

  <script src="assets/js/auth.js"></script>
</body>
</html>
