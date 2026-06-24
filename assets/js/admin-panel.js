(function () {
  'use strict';

  var shell = document.getElementById('adminShell');
  var sidebar = document.getElementById('adminSidebar');
  var backdrop = document.getElementById('adminSidebarBackdrop');
  var openBtn = document.getElementById('adminSidebarOpen');
  var closeBtn = document.getElementById('adminSidebarClose');
  var profileRoot = document.querySelector('[data-admin-profile]');
  var profileTrigger = document.getElementById('adminProfileTrigger');
  var profileMenu = document.getElementById('adminProfileMenu');

  function openSidebar() {
    if (!shell) return;
    shell.classList.add('is-sidebar-open');
    if (backdrop) backdrop.hidden = false;
    document.body.classList.add('admin-no-scroll');
  }

  function closeSidebar() {
    if (!shell) return;
    shell.classList.remove('is-sidebar-open');
    if (backdrop) backdrop.hidden = true;
    document.body.classList.remove('admin-no-scroll');
  }

  if (openBtn) {
    openBtn.addEventListener('click', openSidebar);
  }
  if (closeBtn) {
    closeBtn.addEventListener('click', closeSidebar);
  }
  if (backdrop) {
    backdrop.addEventListener('click', closeSidebar);
  }

  window.addEventListener('resize', function () {
    if (window.innerWidth >= 992) {
      closeSidebar();
    }
  });

  function closeProfileMenu() {
    if (!profileTrigger || !profileMenu) return;
    profileTrigger.setAttribute('aria-expanded', 'false');
    profileMenu.hidden = true;
  }

  function toggleProfileMenu() {
    if (!profileTrigger || !profileMenu) return;
    var open = profileMenu.hidden;
    profileTrigger.setAttribute('aria-expanded', open ? 'true' : 'false');
    profileMenu.hidden = !open;
  }

  document.querySelectorAll('[data-file-label]').forEach(function (input) {
    var nameEl = input.closest('.admin-file-upload') && input.closest('.admin-file-upload').querySelector('[data-file-name]');
    if (!nameEl) return;

    input.addEventListener('change', function () {
      var file = input.files && input.files[0];
      nameEl.textContent = file ? file.name : 'No file chosen';
    });
  });

  if (profileTrigger && profileMenu) {
    profileTrigger.addEventListener('click', function (e) {
      e.stopPropagation();
      toggleProfileMenu();
    });

    document.addEventListener('click', function (e) {
      if (!profileRoot || !profileRoot.contains(e.target)) {
        closeProfileMenu();
      }
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') {
        closeProfileMenu();
        closeSidebar();
      }
    });
  }
})();
