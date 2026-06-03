(function () {
  function getSlideStep(track) {
    var slide = track.querySelector('.product-carousel__slide');
    if (!slide) {
      return track.clientWidth;
    }
    var styles = window.getComputedStyle(track);
    var gap = parseFloat(styles.columnGap || styles.gap || '20') || 20;
    return slide.offsetWidth + gap;
  }

  function updateButtons(track, prevBtn, nextBtn) {
    var maxScroll = track.scrollWidth - track.clientWidth - 2;
    prevBtn.disabled = track.scrollLeft <= 2;
    nextBtn.disabled = track.scrollLeft >= maxScroll;
  }

  function initCarousel(root) {
    var carouselId = root.getAttribute('data-carousel');
    var track = root.querySelector('[data-carousel-track]');
    var prevBtn = document.querySelector('[data-carousel-prev="' + carouselId + '"]');
    var nextBtn = document.querySelector('[data-carousel-next="' + carouselId + '"]');

    if (!track || !prevBtn || !nextBtn) {
      return;
    }

    function scrollByStep(direction) {
      track.scrollBy({
        left: direction * getSlideStep(track),
        behavior: 'smooth'
      });
    }

    prevBtn.addEventListener('click', function () {
      scrollByStep(-1);
    });

    nextBtn.addEventListener('click', function () {
      scrollByStep(1);
    });

    track.addEventListener('scroll', function () {
      updateButtons(track, prevBtn, nextBtn);
    });

    window.addEventListener('resize', function () {
      updateButtons(track, prevBtn, nextBtn);
    });

    updateButtons(track, prevBtn, nextBtn);
  }

  document.addEventListener('DOMContentLoaded', function () {
    var carousels = document.querySelectorAll('[data-carousel]');
    carousels.forEach(initCarousel);
  });
})();
