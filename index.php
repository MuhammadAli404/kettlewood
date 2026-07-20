<?php
require_once 'config.php';
require_once 'includes/functions.php';
$page_title = 'Home';
require 'includes/header.php';

$featured = mysqli_query($conn, "SELECT * FROM products WHERE is_featured = 1 LIMIT 4");
?>

<section class="hero">
    <div class="wrap">
        <div>
            <div class="eyebrow">Roasted weekly &middot; Shipped fresh</div>
            <h1>Coffee traced back<br>to the farm that grew it.</h1>
            <p>We buy direct from smallholder growers, roast in small batches, and print the harvest details on every bag — no blends dressed up as origin coffee.</p>
            <a href="shop.php" class="btn btn-primary">Shop the roastery</a>
            <a href="shop.php?category=subscriptions" class="btn btn-outline" style="margin-left:10px;">Start a subscription</a>
        </div>
        <div class="stamp">
            <div class="stamp-title">Today's Roast Log</div>
            <div class="stamp-row"><span>Lot</span><b>Nyeri Peaberry</b></div>
            <div class="stamp-row"><span>Origin</span><b>Nyeri, Kenya</b></div>
            <div class="stamp-row"><span>Altitude</span><b>1,750–2,000m</b></div>
            <div class="stamp-row"><span>Roast date</span><b><?= date('d M Y') ?></b></div>
            <div class="stamp-row"><span>Cupping score</span><b>87.5</b></div>
        </div>
    </div>
</section>

<section class="wrap">
    <div class="section-head">
        <div>
            <h2>Featured this week</h2>
            <p>Small lots, picked by our roastmaster.</p>
        </div>
        <a href="shop.php" class="btn btn-dark btn-sm">View all →</a>
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

<section style="background:var(--parchment);">
    <div class="wrap" style="display:grid;grid-template-columns:repeat(3,1fr);gap:32px;text-align:center;">
        <div>
            <div class="eyebrow">01</div>
            <h3>Sourced direct</h3>
            <p style="color:#6b6156;font-size:14px;">We pay growers above fair-trade minimums and publish the price on every lot page.</p>
        </div>
        <div>
            <div class="eyebrow">02</div>
            <h3>Roasted to order</h3>
            <p style="color:#6b6156;font-size:14px;">Nothing sits on a shelf. Bags are roasted after you order and shipped within 48 hours.</p>
        </div>
        <div>
            <div class="eyebrow">03</div>
            <h3>Brew-guide included</h3>
            <p style="color:#6b6156;font-size:14px;">Every bag ships with a recipe card tuned to that specific lot's density and roast level.</p>
        </div>
    </div>
</section>

<?php require 'includes/footer.php'; ?>
