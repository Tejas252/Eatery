<?php
session_start();

if (!isset($_SESSION['login']) || !isset($_SESSION['id'])) {
    header('Location: ../../login.php');
    exit;
}

include 'config.php';
require_once __DIR__ . '/order_history_helpers.php';
require_once __DIR__ . '/cart_helpers.php';

$customerId = (int) $_SESSION['id'];
$orderRows = fetch_customer_orders($conn, $customerId);
$orderGroups = group_order_rows($orderRows);
$totalOrders = count($orderGroups);

$completedCount = 0;
$activeCount = 0;
foreach ($orderGroups as $group) {
    $meta = history_status_meta($group['status']);
    if ($meta['class'] === 'completed') {
        $completedCount++;
    } else {
        $activeCount++;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Eatery | Order History</title>
  <link rel="icon" href="../../assets/imgs/logo3.png">
  <link rel="stylesheet" href="../../assets/css/foodhut.css">
  <link rel="stylesheet" href="../../assets/css/cart.css">
  <link rel="stylesheet" href="../../assets/css/add-to-cart.css">
  <link rel="stylesheet" href="../../assets/css/history.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&display=swap" rel="stylesheet">
</head>
<body class="history-body">

  <nav class="cart-nav navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="cart-nav__inner">
      <a class="cart-nav__brand" href="../../index.php">
        <img src="../../assets/imgs/logo3.png" alt="Eatery" class="cart-nav__logo">
        <span class="cart-nav__name">Eatery</span>
      </a>

      <button class="navbar-toggler cart-nav__toggler" type="button" data-toggle="collapse" data-target="#historyNavMenu" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="historyNavMenu">
        <ul class="navbar-nav cart-nav__links">
          <li class="nav-item"><a class="nav-link" href="../../index.php#home">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="../../index.php#Menu">Menu</a></li>
          <li class="nav-item"><a class="nav-link" href="../../cart.php">Cart</a></li>
          <li class="nav-item"><a class="nav-link" href="profile.php">Profile</a></li>
        </ul>

        <ul class="navbar-nav cart-nav__actions">
          <li class="nav-item">
            <a href="../../cart.php" class="cart-nav__icon-btn cart-link" aria-label="View cart">
              <img src="../../assets/imgs/cart.png" class="nav-cart-icon" alt="">
              <span class="cart-badge<?php echo get_cart_item_count() > 0 ? '' : ' is-empty'; ?>"><?php echo get_cart_item_count(); ?></span>
            </a>
          </li>
          <li class="nav-item">
            <a href="logout.php" class="cart-nav__auth">Log out</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <main class="history-page">
    <div class="history-page__inner">

      <header class="history-page__hero">
        <span class="history-page__eyebrow">Your Account</span>
        <h1 class="history-page__title">Order History</h1>
        <p class="history-page__subtitle">Track your past and active orders, view details, and check payment status.</p>
      </header>

      <?php if ($totalOrders > 0) : ?>
        <div class="history-stats">
          <div class="history-stat">
            <span class="history-stat__value"><?php echo $totalOrders; ?></span>
            <span class="history-stat__label">Total Orders</span>
          </div>
          <div class="history-stat">
            <span class="history-stat__value"><?php echo $activeCount; ?></span>
            <span class="history-stat__label">Active</span>
          </div>
          <div class="history-stat">
            <span class="history-stat__value"><?php echo $completedCount; ?></span>
            <span class="history-stat__label">Completed</span>
          </div>
        </div>

        <div class="history-list">
          <?php foreach ($orderGroups as $group) :
            $statusMeta = history_status_meta($group['status']);
            $paymentMeta = history_payment_meta($group['status']);
            $itemCount = count($group['items']);
            ?>
            <article class="history-order">
              <div class="history-order__head">
                <div class="history-order__meta">
                  <p class="history-order__number">Order #<?php echo str_pad((string) $group['order_number'], 4, '0', STR_PAD_LEFT); ?></p>
                  <p class="history-order__date"><?php echo htmlspecialchars(format_order_date($group['order_time'])); ?></p>
                </div>
                <div class="history-order__badges">
                  <span class="history-badge history-badge--<?php echo $statusMeta['class']; ?>"><?php echo htmlspecialchars($statusMeta['label']); ?></span>
                  <span class="history-badge history-badge--<?php echo $paymentMeta['class']; ?>"><?php echo htmlspecialchars($paymentMeta['label']); ?></span>
                </div>
              </div>

              <div class="history-order__summary">
                <div class="history-order__stat">
                  <span class="history-order__stat-label">Items</span>
                  <span class="history-order__stat-value"><?php echo $itemCount; ?> product<?php echo $itemCount === 1 ? '' : 's'; ?></span>
                </div>
                <div class="history-order__stat">
                  <span class="history-order__stat-label">Table</span>
                  <span class="history-order__stat-value">#<?php echo (int) $group['table_no']; ?></span>
                </div>
                <div class="history-order__stat">
                  <span class="history-order__stat-label">Total</span>
                  <span class="history-order__stat-value history-order__stat-value--total">&#8377;<?php echo number_format((int) $group['total']); ?></span>
                </div>
              </div>

              <button type="button" class="history-order__toggle" data-history-toggle aria-expanded="false">
                <span>View order details</span>
                <span class="history-order__toggle-icon">&#9662;</span>
              </button>

              <div class="history-order__details">
                <?php foreach ($group['items'] as $item) : ?>
                  <div class="history-item">
                    <?php if (!empty($item['img'])) : ?>
                      <div class="history-item__media">
                        <img src="../../assets/uploads/<?php echo htmlspecialchars($item['img']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                      </div>
                    <?php else : ?>
                      <div class="history-item__media history-item__media--placeholder">&#127860;</div>
                    <?php endif; ?>
                    <div class="history-item__info">
                      <p class="history-item__name"><?php echo htmlspecialchars($item['name']); ?></p>
                      <p class="history-item__meta">
                        Qty <?php echo (int) $item['qty']; ?>
                        &middot; &#8377;<?php echo number_format((int) $item['price']); ?> each
                        <?php if (!empty($item['desc'])) : ?>
                          &middot; <?php echo htmlspecialchars($item['desc']); ?>
                        <?php endif; ?>
                      </p>
                    </div>
                    <span class="history-item__price">&#8377;<?php echo number_format((int) $item['line_total']); ?></span>
                  </div>
                <?php endforeach; ?>
              </div>
            </article>
          <?php endforeach; ?>
        </div>

      <?php else : ?>
        <section class="history-empty">
          <div class="history-empty__icon">&#128203;</div>
          <h2 class="history-empty__title">No orders yet</h2>
          <p class="history-empty__text">When you place an order, it will appear here with status updates and full details.</p>
          <a href="../../index.php#Menu" class="history-empty__btn">Start Ordering</a>
        </section>
      <?php endif; ?>

    </div>
  </main>

  <footer class="cart-footer">
    <div class="cart-footer__inner">
      <div class="cart-footer__col"><h3>Email</h3><p>Contact@eatery.com</p></div>
      <div class="cart-footer__col"><h3>Call</h3><p>+91 9898252898</p></div>
      <div class="cart-footer__col"><h3>Visit</h3><p>111, Platinam hub, Noida</p></div>
    </div>
  </footer>

  <script src="../../assets/vendors/jquery/jquery-3.4.1.js"></script>
  <script src="../../assets/vendors/bootstrap/bootstrap.bundle.js"></script>
  <script src="../../assets/js/history-page.js"></script>
</body>
</html>
