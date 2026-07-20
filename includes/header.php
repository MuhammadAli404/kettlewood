<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($page_title) ? htmlspecialchars($page_title) . ' — ' . SITE_NAME : SITE_NAME ?></title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<header class="site-header">
    <div class="wrap">
        <a href="<?= BASE_URL ?>/index.php" class="logo">KETTLE<span>WOOD</span></a>
        <nav class="nav">
            <a href="<?= BASE_URL ?>/index.php">Home</a>
            <a href="<?= BASE_URL ?>/shop.php">Shop</a>
            <a href="<?= BASE_URL ?>/track_order.php">Track Order</a>
            <?php if (is_logged_in()): ?>
                <a href="<?= BASE_URL ?>/logout.php">Log out</a>
            <?php else: ?>
                <a href="<?= BASE_URL ?>/login.php">Log in</a>
            <?php endif; ?>
            <a href="<?= BASE_URL ?>/cart.php" class="cart-pill">🛒 Cart (<?= cart_count() ?>)</a>
        </nav>
    </div>
</header>
