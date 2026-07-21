<?php
require_once 'config.php';
require_once 'includes/functions.php';
$page_title = 'Home';
require 'includes/header.php';

$featured = mysqli_query($conn, "SELECT * FROM products WHERE is_featured = 1 LIMIT 4");

$newsletter_message = '';
$newsletter_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['newsletter_email'])) {
    $newsletter_email = trim($_POST['newsletter_email']);
    if (!filter_var($newsletter_email, FILTER_VALIDATE_EMAIL)) {
        $newsletter_error = 'Please enter a valid email address.';
    } else {
        $newsletter_message = 'You are on the list. Fresh roast notes are on the way.';
    }
}
?>

<section class="hero">
    <div class="wrap">
        <div>
            <div class="eyebrow">Roasted weekly &middot; Shipped fresh</div>
            <h1>Coffee traced back<br>to the farm that grew it.</h1>
            <p>We buy direct from smallholder growers, roast in small batches, and print the harvest details on every bag - no blends dressed up as origin coffee.</p>
            <div class="hero-actions">
                <a href="shop.php" class="btn btn-primary">Shop the roastery</a>
                <a href="shop.php?category=subscriptions" class="btn btn-outline">Start a subscription</a>
            </div>
        </div>
        <div class="stamp">
            <div class="stamp-title">Today's Roast Log</div>
            <div class="stamp-row"><span>Lot</span><b>Nyeri Peaberry</b></div>
            <div class="stamp-row"><span>Origin</span><b>Nyeri, Kenya</b></div>
            <div class="stamp-row"><span>Altitude</span><b>1,750-2,000m</b></div>
            <div class="stamp-row"><span>Roast date</span><b><?= date('d M Y') ?></b></div>
            <div class="stamp-row"><span>Cupping score</span><b>87.5</b></div>
        </div>
    </div>
</section>

<section class="wrap">
    <div class="feature-band">
        <div class="feature-card">
            <div class="eyebrow">01</div>
            <h3>Fresh roast every week</h3>
            <p>Small-batch coffee lands at your door within 48 hours of roasting.</p>
        </div>
        <div class="feature-card">
            <div class="eyebrow">02</div>
            <h3>Traceable origins</h3>
            <p>Every bag includes the farm, altitude, and roast profile for that lot.</p>
        </div>
        <div class="feature-card">
            <div class="eyebrow">03</div>
            <h3>Easy brewing support</h3>
            <p>Get tasting notes, brew guides, and pairing tips in one place.</p>
        </div>
    </div>
</section>

<section class="wrap">
    <div class="section-head">
        <div>
            <h2>Explore the roastery</h2>
            <p>Pick a ritual that fits your taste.</p>
        </div>
    </div>
    <div class="collection-grid">
        <a href="shop.php" class="collection-card">
            <h3>Signature roasts</h3>
            <p>Discover our best-selling single origins and seasonal lots.</p>
            <span>Browse coffee</span>
        </a>
        <a href="shop.php?category=subscriptions" class="collection-card alt">
            <h3>Subscriptions</h3>
            <p>Build a recurring ritual with flexible delivery and savings.</p>
            <span>Start a plan</span>
        </a>
        <a href="shop.php" class="collection-card">
            <h3>Gift-ready bundles</h3>
            <p>Thoughtful sets for birthdays, offices, and coffee-loving friends.</p>
            <span>Shop bundles</span>
        </a>
    </div>
</section>

<section class="newsletter-section">
    <div class="wrap">
        <div class="newsletter-card">
            <div>
                <div class="eyebrow">Stay in the loop</div>
                <h2>Get new roast drops and brewing notes first.</h2>
                <p>Join our list for launch-day coffee, seasonal tasting notes, and limited releases.</p>
            </div>
            <form method="POST" class="newsletter-form">
                <input type="email" name="newsletter_email" placeholder="Enter your email" required>
                <button type="submit" class="btn btn-primary">Join now</button>
            </form>
            <?php if ($newsletter_message): ?>
                <p class="newsletter-feedback success"><?= htmlspecialchars($newsletter_message) ?></p>
            <?php elseif ($newsletter_error): ?>
                <p class="newsletter-feedback error"><?= htmlspecialchars($newsletter_error) ?></p>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="wrap">
    <div class="section-head">
        <div>
            <h2>Featured this week</h2>
            <p>Small lots, picked by our roastmaster.</p>
        </div>
        <a href="shop.php" class="btn btn-dark btn-sm">View all</a>
    </div>
    <div class="grid">
        <?php while ($p = mysqli_fetch_assoc($featured)): ?>
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

<section class="promise-band">
    <div class="wrap promise-grid">
        <div class="promise">
            <div class="eyebrow">01</div>
            <h3>Sourced direct</h3>
            <p>We pay growers above fair-trade minimums and publish the price on every lot page.</p>
        </div>
        <div class="promise">
            <div class="eyebrow">02</div>
            <h3>Roasted to order</h3>
            <p>Nothing sits on a shelf. Bags are roasted after you order and shipped within 48 hours.</p>
        </div>
        <div class="promise">
            <div class="eyebrow">03</div>
            <h3>Brew-guide included</h3>
            <p>Every bag ships with a recipe card tuned to that specific lot's density and roast level.</p>
        </div>
    </div>
</section>

<?php require 'includes/footer.php'; ?>
