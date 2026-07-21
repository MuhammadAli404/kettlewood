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
$free_shipping_left = max(0, FREE_SHIPPING_THRESHOLD - $subtotal);

require 'includes/header.php';
?>

<section class="wrap cart-page">
    <div class="breadcrumb"><a href="index.php">Home</a> / Cart</div>
    <h2>Your cart</h2>

    <?php if (empty($items)): ?>
    <div class="empty-state">
        <p>Your cart is empty.</p>
        <a href="shop.php" class="btn btn-primary">Browse coffee</a>
    </div>
    <?php else: ?>
    <div class="cart-layout">
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
                                    <div class="cart-item-name"><?= htmlspecialchars($item['name']) ?></div>
                                    <?php if ($item['origin']): ?><div class="cart-item-origin"><?= htmlspecialchars($item['origin']) ?></div><?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td class="mono"><?= money($item['price']) ?></td>
                        <td>
                            <input class="cart-qty" type="number" name="qty[<?= $item['id'] ?>]" value="<?= $item['quantity'] ?>" min="1" max="<?= $item['stock'] ?>">
                        </td>
                        <td class="mono"><?= money($item['line_total']) ?></td>
                        <td>
                            <button type="submit" name="remove_item" value="<?= $item['id'] ?>" class="remove-link">Remove</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="cart-actions">
                <button type="submit" name="update_cart" class="btn btn-dark btn-sm">Update cart</button>
                <a href="shop.php" class="btn btn-quiet btn-sm">Continue shopping</a>
            </div>
        </form>

        <aside class="cart-summary">
            <h3>Order summary</h3>
            <div class="summary-row"><span>Subtotal</span><span class="mono"><?= money($subtotal) ?></span></div>
            <div class="summary-row"><span>Shipping</span><span class="mono"><?= $shipping == 0 ? 'FREE' : money($shipping) ?></span></div>
            <?php if ($free_shipping_left > 0): ?>
                <div class="shipping-meter">
                    <div style="width:<?= min(100, ($subtotal / FREE_SHIPPING_THRESHOLD) * 100) ?>%;"></div>
                </div>
                <p class="shipping-note">Add <?= money($free_shipping_left) ?> more for free shipping.</p>
            <?php else: ?>
                <p class="shipping-note">You unlocked free shipping.</p>
            <?php endif; ?>
            <div class="summary-row total"><span>Total</span><span class="mono"><?= money($total) ?></span></div>
            <a href="checkout.php" class="btn btn-primary btn-block">Proceed to checkout</a>
        </aside>
    </div>
    <?php endif; ?>
</section>

<?php require 'includes/footer.php'; ?>
