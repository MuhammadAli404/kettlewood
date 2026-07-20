<?php
require_once '../config.php';
require_once '../includes/functions.php';
require_admin();

$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id LIMIT 1"));

if (!$user) {
    redirect('users.php');
}

$orders = mysqli_query($conn, "SELECT * FROM orders WHERE user_id = $user_id ORDER BY created_at DESC");

require 'includes/admin_header.php';
?>

<section class="wrap" style="padding-top:40px;padding-bottom:60px;">
    <div class="breadcrumb"><a href="index.php">Dashboard</a> / <a href="users.php">Customers</a> / Order history</div>
    <h2>Order history for <?= htmlspecialchars($user['name']) ?></h2>
    <p style="color:#6b6156;margin-bottom:20px;">Email: <?= htmlspecialchars($user['email']) ?></p>

    <?php if (mysqli_num_rows($orders) === 0): ?>
        <div class="cart-summary">No orders found for this customer yet.</div>
    <?php else: ?>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Address</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($o = mysqli_fetch_assoc($orders)): ?>
                <tr>
                    <td class="mono">#KW-<?= str_pad($o['id'], 5, '0', STR_PAD_LEFT) ?></td>
                    <td class="mono"><?= money($o['total']) ?></td>
                    <td><?= htmlspecialchars($o['status']) ?></td>
                    <td><?= date('d M, Y H:i', strtotime($o['created_at'])) ?></td>
                    <td><?= htmlspecialchars($o['address']) ?>, <?= htmlspecialchars($o['city']) ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>

<?php require '../includes/footer.php'; ?>
