<?php

require_once __DIR__ . '/admin_helpers.php';

function product_admin_categories(mysqli $conn): array
{
    $categories = [];
    $result = mysqli_query($conn, 'SELECT DISTINCT product_type FROM products ORDER BY product_type ASC');
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $categories[] = $row['product_type'];
        }
    }

    if (!$categories) {
        $categories = ['Pizza', 'Burger', 'Chinease', 'Maxican'];
    }

    return $categories;
}

function product_admin_stock_state(int $qty): string
{
    if ($qty <= 0) {
        return 'out';
    }
    if ($qty <= 10) {
        return 'low';
    }
    return 'in';
}

function product_admin_sort_sql(string $sort): string
{
    $map = [
        'name_asc' => 'product_name ASC',
        'name_desc' => 'product_name DESC',
        'price_asc' => 'product_price ASC',
        'price_desc' => 'product_price DESC',
        'stock_asc' => 'product_qty ASC',
        'stock_desc' => 'product_qty DESC',
        'id_asc' => 'product_id ASC',
        'id_desc' => 'product_id DESC',
    ];

    return $map[$sort] ?? 'product_name ASC';
}

function product_admin_fetch_list(mysqli $conn, array $filters): array
{
    $search = trim((string) ($filters['q'] ?? ''));
    $category = trim((string) ($filters['category'] ?? ''));
    $stock = trim((string) ($filters['stock'] ?? ''));
    $sort = trim((string) ($filters['sort'] ?? 'name_asc'));
    $page = max(1, (int) ($filters['page'] ?? 1));
    $perPage = max(5, min(50, (int) ($filters['per_page'] ?? 10)));

    $where = ['1=1'];
    $types = '';
    $params = [];

    if ($search !== '') {
        $where[] = '(product_name LIKE ? OR product_desc LIKE ? OR CAST(product_no AS CHAR) LIKE ?)';
        $like = '%' . $search . '%';
        $types .= 'sss';
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
    }

    if ($category !== '') {
        $where[] = 'LOWER(product_type) = LOWER(?)';
        $types .= 's';
        $params[] = $category;
    }

    if ($stock === 'out') {
        $where[] = 'product_qty <= 0';
    } elseif ($stock === 'low') {
        $where[] = 'product_qty > 0 AND product_qty <= 10';
    } elseif ($stock === 'in') {
        $where[] = 'product_qty > 10';
    }

    $whereSql = implode(' AND ', $where);
    $countSql = "SELECT COUNT(*) FROM products WHERE $whereSql";
    $countStmt = $conn->prepare($countSql);
    if (!$countStmt) {
        return ['rows' => [], 'total' => 0, 'page' => 1, 'pages' => 1, 'per_page' => $perPage];
    }

    if ($types !== '') {
        $countStmt->bind_param($types, ...$params);
    }
    $countStmt->execute();
    $countResult = $countStmt->get_result();
    $total = (int) ($countResult->fetch_row()[0] ?? 0);
    $countStmt->close();

    $pages = max(1, (int) ceil($total / $perPage));
    $page = min($page, $pages);
    $offset = ($page - 1) * $perPage;

    $orderSql = product_admin_sort_sql($sort);
    $listSql = "SELECT * FROM products WHERE $whereSql ORDER BY $orderSql LIMIT ? OFFSET ?";
    $listStmt = $conn->prepare($listSql);
    if (!$listStmt) {
        return ['rows' => [], 'total' => $total, 'page' => $page, 'pages' => $pages, 'per_page' => $perPage];
    }

    $limitTypes = $types . 'ii';
    $limitParams = array_merge($params, [$perPage, $offset]);
    $listStmt->bind_param($limitTypes, ...$limitParams);
    $listStmt->execute();
    $result = $listStmt->get_result();
    $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    $listStmt->close();

    return [
        'rows' => $rows,
        'total' => $total,
        'page' => $page,
        'pages' => $pages,
        'per_page' => $perPage,
    ];
}

function product_admin_get(mysqli $conn, int $productId): ?array
{
    $productId = (int) $productId;
    $stmt = $conn->prepare('SELECT * FROM products WHERE product_id = ? LIMIT 1');
    if (!$stmt) {
        return null;
    }
    $stmt->bind_param('i', $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result ? $result->fetch_assoc() : null;
    $stmt->close();

    return $row ?: null;
}

function product_admin_upload_image(array $file): array
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'message' => 'Image upload failed.'];
    }

    if (($file['size'] ?? 0) > 12500000) {
        return ['ok' => false, 'message' => 'Image file is too large (max 12MB).'];
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg', 'jpeg', 'png'], true)) {
        return ['ok' => false, 'message' => 'Only JPG and PNG images are allowed.'];
    }

    $filename = uniqid('IMG-', true) . '.' . $ext;
    $destination = dirname(__DIR__, 2) . '/assets/uploads/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        return ['ok' => false, 'message' => 'Could not save uploaded image.'];
    }

    return ['ok' => true, 'filename' => $filename];
}

function product_admin_delete_image_file(?string $filename): void
{
    if (!$filename) {
        return;
    }
    $path = dirname(__DIR__, 2) . '/assets/uploads/' . basename($filename);
    if (is_file($path)) {
        @unlink($path);
    }
}

function product_admin_redirect(string $message = '', string $type = 'success', array $query = []): void
{
    if ($message !== '') {
        $_SESSION['admin_order_notice'] = ['type' => $type, 'message' => $message];
    }

    $base = '../../products.php';
    if ($query) {
        $base .= '?' . http_build_query($query);
    }

    header('Location: ' . $base);
    exit;
}

function product_admin_validate_payload(array $data, bool $isUpdate = true): array
{
    $errors = [];
    $name = trim((string) ($data['product_name'] ?? ''));
    $type = trim((string) ($data['product_type'] ?? ''));
    $desc = trim((string) ($data['product_desc'] ?? ''));
    $price = (int) ($data['product_price'] ?? 0);
    $qty = (int) ($data['product_qty'] ?? 0);

    if ($name === '') {
        $errors[] = 'Product name is required.';
    } elseif (strlen($name) > 20) {
        $errors[] = 'Product name must be 20 characters or fewer.';
    }

    if ($type === '') {
        $errors[] = 'Category is required.';
    } elseif (strlen($type) > 15) {
        $errors[] = 'Category must be 15 characters or fewer.';
    }

    if ($desc === '') {
        $errors[] = 'Description is required.';
    } elseif (strlen($desc) > 100) {
        $errors[] = 'Description must be 100 characters or fewer.';
    }

    if ($price < 0) {
        $errors[] = 'Price cannot be negative.';
    }

    if ($qty < 0) {
        $errors[] = 'Stock quantity cannot be negative.';
    }

    if (!$isUpdate && (int) ($data['product_no'] ?? 0) <= 0) {
        $errors[] = 'Product number is required.';
    }

    return $errors;
}

function product_admin_query_params(array $overrides = []): array
{
    $params = [];
    foreach (['q', 'category', 'stock', 'sort', 'page'] as $key) {
        if (isset($_GET[$key]) && $_GET[$key] !== '') {
            $params[$key] = (string) $_GET[$key];
        }
    }
    return array_merge($params, $overrides);
}

function product_admin_query_params_from_request(): array
{
    $params = [];
    foreach (['q', 'category', 'stock', 'sort', 'page'] as $key) {
        $postKey = 'return_' . $key;
        if (isset($_POST[$postKey]) && $_POST[$postKey] !== '') {
            $params[$key] = (string) $_POST[$postKey];
        } elseif (isset($_GET[$key]) && $_GET[$key] !== '') {
            $params[$key] = (string) $_GET[$key];
        }
    }
    return $params;
}

function product_admin_render_return_fields(array $params): void
{
    foreach ($params as $key => $value) {
        echo '<input type="hidden" name="return_' . htmlspecialchars($key) . '" value="' . htmlspecialchars($value) . '">';
    }
}

function product_admin_pagination_url(array $params, int $page): string
{
    $params['page'] = $page;
    return 'products.php?' . http_build_query($params);
}

function product_admin_json_attr(array $product): string
{
    $payload = [
        'product_id' => (int) $product['product_id'],
        'product_no' => (int) $product['product_no'],
        'product_name' => $product['product_name'],
        'product_price' => (int) $product['product_price'],
        'product_type' => $product['product_type'],
        'product_qty' => (int) $product['product_qty'],
        'product_desc' => $product['product_desc'],
        'product_img' => $product['product_img'],
        'view_url' => 'product.php?id=' . (int) $product['product_no'],
        'img_url' => 'assets/uploads/' . $product['product_img'],
    ];

    return htmlspecialchars(json_encode($payload), ENT_QUOTES, 'UTF-8');
}
