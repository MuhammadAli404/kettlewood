<?php
require_once '../config.php';
require_once '../includes/functions.php';
require_admin();

$msg = '';

// Add product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = clean($_POST['name']);
    $slug = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $name), '-'));
    $sku = clean($_POST['sku']);
    $price = (float)$_POST['price'];
    $category_id = (int)$_POST['category_id'];
    $stock = (int)$_POST['stock'];
    $origin = clean($_POST['origin']);
    $tasting_notes = clean($_POST['tasting_notes']);
    $description = clean($_POST['description']);

    mysqli_query($conn, "INSERT INTO products (category_id, name, slug, sku, price, origin, tasting_notes, description, stock, is_featured)
        VALUES ($category_id, '$name', '$slug', '$sku', $price, '$origin', '$tasting_notes', '$description', $stock, 0)");
    $msg = 'Product added.';
}

// Delete product
if (isset($_GET['delete'])) {
    mysqli_query($conn, "DELETE FROM products WHERE id = " . (int)$_GET['delete']);
    header('Location: products.php'); exit;
}

$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY name");
$products = mysqli_query($conn, "SELECT p.*, c.name AS cat_name FROM products p JOIN categories c ON p.category_id=c.id ORDER BY p.id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Products — Admin</title><link rel="stylesheet" href="../css/style.css"></head>
<body>
<header class="site-header">
    <div class="wrap">
        <a href="index.php" class="logo">KETTLE<span>WOOD</span> <small style="font-size:12px;color:#a89e8f;">/ admin</small></a>
        <nav class="nav">
            <a href="index.php">Dashboard</a>
            <a href="products.php">Products</a>
            <a href="../logout.php">Log out</a>
        </nav>
    </div>
</header>

<section class="wrap" style="padding-top:40px;">
    <?php if ($msg): ?><div class="alert alert-success"><?= $msg ?></div><?php endif; ?>

    <div style="display:grid;grid-template-columns:1fr 1.3fr;gap:40px;align-items:flex-start;">
        <div>
            <h3>Add new product</h3>
            <form method="POST">
                <div class="field"><label>Name</label><input type="text" name="name" required></div>
                <div class="field"><label>SKU</label><input type="text" name="sku" required></div>
                <div class="field"><label>Category</label>
                    <select name="category_id" required>
                        <?php mysqli_data_seek($categories,0); while ($c = mysqli_fetch_assoc($categories)): ?>
                            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="field"><label>Price (<?= trim(CURRENCY) ?>)</label><input type="number" step="0.01" name="price" required></div>
                <div class="field"><label>Stock</label><input type="number" name="stock" value="20" required></div>
                <div class="field"><label>Origin (optional)</label><input type="text" name="origin"></div>
                <div class="field"><label>Tasting notes (optional)</label><input type="text" name="tasting_notes"></div>
                <div class="field"><label>Description</label><textarea name="description" rows="3"></textarea></div>
                <button type="submit" name="add_product" class="btn btn-primary btn-block">Add product</button>
            </form>
        </div>

        <div>
            <h3>All products</h3>
            <table class="cart-table">
                <thead><tr><th>Product</th><th>Category</th><th>Price</th><th>Stock</th><th></th></tr></thead>
                <tbody>
                <?php while ($p = mysqli_fetch_assoc($products)): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['name']) ?><br><span class="mono" style="font-size:11px;color:#6b6156;"><?= htmlspecialchars($p['sku']) ?></span></td>
                        <td><?= htmlspecialchars($p['cat_name']) ?></td>
                        <td class="mono"><?= money($p['price']) ?></td>
                        <td><?= $p['stock'] ?></td>
                        <td><a href="products.php?delete=<?= $p['id'] ?>" onclick="return confirm('Delete this product?')" class="remove-link">Delete</a></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
</body>
</html>
