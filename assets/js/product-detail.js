(function () {
  'use strict';

  var config = window.PRODUCT_DETAIL || {};
  var productNo = config.productNo || 0;

  /* ── Gallery ─────────────────────────────────────────────────────── */
  var slides = Array.prototype.slice.call(document.querySelectorAll('[data-gallery-slide]'));
  var thumbs = Array.prototype.slice.call(document.querySelectorAll('[data-gallery-thumb]'));
  var prevBtn = document.getElementById('galleryPrev');
  var nextBtn = document.getElementById('galleryNext');
  var activeIndex = 0;

  function showSlide(index) {
    if (!slides.length) {
      return;
    }
    activeIndex = (index + slides.length) % slides.length;
    slides.forEach(function (slide, i) {
      slide.classList.toggle('is-active', i === activeIndex);
    });
    thumbs.forEach(function (thumb, i) {
      thumb.classList.toggle('is-active', i === activeIndex);
    });
  }

  if (prevBtn) {
    prevBtn.addEventListener('click', function () {
      showSlide(activeIndex - 1);
    });
  }
  if (nextBtn) {
    nextBtn.addEventListener('click', function () {
      showSlide(activeIndex + 1);
    });
  }
  thumbs.forEach(function (thumb) {
    thumb.addEventListener('click', function () {
      showSlide(parseInt(thumb.dataset.galleryThumb, 10) || 0);
    });
  });

  /* ── Quantity stepper ────────────────────────────────────────────── */
  var qtyInput = document.getElementById('productQty');
  var qtyDecrease = document.getElementById('productQtyDecrease');
  var qtyIncrease = document.getElementById('productQtyIncrease');
  var addBtn = document.getElementById('productAddBtn');
  var purchase = document.getElementById('productPurchase');
  var stock = purchase ? parseInt(purchase.dataset.stock, 10) || 0 : 0;
  var maxQty = Math.min(stock, 10);

  function clampQty(value) {
    var qty = parseInt(value, 10);
    if (isNaN(qty) || qty < 1) {
      qty = 1;
    }
    return Math.min(qty, maxQty);
  }

  function updateQtyControls() {
    if (!qtyInput) {
      return;
    }
    var qty = clampQty(qtyInput.value);
    qtyInput.value = qty;
    if (qtyDecrease) {
      qtyDecrease.disabled = qty <= 1;
    }
    if (qtyIncrease) {
      qtyIncrease.disabled = qty >= maxQty;
    }
  }

  if (qtyDecrease) {
    qtyDecrease.addEventListener('click', function () {
      qtyInput.value = clampQty(parseInt(qtyInput.value, 10) - 1);
      updateQtyControls();
    });
  }
  if (qtyIncrease) {
    qtyIncrease.addEventListener('click', function () {
      qtyInput.value = clampQty(parseInt(qtyInput.value, 10) + 1);
      updateQtyControls();
    });
  }
  if (qtyInput) {
    qtyInput.addEventListener('change', updateQtyControls);
  }

  /* ── Add to cart (direct, no modal) ──────────────────────────────── */
  var toastStack = document.getElementById('cartToastStack');
  var cartBadge = document.getElementById('cart-count');

  function showToast(message, type, title) {
    if (!toastStack) {
      return;
    }
    var toast = document.createElement('div');
    toast.className = 'cart-toast cart-toast--' + (type || 'success');
    toast.innerHTML =
      '<span class="cart-toast__icon">' + (type === 'error' ? '!' : '\u2713') + '</span>' +
      '<div class="cart-toast__body">' +
        '<p class="cart-toast__title">' + (title || (type === 'error' ? 'Unable to add' : 'Added to cart')) + '</p>' +
        '<p class="cart-toast__message"></p></div>';
    toast.querySelector('.cart-toast__message').textContent = message;
    toastStack.appendChild(toast);
    window.setTimeout(function () {
      toast.classList.add('is-leaving');
      window.setTimeout(function () { toast.remove(); }, 250);
    }, 3800);
  }

  function updateCartBadge(count) {
    if (!cartBadge) {
      return;
    }
    cartBadge.textContent = count;
    cartBadge.classList.toggle('is-empty', count <= 0);
    cartBadge.classList.remove('is-bump');
    void cartBadge.offsetWidth;
    cartBadge.classList.add('is-bump');
  }

  if (addBtn && maxQty > 0) {
    addBtn.addEventListener('click', async function () {
      if (addBtn.disabled || addBtn.classList.contains('is-loading')) {
        return;
      }
      addBtn.classList.add('is-loading');
      addBtn.disabled = true;

      try {
        var body = new FormData();
        body.append('action', 'add');
        body.append('product_no', String(productNo));
        body.append('qty', String(clampQty(qtyInput.value)));

        var response = await fetch('assets/php/cart_api.php', {
          method: 'POST',
          body: body,
          credentials: 'same-origin'
        });
        var data = await response.json();

        if (!data.success) {
          throw new Error(data.message || 'Could not add to cart.');
        }

        updateCartBadge(data.count);
        showToast(data.message, 'success', data.product_name || 'Added to cart');
      } catch (error) {
        showToast(error.message || 'Could not add to cart.', 'error');
      } finally {
        addBtn.classList.remove('is-loading');
        addBtn.disabled = maxQty <= 0;
      }
    });
  }

  /* ── Review stars input ──────────────────────────────────────────── */
  var starsInput = document.getElementById('reviewStarsInput');
  var ratingField = document.getElementById('reviewRating');

  if (starsInput && ratingField) {
    var starButtons = starsInput.querySelectorAll('.review-stars-input__star');

    function setRating(value) {
      ratingField.value = String(value);
      starButtons.forEach(function (btn) {
        btn.classList.toggle('is-active', parseInt(btn.dataset.rating, 10) <= value);
      });
    }

    starButtons.forEach(function (btn) {
      btn.addEventListener('click', function () {
        setRating(parseInt(btn.dataset.rating, 10) || 5);
      });
    });
  }

  /* ── Review form ─────────────────────────────────────────────────── */
  var reviewForm = document.getElementById('reviewForm');
  var reviewSubmit = document.getElementById('reviewSubmitBtn');
  var reviewMessage = document.getElementById('reviewFormMessage');

  function renderStarsHtml(rating) {
    var html = '';
    for (var i = 1; i <= 5; i++) {
      html += '<span class="star-rating__star' + (rating >= i ? ' is-filled' : '') + '" aria-hidden="true">&#9733;</span>';
    }
    return '<span class="star-rating star-rating--sm">' + html + '</span>';
  }

  function updateSummaryUI(summary) {
    var avgEl = document.getElementById('reviewAverage');
    var countEl = document.getElementById('reviewCount');
    var breakdownEl = document.getElementById('reviewBreakdown');
    var ratingText = document.querySelector('.product-info__rating-text');

    if (avgEl) {
      avgEl.textContent = summary.average_display;
    }
    if (countEl) {
      countEl.textContent = 'Based on ' + summary.count + ' review' + (summary.count === 1 ? '' : 's');
    }
    if (ratingText) {
      ratingText.textContent = summary.count > 0
        ? summary.average_display + ' · ' + summary.count + ' review' + (summary.count === 1 ? '' : 's')
        : 'No reviews yet';
    }
    if (breakdownEl && summary.breakdown) {
      var rows = breakdownEl.querySelectorAll('.product-reviews__bar-row');
      for (var star = 5; star >= 1; star--) {
        var rowIndex = 5 - star;
        var row = rows[rowIndex];
        if (!row) {
          continue;
        }
        var count = summary.breakdown[star] || 0;
        var percent = summary.count > 0 ? Math.round((count / summary.count) * 100) : 0;
        var bar = row.querySelector('.product-reviews__bar span');
        var countSpan = row.querySelector('span:last-child');
        if (bar) {
          bar.style.width = percent + '%';
        }
        if (countSpan) {
          countSpan.textContent = String(count);
        }
      }
    }
  }

  async function reloadReviews() {
    var response = await fetch('assets/php/product_reviews_api.php?action=list&product_no=' + productNo, {
      credentials: 'same-origin'
    });
    var data = await response.json();
    if (!data.success) {
      return;
    }

    updateSummaryUI(data.summary);

    var list = document.getElementById('reviewsList');
    if (!list) {
      return;
    }

    if (!data.reviews.length) {
      list.innerHTML =
        '<div class="product-reviews__empty" id="reviewsEmpty">' +
          '<p class="product-reviews__empty-title">No reviews yet</p>' +
          '<p class="product-reviews__empty-text">Be the first to share your thoughts about this dish.</p>' +
        '</div>';
      return;
    }

    list.innerHTML = data.reviews.map(function (review) {
      return (
        '<article class="product-review-card">' +
          '<div class="product-review-card__head">' +
            '<div>' +
              '<p class="product-review-card__author">' + escapeHtml(review.author) + '</p>' +
              '<p class="product-review-card__date">' + escapeHtml(review.created_at) + '</p>' +
            '</div>' +
            renderStarsHtml(review.rating) +
          '</div>' +
          '<p class="product-review-card__text">' + escapeHtml(review.review_text).replace(/\n/g, '<br>') + '</p>' +
        '</article>'
      );
    }).join('');
  }

  function escapeHtml(text) {
    var div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }

  if (reviewForm) {
    reviewForm.addEventListener('submit', async function (event) {
      event.preventDefault();
      if (!reviewSubmit || reviewSubmit.disabled) {
        return;
      }

      reviewSubmit.disabled = true;
      reviewSubmit.classList.add('is-loading');
      if (reviewMessage) {
        reviewMessage.hidden = true;
      }

      try {
        var body = new FormData(reviewForm);
        body.append('action', 'submit');
        body.append('product_no', String(productNo));

        var response = await fetch('assets/php/product_reviews_api.php', {
          method: 'POST',
          body: body,
          credentials: 'same-origin'
        });
        var data = await response.json();

        if (!data.success) {
          throw new Error(data.message || 'Could not submit review.');
        }

        if (reviewMessage) {
          reviewMessage.textContent = data.message;
          reviewMessage.className = 'product-review-form__message is-success';
          reviewMessage.hidden = false;
        }

        await reloadReviews();
      } catch (error) {
        if (reviewMessage) {
          reviewMessage.textContent = error.message || 'Could not submit review.';
          reviewMessage.className = 'product-review-form__message is-error';
          reviewMessage.hidden = false;
        }
      } finally {
        reviewSubmit.disabled = false;
        reviewSubmit.classList.remove('is-loading');
      }
    });
  }
})();
