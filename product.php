<?php
require_once 'config.php';
require_once 'includes/functions.php';

$slug = isset($_GET['slug']) ? clean($_GET['slug']) : '';
$result = mysqli_query($conn, "SELECT * FROM products WHERE slug = '$slug' LIMIT 1");

if (!$result || mysqli_num_rows($result) === 0) {
    header('HTTP/1.0 404 Not Found');
    require 'includes/header.php';
    echo '<div class="wrap empty-state"><h2>Product not found</h2><a href="shop.php" class="btn btn-primary">Back to shop</a></div>';
    require 'includes/footer.php';
    exit;
}
$p = mysqli_fetch_assoc($result);
$page_title = $p['name'];

$added = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $qty = max(1, (int)$_POST['quantity']);
    $qty = min($qty, max(1, (int)$p['stock']));
    cart_add($p['id'], $qty);
    $added = true;
}

$related = mysqli_query($conn, "SELECT * FROM products WHERE category_id = {$p['category_id']} AND id != {$p['id']} LIMIT 4");

require 'includes/header.php';
?>

<section class="wrap product-page">
    <div class="breadcrumb"><a href="index.php">Home</a> / <a href="shop.php">Shop</a> / <?= htmlspecialchars($p['name']) ?></div>

    <?php if ($added): ?>
        <div class="alert alert-success">Added to cart. <a href="cart.php">View cart</a></div>
    <?php endif; ?>

    <div class="product-detail">
        <div class="pd-image">
            <img src="<?= image_src($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
        </div>
        <div class="pd-info">
            <?php if ($p['origin']): ?><div class="card-tag"><?= htmlspecialchars($p['origin']) ?></div><?php endif; ?>
            <h1><?= htmlspecialchars($p['name']) ?></h1>
            <?php if ($p['tasting_notes']): ?><p class="pd-notes"><?= htmlspecialchars($p['tasting_notes']) ?></p><?php endif; ?>

            <div class="pd-price">
                <?php if ($p['compare_price']): ?><span class="price-strike"><?= money($p['compare_price']) ?></span><?php endif; ?>
                <?= money($p['price']) ?>
            </div>

            <p class="pd-desc"><?= nl2br(htmlspecialchars($p['description'])) ?></p>

            <?php if ($p['altitude'] || $p['roast_level']): ?>
            <div class="stamp lot-card">
                <div class="stamp-title">Lot Details</div>
                <?php if ($p['altitude']): ?><div class="stamp-row"><span>Altitude</span><b><?= htmlspecialchars($p['altitude']) ?></b></div><?php endif; ?>
                <?php if ($p['roast_level']): ?><div class="stamp-row"><span>Roast level</span><b><?= htmlspecialchars($p['roast_level']) ?></b></div><?php endif; ?>
                <div class="stamp-row"><span>SKU</span><b class="mono"><?= htmlspecialchars($p['sku']) ?></b></div>
                <div class="stamp-row"><span>Stock</span><b><?= $p['stock'] > 0 ? $p['stock'] . ' bags available' : 'Out of stock' ?></b></div>
            </div>
            <?php endif; ?>

            <?php if ($p['stock'] > 0): ?>
            <form method="POST">
                <div class="qty-row">
                    <div class="qty-box" aria-label="Quantity">
                        <button type="button" onclick="stepQty(-1)" aria-label="Decrease quantity">&minus;</button>
                        <input type="number" name="quantity" id="qtyInput" value="1" min="1" max="<?= $p['stock'] ?>">
                        <button type="button" onclick="stepQty(1)" aria-label="Increase quantity">&plus;</button>
                    </div>
                    <button type="submit" name="add_to_cart" class="btn btn-primary">Add to cart - <?= money($p['price']) ?></button>
                </div>
            </form>
            <?php else: ?>
                <button class="btn btn-dark" disabled>Out of stock</button>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php if (mysqli_num_rows($related) > 0): ?>
<section class="wrap">
    <div class="section-head"><h2>You might also like</h2></div>
    <div class="grid">
        <?php while ($r = mysqli_fetch_assoc($related)): ?>
            <a href="product.php?slug=<?= urlencode($r['slug']) ?>" class="card">
                <div class="card-img"><img src="<?= image_src($r['image']) ?>" alt="<?= htmlspecialchars($r['name']) ?>"></div>
                <div class="card-body">
                    <h3><?= htmlspecialchars($r['name']) ?></h3>
                    <div class="card-price-row"><span class="price"><?= money($r['price']) ?></span></div>
                </div>
            </a>
        <?php endwhile; ?>
    </div>
</section>
<?php endif; ?>

<script>
function stepQty(delta) {
    const input = document.getElementById('qtyInput');
    let val = parseInt(input.value || '1') + delta;
    const max = parseInt(input.max || '99');
    if (val < 1) val = 1;
    if (val > max) val = max;
    input.value = val;
}
</script>

<?php require 'includes/footer.php'; ?>
