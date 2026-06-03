<?php

function product_card_badge(array $product, int $index): string
{
    if ($index === 0) {
        return 'featured';
    }
    if ((int) $product['product_qty'] >= 500) {
        return 'bestseller';
    }
    if ((int) $product['product_price'] >= 400) {
        return 'premium';
    }
    return '';
}

function product_card_badge_label(string $badge): string
{
    $labels = [
        'featured' => 'Featured',
        'bestseller' => 'Best Seller',
        'premium' => 'Premium',
    ];
    return $labels[$badge] ?? '';
}

function render_product_card(array $product, int $index = 0): void
{
    $badge = product_card_badge($product, $index);
    $badgeLabel = product_card_badge_label($badge);
    $type = htmlspecialchars(ucfirst(strtolower($product['product_type'])));
    $name = htmlspecialchars($product['product_name']);
    $desc = htmlspecialchars($product['product_desc']);
    $price = number_format((int) $product['product_price']);
    $img = htmlspecialchars($product['product_img']);
    $typeClass = strtolower($product['product_type']) === 'burger' ? 'burger' : 'pizza';
    ?>
    <div class="product-carousel__slide">
        <article class="product-card">
            <div class="product-card__media">
                <?php if ($badge !== '') : ?>
                    <span class="product-card__badge product-card__badge--<?php echo $badge; ?>">
                        <?php echo htmlspecialchars($badgeLabel); ?>
                    </span>
                <?php endif; ?>
                <img
                    class="product-card__img"
                    src="assets/uploads/<?php echo $img; ?>"
                    alt="<?php echo $name; ?>"
                    loading="lazy"
                >
            </div>
            <div class="product-card__body">
                <div class="product-card__meta">
                    <span class="product-card__category product-card__category--<?php echo $typeClass; ?>">
                        <?php echo $type; ?>
                    </span>
                </div>
                <h3 class="product-card__title"><?php echo $name; ?></h3>
                <p class="product-card__desc"><?php echo $desc; ?></p>
                <div class="product-card__footer">
                    <div class="product-card__price">
                        <span class="product-card__price-label">Price</span>
                        <span class="product-card__price-value">&#8377;<?php echo $price; ?></span>
                    </div>
                    <button
                        type="button"
                        class="product-card__cta js-add-to-cart"
                        data-product-no="<?php echo (int) $product['product_no']; ?>"
                        data-product-name="<?php echo $name; ?>"
                        data-product-price="<?php echo (int) $product['product_price']; ?>"
                        data-product-img="assets/uploads/<?php echo $img; ?>"
                        data-stock="<?php echo (int) $product['product_qty']; ?>"
                        aria-label="Add <?php echo $name; ?> to cart"
                    >
                        <span class="product-card__cta-spinner" aria-hidden="true"></span>
                        <span class="product-card__cta-icon">+</span>
                        <span class="product-card__cta-text">Add to Cart</span>
                    </button>
                </div>
            </div>
        </article>
    </div>
    <?php
}

function render_product_carousel(string $id, string $title, string $subtitle, string $query, mysqli $conn, bool $collapsed = false): void
{
    $result = mysqli_query($conn, $query);
    $count = $result ? mysqli_num_rows($result) : 0;
    $sectionClass = $collapsed ? ' menu-category is-collapsed' : ' menu-category';
    $carouselClass = $collapsed ? 'product-carousel hide' : 'product-carousel';
    ?>
    <section class="<?php echo trim($sectionClass); ?>" id="<?php echo htmlspecialchars($id); ?>-section">
        <div class="menu-category__header">
            <div class="menu-category__intro">
                <span class="menu-category__eyebrow"><?php echo htmlspecialchars($subtitle); ?></span>
                <h2 class="menu-category__title"><?php echo htmlspecialchars($title); ?></h2>
                <p class="menu-category__count"><?php echo $count; ?> item<?php echo $count === 1 ? '' : 's'; ?> available</p>
            </div>
            <div class="menu-category__actions">
                <div class="product-carousel__nav">
                    <button type="button" class="carousel-btn carousel-btn--prev" aria-label="Previous <?php echo htmlspecialchars($title); ?> products" data-carousel-prev="<?php echo htmlspecialchars($id); ?>">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                    <button type="button" class="carousel-btn carousel-btn--next" aria-label="Next <?php echo htmlspecialchars($title); ?> products" data-carousel-next="<?php echo htmlspecialchars($id); ?>">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>
                <button type="button" class="menu-category__toggle" onclick="shw('<?php echo htmlspecialchars($id); ?>')" aria-expanded="<?php echo $collapsed ? 'false' : 'true'; ?>" aria-controls="<?php echo htmlspecialchars($id); ?>">
                    <span class="menu-category__toggle-icon">&#9662;</span>
                </button>
            </div>
        </div>

        <div class="<?php echo $carouselClass; ?>" id="<?php echo htmlspecialchars($id); ?>" data-carousel="<?php echo htmlspecialchars($id); ?>">
            <div class="product-carousel__track" data-carousel-track>
                <?php
                if ($count > 0) {
                    $index = 0;
                    while ($product = mysqli_fetch_assoc($result)) {
                        render_product_card($product, $index);
                        $index++;
                    }
                } else {
                    echo '<div class="product-carousel__empty"><p>New items coming soon.</p></div>';
                }
                ?>
            </div>
        </div>
    </section>
    <?php
}
