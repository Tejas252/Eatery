(function () {
  'use strict';

  var TAX_RATE = 0.03;
  var page = document.querySelector('.cart-page');
  if (!page) {
    return;
  }

  var API_URL = 'assets/php/cart_api.php';
  var toastStack = document.getElementById('cartToastStack');

  function formatCurrency(amount) {
    return '\u20B9' + Math.round(amount).toLocaleString('en-IN');
  }

  function showToast(message, type, title) {
    if (!toastStack) {
      return;
    }

    var toast = document.createElement('div');
    toast.className = 'cart-toast cart-toast--' + (type || 'success');
    toast.innerHTML =
      '<span class="cart-toast__icon">' + (type === 'error' ? '!' : '\u2713') + '</span>' +
      '<div class="cart-toast__body">' +
        '<p class="cart-toast__title">' + (title || (type === 'error' ? 'Action failed' : 'Success')) + '</p>' +
        '<p class="cart-toast__message"></p>' +
      '</div>';
    toast.querySelector('.cart-toast__message').textContent = message;
    toastStack.appendChild(toast);

    window.setTimeout(function () {
      toast.classList.add('is-leaving');
      window.setTimeout(function () {
        toast.remove();
      }, 250);
    }, 3200);
  }

  function updateCartBadge(count) {
    var badge = document.getElementById('cart-count');
    if (!badge) {
      return;
    }
    badge.textContent = count;
    badge.classList.toggle('is-empty', count <= 0);
  }

  function clampQty(value, min, max) {
    var qty = parseInt(value, 10);
    if (isNaN(qty)) {
      qty = min;
    }
    return Math.max(min, Math.min(max, qty));
  }

  function recalculateTotals() {
    var subtotal = 0;
    var items = page.querySelectorAll('.cart-item');

    items.forEach(function (item) {
      var price = parseFloat(item.dataset.price) || 0;
      var qtyInput = item.querySelector('.cart-item__qty-input');
      var lineTotalEl = item.querySelector('.cart-item__line-total');
      var qty = clampQty(qtyInput.value, 1, parseInt(qtyInput.max, 10) || 10);
      qtyInput.value = qty;

      var lineTotal = price * qty;
      subtotal += lineTotal;
      lineTotalEl.textContent = formatCurrency(lineTotal);

      var decreaseBtn = item.querySelector('[data-qty-decrease]');
      var increaseBtn = item.querySelector('[data-qty-increase]');
      if (decreaseBtn) {
        decreaseBtn.disabled = qty <= 1;
      }
      if (increaseBtn) {
        increaseBtn.disabled = qty >= parseInt(qtyInput.max, 10);
      }
    });

    var tax = Math.round(subtotal * TAX_RATE);
    var shipping = 0;
    var discount = 0;
    var grandTotal = subtotal + tax + shipping - discount;

    var subtotalEl = document.getElementById('cartSubtotal');
    var taxEl = document.getElementById('cartTax');
    var shippingEl = document.getElementById('cartShipping');
    var discountEl = document.getElementById('cartDiscount');
    var totalEl = document.getElementById('cartGrandTotal');
    var billInput = document.getElementById('bill');

    if (subtotalEl) subtotalEl.textContent = formatCurrency(subtotal);
    if (taxEl) taxEl.textContent = formatCurrency(tax);
    if (shippingEl) shippingEl.textContent = shipping === 0 ? 'Free' : formatCurrency(shipping);
    if (discountEl) discountEl.textContent = discount === 0 ? '\u2014' : formatCurrency(discount);
    if (totalEl) totalEl.textContent = formatCurrency(grandTotal);
    if (billInput) billInput.value = String(grandTotal);
  }

  function removeItem(button) {
    if (button.disabled) {
      return;
    }

    var item = button.closest('.cart-item');
    if (!item) {
      return;
    }

    var productNo = item.dataset.productNo;
    button.disabled = true;
    item.classList.add('is-removing');

    var body = new FormData();
    body.append('action', 'remove');
    body.append('product_no', productNo);

    fetch(API_URL, {
      method: 'POST',
      body: body,
      credentials: 'same-origin'
    })
      .then(function (response) {
        return response.json();
      })
      .then(function (data) {
        if (!data.success) {
          throw new Error(data.message || 'Could not remove item.');
        }

        updateCartBadge(data.count);
        showToast(data.message, 'success', 'Removed');

        item.style.height = item.offsetHeight + 'px';
        item.classList.add('is-removed');
        window.setTimeout(function () {
          item.remove();
          if (!page.querySelector('.cart-item')) {
            window.location.reload();
          } else {
            recalculateTotals();
          }
        }, 280);
      })
      .catch(function (error) {
        item.classList.remove('is-removing');
        button.disabled = false;
        showToast(error.message || 'Could not remove item.', 'error');
      });
  }

  page.addEventListener('click', function (event) {
    var decreaseBtn = event.target.closest('[data-qty-decrease]');
    var increaseBtn = event.target.closest('[data-qty-increase]');
    var removeBtn = event.target.closest('[data-cart-remove]');

    if (decreaseBtn) {
      var item = decreaseBtn.closest('.cart-item');
      var input = item.querySelector('.cart-item__qty-input');
      input.value = clampQty(parseInt(input.value, 10) - 1, 1, parseInt(input.max, 10));
      recalculateTotals();
      return;
    }

    if (increaseBtn) {
      var itemInc = increaseBtn.closest('.cart-item');
      var inputInc = itemInc.querySelector('.cart-item__qty-input');
      inputInc.value = clampQty(parseInt(inputInc.value, 10) + 1, 1, parseInt(inputInc.max, 10));
      recalculateTotals();
      return;
    }

    if (removeBtn) {
      event.preventDefault();
      removeItem(removeBtn);
    }
  });

  page.addEventListener('input', function (event) {
    if (event.target.matches('.cart-item__qty-input')) {
      recalculateTotals();
    }
  });

  recalculateTotals();
})();
