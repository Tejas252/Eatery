<?php

require_once __DIR__ . '/product_card.php';

function render_collection_carousel_section(array $config, array $products): void
{
    $carouselId = $config['carousel_id'];
    $sectionId = $config['section_id'] ?? $carouselId;
    $title = $config['title'];
    $eyebrow = $config['eyebrow'];
    $description = $config['description'] ?? '';
    $theme = $config['theme'] ?? 'default';
    $ctaUrl = $config['cta_url'] ?? '';
    $ctaLabel = $config['cta_label'] ?? 'View All';
    $count = count($products);
    ?>
    <section
        class="collection-section collection-section--<?php echo htmlspecialchars($theme); ?>"
        id="<?php echo htmlspecialchars($sectionId); ?>"
        aria-labelledby="<?php echo htmlspecialchars($sectionId); ?>-title"
    >
        <div class="collection-section__inner">
            <header class="collection-section__header">
                <div class="collection-section__intro">
                    <span class="collection-section__eyebrow"><?php echo htmlspecialchars($eyebrow); ?></span>
                    <h2 class="collection-section__title" id="<?php echo htmlspecialchars($sectionId); ?>-title">
                        <?php echo htmlspecialchars($title); ?>
                    </h2>
                    <?php if ($description !== '') : ?>
                        <p class="collection-section__desc"><?php echo htmlspecialchars($description); ?></p>
                    <?php endif; ?>
                    <p class="collection-section__count"><?php echo $count; ?> item<?php echo $count === 1 ? '' : 's'; ?> shown</p>
                </div>
                <div class="collection-section__actions">
                    <div class="product-carousel__nav">
                        <button type="button" class="carousel-btn carousel-btn--prev" aria-label="Previous <?php echo htmlspecialchars($title); ?> products" data-carousel-prev="<?php echo htmlspecialchars($carouselId); ?>">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                        <button type="button" class="carousel-btn carousel-btn--next" aria-label="Next <?php echo htmlspecialchars($title); ?> products" data-carousel-next="<?php echo htmlspecialchars($carouselId); ?>">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                    </div>
                    <?php if ($ctaUrl !== '') : ?>
                        <a class="collection-section__cta" href="<?php echo htmlspecialchars($ctaUrl); ?>">
                            <?php echo htmlspecialchars($ctaLabel); ?>
                            <span aria-hidden="true">&rarr;</span>
                        </a>
                    <?php endif; ?>
                </div>
            </header>

            <div class="product-carousel" id="<?php echo htmlspecialchars($carouselId); ?>" data-carousel="<?php echo htmlspecialchars($carouselId); ?>">
                <div class="product-carousel__track" data-carousel-track>
                    <?php
                    if ($count > 0) {
                        foreach ($products as $index => $product) {
                            render_product_card($product, $index);
                        }
                    } else {
                        echo '<div class="product-carousel__empty"><p>New items coming soon.</p></div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </section>
    <?php
}

function render_product_grid(array $products): void
{
    if (empty($products)) {
        echo '<div class="collection-page__empty"><p>No products available in this collection right now.</p></div>';
        return;
    }

    echo '<div class="collection-page__grid">';
    foreach ($products as $index => $product) {
        echo '<div class="collection-page__grid-item">';
        render_product_card($product, $index);
        echo '</div>';
    }
    echo '</div>';
}

function render_featured_menu_section(mysqli $conn, array $products): void
{
    $count = count($products);
    ?>
    <section class="featured-menu" id="featured-menu" aria-labelledby="featuredMenuTitle">
        <div class="featured-menu__inner">
            <header class="featured-menu__header">
                <span class="featured-menu__eyebrow">Our Menu</span>
                <h2 class="featured-menu__title" id="featuredMenuTitle">Choose Your Favorite</h2>
                <div class="featured-menu__divider" aria-hidden="true">
                    <span class="featured-menu__divider-line"></span>
                    <span class="featured-menu__divider-icon">🍕</span>
                    <span class="featured-menu__divider-line"></span>
                </div>
                <p class="featured-menu__subtitle">Handcrafted pizzas and signature burgers — filter by category and order in seconds.</p>
            </header>

            <div class="featured-menu__toolbar">
                <div class="featured-menu__tabs" role="tablist" aria-label="Menu categories">
                    <button type="button" class="featured-menu__tab is-active" role="tab" aria-selected="true" data-filter="all" id="featured-tab-all" aria-controls="featuredMenuGrid">
                        All Items
                    </button>
                    <button type="button" class="featured-menu__tab" role="tab" aria-selected="false" data-filter="pizza" id="featured-tab-pizza" aria-controls="featuredMenuGrid">
                        Pizza
                    </button>
                    <button type="button" class="featured-menu__tab" role="tab" aria-selected="false" data-filter="burger" id="featured-tab-burger" aria-controls="featuredMenuGrid">
                        Burgers
                    </button>
                </div>
                <p class="featured-menu__count" id="featuredMenuCount" aria-live="polite">
                    <?php echo $count; ?> item<?php echo $count === 1 ? '' : 's'; ?>
                </p>
            </div>

            <div class="featured-menu__grid-wrap">
                <div class="featured-menu__grid" id="featuredMenuGrid" role="tabpanel" aria-labelledby="featured-tab-all">
                    <?php
                    if ($count > 0) {
                        foreach ($products as $index => $product) {
                            render_product_card($product, $index, ['layout' => 'grid', 'show_rating' => true]);
                        }
                    } else {
                        echo '<div class="featured-menu__empty"><p>New menu items coming soon.</p></div>';
                    }
                    ?>
                </div>
                <div class="featured-menu__empty featured-menu__empty--filter" id="featuredMenuEmptyFilter" hidden>
                    <p>No items in this category right now.</p>
                </div>
            </div>

            <div class="featured-menu__view-all">
                <a class="featured-menu__view-all-link" href="<?php echo htmlspecialchars(collection_page_url('pizza')); ?>">View Full Menu</a>
            </div>
        </div>
    </section>
    <?php
}
