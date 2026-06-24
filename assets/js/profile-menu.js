(function () {
  'use strict';

  function closeMenu(menu, trigger) {
    if (!menu || !trigger) {
      return;
    }
    menu.classList.remove('is-open');
    trigger.classList.remove('is-open');
    trigger.setAttribute('aria-expanded', 'false');
    menu.setAttribute('hidden', '');
  }

  function openMenu(menu, trigger) {
    menu.classList.add('is-open');
    trigger.classList.add('is-open');
    trigger.setAttribute('aria-expanded', 'true');
    menu.removeAttribute('hidden');
  }

  function initProfileMenu(root) {
    var trigger = root.querySelector('.nav-profile__trigger');
    var menu = root.querySelector('.nav-profile__dropdown');

    if (!trigger || !menu) {
      return;
    }

    trigger.addEventListener('click', function (event) {
      event.stopPropagation();
      var isOpen = menu.classList.contains('is-open');
      document.querySelectorAll('[data-profile-menu]').forEach(function (other) {
        if (other !== root) {
          closeMenu(other.querySelector('.nav-profile__dropdown'), other.querySelector('.nav-profile__trigger'));
        }
      });
      if (isOpen) {
        closeMenu(menu, trigger);
      } else {
        openMenu(menu, trigger);
      }
    });

    menu.addEventListener('click', function (event) {
      event.stopPropagation();
    });

    menu.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') {
        closeMenu(menu, trigger);
        trigger.focus();
      }
    });
  }

  document.addEventListener('click', function () {
    document.querySelectorAll('[data-profile-menu]').forEach(function (root) {
      closeMenu(root.querySelector('.nav-profile__dropdown'), root.querySelector('.nav-profile__trigger'));
    });
  });

  document.addEventListener('keydown', function (event) {
    if (event.key !== 'Escape') {
      return;
    }
    document.querySelectorAll('[data-profile-menu]').forEach(function (root) {
      closeMenu(root.querySelector('.nav-profile__dropdown'), root.querySelector('.nav-profile__trigger'));
    });
  });

  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-profile-menu]').forEach(initProfileMenu);
  });
})();
