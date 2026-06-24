(function () {
  'use strict';

  var prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  function animateCount(el) {
    var target = parseInt(el.getAttribute('data-count'), 10);
    var suffix = el.getAttribute('data-suffix') || '';
    if (isNaN(target) || prefersReducedMotion) {
      el.textContent = target + suffix;
      return;
    }

    var duration = 1400;
    var start = null;

    function step(timestamp) {
      if (!start) {
        start = timestamp;
      }
      var progress = Math.min((timestamp - start) / duration, 1);
      var eased = 1 - Math.pow(1 - progress, 3);
      var current = Math.floor(eased * target);
      el.textContent = current.toLocaleString() + suffix;
      if (progress < 1) {
        window.requestAnimationFrame(step);
      }
    }

    window.requestAnimationFrame(step);
  }

  function initScrollAnimations() {
    var items = document.querySelectorAll('[data-about-animate]');
    if (!items.length) {
      return;
    }

    if (prefersReducedMotion || !('IntersectionObserver' in window)) {
      items.forEach(function (el) {
        el.classList.add('is-visible');
      });
      document.querySelectorAll('.about-stats__value[data-count]').forEach(animateCount);
      return;
    }

    var observer = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (!entry.isIntersecting) {
            return;
          }
          entry.target.classList.add('is-visible');
          observer.unobserve(entry.target);

          entry.target.querySelectorAll('.about-stats__value[data-count]').forEach(function (counter) {
            if (!counter.dataset.animated) {
              counter.dataset.animated = '1';
              animateCount(counter);
            }
          });
        });
      },
      { root: null, rootMargin: '0px 0px -8% 0px', threshold: 0.12 }
    );

    items.forEach(function (el) {
      observer.observe(el);
    });

    document.querySelectorAll('.about-stats__item[data-about-animate]').forEach(function (statItem) {
      observer.observe(statItem);
    });
  }

  document.addEventListener('DOMContentLoaded', initScrollAnimations);
})();
