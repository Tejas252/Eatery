(function () {
  'use strict';

  function initFeaturedMenu() {
    var section = document.getElementById('featured-menu');
    if (!section) {
      return;
    }

    var tabs = section.querySelectorAll('.featured-menu__tab');
    var grid = document.getElementById('featuredMenuGrid');
    var countEl = document.getElementById('featuredMenuCount');
    var emptyFilter = document.getElementById('featuredMenuEmptyFilter');

    if (!grid || !tabs.length) {
      return;
    }

    var items = grid.querySelectorAll('.featured-menu__item');

    function setActiveTab(activeTab) {
      tabs.forEach(function (tab) {
        var isActive = tab === activeTab;
        tab.classList.toggle('is-active', isActive);
        tab.setAttribute('aria-selected', isActive ? 'true' : 'false');
      });
    }

    function updateCount(visible) {
      if (!countEl) {
        return;
      }
      var n = visible.length;
      countEl.textContent = n + ' item' + (n === 1 ? '' : 's');
    }

    function applyFilter(filter, activeTab) {
      setActiveTab(activeTab);
      grid.classList.add('is-filtering');

      window.setTimeout(function () {
        var visible = [];

        items.forEach(function (item) {
          var category = (item.getAttribute('data-category') || '').toLowerCase();
          var show = filter === 'all' || category === filter;
          item.classList.toggle('is-hidden', !show);

          if (show) {
            visible.push(item);
            item.classList.remove('is-entering');
            void item.offsetWidth;
            item.classList.add('is-entering');
          } else {
            item.classList.remove('is-entering');
          }
        });

        if (emptyFilter) {
          emptyFilter.hidden = visible.length > 0;
        }

        updateCount(visible);
        grid.classList.remove('is-filtering');
      }, 120);
    }

    tabs.forEach(function (tab) {
      tab.addEventListener('click', function () {
        if (tab.classList.contains('is-active')) {
          return;
        }
        applyFilter(tab.getAttribute('data-filter') || 'all', tab);
      });
    });

    updateCount(Array.prototype.slice.call(items));
  }

  document.addEventListener('DOMContentLoaded', initFeaturedMenu);
})();
