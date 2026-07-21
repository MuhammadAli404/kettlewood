<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? htmlspecialchars($page_title) . ' - ' . SITE_NAME : SITE_NAME ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <header class="site-header">
        <div class="wrap">
            <a href="<?= BASE_URL ?>/index.php" class="logo">KETTLE<span>WOOD</span></a>
            <button class="menu-toggle" type="button" aria-label="Toggle navigation" aria-expanded="false">
                <span></span>
                <span></span>
                <span></span>
            </button>
            <nav class="nav">
                <a href="<?= BASE_URL ?>/index.php">Home</a>
                <a href="<?= BASE_URL ?>/shop.php">Shop</a>
                <a href="<?= BASE_URL ?>/track_order.php">Track Order</a>
                <?php if (is_logged_in()): ?>
                <a href="<?= BASE_URL ?>/logout.php">Log out</a>
                <?php else: ?>
                <a href="<?= BASE_URL ?>/login.php">Log in</a>
                <?php endif; ?>
                <a href="<?= BASE_URL ?>/cart.php" class="cart-pill" aria-label="Cart with <?= cart_count() ?> items">
                    <span>Cart</span>
                    <strong><?= cart_count() ?></strong>
                </a>
            </nav>
        </div>
    </header>

    <button class="back-to-top" type="button" aria-label="Back to top">↑</button>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggle = document.querySelector('.menu-toggle');
        const nav = document.querySelector('.site-header .nav');
        const backToTop = document.querySelector('.back-to-top');

        if (!toggle || !nav) return;

        toggle.addEventListener('click', function() {
            const expanded = this.getAttribute('aria-expanded') === 'true';
            this.setAttribute('aria-expanded', String(!expanded));
            nav.classList.toggle('is-open');
        });

        nav.querySelectorAll('a').forEach(function(link) {
            link.addEventListener('click', function() {
                toggle.setAttribute('aria-expanded', 'false');
                nav.classList.remove('is-open');
            });
        });

        if (backToTop) {
            window.addEventListener('scroll', function() {
                backToTop.classList.toggle('is-visible', window.scrollY > 400);
            });

            backToTop.addEventListener('click', function() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        }
    });
    </script>