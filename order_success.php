<?php
require_once 'config.php';
require_once 'includes/functions.php';
$page_title = 'Order Confirmed';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$order = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM orders WHERE id = $id"));

if (!$order) redirect('index.php');

$items = mysqli_query($conn, "SELECT * FROM order_items WHERE order_id = $id");

require 'includes/header.php';
?>

<section class="wrap" style="padding-top:60px;padding-bottom:80px;max-width:640px;">
    <div style="text-align:center;margin-bottom:36px;">
        <div class="eyebrow" style="text-align:center;">Order confirmed</div>
        <h1>Thank you, <?= htmlspecialchars(explode(' ', $order['full_name'])[0]) ?>.</h1>
        <p style="color:#6b6156;">Your order <b class="mono">#KW-<?= str_pad($order['id'], 5, '0', STR_PAD_LEFT) ?></b>
            has been placed and will be roasted before it ships.</p>
    </div>

    <div class="cart-summary">
        <?php while ($item = mysqli_fetch_assoc($items)): ?>
        <div
            style="display:flex;justify-content:space-between;font-size:14px;padding:8px 0;border-bottom:1px solid var(--line);">
            <span><?= htmlspecialchars($item['product_name']) ?> × <?= $item['quantity'] ?></span>
            <span class="mono"><?= money($item['price'] * $item['quantity']) ?></span>
        </div>
        <?php endwhile; ?>
        <div class="summary-row"><span>Shipping</span><span
                class="mono"><?= $order['shipping_fee'] == 0 ? 'FREE' : money($order['shipping_fee']) ?></span></div>
        <div class="summary-row total"><span>Total</span><span class="mono"><?= money($order['total']) ?></span></div>
    </div>

    <div style="margin-top:24px;">
        <h3 style="font-size:15px;">Shipping to</h3>
        <p style="color:#6b6156;font-size:14px;"><?= htmlspecialchars($order['full_name']) ?><br>
            <?= htmlspecialchars($order['address']) ?>, <?= htmlspecialchars($order['city']) ?>
            <?= htmlspecialchars($order['postal_code']) ?><br>
            <?= htmlspecialchars($order['phone']) ?> &middot; <?= htmlspecialchars($order['email']) ?></p>
    </div>

    <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:20px;">
        <a href="track_order.php" class="btn btn-outline btn-block" style="flex:1;text-align:center;">Track order</a>
        <a href="shop.php" class="btn btn-primary btn-block" style="flex:1;text-align:center;">Continue shopping</a>
    </div>
</section>

<?php require 'includes/footer.php'; ?>