<?php
require_once 'config.php';
require_once 'includes/functions.php';
$page_title = 'Shop';

$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY id");
$active_cat = isset($_GET['category']) ? clean($_GET['category']) : '';
$search = isset($_GET['q']) ? clean($_GET['q']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
$added = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quick_add'])) {
    $product_id = (int)$_POST['product_id'];
    $stock_check = mysqli_query($conn, "SELECT id, stock FROM products WHERE id = $product_id LIMIT 1");
    if ($stock_check && ($item = mysqli_fetch_assoc($stock_check)) && (int)$item['stock'] > 0) {
        cart_add($item['id'], 1);
        $added = true;
    }
}

$where = [];
if ($active_cat) {
    $where[] = "c.slug = '$active_cat'";
}
if ($search) {
    $where[] = "(p.name LIKE '%$search%' OR p.origin LIKE '%$search%' OR p.tasting_notes LIKE '%$search%' OR p.roast_level LIKE '%$search%')";
}

$order = "p.created_at DESC";
if ($sort === 'price_low') {
    $order = "p.price ASC";
} elseif ($sort === 'price_high') {
    $order = "p.price DESC";
} elseif ($sort === 'name') {
    $order = "p.name ASC";
}

$sql = "SELECT p.*, c.name AS category_name, c.slug AS category_slug FROM products p JOIN categories c ON p.category_id = c.id";
if ($where) {
    $sql .= " WHERE " . implode(' AND ', $where);
}
$sql .= " ORDER BY $order";
$products = mysqli_query($conn, $sql);

require 'includes/header.php';
?>

<section class="wrap shop-page">
    <div class="breadcrumb"><a href="index.php">Home</a> / Shop</div>

    <?php if ($added): ?>
        <div class="alert alert-success">Added to cart. <a href="cart.php">View cart</a></div>
    <?php endif; ?>

    <div class="section-head shop-heading">
        <div>
            <h2>All coffee</h2>
            <p>Search by origin, tasting note, or roast level, then sort the shelf your way.</p>
        </div>
        <a href="cart.php" class="btn btn-dark btn-sm">Cart: <?= cart_count() ?></a>
    </div>

    <form method="GET" class="shop-tools">
        <div class="field search-field">
            <label for="q">Search coffee</label>
            <input type="search" id="q" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Try Ethiopia, chocolate, light...">
        </div>
        <div class="field">
            <label for="sort">Sort</label>
            <select id="sort" name="sort">
                <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest arrivals</option>
                <option value="price_low" <?= $sort === 'price_low' ? 'selected' : '' ?>>Price: low to high</option>
                <option value="price_high" <?= $sort === 'price_high' ? 'selected' : '' ?>>Price: high to low</option>
                <option value="name" <?= $sort === 'name' ? 'selected' : '' ?>>Name A-Z</option>
            </select>
        </div>
        <?php if ($active_cat): ?><input type="hidden" name="category" value="<?= htmlspecialchars($active_cat) ?>"><?php endif; ?>
        <button type="submit" class="btn btn-primary">Apply</button>
        <?php if ($search || $sort !== 'newest'): ?><a href="shop.php<?= $active_cat ? '?category=' . urlencode($active_cat) : '' ?>" class="btn btn-quiet">Clear</a><?php endif; ?>
    </form>

    <div class="filter-bar">
        <a href="shop.php<?= $search ? '?q=' . urlencode($search) : '' ?>" class="<?= $active_cat === '' ? 'active' : '' ?>">All</a>
        <?php mysqli_data_seek($categories, 0); while ($c = mysqli_fetch_assoc($categories)): ?>
            <?php
            $params = ['category' => $c['slug']];
            if ($search) $params['q'] = $search;
            if ($sort !== 'newest') $params['sort'] = $sort;
            ?>
            <a href="shop.php?<?= http_build_query($params) ?>" class="<?= $active_cat === $c['slug'] ? 'active' : '' ?>">
                <?= htmlspecialchars($c['name']) ?>
            </a>
        <?php endwhile; ?>
    </div>

    <div class="grid">
        <?php if (!$products || mysqli_num_rows($products) === 0): ?>
            <div class="empty-state" style="grid-column:1/-1;">
                <h3>No matching coffee yet.</h3>
                <p>Try a broader tasting note, origin, or category.</p>
                <a href="shop.php" class="btn btn-primary">Reset shop</a>
            </div>
        <?php endif; ?>
        <?php while ($p = mysqli_fetch_assoc($products)): ?>
            <article class="card product-card">
                <a href="product.php?slug=<?= urlencode($p['slug']) ?>" class="card-link">
                    <div class="card-img-wrap">
                        <?php if ($p['compare_price']): ?><span class="badge-featured">SALE</span><?php endif; ?>
                        <div class="card-img"><img src="<?= image_src($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>"></div>
                    </div>
                    <div class="card-body">
                        <div class="card-meta">
                            <?php if ($p['origin']): ?><span class="card-tag"><?= htmlspecialchars($p['origin']) ?></span><?php endif; ?>
                            <span class="stock-dot <?= $p['stock'] > 0 ? 'in' : 'out' ?>"><?= $p['stock'] > 0 ? 'In stock' : 'Sold out' ?></span>
                        </div>
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
                <form method="POST" class="quick-add">
                    <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                    <button type="submit" name="quick_add" class="btn btn-primary btn-sm" <?= $p['stock'] <= 0 ? 'disabled' : '' ?>>
                        <?= $p['stock'] > 0 ? 'Quick add' : 'Sold out' ?>
                    </button>
                </form>
            </article>
        <?php endwhile; ?>
    </div>
</section>

<?php require 'includes/footer.php'; ?>
