<?php
require_once 'config.php';
require_once 'includes/functions.php';
$page_title = 'Track Your Order';

$entered_number = '';
$order = null;
$items = [];
$errors = [];

function format_order_number($id) {
    return 'KW-' . str_pad((int)$id, 5, '0', STR_PAD_LEFT);
}

function status_label($status) {
    $status = strtolower(trim($status));
    $labels = [
        'pending' => 'Pending',
        'processing' => 'Processing',
        'shipped' => 'Shipped',
        'delivered' => 'Delivered',
        'cancelled' => 'Cancelled',
    ];

    return $labels[$status] ?? ucfirst($status);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entered_number = trim($_POST['order_number'] ?? '');

    if ($entered_number === '') {
        $errors[] = 'Please enter an order number.';
    } else {
        $search_id = preg_replace('/[^0-9]/', '', $entered_number);

        if ($search_id === '') {
            $errors[] = 'Please enter a valid order number.';
        } else {
            $stmt = mysqli_prepare($conn, 'SELECT * FROM orders WHERE id = ?');
            mysqli_stmt_bind_param($stmt, 'i', $search_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $order = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);

            if ($order) {
                $item_stmt = mysqli_prepare($conn, 'SELECT * FROM order_items WHERE order_id = ?');
                mysqli_stmt_bind_param($item_stmt, 'i', $order['id']);
                mysqli_stmt_execute($item_stmt);
                $item_result = mysqli_stmt_get_result($item_stmt);

                while ($row = mysqli_fetch_assoc($item_result)) {
                    $items[] = $row;
                }

                mysqli_stmt_close($item_stmt);
            } else {
                $errors[] = 'No order found for that number.';
            }
        }
    }
}

require 'includes/header.php';
?>

<section class="wrap" style="padding-top:40px;padding-bottom:80px;max-width:760px;">
    <div class="breadcrumb"><a href="index.php">Home</a> / Track Order</div>
    <h2>Track your order</h2>
    <p style="color:#6b6156;margin-bottom:24px;">Enter your order number to see the current status and your order details.</p>

    <form method="POST" style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:28px;">
        <input type="text" name="order_number" value="<?= htmlspecialchars($entered_number) ?>" placeholder="Example: KW-00001" required style="flex:1;min-width:220px;padding:12px 14px;border:1px solid var(--line);border-radius:6px;">
        <button type="submit" class="btn btn-primary">Check order</button>
    </form>

    <?php foreach ($errors as $err): ?>
        <div class="alert alert-error"><?= htmlspecialchars($err) ?></div>
    <?php endforeach; ?>

    <?php if ($order): ?>
        <div class="cart-summary">
            <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;margin-bottom:12px;">
                <div>
                    <div class="eyebrow">Order found</div>
                    <h3 style="margin:4px 0;">Order <?= htmlspecialchars(format_order_number($order['id'])) ?></h3>
                </div>
                <span style="display:inline-block;padding:8px 12px;border-radius:999px;background:#f3eee6;color:#201a17;font-weight:600;">
                    Status: <?= htmlspecialchars(status_label($order['status'])) ?>
                </span>
            </div>

            <div style="display:grid;gap:8px;font-size:14px;color:#6b6156;">
                <div><strong>Placed:</strong> <?= htmlspecialchars($order['created_at']) ?></div>
                <div><strong>Customer:</strong> <?= htmlspecialchars($order['full_name']) ?></div>
                <div><strong>Phone:</strong> <?= htmlspecialchars($order['phone']) ?></div>
                <div><strong>Address:</strong> <?= htmlspecialchars($order['address']) ?>, <?= htmlspecialchars($order['city']) ?> <?= htmlspecialchars($order['postal_code']) ?></div>
                <div><strong>Payment:</strong> <?= htmlspecialchars(strtoupper($order['payment_method'])) ?></div>
            </div>

            <div style="margin-top:18px;">
                <h4 style="margin-bottom:10px;">Items</h4>
                <?php foreach ($items as $item): ?>
                    <div style="display:flex;justify-content:space-between;padding:8px 0;border-top:1px solid var(--line);">
                        <span><?= htmlspecialchars($item['product_name']) ?> × <?= (int)$item['quantity'] ?></span>
                        <span class="mono"><?= money($item['price'] * $item['quantity']) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="summary-row" style="margin-top:14px;"><span>Subtotal</span><span class="mono"><?= money($order['subtotal']) ?></span></div>
            <div class="summary-row"><span>Shipping</span><span class="mono"><?= $order['shipping_fee'] == 0 ? 'FREE' : money($order['shipping_fee']) ?></span></div>
            <div class="summary-row total"><span>Total</span><span class="mono"><?= money($order['total']) ?></span></div>
        </div>
    <?php endif; ?>
</section>

<?php require 'includes/footer.php'; ?>
