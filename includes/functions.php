<?php
// ---------------------------------------------------------
// Shared helper functions
// ---------------------------------------------------------

function clean($str) {
    global $conn;
    return mysqli_real_escape_string($conn, trim($str));
}

function money($amount) {
    return CURRENCY . number_format((float)$amount, 2);
}

function redirect($path) {
    header('Location: ' . BASE_URL . '/' . ltrim($path, '/'));
    exit;
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function is_admin() {
    return !empty($_SESSION['is_admin']);
}

function require_admin() {
    if (!is_admin()) {
        redirect('admin/login.php');
    }
}

// ---------------- CART (session based, works for guests) ----------------

function cart_init() {
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        $_SESSION['cart'] = []; // [product_id => quantity]
    }
}

function cart_add($product_id, $qty = 1) {
    cart_init();
    $product_id = (int)$product_id;
    $qty = max(1, (int)$qty);
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $qty;
    } else {
        $_SESSION['cart'][$product_id] = $qty;
    }
}

function cart_update($product_id, $qty) {
    cart_init();
    $product_id = (int)$product_id;
    $qty = (int)$qty;
    if ($qty <= 0) {
        unset($_SESSION['cart'][$product_id]);
    } else {
        $_SESSION['cart'][$product_id] = $qty;
    }
}

function cart_remove($product_id) {
    cart_init();
    unset($_SESSION['cart'][(int)$product_id]);
}

function cart_clear() {
    $_SESSION['cart'] = [];
}

function cart_count() {
    cart_init();
    return array_sum($_SESSION['cart']);
}

// Returns array of cart line items with full product info + subtotal
function cart_items() {
    global $conn;
    cart_init();
    $items = [];
    if (empty($_SESSION['cart'])) return $items;

    $ids = array_map('intval', array_keys($_SESSION['cart']));
    $ids_str = implode(',', $ids);
    $result = mysqli_query($conn, "SELECT * FROM products WHERE id IN ($ids_str)");

    while ($row = mysqli_fetch_assoc($result)) {
        $qty = $_SESSION['cart'][$row['id']];
        $row['quantity'] = $qty;
        $row['line_total'] = $qty * $row['price'];
        $items[] = $row;
    }
    return $items;
}

function cart_subtotal() {
    $total = 0;
    foreach (cart_items() as $item) {
        $total += $item['line_total'];
    }
    return $total;
}

function shipping_fee_for($subtotal) {
    return $subtotal >= FREE_SHIPPING_THRESHOLD ? 0 : SHIPPING_FEE;
}

function image_src($filename) {
    // Falls back to a generated placeholder if the real product photo isn't present
    $path = BASE_URL . '/uploads/' . $filename;
    if ($filename && file_exists(__DIR__ . '/../uploads/' . $filename)) {
        return $path;
    }
    return 'https://placehold.co/600x600/201A17/EDE6D6?text=' . urlencode('KETTLEWOOD');
}
