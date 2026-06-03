(function () {
  'use strict';

  var API_URL = 'assets/php/cart_api.php';
  var isSubmitting = false;
  var activeTrigger = null;
  var currentProduct = null;

  var modal = document.getElementById('cartModal');
  var toastStack = document.getElementById('cartToastStack');
  var cartBadge = document.getElementById('cart-count');

  if (!modal) {
    return;
  }

  var modalTitle = document.getElementById('cartModalTitle');
  var modalPrice = document.getElementById('cartModalPrice');
  var modalStock = document.getElementById('cartModalStock');
  var modalImg = document.getElementById('cartModalImg');
  var modalQty = document.getElementById('cartModalQty');
  var modalSubmit = document.getElementById('cartModalSubmit');
  var qtyDecrease = document.getElementById('cartQtyDecrease');
  var qtyIncrease = document.getElementById('cartQtyIncrease');

  function formatPrice(value) {
    return '\u20B9' + Number(value).toLocaleString('en-IN');
  }

  function clampQty(value, max) {
    var qty = parseInt(value, 10);
    if (isNaN(qty) || qty < 1) {
      qty = 1;
    }
    return Math.min(qty, max, 10);
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

  function showToast(message, type, title) {
    if (!toastStack) {
      return;
    }

    var toast = document.createElement('div');
    toast.className = 'cart-toast cart-toast--' + (type || 'success');
    toast.innerHTML =
      '<span class="cart-toast__icon">' + (type === 'error' ? '!' : '\u2713') + '</span>' +
      '<div class="cart-toast__body">' +
        '<p class="cart-toast__title">' + (title || (type === 'error' ? 'Unable to add item' : 'Added to cart')) + '</p>' +
        '<p class="cart-toast__message"></p>' +
      '</div>';

    toast.querySelector('.cart-toast__message').textContent = message;
    toastStack.appendChild(toast);

    window.setTimeout(function () {
      toast.classList.add('is-leaving');
      window.setTimeout(function () {
        toast.remove();
      }, 250);
    }, 3800);
  }

  function setModalOpen(isOpen) {
    modal.classList.toggle('is-open', isOpen);
    modal.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
    document.body.style.overflow = isOpen ? 'hidden' : '';
  }

  function setModalLoading(loading) {
    modalSubmit.disabled = loading;
    modalSubmit.classList.toggle('is-loading', loading);
    qtyDecrease.disabled = loading;
    qtyIncrease.disabled = loading;
    modalQty.disabled = loading;
  }

  function updateQtyControls() {
    if (!currentProduct) {
      return;
    }

    var max = Math.min(currentProduct.stock, 10);
    var qty = clampQty(modalQty.value, max);
    modalQty.value = qty;
    modalQty.max = max;
    qtyDecrease.disabled = qty <= 1 || isSubmitting;
    qtyIncrease.disabled = qty >= max || isSubmitting;
  }

  function openModal(button) {
    var stock = parseInt(button.dataset.stock, 10) || 0;
    if (stock <= 0) {
      showToast('This item is currently out of stock.', 'error', 'Out of stock');
      return;
    }

    activeTrigger = button;
    currentProduct = {
      productNo: parseInt(button.dataset.productNo, 10),
      name: button.dataset.productName || '',
      price: parseInt(button.dataset.productPrice, 10) || 0,
      img: button.dataset.productImg || '',
      stock: stock
    };

    modalTitle.textContent = currentProduct.name;
    modalPrice.textContent = formatPrice(currentProduct.price);
    modalStock.textContent = stock + ' in stock';
    modalImg.src = currentProduct.img;
    modalImg.alt = currentProduct.name;
    modalQty.value = 1;

    updateQtyControls();
    setModalLoading(false);
    setModalOpen(true);
    modalSubmit.focus();
  }

  function closeModal() {
    setModalOpen(false);
    setModalLoading(false);
    currentProduct = null;
    activeTrigger = null;
  }

  function flashButtonSuccess(button) {
    if (!button) {
      return;
    }

    button.classList.remove('is-loading');
    button.disabled = false;
    button.classList.add('is-success');

    window.setTimeout(function () {
      button.classList.remove('is-success');
    }, 1800);
  }

  function setTriggerLoading(button, loading) {
    if (!button) {
      return;
    }

    button.classList.toggle('is-loading', loading);
    button.disabled = loading;
  }

  async function submitAddToCart() {
    if (!currentProduct || isSubmitting) {
      return;
    }

    var max = Math.min(currentProduct.stock, 10);
    var qty = clampQty(modalQty.value, max);

    isSubmitting = true;
    setModalLoading(true);
    setTriggerLoading(activeTrigger, true);

    try {
      var body = new FormData();
      body.append('action', 'add');
      body.append('product_no', String(currentProduct.productNo));
      body.append('qty', String(qty));

      var response = await fetch(API_URL, {
        method: 'POST',
        body: body,
        credentials: 'same-origin',
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      });

      var data = await response.json();

      if (data.success) {
        var trigger = activeTrigger;
        updateCartBadge(data.count);
        showToast(data.message, 'success', data.product_name || 'Added to cart');
        closeModal();
        setTriggerLoading(trigger, false);
        flashButtonSuccess(trigger);
      } else {
        if (typeof data.count === 'number') {
          updateCartBadge(data.count);
        }
        showToast(data.message || 'Could not add item to cart.', 'error');
        if (data.available && data.available > 0) {
          modalQty.value = data.available;
          updateQtyControls();
        }
      }
    } catch (error) {
      showToast('Something went wrong. Please try again.', 'error', 'Network error');
    } finally {
      isSubmitting = false;
      setModalLoading(false);
      if (activeTrigger) {
        setTriggerLoading(activeTrigger, false);
      }
    }
  }

  document.addEventListener('click', function (event) {
    var addButton = event.target.closest('.js-add-to-cart');
    if (addButton && !addButton.disabled && !addButton.classList.contains('is-loading')) {
      event.preventDefault();
      openModal(addButton);
      return;
    }

    if (event.target.closest('[data-cart-modal-close]')) {
      if (!isSubmitting) {
        closeModal();
      }
      return;
    }
  });

  modalSubmit.addEventListener('click', submitAddToCart);

  qtyDecrease.addEventListener('click', function () {
    modalQty.value = clampQty(parseInt(modalQty.value, 10) - 1, parseInt(modalQty.max, 10) || 10);
    updateQtyControls();
  });

  qtyIncrease.addEventListener('click', function () {
    modalQty.value = clampQty(parseInt(modalQty.value, 10) + 1, parseInt(modalQty.max, 10) || 10);
    updateQtyControls();
  });

  modalQty.addEventListener('input', updateQtyControls);

  modalQty.addEventListener('keydown', function (event) {
    if (event.key === 'Enter') {
      event.preventDefault();
      submitAddToCart();
    }
  });

  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape' && modal.classList.contains('is-open') && !isSubmitting) {
      closeModal();
    }
  });

  if (cartBadge) {
    var initialCount = parseInt(cartBadge.textContent, 10) || 0;
    cartBadge.classList.toggle('is-empty', initialCount <= 0);
  }
})();
