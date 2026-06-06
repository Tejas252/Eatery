<?php

function product_page_url(int $productNo): string
{
    return 'product.php?id=' . max(1, $productNo);
}

function ensure_product_reviews_table(mysqli $conn): void
{
    $sql = "CREATE TABLE IF NOT EXISTS product_reviews (
        review_id INT NOT NULL AUTO_INCREMENT,
        product_no INT NOT NULL,
        customer_id INT NOT NULL,
        rating TINYINT UNSIGNED NOT NULL,
        review_text VARCHAR(500) NOT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (review_id),
        UNIQUE KEY uniq_customer_product (product_no, customer_id),
        KEY idx_product_no (product_no)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

    mysqli_query($conn, $sql);
}

function get_product_detail(mysqli $conn, int $productNo): ?array
{
    if ($productNo <= 0) {
        return null;
    }

    $stmt = $conn->prepare(
        'SELECT product_no, product_name, product_price, product_type, product_qty, product_desc, product_img
         FROM products WHERE product_no = ? LIMIT 1'
    );
    if (!$stmt) {
        return null;
    }

    $stmt->bind_param('i', $productNo);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    return $row ?: null;
}

function product_gallery_images(array $product): array
{
    $images = [];
    $main = trim($product['product_img'] ?? '');
    if ($main !== '') {
        $images[] = $main;
    }

    return $images;
}

function product_availability_meta(array $product): array
{
    $stock = (int) ($product['product_qty'] ?? 0);

    if ($stock <= 0) {
        return ['label' => 'Out of Stock', 'class' => 'out'];
    }
    if ($stock <= 10) {
        return ['label' => 'Low Stock · ' . $stock . ' left', 'class' => 'low'];
    }

    return ['label' => 'In Stock · ' . $stock . ' available', 'class' => 'in'];
}

function product_max_purchase_qty(array $product): int
{
    return max(0, min(10, (int) ($product['product_qty'] ?? 0)));
}

function fetch_product_reviews(mysqli $conn, int $productNo): array
{
    ensure_product_reviews_table($conn);

    $stmt = $conn->prepare(
        'SELECT r.review_id, r.product_no, r.customer_id, r.rating, r.review_text, r.created_at,
                c.cust_name, c.cust_username
         FROM product_reviews r
         LEFT JOIN customer c ON c.cust_id = r.customer_id
         WHERE r.product_no = ?
         ORDER BY r.created_at DESC'
    );
    if (!$stmt) {
        return [];
    }

    $stmt->bind_param('i', $productNo);
    $stmt->execute();
    $result = $stmt->get_result();

    $reviews = [];
    while ($row = $result->fetch_assoc()) {
        $reviews[] = $row;
    }
    $stmt->close();

    return $reviews;
}

function product_review_summary(array $reviews): array
{
    if (empty($reviews)) {
        return [
            'count' => 0,
            'average' => 0,
            'average_display' => '0.0',
            'breakdown' => [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0],
        ];
    }

    $total = 0;
    $breakdown = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];

    foreach ($reviews as $review) {
        $rating = max(1, min(5, (int) $review['rating']));
        $breakdown[$rating]++;
        $total += $rating;
    }

    $count = count($reviews);
    $average = $count > 0 ? round($total / $count, 1) : 0;

    return [
        'count' => $count,
        'average' => $average,
        'average_display' => number_format($average, 1),
        'breakdown' => $breakdown,
    ];
}

function format_review_date(string $datetime): string
{
    $time = strtotime($datetime);
    if ($time === false) {
        return $datetime;
    }
    return date('M j, Y', $time);
}

function review_author_name(array $review): string
{
    $name = trim($review['cust_name'] ?? '');
    if ($name !== '') {
        return $name;
    }
    $username = trim($review['cust_username'] ?? '');
    if ($username !== '') {
        return $username;
    }
    return 'Customer';
}

function render_star_rating(float $rating, string $extraClass = ''): void
{
    $class = 'star-rating' . ($extraClass !== '' ? ' ' . $extraClass : '');
    echo '<span class="' . htmlspecialchars($class) . '" aria-label="' . htmlspecialchars(number_format($rating, 1)) . ' out of 5 stars">';
    for ($i = 1; $i <= 5; $i++) {
        $filled = $rating >= $i;
        $half = !$filled && $rating >= ($i - 0.5);
        $starClass = 'star-rating__star';
        if ($filled) {
            $starClass .= ' is-filled';
        } elseif ($half) {
            $starClass .= ' is-half';
        }
        echo '<span class="' . $starClass . '" aria-hidden="true">&#9733;</span>';
    }
    echo '</span>';
}

function get_customer_review_for_product(mysqli $conn, int $productNo, int $customerId): ?array
{
    ensure_product_reviews_table($conn);

    $stmt = $conn->prepare(
        'SELECT review_id, rating, review_text FROM product_reviews WHERE product_no = ? AND customer_id = ? LIMIT 1'
    );
    if (!$stmt) {
        return null;
    }

    $stmt->bind_param('ii', $productNo, $customerId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    return $row ?: null;
}

function save_product_review(mysqli $conn, int $productNo, int $customerId, int $rating, string $text): array
{
    ensure_product_reviews_table($conn);

    $rating = max(1, min(5, $rating));
    $text = trim($text);

    if ($text === '') {
        return ['success' => false, 'message' => 'Please write a review before submitting.'];
    }
    if (strlen($text) > 500) {
        return ['success' => false, 'message' => 'Review must be 500 characters or fewer.'];
    }

    $product = get_product_detail($conn, $productNo);
    if (!$product) {
        return ['success' => false, 'message' => 'Product not found.'];
    }

    $existing = get_customer_review_for_product($conn, $productNo, $customerId);
    if ($existing) {
        $stmt = $conn->prepare(
            'UPDATE product_reviews SET rating = ?, review_text = ?, created_at = NOW() WHERE review_id = ?'
        );
        $reviewId = (int) $existing['review_id'];
        $stmt->bind_param('isi', $rating, $text, $reviewId);
        $ok = $stmt->execute();
        $stmt->close();
        $message = 'Your review was updated.';
    } else {
        $stmt = $conn->prepare(
            'INSERT INTO product_reviews (product_no, customer_id, rating, review_text) VALUES (?, ?, ?, ?)'
        );
        $stmt->bind_param('iiis', $productNo, $customerId, $rating, $text);
        $ok = $stmt->execute();
        $stmt->close();
        $message = 'Thank you for your review!';
    }

    if (!$ok) {
        return ['success' => false, 'message' => 'Could not save review. Please try again.'];
    }

    return ['success' => true, 'message' => $message];
}

function product_reviews_json(bool $success, string $message, array $data = [], int $code = 200): void
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array_merge(['success' => $success, 'message' => $message], $data));
    exit;
}

function track_recently_viewed_product(int $productNo, int $maxItems = 12): void
{
    if ($productNo <= 0) {
        return;
    }

    if (!isset($_SESSION['recently_viewed']) || !is_array($_SESSION['recently_viewed'])) {
        $_SESSION['recently_viewed'] = [];
    }

    $history = array_values(array_filter(
        array_map('intval', $_SESSION['recently_viewed']),
        static function (int $id) use ($productNo): bool {
            return $id > 0 && $id !== $productNo;
        }
    ));

    array_unshift($history, $productNo);
    $_SESSION['recently_viewed'] = array_slice($history, 0, max(1, $maxItems));
}

function fetch_products_by_nos(mysqli $conn, array $productNos): array
{
    $productNos = array_values(array_unique(array_filter(array_map('intval', $productNos), static function (int $id): bool {
        return $id > 0;
    })));

    if (empty($productNos)) {
        return [];
    }

    $placeholders = implode(',', array_fill(0, count($productNos), '?'));
    $types = str_repeat('i', count($productNos));

    $sql = "SELECT product_no, product_name, product_price, product_type, product_qty, product_desc, product_img
            FROM products
            WHERE product_no IN ($placeholders) AND product_qty > 0";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return [];
    }

    $stmt->bind_param($types, ...$productNos);
    $stmt->execute();
    $result = $stmt->get_result();

    $byNo = [];
    while ($row = $result->fetch_assoc()) {
        $byNo[(int) $row['product_no']] = $row;
    }
    $stmt->close();

    $ordered = [];
    foreach ($productNos as $productNo) {
        if (isset($byNo[$productNo])) {
            $ordered[] = $byNo[$productNo];
        }
    }

    return $ordered;
}

function get_recently_viewed_products(mysqli $conn, int $excludeProductNo, int $limit = 8): array
{
    $limit = max(1, min(12, $limit));
    $history = $_SESSION['recently_viewed'] ?? [];

    if (!is_array($history) || empty($history)) {
        return [];
    }

    $ids = [];
    foreach ($history as $id) {
        $id = (int) $id;
        if ($id <= 0 || $id === $excludeProductNo) {
            continue;
        }
        if (!in_array($id, $ids, true)) {
            $ids[] = $id;
        }
        if (count($ids) >= $limit) {
            break;
        }
    }

    return fetch_products_by_nos($conn, $ids);
}

function fetch_related_products(mysqli $conn, array $product, int $excludeProductNo, int $limit = 8): array
{
    $limit = max(1, min(12, $limit));
    $type = strtolower(trim($product['product_type'] ?? ''));
    if ($type === '') {
        return [];
    }

    $stmt = $conn->prepare(
        'SELECT product_no, product_name, product_price, product_type, product_qty, product_desc, product_img
         FROM products
         WHERE LOWER(product_type) = ? AND product_no != ? AND product_qty > 0
         ORDER BY product_no ASC
         LIMIT ?'
    );
    if (!$stmt) {
        return [];
    }

    $stmt->bind_param('sii', $type, $excludeProductNo, $limit);
    $stmt->execute();
    $result = $stmt->get_result();

    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    $stmt->close();

    return $products;
}

function render_product_rail_section(array $config, array $products): void
{
    if (empty($products)) {
        return;
    }

    require_once __DIR__ . '/product_card.php';

    $carouselId = $config['carousel_id'];
    $sectionId = $config['section_id'] ?? $carouselId;
    $title = $config['title'];
    $eyebrow = $config['eyebrow'];
    $description = $config['description'] ?? '';
    $theme = $config['theme'] ?? 'default';
    $count = count($products);
    ?>
    <section
        class="product-rail product-rail--<?php echo htmlspecialchars($theme); ?>"
        id="<?php echo htmlspecialchars($sectionId); ?>"
        aria-labelledby="<?php echo htmlspecialchars($sectionId); ?>-title"
    >
        <header class="product-rail__header">
            <div class="product-rail__intro">
                <span class="product-rail__eyebrow"><?php echo htmlspecialchars($eyebrow); ?></span>
                <h2 class="product-rail__title" id="<?php echo htmlspecialchars($sectionId); ?>-title">
                    <?php echo htmlspecialchars($title); ?>
                </h2>
                <?php if ($description !== '') : ?>
                    <p class="product-rail__desc"><?php echo htmlspecialchars($description); ?></p>
                <?php endif; ?>
            </div>
            <div class="product-rail__actions">
                <div class="product-carousel__nav">
                    <button type="button" class="carousel-btn carousel-btn--prev" aria-label="Previous <?php echo htmlspecialchars($title); ?>" data-carousel-prev="<?php echo htmlspecialchars($carouselId); ?>">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                    <button type="button" class="carousel-btn carousel-btn--next" aria-label="Next <?php echo htmlspecialchars($title); ?>" data-carousel-next="<?php echo htmlspecialchars($carouselId); ?>">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>
            </div>
        </header>

        <div class="product-carousel product-rail__carousel" id="<?php echo htmlspecialchars($carouselId); ?>" data-carousel="<?php echo htmlspecialchars($carouselId); ?>">
            <div class="product-carousel__track" data-carousel-track>
                <?php foreach ($products as $index => $railProduct) : ?>
                    <?php render_product_card($railProduct, $index); ?>
                <?php endforeach; ?>
            </div>
        </div>
        <p class="product-rail__count"><?php echo $count; ?> product<?php echo $count === 1 ? '' : 's'; ?></p>
    </section>
    <?php
}
