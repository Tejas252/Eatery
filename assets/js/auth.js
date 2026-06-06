(function () {
  'use strict';

  function setFieldError(input, message) {
    var field = input.closest('.auth-field') || input.closest('.auth-terms');
    if (!field) return;
    var errorEl = field.querySelector('.auth-field__error');
    if (errorEl) {
      errorEl.textContent = message || '';
    }
    if (input.classList) {
      input.classList.toggle('is-invalid', Boolean(message));
    }
    if (field.classList && field.classList.contains('auth-terms')) {
      field.classList.toggle('is-invalid', Boolean(message));
    }
  }

  function clearFieldError(input) {
    setFieldError(input, '');
  }

  function isValidEmail(value) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
  }

  document.querySelectorAll('[data-password-toggle]').forEach(function (btn) {
    var targetId = btn.getAttribute('data-password-toggle');
    var input = document.getElementById(targetId);
    if (!input) return;

    btn.addEventListener('click', function () {
      var isHidden = input.type === 'password';
      input.type = isHidden ? 'text' : 'password';
      btn.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
      btn.setAttribute('aria-pressed', isHidden ? 'true' : 'false');
    });
  });

  document.querySelectorAll('.auth-input').forEach(function (input) {
    input.addEventListener('input', function () {
      clearFieldError(input);
    });
  });

  var termsInput = document.getElementById('signup-terms');
  if (termsInput) {
    termsInput.addEventListener('change', function () {
      var termsField = termsInput.closest('.auth-field');
      if (!termsField) return;
      termsField.querySelector('.auth-field__error').textContent = '';
      var termsBox = termsField.querySelector('.auth-terms');
      if (termsBox) {
        termsBox.classList.remove('is-invalid');
      }
    });
  }

  var loginForm = document.getElementById('loginForm');
  if (loginForm) {
    loginForm.addEventListener('submit', function (e) {
      var email = loginForm.querySelector('[name="email"]');
      var password = loginForm.querySelector('[name="password"]');
      var valid = true;

      if (!email.value.trim()) {
        setFieldError(email, 'Email is required.');
        valid = false;
      } else if (!isValidEmail(email.value.trim())) {
        setFieldError(email, 'Enter a valid email address.');
        valid = false;
      }

      if (!password.value) {
        setFieldError(password, 'Password is required.');
        valid = false;
      }

      if (!valid) {
        e.preventDefault();
        return;
      }

      var btn = loginForm.querySelector('[type="submit"]');
      if (btn) {
        btn.classList.add('is-loading');
        btn.setAttribute('aria-busy', 'true');
      }
    });
  }

  var signupForm = document.getElementById('signupForm');
  if (signupForm) {
    signupForm.addEventListener('submit', function (e) {
      var email = signupForm.querySelector('[name="Email"]');
      var username = signupForm.querySelector('[name="username"]');
      var name = signupForm.querySelector('[name="name"]');
      var phone = signupForm.querySelector('[name="phone"]');
      var password = signupForm.querySelector('[name="password"]');
      var cpassword = signupForm.querySelector('[name="cpassword"]');
      var terms = signupForm.querySelector('[name="terms"]');
      var valid = true;

      [email, username, name, phone, password, cpassword].forEach(function (input) {
        if (!input) return;
        if (!input.value.trim()) {
          setFieldError(input, 'This field is required.');
          valid = false;
        } else {
          clearFieldError(input);
        }
      });

      if (email && email.value.trim() && !isValidEmail(email.value.trim())) {
        setFieldError(email, 'Enter a valid email address.');
        valid = false;
      }

      if (username && username.value.trim().length > 8) {
        setFieldError(username, 'Username must be 8 characters or fewer.');
        valid = false;
      }

      if (name && name.value.trim().length > 20) {
        setFieldError(name, 'Name must be 20 characters or fewer.');
        valid = false;
      }

      if (password && password.value.length > 20) {
        setFieldError(password, 'Password must be 20 characters or fewer.');
        valid = false;
      }

      if (phone && phone.value.trim() && !/^\d{10,15}$/.test(phone.value.trim())) {
        setFieldError(phone, 'Enter a valid phone number (10–15 digits).');
        valid = false;
      }

      if (password && cpassword && password.value !== cpassword.value) {
        setFieldError(cpassword, 'Passwords do not match.');
        valid = false;
      }

      if (terms && !terms.checked) {
        var termsField = terms.closest('.auth-field');
        if (termsField) {
          var termsError = termsField.querySelector('.auth-field__error');
          if (termsError) {
            termsError.textContent = 'You must accept the Terms & Conditions.';
          }
          var termsBox = termsField.querySelector('.auth-terms');
          if (termsBox) {
            termsBox.classList.add('is-invalid');
          }
        }
        valid = false;
      } else if (terms) {
        var termsFieldClear = terms.closest('.auth-field');
        if (termsFieldClear) {
          var termsErrorClear = termsFieldClear.querySelector('.auth-field__error');
          if (termsErrorClear) {
            termsErrorClear.textContent = '';
          }
          var termsBoxClear = termsFieldClear.querySelector('.auth-terms');
          if (termsBoxClear) {
            termsBoxClear.classList.remove('is-invalid');
          }
        }
      }

      if (!valid) {
        e.preventDefault();
        return;
      }

      var btn = signupForm.querySelector('[type="submit"]');
      if (btn) {
        btn.classList.add('is-loading');
        btn.setAttribute('aria-busy', 'true');
      }
    });
  }
})();
