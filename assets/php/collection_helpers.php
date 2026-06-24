<?php

function menu_category_filter_slug(string $type): string
{
    return strtolower(trim($type));
}

function menu_category_display_label(string $type): string
{
    $slug = menu_category_filter_slug($type);
    $labels = [
        'burger' => 'Burgers',
        'pizza' => 'Pizza',
    ];

    if (isset($labels[$slug])) {
        return $labels[$slug];
    }

    return ucwords(str_replace(['_', '-'], ' ', $slug));
}

function menu_category_css_class(string $type): string
{
    $slug = menu_category_filter_slug($type);

    if (in_array($slug, ['pizza', 'burger'], true)) {
        return $slug;
    }

    return 'default';
}

function fetch_menu_categories(mysqli $conn): array
{
    $result = mysqli_query(
        $conn,
        'SELECT DISTINCT product_type FROM products WHERE product_qty > 0 ORDER BY product_type ASC'
    );

    if (!$result) {
        return [];
    }

    $seen = [];
    $categories = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $type = trim((string) ($row['product_type'] ?? ''));
        if ($type === '') {
            continue;
        }

        $slug = menu_category_filter_slug($type);
        if (isset($seen[$slug])) {
            continue;
        }

        $seen[$slug] = true;
        $categories[] = [
            'type' => $type,
            'slug' => $slug,
            'label' => menu_category_display_label($type),
        ];
    }

    usort($categories, static function (array $a, array $b): int {
        return strcasecmp($a['label'], $b['label']);
    });

    return $categories;
}

function get_collections(): array
{
    return [
        'pizza' => [
            'slug' => 'pizza',
            'nav_label' => 'Pizza',
            'title' => 'Pizza Collection',
            'eyebrow' => 'Handcrafted Favorites',
            'description' => 'Wood-fired crusts, premium toppings, and bold flavors made fresh for every order.',
            'product_types' => ['pizza'],
            'theme' => 'pizza',
        ],
        'burgers' => [
            'slug' => 'burgers',
            'nav_label' => 'Burgers',
            'title' => 'Burger Collection',
            'eyebrow' => 'Signature Classics',
            'description' => 'Juicy patties, fresh buns, and crave-worthy combos stacked to perfection.',
            'product_types' => ['burger'],
            'theme' => 'burger',
        ],
    ];
}

function get_collection_by_slug(string $slug): ?array
{
    $collections = get_collections();
    return $collections[$slug] ?? null;
}

function fetch_collection_products(mysqli $conn, array $collection, ?int $limit = null): array
{
    $types = $collection['product_types'] ?? [];
    if (empty($types)) {
        return [];
    }

    $escaped = array_map(function ($type) use ($conn) {
        return "'" . mysqli_real_escape_string($conn, strtolower($type)) . "'";
    }, $types);

    $sql = 'SELECT * FROM products WHERE LOWER(product_type) IN (' . implode(',', $escaped) . ') AND product_qty > 0 ORDER BY product_no ASC';

    if ($limit !== null && $limit > 0) {
        $sql .= ' LIMIT ' . (int) $limit;
    }

    $result = mysqli_query($conn, $sql);
    if (!$result) {
        return [];
    }

    $products = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }

    return $products;
}

function fetch_best_selling_products(mysqli $conn, int $limit = 8): array
{
    $limit = max(1, min(20, $limit));

    $sql = "
        SELECT p.*, COALESCE(SUM(o.qty), 0) AS total_sold
        FROM products p
        LEFT JOIN orders o ON o.product_id = p.product_no
        WHERE p.product_qty > 0
        GROUP BY p.product_no
        ORDER BY total_sold DESC, p.product_no ASC
        LIMIT ?
    ";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return fetch_fallback_featured_products($conn, $limit);
    }

    $stmt->bind_param('i', $limit);
    $stmt->execute();
    $result = $stmt->get_result();

    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    $stmt->close();

    if (empty($products)) {
        return fetch_fallback_featured_products($conn, $limit);
    }

    return $products;
}

function fetch_fallback_featured_products(mysqli $conn, int $limit): array
{
    $limit = max(1, min(20, $limit));
    $result = mysqli_query(
        $conn,
        'SELECT * FROM products WHERE product_qty > 0 ORDER BY product_price DESC, product_no ASC LIMIT ' . (int) $limit
    );

    if (!$result) {
        return [];
    }

    $products = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }

    return $products;
}

function collection_page_url(string $slug): string
{
    return 'collection.php?slug=' . urlencode($slug);
}

function fetch_featured_menu_products(mysqli $conn, int $limitPerCategory = 12): array
{
    $categories = fetch_menu_categories($conn);
    if (empty($categories)) {
        return [];
    }

    $limitPerCategory = max(1, min(50, $limitPerCategory));
    $products = [];

    foreach ($categories as $category) {
        $slug = $category['slug'];
        $stmt = $conn->prepare(
            'SELECT * FROM products
             WHERE product_qty > 0 AND LOWER(TRIM(product_type)) = ?
             ORDER BY product_no ASC
             LIMIT ?'
        );

        if (!$stmt) {
            continue;
        }

        $stmt->bind_param('si', $slug, $limitPerCategory);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }

        $stmt->close();
    }

    return $products;
}

function fetch_products_rating_summaries(mysqli $conn, array $productNos): array
{
    if (!function_exists('ensure_product_reviews_table')) {
        require_once __DIR__ . '/product_helpers.php';
    }

    $productNos = array_values(array_unique(array_filter(array_map('intval', $productNos))));
    if (empty($productNos)) {
        return [];
    }

    ensure_product_reviews_table($conn);

    $placeholders = implode(',', array_fill(0, count($productNos), '?'));
    $types = str_repeat('i', count($productNos));

    $sql = "SELECT product_no, AVG(rating) AS avg_rating, COUNT(*) AS review_count
            FROM product_reviews
            WHERE product_no IN ($placeholders)
            GROUP BY product_no";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return [];
    }

    $stmt->bind_param($types, ...$productNos);
    $stmt->execute();
    $result = $stmt->get_result();

    $map = [];
    while ($row = $result->fetch_assoc()) {
        $map[(int) $row['product_no']] = [
            'average' => round((float) $row['avg_rating'], 1),
            'count' => (int) $row['review_count'],
        ];
    }
    $stmt->close();

    return $map;
}

function enrich_products_with_ratings(mysqli $conn, array $products): array
{
    if (empty($products)) {
        return [];
    }

    $ids = array_map(static function (array $p): int {
        return (int) $p['product_no'];
    }, $products);

    $summaries = fetch_products_rating_summaries($conn, $ids);

    foreach ($products as &$product) {
        $id = (int) $product['product_no'];
        if (isset($summaries[$id])) {
            $product['rating_average'] = $summaries[$id]['average'];
            $product['rating_count'] = $summaries[$id]['count'];
        } else {
            $product['rating_average'] = null;
            $product['rating_count'] = 0;
        }
    }
    unset($product);

    return $products;
}
