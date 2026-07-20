<?php
require_once '../config.php';
require_once '../includes/functions.php';
require_admin();

$users = mysqli_query($conn, "SELECT u.*, (SELECT COUNT(*) FROM orders o WHERE o.user_id = u.id) AS order_count FROM users u WHERE u.is_admin = 0 ORDER BY u.created_at DESC");

require 'includes/admin_header.php';
?>

<section class="wrap" style="padding-top:40px;padding-bottom:60px;">
    <h2>Customers</h2>
    <p style="color:#6b6156;margin-bottom:20px;">All customer accounts created from checkout or registration.</p>

    <table class="cart-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Orders</th>
                <th>Joined</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($u = mysqli_fetch_assoc($users)): ?>
            <tr>
                <td><?= htmlspecialchars($u['name']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td><?= htmlspecialchars($u['phone'] ?? '-') ?></td>
                <td><?= (int)$u['order_count'] ?></td>
                <td><?= date('d M, Y', strtotime($u['created_at'])) ?></td>
                <td>
                    <a href="user_orders.php?id=<?= (int)$u['id'] ?>" class="btn btn-sm btn-primary">View all orders</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</section>

<?php require '../includes/footer.php'; ?>