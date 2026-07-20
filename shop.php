<?php
require_once 'config.php';
require_once 'includes/functions.php';
$page_title = 'Shop';

$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY id");
$active_cat = isset($_GET['category']) ? clean($_GET['category']) : '';

if ($active_cat) {
    $sql = "SELECT p.* FROM products p JOIN categories c ON p.category_id = c.id WHERE c.slug = '$active_cat' ORDER BY p.created_at DESC";
} else {
    $sql = "SELECT * FROM products ORDER BY created_at DESC";
}
$products = mysqli_query($conn, $sql);

require 'includes/header.php';
?>

<section class="wrap" style="padding-top:40px;">
    <div class="breadcrumb"><a href="index.php">Home</a> / Shop</div>
    <div class="section-head">
        <div>
            <h2>All coffee</h2>
            <p>Every bag lists origin, altitude and roast date.</p>
        </div>
    </div>

    <div class="filter-bar">
        <a href="shop.php" class="<?= $active_cat === '' ? 'active' : '' ?>">All</a>
        <?php mysqli_data_seek($categories, 0); while ($c = mysqli_fetch_assoc($categories)): ?>
            <a href="shop.php?category=<?= urlencode($c['slug']) ?>" class="<?= $active_cat === $c['slug'] ? 'active' : '' ?>">
                <?= htmlspecialchars($c['name']) ?>
            </a>
        <?php endwhile; ?>
    </div>

    <div class="grid">
        <?php if (mysqli_num_rows($products) === 0): ?>
            <div class="empty-state" style="grid-column:1/-1;">No products in this category yet.</div>
        <?php endif; ?>
        <?php while ($p = mysqli_fetch_assoc($products)): ?>
            <a href="product.php?slug=<?= urlencode($p['slug']) ?>" class="card">
                <div class="card-img-wrap">
                    <?php if ($p['compare_price']): ?><span class="badge-featured">SALE</span><?php endif; ?>
                    <div class="card-img"><img src="<?= image_src($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>"></div>
                </div>
                <div class="card-body">
                    <?php if ($p['origin']): ?><div class="card-tag"><?= htmlspecialchars($p['origin']) ?></div><?php endif; ?>
                    <h3><?= htmlspecialchars($p['name']) ?></h3>
                    <?php if ($p['tasting_notes']): ?><p class="card-notes"><?= htmlspecialchars($p['tasting_notes']) ?></p><?php endif; ?>
                    <div class="card-price-row">
                        <div>
                            <?php if ($p['compare_price']): ?><span class="price-strike"><?= money($p['compare_price']) ?></span><?php endif; ?>
                            <span class="price"><?= money($p['price']) ?></span>
                        </div>
                    </div>
                </div>
            </a>
        <?php endwhile; ?>
    </div>
</section>

<?php require 'includes/footer.php'; ?>
