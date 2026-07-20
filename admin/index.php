<?php
require_once '../config.php';
require_once '../includes/functions.php';
require_admin();

$total_orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM orders"))['c'];
$total_revenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(total),0) s FROM orders"))['s'];
$total_products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM products"))['c'];
$low_stock = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM products WHERE stock <= 10"))['c'];

$recent_orders = mysqli_query($conn, "SELECT * FROM orders ORDER BY created_at DESC LIMIT 10");
$recent_users = mysqli_query($conn, "SELECT u.*, (SELECT COUNT(*) FROM orders o WHERE o.user_id = u.id) AS order_count FROM users u WHERE u.is_admin = 0 ORDER BY u.created_at DESC LIMIT 8");

// order status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $oid = (int)$_POST['order_id'];
    $status = clean($_POST['status']);
    mysqli_query($conn, "UPDATE orders SET status='$status' WHERE id=$oid");
    header('Location: index.php'); exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Admin Dashboard — Kettlewood</title><link rel="stylesheet" href="../css/style.css"></head>
<body>
<header class="site-header">
    <div class="wrap">
        <a href="index.php" class="logo">KETTLE<span>WOOD</span> <small style="font-size:12px;color:#a89e8f;">/ admin</small></a>
        <nav class="nav">
            <a href="index.php">Dashboard</a>
            <a href="products.php">Products</a>
            <a href="users.php">Users</a>
            <a href="../logout.php">Log out</a>
        </nav>
    </div>
</header>

<section class="wrap" style="padding-top:40px;">
    <h2>Dashboard</h2>
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:20px;margin-bottom:40px;">
        <div class="cart-summary"><div class="eyebrow">Orders</div><div style="font-size:28px;font-family:'Fraunces',serif;"><?= $total_orders ?></div></div>
        <div class="cart-summary"><div class="eyebrow">Revenue</div><div style="font-size:28px;font-family:'Fraunces',serif;"><?= money($total_revenue) ?></div></div>
        <div class="cart-summary"><div class="eyebrow">Products</div><div style="font-size:28px;font-family:'Fraunces',serif;"><?= $total_products ?></div></div>
        <div class="cart-summary"><div class="eyebrow">Low stock (≤10)</div><div style="font-size:28px;font-family:'Fraunces',serif;"><?= $low_stock ?></div></div>
    </div>

    <h3>Recent orders</h3>
    <table class="cart-table">
        <thead><tr><th>Order</th><th>Customer</th><th>Total</th><th>Status</th><th>Date</th></tr></thead>
        <tbody>
        <?php while ($o = mysqli_fetch_assoc($recent_orders)): ?>
            <tr>
                <td class="mono">#KW-<?= str_pad($o['id'],5,'0',STR_PAD_LEFT) ?></td>
                <td><?= htmlspecialchars($o['full_name']) ?><br><span style="font-size:12px;color:#6b6156;"><?= htmlspecialchars($o['email']) ?></span></td>
                <td class="mono"><?= money($o['total']) ?></td>
                <td>
                    <form method="POST" style="display:flex;gap:6px;">
                        <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                        <select name="status" style="padding:6px;border:1px solid var(--line);border-radius:3px;">
                            <?php foreach (['pending','processing','shipped','delivered','cancelled'] as $s): ?>
                                <option value="<?= $s ?>" <?= $o['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" name="update_status" class="btn btn-sm btn-dark">Save</button>
                    </form>
                </td>
                <td style="font-size:13px;color:#6b6156;"><?= date('d M, H:i', strtotime($o['created_at'])) ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <h3 style="margin-top:36px;">Customers</h3>
    <table class="cart-table">
        <thead><tr><th>Name</th><th>Email</th><th>Orders</th><th>Actions</th></tr></thead>
        <tbody>
        <?php while ($u = mysqli_fetch_assoc($recent_users)): ?>
            <tr>
                <td><?= htmlspecialchars($u['name']) ?><br><span style="font-size:12px;color:#6b6156;"><?= htmlspecialchars($u['phone'] ?? '') ?></span></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td><?= (int)$u['order_count'] ?></td>
                <td><a href="user_orders.php?id=<?= (int)$u['id'] ?>" class="btn btn-sm btn-outline">View history</a></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</section>
</body>
</html>
