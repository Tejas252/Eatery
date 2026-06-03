<?php

session_start();

include('data_insert_take.php');

require_once('assets/php/cart_helpers.php');



$cartItems = [];

$subtotal = 0;

$taxRate = 0.03;



if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {

    foreach ($_SESSION['cart'] as $value) {

        $proNo = (int) ($value['no'] ?? 0);

        $qty = (int) ($value['qty'] ?? 1);



        if ($proNo <= 0) {

            continue;

        }



        $stmt = $conn->prepare('SELECT product_no, product_name, product_price, product_qty, product_img, product_type FROM products WHERE product_no = ? LIMIT 1');

        $stmt->bind_param('i', $proNo);

        $stmt->execute();

        $product = $stmt->get_result()->fetch_assoc();

        $stmt->close();



        if (!$product) {

            continue;

        }



        $price = (int) $product['product_price'];

        $lineTotal = $price * $qty;

        $subtotal += $lineTotal;

        $maxQty = min((int) $product['product_qty'], 10);



        $cartItems[] = [

            'no' => $proNo,

            'qty' => $qty,

            'name' => $product['product_name'],

            'price' => $price,

            'img' => $product['product_img'],

            'type' => $product['product_type'],

            'line_total' => $lineTotal,

            'max_qty' => max(1, $maxQty),

        ];

    }

}



$tax = (int) round($subtotal * $taxRate);

$shipping = 0;

$discount = 0;

$grandTotal = $subtotal + $tax + $shipping - $discount;

$hasItems = count($cartItems) > 0;

if ($hasItems && isset($_SESSION['ordered'])) {
    unset($_SESSION['ordered']);
}

$isOrdered = isset($_SESSION['ordered']);

$orderStatus = '';

$tableNo = isset($_SESSION['table']) ? (int) $_SESSION['table'] : null;



if ($isOrdered && isset($_SESSION['id'])) {

    $customerId = (int) $_SESSION['id'];

    $statusQuery = "SELECT status FROM orders WHERE customer_id = $customerId AND (status = 'ordered' OR status = 'accepted' OR status = 'Deliverd') ORDER BY order_id DESC LIMIT 1";

    $statusResult = mysqli_query($conn, $statusQuery);

    if ($statusResult && ($statusRow = mysqli_fetch_assoc($statusResult))) {

        $orderStatus = $statusRow['status'];

    }

}



$showOrderSuccess = $isOrdered && !$hasItems;

$showEmptyCart = !$hasItems && !$isOrdered;

$showCart = $hasItems;



function cart_status_label(string $status): string

{

    $labels = [

        'ordered' => 'Order Received',

        'accepted' => 'Being Prepared',

        'Deliverd' => 'Delivered',

        'done' => 'Completed',

    ];

    return $labels[strtolower($status)] ?? ucfirst($status);

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

  <meta charset="utf-8">

  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <meta name="description" content="Your Eatery cart">

  <title>Eatery | Your Cart</title>

  <link rel="icon" href="assets/imgs/logo3.png">

  <link rel="stylesheet" href="assets/vendors/themify-icons/css/themify-icons.css">

  <link rel="stylesheet" href="assets/css/foodhut.css">

  <link rel="stylesheet" href="assets/css/cart.css">

  <link rel="stylesheet" href="assets/css/add-to-cart.css">

  <link rel="preconnect" href="https://fonts.googleapis.com">

  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&display=swap" rel="stylesheet">

</head>

<body class="cart-body">



  <nav class="cart-nav navbar navbar-expand-lg navbar-dark fixed-top">

    <div class="cart-nav__inner">

      <a class="cart-nav__brand" href="index.php">

        <img src="assets/imgs/logo3.png" alt="Eatery" class="cart-nav__logo">

        <span class="cart-nav__name">Eatery</span>

      </a>



      <button class="navbar-toggler cart-nav__toggler" type="button" data-toggle="collapse" data-target="#cartNavMenu" aria-label="Toggle navigation">

        <span class="navbar-toggler-icon"></span>

      </button>



      <div class="collapse navbar-collapse" id="cartNavMenu">

        <ul class="navbar-nav cart-nav__links">

          <li class="nav-item"><a class="nav-link" href="index.php#home">Home</a></li>

          <li class="nav-item"><a class="nav-link" href="index.php#book-table">Book Table</a></li>

          <li class="nav-item"><a class="nav-link" href="index.php#Menu">Menu</a></li>

          <li class="nav-item"><a class="nav-link" href="index.php#about">About</a></li>

        </ul>



        <ul class="navbar-nav cart-nav__actions">

          <li class="nav-item">

            <a href="cart.php" class="cart-nav__icon-btn cart-link" aria-label="View cart">

              <img src="assets/imgs/cart.png" class="nav-cart-icon" alt="">

              <span class="cart-badge<?php echo get_cart_item_count() > 0 ? '' : ' is-empty'; ?>" id="cart-count"><?php echo get_cart_item_count(); ?></span>

            </a>

          </li>

          <li class="nav-item">

            <a href="<?php echo isset($_SESSION['login']) ? 'assets/php/profile.php' : 'login.php'; ?>" class="cart-nav__icon-btn" aria-label="Account">

              <img src="assets/imgs/user.png" class="nav-cart-icon" alt="">

            </a>

          </li>

          <li class="nav-item">

            <a href="<?php echo isset($_SESSION['login']) ? 'assets/php/logout.php' : 'login.php'; ?>" class="cart-nav__auth">

              <?php echo isset($_SESSION['login']) ? 'Log out' : 'Login'; ?>

            </a>

          </li>

        </ul>

      </div>

    </div>

  </nav>



  <main class="cart-page">

    <div class="cart-page__inner">



      <header class="cart-page__hero">

        <div class="cart-page__hero-text">

          <?php if ($showOrderSuccess) : ?>

            <span class="cart-page__eyebrow">Order Confirmed</span>

            <h1 class="cart-page__title">Thank you!</h1>

            <p class="cart-page__subtitle">Your order has been placed and our kitchen is getting started.</p>

          <?php elseif ($showEmptyCart) : ?>

            <span class="cart-page__eyebrow">Your Bag</span>

            <h1 class="cart-page__title">Shopping Cart</h1>

            <p class="cart-page__subtitle">Your cart is empty — add something delicious from our menu.</p>

          <?php else : ?>

            <span class="cart-page__eyebrow">Checkout</span>

            <h1 class="cart-page__title">Shopping Cart</h1>

            <p class="cart-page__subtitle"><?php echo count($cartItems); ?> item<?php echo count($cartItems) === 1 ? '' : 's'; ?> ready for checkout</p>

          <?php endif; ?>

        </div>

      </header>



      <?php if ($showOrderSuccess) : ?>

        <section class="cart-order-success">

          <div class="cart-order-success__card">

            <div class="cart-order-success__icon-wrap">

              <span class="cart-order-success__icon">&#10003;</span>

            </div>

            <h2 class="cart-order-success__title">Order placed successfully</h2>

            <p class="cart-order-success__text">Sit back and relax. We will prepare your food and serve it to your table.</p>



            <div class="cart-order-success__meta">

              <?php if ($orderStatus !== '') : ?>

                <div class="cart-order-success__badge"><?php echo htmlspecialchars(cart_status_label($orderStatus)); ?></div>

              <?php endif; ?>

              <?php if ($tableNo) : ?>

                <div class="cart-order-success__detail">Table <?php echo $tableNo; ?></div>

              <?php endif; ?>

            </div>



            <div class="cart-order-success__actions">

              <a href="assets/php/history.php" class="cart-order-success__btn cart-order-success__btn--primary">View Order History</a>

              <a href="index.php#Menu" class="cart-order-success__btn cart-order-success__btn--ghost">Order More</a>

            </div>

          </div>

        </section>



      <?php elseif ($showEmptyCart) : ?>

        <section class="cart-empty">

          <div class="cart-empty__visual">

            <div class="cart-empty__ring"></div>

            <div class="cart-empty__icon">&#128722;</div>

          </div>

          <h2 class="cart-empty__title">Nothing here yet</h2>

          <p class="cart-empty__text">Browse our pizzas, burgers, and chef specials. Your favorites are just a few clicks away.</p>

          <a href="index.php#Menu" class="cart-empty__btn">Explore Menu</a>

        </section>



      <?php else : ?>

        <form action="assets/php/manage_order.php" method="post" id="checkoutForm">

          <div class="cart-layout">

            <section class="cart-items-panel" aria-label="Cart items">

              <div class="cart-items-panel__head">

                <span>Product</span>

                <span>Quantity</span>

                <span>Total</span>

              </div>

              <div class="cart-items-list">

                <?php foreach ($cartItems as $item) : ?>

                  <article class="cart-item" data-product-no="<?php echo (int) $item['no']; ?>" data-price="<?php echo (int) $item['price']; ?>">

                    <div class="cart-item__product">

                      <div class="cart-item__media">

                        <img class="cart-item__img" src="assets/uploads/<?php echo htmlspecialchars($item['img']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">

                      </div>

                      <div class="cart-item__info">

                        <span class="cart-item__type"><?php echo htmlspecialchars(ucfirst(strtolower($item['type']))); ?></span>

                        <h2 class="cart-item__name"><?php echo htmlspecialchars($item['name']); ?></h2>

                        <p class="cart-item__unit-price"><?php echo '&#8377;' . number_format($item['price']); ?> each</p>

                        <button type="button" class="cart-item__remove" data-cart-remove>Remove item</button>

                      </div>

                    </div>

                    <div class="cart-item__qty-wrap">

                      <div class="cart-item__qty">

                        <button type="button" class="cart-item__qty-btn" data-qty-decrease aria-label="Decrease quantity">−</button>

                        <input class="cart-item__qty-input" type="number" name="qty_<?php echo (int) $item['no']; ?>" value="<?php echo (int) $item['qty']; ?>" min="1" max="<?php echo (int) $item['max_qty']; ?>" inputmode="numeric">

                        <button type="button" class="cart-item__qty-btn" data-qty-increase aria-label="Increase quantity">+</button>

                      </div>

                    </div>

                    <div class="cart-item__line-total"><?php echo '&#8377;' . number_format($item['line_total']); ?></div>

                  </article>

                <?php endforeach; ?>

              </div>

            </section>



            <aside class="cart-summary" aria-label="Order summary">

              <h2 class="cart-summary__title">Order Summary</h2>

              <div class="cart-summary__rows">

                <div class="cart-summary__row"><span>Subtotal</span><span id="cartSubtotal"><?php echo '&#8377;' . number_format($subtotal); ?></span></div>

                <div class="cart-summary__row"><span>Tax (3%)</span><span id="cartTax"><?php echo '&#8377;' . number_format($tax); ?></span></div>

                <div class="cart-summary__row cart-summary__row--shipping"><span>Delivery</span><span id="cartShipping">Free</span></div>

                <div class="cart-summary__row cart-summary__row--discount"><span>Discount</span><span id="cartDiscount">&mdash;</span></div>

              </div>

              <div class="cart-summary__total">

                <span class="cart-summary__total-label">Grand Total</span>

                <span class="cart-summary__total-value" id="cartGrandTotal"><?php echo '&#8377;' . number_format($grandTotal); ?></span>

              </div>



              <?php if (!$isOrdered) : ?>

                <div class="cart-summary__note">

                  <label for="order_desc">Special instructions</label>

                  <textarea name="order_desc" id="order_desc" class="cart-summary__textarea" placeholder="Extra cheese, no onions, etc." rows="3"></textarea>

                </div>

              <?php endif; ?>

              <input type="hidden" name="cust_id" value="<?php echo isset($_SESSION['id']) ? (int) $_SESSION['id'] : ''; ?>">

              <input type="hidden" name="status" value="ordered">

              <input type="hidden" id="bill" name="total" value="<?php echo (int) $grandTotal; ?>">

              <button type="submit" name="order" class="cart-checkout-btn">Proceed to Checkout</button>



              <div class="cart-summary__links">

                <a href="index.php#Menu" class="cart-summary__link">Continue shopping</a>

              </div>

            </aside>

          </div>

        </form>

      <?php endif; ?>



    </div>

  </main>



  <div class="cart-toast-stack" id="cartToastStack" aria-live="polite" aria-atomic="true"></div>



  <footer class="cart-footer">

    <div class="cart-footer__inner">

      <div class="cart-footer__col">

        <h3>Email</h3>

        <p>Contact@eatery.com</p>

      </div>

      <div class="cart-footer__col">

        <h3>Call</h3>

        <p>+91 9898252898</p>

      </div>

      <div class="cart-footer__col">

        <h3>Visit</h3>

        <p>111, Platinam hub, Noida</p>

      </div>

    </div>

  </footer>



  <script src="assets/vendors/jquery/jquery-3.4.1.js"></script>

  <script src="assets/vendors/bootstrap/bootstrap.bundle.js"></script>

  <?php if ($showCart) : ?>

    <script src="assets/js/cart-page.js"></script>

  <?php endif; ?>

</body>

</html>


