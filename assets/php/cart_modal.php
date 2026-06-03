<div class="cart-toast-stack" id="cartToastStack" aria-live="polite" aria-atomic="true"></div>

<div class="cart-modal" id="cartModal" aria-hidden="true">
  <div class="cart-modal__overlay" data-cart-modal-close></div>
  <div class="cart-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="cartModalTitle">
    <button type="button" class="cart-modal__close" data-cart-modal-close aria-label="Close">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
      </svg>
    </button>

    <div class="cart-modal__preview">
      <img class="cart-modal__img" id="cartModalImg" src="" alt="">
    </div>

    <div class="cart-modal__content">
      <p class="cart-modal__eyebrow">Add to your order</p>
      <h3 class="cart-modal__title" id="cartModalTitle"></h3>
      <p class="cart-modal__price" id="cartModalPrice"></p>
      <p class="cart-modal__stock" id="cartModalStock"></p>

      <div class="cart-modal__qty">
        <span class="cart-modal__qty-label">Quantity</span>
        <div class="qty-stepper">
          <button type="button" class="qty-stepper__btn" id="cartQtyDecrease" aria-label="Decrease quantity">−</button>
          <input type="number" class="qty-stepper__input" id="cartModalQty" value="1" min="1" max="10" inputmode="numeric" aria-label="Quantity">
          <button type="button" class="qty-stepper__btn" id="cartQtyIncrease" aria-label="Increase quantity">+</button>
        </div>
      </div>

      <button type="button" class="cart-modal__submit" id="cartModalSubmit">
        <span class="cart-modal__submit-text">Add to Cart</span>
        <span class="cart-modal__submit-loader" aria-hidden="true"></span>
      </button>
    </div>
  </div>
</div>
