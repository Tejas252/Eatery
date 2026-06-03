(function () {
  'use strict';

  document.querySelectorAll('[data-history-toggle]').forEach(function (button) {
    button.addEventListener('click', function () {
      var order = button.closest('.history-order');
      if (!order) {
        return;
      }
      var isOpen = order.classList.toggle('is-open');
      button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });
  });
})();
