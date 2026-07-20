<?php
require_once 'config.php';
require_once 'includes/functions.php';
$page_title = 'Checkout';

$items = cart_items();
if (empty($items)) {
    redirect('cart.php');
}

$subtotal = cart_subtotal();
$shipping = shipping_fee_for($subtotal);
$total = $subtotal + $shipping;
$errors = [];

$default_values = [
    'full_name' => '',
    'email' => '',
    'phone' => '',
    'address' => '',
    'city' => '',
    'postal_code' => '',
];

if (is_logged_in()) {
    $profile_result = mysqli_query($conn, "SELECT * FROM users WHERE id = " . (int)$_SESSION['user_id'] . " LIMIT 1");
    $profile = mysqli_fetch_assoc($profile_result);

    if ($profile) {
        $default_values['full_name'] = $profile['name'] ?? '';
        $default_values['email'] = $profile['email'] ?? '';
        $default_values['phone'] = $profile['phone'] ?? '';
        $default_values['address'] = $profile['address'] ?? '';
        $default_values['city'] = $profile['city'] ?? '';
        $default_values['postal_code'] = $profile['postal_code'] ?? '';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = clean($_POST['full_name']);
    $email = clean($_POST['email']);
    $phone = clean($_POST['phone']);
    $address = clean($_POST['address']);
    $city = clean($_POST['city']);
    $postal_code = clean($_POST['postal_code']);
    $payment_method = clean($_POST['payment_method']);
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (!$full_name || !$email || !$phone || !$address || !$city) {
        $errors[] = 'Please fill in all required fields.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }
    if (!is_logged_in()) {
        if (!$password) {
            $errors[] = 'Please choose a password so your account can be created for future checkout.';
        } elseif (strlen($password) < 6) {
            $errors[] = 'Password must be at least 6 characters.';
        }
    }

    if (empty($errors)) {
        $user_id = null;

        if (is_logged_in()) {
            $user_id = (int)$_SESSION['user_id'];
        } else {
            $existing_user_result = mysqli_query($conn, "SELECT id, name, is_admin FROM users WHERE email = '$email' LIMIT 1");
            $existing_user = mysqli_fetch_assoc($existing_user_result);

            if ($existing_user) {
                $user_id = (int)$existing_user['id'];
                $_SESSION['user_id'] = $user_id;
                $_SESSION['user_name'] = $existing_user['name'];
                $_SESSION['is_admin'] = (bool)$existing_user['is_admin'];
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                mysqli_query($conn, "INSERT INTO users (name, email, password, phone, address, city, postal_code)
                    VALUES ('$full_name', '$email', '$hash', '$phone', '$address', '$city', '$postal_code')");
                $user_id = mysqli_insert_id($conn);
                $_SESSION['user_id'] = $user_id;
                $_SESSION['user_name'] = $full_name;
                $_SESSION['is_admin'] = false;
            }
        }

        if ($user_id) {
            mysqli_query($conn, "UPDATE users SET name = '$full_name', email = '$email', phone = '$phone', address = '$address', city = '$city', postal_code = '$postal_code' WHERE id = $user_id");

            mysqli_query($conn, "INSERT INTO orders
                (user_id, full_name, email, phone, address, city, postal_code, payment_method, subtotal, shipping_fee, total, status)
                VALUES
                ($user_id, '$full_name', '$email', '$phone', '$address', '$city', '$postal_code', '$payment_method', $subtotal, $shipping, $total, 'pending')");

            $order_id = mysqli_insert_id($conn);

            foreach ($items as $item) {
                $pname = clean($item['name']);
                mysqli_query($conn, "INSERT INTO order_items (order_id, product_id, product_name, price, quantity)
                    VALUES ($order_id, {$item['id']}, '$pname', {$item['price']}, {$item['quantity']})");

                mysqli_query($conn, "UPDATE products SET stock = GREATEST(0, stock - {$item['quantity']}) WHERE id = {$item['id']}");
            }

            cart_clear();
            redirect('order_success.php?id=' . $order_id);
        }
    }
}

$posted = $_SERVER['REQUEST_METHOD'] === 'POST' ? $_POST : [];

require 'includes/header.php';
?>

<section class="wrap" style="padding-top:40px;">
    <div class="breadcrumb"><a href="index.php">Home</a> / <a href="cart.php">Cart</a> / Checkout</div>
    <h2>Checkout</h2>

    <?php foreach ($errors as $err): ?>
        <div class="alert alert-error"><?= htmlspecialchars($err) ?></div>
    <?php endforeach; ?>

    <div style="display:grid;grid-template-columns:1.4fr 1fr;gap:40px;align-items:flex-start;">
        <form method="POST">
            <h3 style="font-size:16px;">Shipping details</h3>
            <div class="form-grid">
                <div class="field">
                    <label>Full name *</label>
                    <input type="text" name="full_name" value="<?= htmlspecialchars($posted['full_name'] ?? $default_values['full_name']) ?>" required>
                </div>
                <div class="field">
                    <label>Email *</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($posted['email'] ?? $default_values['email']) ?>" required>
                </div>
                <div class="field">
                    <label>Phone *</label>
                    <input type="text" name="phone" value="<?= htmlspecialchars($posted['phone'] ?? $default_values['phone']) ?>" required>
                </div>
                <div class="field">
                    <label>City *</label>
                    <input type="text" name="city" value="<?= htmlspecialchars($posted['city'] ?? $default_values['city']) ?>" required>
                </div>
                <div class="field full">
                    <label>Street address *</label>
                    <input type="text" name="address" value="<?= htmlspecialchars($posted['address'] ?? $default_values['address']) ?>" required>
                </div>
                <div class="field">
                    <label>Postal code</label>
                    <input type="text" name="postal_code" value="<?= htmlspecialchars($posted['postal_code'] ?? $default_values['postal_code']) ?>">
                </div>
            </div>

            <?php if (!is_logged_in()): ?>
                <div class="field" style="margin-top:12px;">
                    <label>Create password *</label>
                    <input type="password" name="password" minlength="6" required>
                    <small style="display:block;color:#6b6156;margin-top:6px;">Choose a password so you can log in later and reuse these details.</small>
                </div>
            <?php endif; ?>

            <h3 style="font-size:16px;margin-top:12px;">Payment method</h3>
            <div class="field">
                <label style="display:flex;align-items:center;gap:8px;font-weight:400;margin-bottom:8px;">
                    <input type="radio" name="payment_method" value="cod" checked style="width:auto;"> Cash on delivery
                </label>
                <label style="display:flex;align-items:center;gap:8px;font-weight:400;">
                    <input type="radio" name="payment_method" value="card" style="width:auto;"> Card on delivery / bank transfer
                </label>
            </div>

            <button type="submit" class="btn btn-primary btn-block" style="margin-top:20px;">Place order — <?= money($total) ?></button>
        </form>

        <div class="cart-summary">
            <h3 style="font-size:16px;">Order summary</h3>
            <?php foreach ($items as $item): ?>
                <div style="display:flex;justify-content:space-between;font-size:13px;padding:6px 0;border-bottom:1px solid var(--line);">
                    <span><?= htmlspecialchars($item['name']) ?> × <?= $item['quantity'] ?></span>
                    <span class="mono"><?= money($item['line_total']) ?></span>
                </div>
            <?php endforeach; ?>
            <div class="summary-row" style="margin-top:10px;"><span>Subtotal</span><span class="mono"><?= money($subtotal) ?></span></div>
            <div class="summary-row"><span>Shipping</span><span class="mono"><?= $shipping == 0 ? 'FREE' : money($shipping) ?></span></div>
            <div class="summary-row total"><span>Total</span><span class="mono"><?= money($total) ?></span></div>
        </div>
    </div>
</section>

<?php require 'includes/footer.php'; ?>
