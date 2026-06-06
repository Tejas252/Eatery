<?php

session_start();

require_once __DIR__ . '/product_helpers.php';

$conn = mysqli_connect('localhost', 'root', '', 'eatery');
if (!$conn) {
    product_reviews_json(false, 'Unable to connect to the server.', [], 500);
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

if ($action === 'list') {
    $productNo = isset($_GET['product_no']) ? (int) $_GET['product_no'] : 0;
    if ($productNo <= 0) {
        product_reviews_json(false, 'Invalid product.', [], 400);
    }

    $reviews = fetch_product_reviews($conn, $productNo);
    $summary = product_review_summary($reviews);

    $payload = [];
    foreach ($reviews as $review) {
        $payload[] = [
            'review_id' => (int) $review['review_id'],
            'rating' => (int) $review['rating'],
            'review_text' => $review['review_text'],
            'author' => review_author_name($review),
            'created_at' => format_review_date($review['created_at']),
        ];
    }

    product_reviews_json(true, 'Reviews loaded.', [
        'reviews' => $payload,
        'summary' => $summary,
    ]);
}

if ($action === 'submit') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        product_reviews_json(false, 'Invalid request method.', [], 405);
    }

    if (!isset($_SESSION['login']) || !isset($_SESSION['id'])) {
        product_reviews_json(false, 'Please log in to submit a review.', ['code' => 'auth_required'], 401);
    }

    $productNo = isset($_POST['product_no']) ? (int) $_POST['product_no'] : 0;
    $rating = isset($_POST['rating']) ? (int) $_POST['rating'] : 0;
    $text = trim($_POST['review_text'] ?? '');

    if ($productNo <= 0) {
        product_reviews_json(false, 'Invalid product.', [], 400);
    }

    $result = save_product_review($conn, $productNo, (int) $_SESSION['id'], $rating, $text);

    if (!$result['success']) {
        product_reviews_json(false, $result['message'], [], 400);
    }

    $reviews = fetch_product_reviews($conn, $productNo);
    $summary = product_review_summary($reviews);

    product_reviews_json(true, $result['message'], [
        'summary' => $summary,
        'review_count' => $summary['count'],
    ]);
}

product_reviews_json(false, 'Unknown action.', [], 400);
