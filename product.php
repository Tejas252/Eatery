<?php

session_start();
include('data_insert_take.php');
require_once('assets/php/cart_helpers.php');
require_once('assets/php/collection_helpers.php');
require_once('assets/php/book_table_helpers.php');
require_once('assets/php/product_helpers.php');

clear_booking_session_if_logged_out();

$productNo = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$product = get_product_detail($conn, $productNo);

if (!$product) {
    header('Location: index.php');
    exit;
}

$collections = get_collections();
$navHasActiveBooking = get_current_booking_from_session() !== null;
$isLoggedIn = isset($_SESSION['login']) && $_SESSION['login'] === true;
$customerId = $isLoggedIn ? (int) $_SESSION['id'] : 0;

$galleryImages = product_gallery_images($product);
$availability = product_availability_meta($product);
$maxQty = product_max_purchase_qty($product);
$reviews = fetch_product_reviews($conn, $productNo);
$reviewSummary = product_review_summary($reviews);
$customerReview = $isLoggedIn ? get_customer_review_for_product($conn, $productNo, $customerId) : null;

$typeLabel = ucfirst(strtolower($product['product_type']));
$typeClass = strtolower($product['product_type']) === 'burger' ? 'burger' : 'pizza';
$productName = htmlspecialchars($product['product_name']);
$productDesc = htmlspecialchars($product['product_desc']);
$productPrice = number_format((int) $product['product_price']);
$mainImg = htmlspecialchars($product['product_img']);

$collectionSlug = strtolower($product['product_type']) === 'burger' ? 'burgers' : 'pizza';
$breadcrumbCollection = $collections[$collectionSlug] ?? null;

$recentlyViewedProducts = get_recently_viewed_products($conn, $productNo, 8);
track_recently_viewed_product($productNo);
$relatedProducts = fetch_related_products($conn, $product, $productNo, 8);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="<?php echo $productDesc; ?>">
  <title><?php echo $productName; ?> | Eatery</title>
  <link rel="icon" href="assets/imgs/logo3.png">
  <link rel="stylesheet" href="assets/css/theme.css">
  <link rel="stylesheet" href="assets/css/foodhut.css">
  <link rel="stylesheet" href="assets/css/add-to-cart.css">
  <link rel="stylesheet" href="assets/css/site-header.css">
  <link rel="stylesheet" href="assets/css/site-footer.css">
  <link rel="stylesheet" href="assets/css/menu.css">
  <link rel="stylesheet" href="assets/css/product-detail.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&display=swap" rel="stylesheet">
</head>
<body class="product-detail-body">

  <?php
    $navBase = '';
    $navCollections = $collections;
    $navActiveCollection = null;
    include('assets/php/site_header.php');
  ?>

  <main class="product-detail-page">
    <div class="product-detail-page__inner">

      <nav class="product-breadcrumb" aria-label="Breadcrumb">
        <a href="index.php">Home</a>
        <span aria-hidden="true">/</span>
        <?php if ($breadcrumbCollection) : ?>
          <a href="<?php echo htmlspecialchars(collection_page_url($collectionSlug)); ?>"><?php echo htmlspecialchars($breadcrumbCollection['nav_label']); ?></a>
          <span aria-hidden="true">/</span>
        <?php endif; ?>
        <span aria-current="page"><?php echo $productName; ?></span>
      </nav>

      <div class="product-detail-layout">
        <section class="product-gallery" aria-label="Product images">
          <div class="product-gallery__stage">
            <button type="button" class="product-gallery__nav product-gallery__nav--prev" id="galleryPrev" aria-label="Previous image" <?php echo count($galleryImages) <= 1 ? 'hidden' : ''; ?>>
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <div class="product-gallery__viewport" id="galleryViewport">
              <?php foreach ($galleryImages as $index => $image) : ?>
                <figure class="product-gallery__slide<?php echo $index === 0 ? ' is-active' : ''; ?>" data-gallery-slide>
                  <img src="assets/uploads/<?php echo htmlspecialchars($image); ?>" alt="<?php echo $productName; ?> — image <?php echo $index + 1; ?>">
                </figure>
              <?php endforeach; ?>
            </div>
            <button type="button" class="product-gallery__nav product-gallery__nav--next" id="galleryNext" aria-label="Next image" <?php echo count($galleryImages) <= 1 ? 'hidden' : ''; ?>>
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
          </div>
          <?php if (count($galleryImages) > 1) : ?>
            <div class="product-gallery__thumbs" id="galleryThumbs">
              <?php foreach ($galleryImages as $index => $image) : ?>
                <button type="button" class="product-gallery__thumb<?php echo $index === 0 ? ' is-active' : ''; ?>" data-gallery-thumb="<?php echo $index; ?>" aria-label="View image <?php echo $index + 1; ?>">
                  <img src="assets/uploads/<?php echo htmlspecialchars($image); ?>" alt="">
                </button>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </section>

        <section class="product-info" aria-label="Product details">
          <span class="product-info__category product-info__category--<?php echo $typeClass; ?>"><?php echo htmlspecialchars($typeLabel); ?></span>
          <h1 class="product-info__title"><?php echo $productName; ?></h1>

          <div class="product-info__rating-row">
            <?php render_star_rating((float) $reviewSummary['average'], 'star-rating--md'); ?>
            <span class="product-info__rating-text">
              <?php if ($reviewSummary['count'] > 0) : ?>
                <?php echo $reviewSummary['average_display']; ?> · <?php echo (int) $reviewSummary['count']; ?> review<?php echo $reviewSummary['count'] === 1 ? '' : 's'; ?>
              <?php else : ?>
                No reviews yet
              <?php endif; ?>
            </span>
          </div>

          <p class="product-info__price">&#8377;<?php echo $productPrice; ?></p>
          <p class="product-info__desc"><?php echo $productDesc; ?></p>

          <div class="product-info__status product-info__status--<?php echo htmlspecialchars($availability['class']); ?>">
            <?php echo htmlspecialchars($availability['label']); ?>
          </div>

          <div class="product-purchase" id="productPurchase" data-product-no="<?php echo (int) $product['product_no']; ?>" data-stock="<?php echo (int) $product['product_qty']; ?>">
            <div class="product-purchase__qty">
              <span class="product-purchase__qty-label">Quantity</span>
              <div class="product-qty-stepper">
                <button type="button" id="productQtyDecrease" aria-label="Decrease quantity" <?php echo $maxQty <= 0 ? 'disabled' : ''; ?>>−</button>
                <input type="number" id="productQty" value="1" min="1" max="<?php echo max(1, $maxQty); ?>" inputmode="numeric" <?php echo $maxQty <= 0 ? 'disabled' : ''; ?>>
                <button type="button" id="productQtyIncrease" aria-label="Increase quantity" <?php echo $maxQty <= 0 ? 'disabled' : ''; ?>>+</button>
              </div>
            </div>

            <button
              type="button"
              class="product-add-btn"
              id="productAddBtn"
              <?php echo $maxQty <= 0 ? 'disabled' : ''; ?>
              data-product-no="<?php echo (int) $product['product_no']; ?>"
              data-product-name="<?php echo $productName; ?>"
              data-product-price="<?php echo (int) $product['product_price']; ?>"
              data-product-img="assets/uploads/<?php echo $mainImg; ?>"
              data-stock="<?php echo (int) $product['product_qty']; ?>"
            >
              <span class="product-add-btn__loader" aria-hidden="true"></span>
              <?php echo $maxQty <= 0 ? 'Out of Stock' : 'Add to Cart'; ?>
            </button>
          </div>
        </section>
      </div>

      <?php
        render_product_rail_section([
          'carousel_id' => 'recently-viewed-rail',
          'section_id' => 'recently-viewed',
          'title' => 'Recently Viewed',
          'eyebrow' => 'Pick Up Where You Left Off',
          'description' => 'Products you browsed recently — jump back in with one click.',
          'theme' => 'recent',
        ], $recentlyViewedProducts);

        render_product_rail_section([
          'carousel_id' => 'related-products-rail',
          'section_id' => 'related-products',
          'title' => 'Related Products',
          'eyebrow' => 'You May Also Like',
          'description' => 'More ' . strtolower($typeLabel) . ' dishes from the same collection.',
          'theme' => 'related',
        ], $relatedProducts);
      ?>

      <section class="product-reviews" id="reviews" aria-labelledby="productReviewsTitle">
        <header class="product-reviews__header">
          <div>
            <span class="product-reviews__eyebrow">Customer Feedback</span>
            <h2 class="product-reviews__title" id="productReviewsTitle">Reviews</h2>
          </div>
        </header>

        <div class="product-reviews__layout">
          <aside class="product-reviews__summary" aria-label="Rating summary">
            <p class="product-reviews__average" id="reviewAverage"><?php echo $reviewSummary['average_display']; ?></p>
            <div class="product-reviews__stars">
              <?php render_star_rating((float) $reviewSummary['average'], 'star-rating--lg'); ?>
            </div>
            <p class="product-reviews__count" id="reviewCount">
              Based on <?php echo (int) $reviewSummary['count']; ?> review<?php echo $reviewSummary['count'] === 1 ? '' : 's'; ?>
            </p>
            <div class="product-reviews__breakdown" id="reviewBreakdown">
              <?php for ($star = 5; $star >= 1; $star--) :
                $count = (int) ($reviewSummary['breakdown'][$star] ?? 0);
                $percent = $reviewSummary['count'] > 0 ? round(($count / $reviewSummary['count']) * 100) : 0;
              ?>
                <div class="product-reviews__bar-row">
                  <span><?php echo $star; ?>★</span>
                  <div class="product-reviews__bar"><span style="width: <?php echo $percent; ?>%"></span></div>
                  <span><?php echo $count; ?></span>
                </div>
              <?php endfor; ?>
            </div>
          </aside>

          <div class="product-reviews__main">
            <?php if ($isLoggedIn) : ?>
              <form class="product-review-form" id="reviewForm" novalidate>
                <h3 class="product-review-form__title"><?php echo $customerReview ? 'Update Your Review' : 'Write a Review'; ?></h3>
                <div class="product-review-form__rating">
                  <span class="product-review-form__label">Your Rating</span>
                  <div class="review-stars-input" id="reviewStarsInput" role="radiogroup" aria-label="Rating">
                    <?php for ($i = 1; $i <= 5; $i++) : ?>
                      <button type="button" class="review-stars-input__star<?php echo ($customerReview && (int) $customerReview['rating'] >= $i) ? ' is-active' : ''; ?>" data-rating="<?php echo $i; ?>" aria-label="<?php echo $i; ?> star<?php echo $i === 1 ? '' : 's'; ?>">&#9733;</button>
                    <?php endfor; ?>
                  </div>
                  <input type="hidden" name="rating" id="reviewRating" value="<?php echo $customerReview ? (int) $customerReview['rating'] : '5'; ?>">
                </div>
                <label class="product-review-form__label" for="reviewText">Your Review</label>
                <textarea id="reviewText" name="review_text" rows="4" maxlength="500" placeholder="Share your experience with this dish..." required><?php echo $customerReview ? htmlspecialchars($customerReview['review_text']) : ''; ?></textarea>
                <div class="product-review-form__actions">
                  <button type="submit" class="product-review-form__submit" id="reviewSubmitBtn">
                    <span class="product-review-form__loader" aria-hidden="true"></span>
                    <?php echo $customerReview ? 'Update Review' : 'Submit Review'; ?>
                  </button>
                </div>
                <p class="product-review-form__message" id="reviewFormMessage" role="status" hidden></p>
              </form>
            <?php else : ?>
              <div class="product-review-login">
                <p>Please <a href="login.php">log in</a> to write a review.</p>
              </div>
            <?php endif; ?>

            <div class="product-reviews__list" id="reviewsList">
              <?php if (empty($reviews)) : ?>
                <div class="product-reviews__empty" id="reviewsEmpty">
                  <p class="product-reviews__empty-title">No reviews yet</p>
                  <p class="product-reviews__empty-text">Be the first to share your thoughts about this dish.</p>
                </div>
              <?php else : ?>
                <?php foreach ($reviews as $review) : ?>
                  <article class="product-review-card">
                    <div class="product-review-card__head">
                      <div>
                        <p class="product-review-card__author"><?php echo htmlspecialchars(review_author_name($review)); ?></p>
                        <p class="product-review-card__date"><?php echo htmlspecialchars(format_review_date($review['created_at'])); ?></p>
                      </div>
                      <?php render_star_rating((float) $review['rating'], 'star-rating--sm'); ?>
                    </div>
                    <p class="product-review-card__text"><?php echo nl2br(htmlspecialchars($review['review_text'])); ?></p>
                  </article>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </section>

    </div>
  </main>

  <?php include('assets/php/site_footer.php'); ?>

  <?php include('assets/php/cart_modal.php'); ?>
  <div class="cart-toast-stack" id="cartToastStack" aria-live="polite" aria-atomic="true"></div>

  <script>
    window.PRODUCT_DETAIL = {
      productNo: <?php echo (int) $product['product_no']; ?>,
      isLoggedIn: <?php echo $isLoggedIn ? 'true' : 'false'; ?>
    };
  </script>
  <script src="assets/vendors/jquery/jquery-3.4.1.js"></script>
  <script src="assets/vendors/bootstrap/bootstrap.bundle.js"></script>
  <script src="assets/js/add-to-cart.js"></script>
  <script src="assets/js/profile-menu.js"></script>
  <script src="assets/js/menu-carousel.js"></script>
  <script src="assets/js/product-detail.js"></script>
</body>
</html>
