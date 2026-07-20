<?php
require_once 'config.php';
require_once 'includes/functions.php';
$page_title = 'Your Cart';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_cart'])) {
        foreach ($_POST['qty'] as $product_id => $qty) {
            cart_update($product_id, $qty);
        }
    } elseif (isset($_POST['remove_item'])) {
        cart_remove($_POST['remove_item']);
    }
    redirect('cart.php');
}

$items = cart_items();
$subtotal = cart_subtotal();
$shipping = $items ? shipping_fee_for($subtotal) : 0;
$total = $subtotal + $shipping;

require 'includes/header.php';
?>

<section class="wrap" style="padding-top:40px;">
    <div class="breadcrumb"><a href="index.php">Home</a> / Cart</div>
    <h2>Your cart</h2>

    <?php if (empty($items)): ?>
    <div class="empty-state">
        <p>Your cart is empty.</p>
        <a href="shop.php" class="btn btn-primary">Browse coffee</a>
    </div>
    <?php else: ?>
    <div style="display:grid;grid-template-columns:2fr 1fr;gap:40px;align-items:flex-start;">
        <form method="POST">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                    <tr>
                        <td>
                            <div class="cart-item">
                                <img src="<?= image_src($item['image']) ?>" alt="">
                                <div>
                                    <div style="font-weight:600;"><?= htmlspecialchars($item['name']) ?></div>
                                    <?php if ($item['origin']): ?><div style="font-size:12px;color:#6b6156;">
                                        <?= htmlspecialchars($item['origin']) ?></div><?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td class="mono"><?= money($item['price']) ?></td>
                        <td><input type="number" name="qty[<?= $item['id'] ?>]" value="<?= $item['quantity'] ?>" min="1"
                                max="<?= $item['stock'] ?>"
                                style="width:60px;padding:6px;border:1px solid var(--line);border-radius:3px;"></td>
                        <td class="mono"><?= money($item['line_total']) ?></td>
                        <td></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div style="margin-top:16px;display:flex;gap:10px;">
                <button type="submit" name="update_cart" class="btn btn-dark btn-sm">Update cart</button>
                <a href="shop.php" class="btn btn-outline btn-sm"
                    style="border-color:var(--line);color:var(--ink);">Continue shopping</a>
            </div>
        </form>

        <div>
            <?php foreach ($items as $item): ?>
            <form method="POST" style="display:inline;">
                <input type="hidden" name="remove_item" value="<?= $item['id'] ?>">
            </form>
            <?php endforeach; ?>

            <div class="cart-summary">
                <h3 style="font-size:18px;">Order summary</h3>
                <div class="summary-row"><span>Subtotal</span><span class="mono"><?= money($subtotal) ?></span></div>
                <div class="summary-row"><span>Shipping</span><span
                        class="mono"><?= $shipping == 0 ? 'FREE' : money($shipping) ?></span></div>
                <?php if ($shipping > 0): ?>
                <div style="font-size:12px;color:#6b6156;">Free shipping on orders over
                    <?= money(FREE_SHIPPING_THRESHOLD) ?></div>
                <?php endif; ?>
                <div class="summary-row total"><span>Total</span><span class="mono"><?= money($total) ?></span></div>
                <a href="checkout.php" class="btn btn-primary btn-block"
                    style="margin-top:16px;text-align:center;">Proceed to checkout →</a>
            </div>

            <div style="margin-top:16px;">
                <p style="font-size:12px;color:#6b6156;margin-bottom:8px;">Remove an item:</p>
                <?php foreach ($items as $item): ?>
                <form method="POST"
                    style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;">
                    <span style="font-size:13px;"><?= htmlspecialchars($item['name']) ?></span>
                    <button type="submit" name="remove_item" value="<?= $item['id'] ?>" class="remove-link"
                        style="background:none;border:none;cursor:pointer;">Remove</button>
                </form>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</section>

<?php require 'includes/footer.php'; ?>